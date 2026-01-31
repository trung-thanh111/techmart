<template>
  <div ref="dropdownRef" class="relative inline-block group">
    <Button unstyled @click="toggleDropdown" class="flex items-center gap-2 px-3 py-1.5 border border-gray-300 rounded text-sm transition select-none" :class="[hasActivePrice ? 'bg-sky-50 border-sky-500 text-sky-600' : 'text-gray-600 hover:border-sky-500 hover:text-sky-500']">
      <span>{{ $t("common.price") }}</span>
      <i class="pi pi-chevron-down text-xs"></i>
    </Button>

    <div v-if="showDropdown" class="absolute top-full left-0 mt-1 w-80 bg-white border border-gray-200 rounded-lg shadow-xl z-50">
      <div class="p-4">
        <div class="flex flex-wrap gap-2 mb-4">
          <Button v-for="option in priceOptions" :key="option.label" unstyled @click="selectPriceOption(option)" class="px-3 py-2 rounded text-xs transition select-none" :class="[isSelectedOption(option) ? 'bg-sky-50 text-sky-600 border border-sky-500' : 'text-gray-700 border border-gray-200 hover:bg-gray-50']">
            {{ option.label }}
          </Button>
        </div>
        <div class="border-t border-gray-200 pt-4">
          <label class="block text-sm font-medium text-gray-700 mb-3">{{ $t("common.selectPriceRange") }}</label>
          <div class="flex flex-wrap items-center gap-2">
            <InputNumber v-model="localMinPrice" inputClass="flex-1 min-w-0 px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" mode="currency" currency="VND" locale="vi-VN" :min="0" :max="localMaxPrice" placeholder="0" class="flex-1 min-w-[100px]" />
            <span class="text-gray-500">-</span>
            <InputNumber v-model="localMaxPrice" inputClass="flex-1 min-w-0 px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" mode="currency" currency="VND" locale="vi-VN" :min="localMinPrice" :max="maxPrice" placeholder="0" class="flex-1 min-w-[100px]" />
          </div>
        </div>
        <div class="flex flex-col sm:flex-row items-center gap-3 mt-4 pt-4 border-t border-gray-200">
          <Button unstyled @click="handleClose" class="flex-1 px-4 py-2 text-sm border border-sky-500 text-sky-500 rounded hover:bg-sky-50 transition">{{ $t("common.close") }}</Button>
          <Button unstyled @click="handleApply" class="flex-1 px-4 py-2 text-sm bg-sky-500 text-white rounded hover:bg-sky-600 transition">{{ $t("common.viewResults") }}</Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps<{
  priceRange: [number, number];
}>();

const emit = defineEmits<{
  "update:price": [range: [number, number]];
}>();

const minPrice = 1000;
const maxPrice = 300000000;
const showDropdown = ref(false);

const priceOptions = [
  { label: t("common.under1000000"), min: minPrice, max: 1000000 },
  { label: t("common.from2000000to5000000"), min: 2000000, max: 5000000 },
  { label: t("common.from5000000to10000000"), min: 5000000, max: 10000000 },
  { label: t("common.over10000000"), min: 10000000, max: maxPrice },
];

const localMinPrice = ref(props.priceRange[0]);
const localMaxPrice = ref(props.priceRange[1]);
const selectedOption = ref<{ min: number; max: number } | null>(null);

const hasActivePrice = computed(() => {
  return props.priceRange[0] !== minPrice || props.priceRange[1] !== maxPrice;
});

const isSelectedOption = (option: { min: number; max: number }) => {
  if (selectedOption.value) {
    return selectedOption.value.min === option.min && selectedOption.value.max === option.max;
  }
  return props.priceRange[0] === option.min && props.priceRange[1] === option.max;
};

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
  if (showDropdown.value) {
    localMinPrice.value = props.priceRange[0];
    localMaxPrice.value = props.priceRange[1];
    selectedOption.value = null;
  }
};

const selectPriceOption = (option: { min: number; max: number }) => {
  selectedOption.value = option;
  localMinPrice.value = option.min;
  localMaxPrice.value = option.max;
};

const handleApply = () => {
  const min = Math.max(minPrice, localMinPrice.value || minPrice);
  const max = Math.min(maxPrice, localMaxPrice.value || maxPrice);
  emit("update:price", [min, max]);
  showDropdown.value = false;
};

const handleClose = () => {
  showDropdown.value = false;
};

// Close dropdown when clicking outside
const dropdownRef = ref<HTMLElement | null>(null);

const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement;
  if (dropdownRef.value && !dropdownRef.value.contains(target)) {
    showDropdown.value = false;
  }
};

onMounted(() => {
  if (import.meta.client) {
    document.addEventListener("click", handleClickOutside);
  }
});

onBeforeUnmount(() => {
  if (import.meta.client) {
    document.removeEventListener("click", handleClickOutside);
  }
});
</script>
