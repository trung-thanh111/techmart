<template>
  <Dialog v-model:visible="isVisible" modal :header="$t('product.selectVariant')" :style="{ width: '90%', maxWidth: '600px' }" :draggable="false">
    <div v-if="product" class="p-4">
      <div class="flex gap-4">
        <!-- Image -->
        <div class="w-1/3">
          <NuxtImg :src="selectedVariant?.image || product.album?.[0] || ''" loading="lazy" class="w-full aspect-square object-contain rounded border border-gray-200" />
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

const hashString = (str: string): number => {
  let hash = 0;
  for (let i = 0; i < str.length; i++) {
    hash = (hash << 5) - hash + str.charCodeAt(i);
    hash |= 0;
  }
  return Math.abs(hash) || 1;
};

const getNormalizedAttributeData = (variant: any): Record<string, { id: number; name: string }> => {
  if (!variant?.attributeData) return {};
  let raw = variant.attributeData;
  if (typeof raw === "string") {
    try {
      raw = JSON.parse(raw);
      if (typeof raw === "string") raw = JSON.parse(raw);
    } catch {
      return {};
    }
  }
  if (!raw || typeof raw !== "object") return {};

  const result: Record<string, { id: number; name: string }> = {};

  Object.entries(raw).forEach(([key, val]: [string, any]) => {
    if (!val) return;
    let groupName = key;
    let id = typeof val === "object" ? (val.id ?? val.attribute_id ?? Number(key)) : Number(key) || 0;
    let name = typeof val === "object" ? (val.name ?? val.value ?? val.label ?? "") : String(val);

    if (/^\d+$/.test(groupName)) {
      if (typeof val === "string") {
        name = val;
        id = Number(key);
      }
      groupName = "Thuộc tính";
    }

    if (name) {
      result[groupName] = {
        id: id || hashString(name),
        name,
      };
    }
  });

  return result;
};

// Compute attribute groups from product variants
const attributeGroups = computed(() => {
  if (!props.product?.productVariants?.length) return {};
  const groups: Record<string, { id: number; name: string }[]> = {};

  (props.product.productVariants as any[]).forEach((variant: any) => {
    const attrData = getNormalizedAttributeData(variant);
    Object.entries(attrData).forEach(([attrName, attrValue]) => {
      if (!groups[attrName]) groups[attrName] = [];
      if (!groups[attrName].find((i) => i.id === attrValue.id || i.name === attrValue.name)) {
        groups[attrName].push(attrValue);
      }
    });
  });
  return groups;
});

const findMatchingVariant = () => {
  if (!props.product?.productVariants?.length) return null;
  return (props.product.productVariants as any[]).find((variant: any) => {
    const attrData = getNormalizedAttributeData(variant);
    if (!Object.keys(attrData).length) return false;
    return Object.entries(selectedAttributes.value).every(([attrName, attrId]) => {
      return attrData[attrName]?.id === attrId;
    });
  });
};

const handleSelectAttribute = (attrName: string, id: number) => {
  selectedAttributes.value[attrName] = id;
  const matched = findMatchingVariant();
  if (matched) {
    selectedVariant.value = matched;
  } else {
    const fallback = (props.product?.productVariants as any[])?.find((v: any) => {
      const data = getNormalizedAttributeData(v);
      return data[attrName]?.id === id;
    });
    if (fallback) {
      selectedVariant.value = fallback;
      const data = getNormalizedAttributeData(fallback);
      Object.entries(data).forEach(([name, val]) => {
        selectedAttributes.value[name] = val.id;
      });
    }
  }
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
        const data = getNormalizedAttributeData(first);
        if (Object.keys(data).length) {
          Object.entries(data).forEach(([k, v]) => {
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
