<template>
  <Dialog v-model:visible="isVisible" modal :header="$t('product.selectVariant')" :style="{ width: '90%', maxWidth: '600px' }" :draggable="false">
    <div v-if="product" class="p-4">
      <div class="flex gap-4">
        <!-- Image -->
        <div class="w-1/3">
          <NuxtImg :src="selectedVariant?.image || product.album?.[0] || ''" class="w-full aspect-square object-contain rounded border border-gray-200" />
        </div>

        <!-- Info -->
        <div class="w-2/3 space-y-3">
          <h3 class="font-medium text-lg">{{ product.name }}</h3>

          <div class="text-red-600 font-bold text-xl">
            {{ formatPrice(selectedVariant?.price ?? product.priceOrigin) }}
          </div>

          <!-- Attributes -->
          <div v-if="attributeGroups && Object.keys(attributeGroups).length > 0">
            <div v-for="(attributes, attrName) in attributeGroups" :key="attrName" class="mb-3">
              <div class="flex flex-col gap-2">
                <span class="text-sm font-medium text-gray-700">{{ attrName }}:</span>
                <div class="flex flex-wrap gap-2">
                  <div v-for="attr in attributes" :key="attr.id" class="cursor-pointer px-3 py-1 border rounded text-sm transition-colors" :class="[selectedAttributes[attrName] === attr.id ? 'border-sky-500 bg-sky-50 text-sky-700 font-semibold' : 'border-gray-300 hover:border-sky-300']" @click="handleSelectAttribute(attrName, attr.id)">
                    {{ attr.name }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-sm text-gray-500 italic">
            {{ $t("product.noVariants") }}
          </div>

          <!-- Quantity (only for cart) -->
          <div v-if="mode === 'cart'" class="flex items-center gap-3 mt-4">
            <span class="text-sm font-medium text-gray-700">{{ $t("field.quantity") }}:</span>
            <div class="flex items-center border border-gray-300 rounded">
              <button @click="quantity > 1 && quantity--" class="px-3 py-1 hover:bg-gray-100">-</button>
              <input v-model.number="quantity" type="number" min="1" class="w-12 text-center border-x border-gray-300 py-1 focus:outline-none" />
              <button @click="quantity++" class="px-3 py-1 hover:bg-gray-100">+</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end gap-3 mt-6">
        <Button :label="$t('common.cancel')" severity="secondary" @click="close" />
        <Button :label="mode === 'cart' ? $t('cart.addToCart') : $t('product.addToWishlist')" :disabled="!selectedVariant" @click="confirm" />
      </div>
    </div>
  </Dialog>
</template>

<script setup lang="ts">
import { computed, ref, watch, onMounted } from "vue";
import type { IProduct } from "~/interfaces/product/product.interface";
import { formatPrice } from "~/utils/helpers"; // Assuming this helper exists

const props = defineProps<{
  visible: boolean;
  product: IProduct | null;
  mode: "cart" | "wishlist";
}>();

const emit = defineEmits(["update:visible", "confirm"]);

const isVisible = computed({
  get: () => props.visible,
  set: (val) => emit("update:visible", val),
});

const selectedAttributes = ref<Record<string, number>>({});
const selectedVariant = ref<any>(null);
const quantity = ref(1);

// Compute attribute groups from product variants
const attributeGroups = computed(() => {
  if (!props.product?.productVariants?.length) return {};
  const groups: Record<string, { id: number; name: string }[]> = {};

  (props.product.productVariants as any[]).forEach((variant: any) => {
    if (variant?.attributeData) {
      Object.entries(variant.attributeData).forEach(([attrName, attrValue]: [string, any]) => {
        if (!groups[attrName]) groups[attrName] = [];
        if (!groups[attrName].find((i) => i.id === attrValue.id)) {
          groups[attrName].push(attrValue);
        }
      });
    }
  });
  return groups;
});

const findMatchingVariant = () => {
  if (!props.product?.productVariants?.length) return null;
  return (props.product.productVariants as any[]).find((variant: any) => {
    if (!variant?.attributeData) return false;
    return Object.entries(selectedAttributes.value).every(([attrName, attrId]) => {
      return variant.attributeData?.[attrName]?.id === attrId;
    });
  });
};

const handleSelectAttribute = (attrName: string, id: number) => {
  selectedAttributes.value[attrName] = id;
  selectedVariant.value = findMatchingVariant();
};

const close = () => {
  isVisible.value = false;
};

const confirm = () => {
  if (selectedVariant.value) {
    emit("confirm", {
      variant: selectedVariant.value,
      quantity: quantity.value,
      mode: props.mode,
    });
    close();
  }
};

watch(
  () => props.product,
  (newProduct) => {
    if (newProduct) {
      // Reset state
      selectedAttributes.value = {};
      quantity.value = 1;
      selectedVariant.value = null;

      // Auto select first variant's attributes
      if (newProduct.productVariants?.length) {
        const first = (newProduct.productVariants as any[])[0];
        if (first.attributeData) {
          Object.entries(first.attributeData).forEach(([k, v]: [string, any]) => {
            selectedAttributes.value[k] = v.id;
          });
          selectedVariant.value = first;
        } else {
          selectedVariant.value = first; // No attributes, just select it
        }
      }
    }
  },
  { immediate: true },
);
</script>
