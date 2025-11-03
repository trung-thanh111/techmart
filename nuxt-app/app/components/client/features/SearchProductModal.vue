<template>
  <Dialog v-model:visible="visible" modal :header="$t('client.utilityTool.searchProduct')" :style="{ width: '90vw', maxWidth: '800px' }" :breakpoints="{ '1199px': '95vw', '575px': '98vw' }" class="search-product-modal" @hide="$emit('close')">
    <div class="flex flex-col gap-4">
      <div class="relative">
        <InputText v-model="searchQuery" :placeholder="$t('client.utilityTool.searchPlaceholder')" class="w-full" autofocus @input="handleSearch" />
        <i class="pi pi-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" />
      </div>
      <div v-if="isLoading" class="flex justify-center items-center py-8">
        <ProgressSpinner />
      </div>

      <div v-else class="space-y-4 max-h-[60vh] overflow-y-auto">
        <div v-if="!searchQuery && featuredProducts.length > 0">
          <h4 class="text-sm font-semibold text-gray-600 mb-2">{{ $t("client.utilityTool.featuredProducts") }}</h4>
          <div class="grid grid-cols-1 gap-2">
            <div v-for="product in featuredProducts" :key="product.id" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors" @click="handleSelectProduct(product)">
              <div class="w-16 h-16 flex-shrink-0 rounded overflow-hidden bg-gray-100">
                <img v-if="getProductImage(product)" :src="getProductImage(product)" :alt="product.name" class="w-full h-full object-contain" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-700 truncate">{{ getProductDisplayName(product) }}</p>
                <p class="text-sm font-bold text-red-600 mt-1">{{ formatPrice(getProductPrice(product)) }}</p>
              </div>
              <Checkbox v-if="selectionMode === 'checkbox'" :model-value="isSelected(product)" @update:model-value="handleCheckboxChange(product, $event)" />
            </div>
          </div>
        </div>

        <div v-if="searchQuery && searchResults.length > 0">
          <h4 class="text-sm font-semibold text-gray-600 mb-2">{{ $t("client.utilityTool.suggestion") }}</h4>
          <div class="grid grid-cols-1 gap-2">
            <div v-for="product in searchResults" :key="product.id" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors" @click="handleSelectProduct(product)">
              <div class="w-16 h-16 flex-shrink-0 rounded overflow-hidden bg-gray-100">
                <img v-if="getProductImage(product)" :src="getProductImage(product)" :alt="product.name" class="w-full h-full object-contain" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-700 truncate">{{ getProductDisplayName(product) }}</p>
                <p class="text-sm font-bold text-red-600 mt-1">{{ formatPrice(getProductPrice(product)) }}</p>
              </div>
              <Checkbox v-if="selectionMode === 'checkbox'" :model-value="isSelected(product)" @update:model-value="handleCheckboxChange(product, $event)" />
            </div>
          </div>
        </div>

        <div v-if="!isLoading && ((searchQuery && searchResults.length === 0) || (!searchQuery && featuredProducts.length === 0))" class="text-center py-8 text-gray-500">
          <p>{{ searchQuery ? "Không tìm thấy sản phẩm" : "Không có sản phẩm nổi bật" }}</p>
        </div>
      </div>
    </div>

    <template #footer>
      <div v-if="selectionMode === 'checkbox' && selectedProducts.length > 0" class="flex justify-between items-center">
        <span class="text-sm text-gray-600">{{ selectedProducts.length }} sản phẩm đã chọn</span>
        <Button label="Thêm vào so sánh" icon="pi pi-check" @click="handleAddSelected" :disabled="isAdding" />
      </div>
    </template>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, watch, computed } from "vue";
import { useCompare } from "~/composables/client/product/useCompare";
import { resolveAlbum, formatPrice } from "~/utils/helpers";
import { useToast } from "primevue/usetoast";
import { useDebounceFn } from "@vueuse/core";
import { productService } from "~/services/product/product.service";
import type { IProduct } from "~/interfaces/product/product.interface";

interface Props {
  modelValue: boolean;
  selectionMode?: "record" | "checkbox";
}

const props = withDefaults(defineProps<Props>(), {
  selectionMode: "record",
});

const emit = defineEmits<{
  "update:modelValue": [value: boolean];
  close: [];
  added: [products: IProduct[]];
}>();

const { searchProducts, getFeaturedProducts, addToCompare, isFull, compareCount } = useCompare();
const toast = useToast();

const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit("update:modelValue", value),
});

const searchQuery = ref("");
const searchResults = ref<IProduct[]>([]);
const featuredProducts = ref<IProduct[]>([]);
const selectedProducts = ref<IProduct[]>([]);
const isLoading = ref(false);
const isAdding = ref(false);

// Debounce search
const debouncedSearch = useDebounceFn(async (keyword: string) => {
  if (!keyword.trim()) {
    searchResults.value = [];
    return;
  }

  isLoading.value = true;
  try {
    const results = await searchProducts(keyword, 20);
    searchResults.value = results;
  } catch (error) {
    console.error("Search error:", error);
    searchResults.value = [];
  } finally {
    isLoading.value = false;
  }
}, 300);

const handleSearch = () => {
  debouncedSearch(searchQuery.value);
};

const getProductImage = (product: IProduct): string => {
  const images = resolveAlbum(product.album || []);
  return images[0] || "";
};

const getProductDisplayName = (product: IProduct): string => {
  const firstVariant = product.productVariants?.[0];
  if (firstVariant?.name) {
    return `${product.name || ""} ${firstVariant.name}`.trim();
  }
  return product.name || "";
};

const getProductPrice = (product: IProduct): number => {
  const firstVariant = product.productVariants?.[0];
  return firstVariant?.price || product.priceOrigin || 0;
};

const handleSelectProduct = async (product: IProduct) => {
  if (props.selectionMode === "checkbox") {
    toggleSelection(product);
    return;
  }

  // Record mode: thêm ngay lập tức
  await addProductToCompare(product);
};

const toggleSelection = (product: IProduct) => {
  const index = selectedProducts.value.findIndex((p) => p.id === product.id);
  if (index > -1) {
    selectedProducts.value.splice(index, 1);
  } else {
    const currentCount = isFull.value ? 0 : compareCount.value;
    if (selectedProducts.value.length + currentCount >= 3) {
      toast.add({
        severity: "warn",
        summary: "Thông báo",
        detail: "Chỉ được chọn tối đa 3 sản phẩm",
        life: 3000,
      });
      return;
    }
    selectedProducts.value.push(product);
  }
};

const isSelected = (product: IProduct): boolean => {
  return selectedProducts.value.some((p) => p.id === product.id);
};

const handleCheckboxChange = (product: IProduct, checked: boolean) => {
  if (checked) {
    if (!isSelected(product)) {
      toggleSelection(product);
    }
  } else {
    const index = selectedProducts.value.findIndex((p) => p.id === product.id);
    if (index > -1) {
      selectedProducts.value.splice(index, 1);
    }
  }
};

const addProductToCompare = async (product: IProduct) => {
  try {
    // Kiểm tra và load product detail nếu chưa có variants
    let productToAdd = product;
    if (!product.productVariants || product.productVariants.length === 0) {
      // Load product detail để lấy variants
      try {
        const response = await productService.show(product.id!);
        if (response.data) {
          productToAdd = response.data;
        }
      } catch (error) {
        console.error("Error loading product detail:", error);
      }
    }

    const firstVariant = productToAdd.productVariants?.[0];
    if (!firstVariant) {
      toast.add({
        severity: "error",
        summary: "Lỗi",
        detail: "Sản phẩm không có phiên bản",
        life: 3000,
      });
      return;
    }

    const success = addToCompare(productToAdd, firstVariant);
    if (success) {
      emit("added", [productToAdd]);
      if (props.selectionMode === "record") {
        visible.value = false;
      }
    }
  } catch (error) {
    console.error("Error adding product to compare:", error);
    toast.add({
      severity: "error",
      summary: "Lỗi",
      detail: "Không thể thêm sản phẩm vào danh sách so sánh",
      life: 3000,
    });
  }
};

const handleAddSelected = async () => {
  if (selectedProducts.value.length === 0) return;

  isAdding.value = true;
  try {
    const added: IProduct[] = [];
    for (const product of selectedProducts.value) {
      const firstVariant = product.productVariants?.[0];
      if (firstVariant && addToCompare(product, firstVariant)) {
        added.push(product);
      }
    }

    if (added.length > 0) {
      emit("added", added);
      selectedProducts.value = [];
      visible.value = false;
    }
  } finally {
    isAdding.value = false;
  }
};

// Load featured products khi mở modal
watch(visible, async (newVal) => {
  if (newVal) {
    searchQuery.value = "";
    searchResults.value = [];
    selectedProducts.value = [];
    isLoading.value = true;
    try {
      featuredProducts.value = await getFeaturedProducts(10);
    } catch (error) {
      console.error("Error loading featured products:", error);
      featuredProducts.value = [];
    } finally {
      isLoading.value = false;
    }
  }
});
</script>
