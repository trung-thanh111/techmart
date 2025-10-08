<template>
  <div class="bg-white rounded border border-gray-200 shadow-sm p-4 space-y-4">
    <!-- Header -->
    <div class="flex justify-between items-center border-b border-gray-100">
      <div>
        <h2 class="text-base font-semibold text-gray-900">Phiên bản sản phẩm</h2>
        <p class="text-xs text-gray-500 mt-1">Quản lý các biến thể khác nhau như màu sắc, kích thước, dung lượng...</p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <Checkbox input-id="has-variants" v-model="hasVariants" :binary="true" />
      <label for="has-variants" class="text-sm cursor-pointer text-gray-600 mb-0">Kích hoạt biến thể</label>
    </div>

    <div v-if="hasVariants" class="space-y-5">
      <h3 class="text-sm font-medium text-gray-800">Thuộc tính</h3>
      <div class="space-y-3">
        <div v-for="(row, idx) in groupRows" :key="row.uid" class="flex items-center gap-3">
          <Select class="w-1/3 h-[38px] text-sm" :options="attributeCatalogues" option-label="name" option-value="id" v-model="row.catalogueId" placeholder="Nhóm thuộc tính" :option-disabled="(opt) => isCatalogueOptionDisabled(opt.id, row.uid)" checkmark @update:model-value="onSelectGroup(row)" />
          <MultiSelect class="flex-1 h-[38px] text-[14px]" :options="getAttributesForRow(row)" option-label="name" option-value="id" v-model="row.attributeIds" placeholder="Thuộc tính" display="chip" filter showClear :disabled="!row.catalogueId" @update:model-value="onSelectAttributes(row)" />
        </div>
      </div>
      <Button label="Thêm mới" class="rounded font-light text-sm" size="small" icon="ri-add-line" outlined @click="addRow" />
    </div>

    <!-- Variant Table -->
    <div v-if="variants.length" class="pt-4 border-t border-gray-100">
      <div class="flex justify-between items-center mb-3">
        <h4 class="text-sm font-medium text-gray-800">Biến thể ({{ variants.length }})</h4>
      </div>

      <div class="overflow-x-auto border border-gray-100 rounded-lg">
        <DataTable :value="variants" dataKey="id" :expandedRows="expandedRows" @row-toggle="expandedRows = $event.data" class="p-datatable-sm auto-width-table" rowHover>
          <Column expander class="p-0 w-[1rem]" />
          <Column field="sku" header="SKU" class="auto-width-column" />
          <Column field="name" header="Tên" class="auto-width-column" />
          <Column field="stock" header="Tồn kho" class="auto-width-column">
            <template #body="{ data }">
              <span>{{ data.stock ?? 0 }}</span>
            </template>
          </Column>
          <Column field="price" header="Giá" class="auto-width-column">
            <template #body="{ data }">
              <span>{{ data.price?.toLocaleString("vi-VN") || "0" }} ₫</span>
            </template>
          </Column>

          <template #expansion="{ data, index }" class="p-0">
            <div class="p-2 bg-gray-50 space-y-4">
              <input type="hidden" :name="`variants[${index}][code]`" v-model="data.code" />
              <input type="hidden" :name="`variants[${index}][attributeData]`" :value="JSON.stringify(data.attributeData)" />

              <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600">SKU</label>
                    <InputText v-model="data.sku" class="w-full !h-[38px] text-sm" disabled />
                  </div>
                  <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600">Tên biến thể</label>
                    <InputText v-model="data.name" class="w-full !h-[38px] text-sm" />
                  </div>
                  <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600">Mã vạch</label>
                    <InputText v-model="data.barcode" class="w-full !h-[38px] text-sm" />
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                  <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600">Giá (VNĐ)</label>
                    <InputNumber v-model="data.price" input-class="!h-[38px] text-sm" mode="currency" currency="VND" locale="vi-VN" class="w-full" />
                  </div>
                  <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600">Tồn kho</label>
                    <InputNumber v-model="data.stock" mode="decimal" input-class="!h-[38px] text-sm" showButtons :min="0" fluid class="w-full" />
                  </div>
                </div>

                <div class="flex flex-col gap-1">
                  <label class="text-xs font-medium text-gray-600">Đường dẫn</label>
                  <InputText v-model="data.url" class="w-full !h-[38px] text-sm font-mono" placeholder="https://domain/san-pham?variant=..." />
                </div>

                <div class="flex items-center justify-start py-1">
                  <ToggleSwitch v-model="data.publish" @update:modelValue="" class="scale-75" />
                  <label class="text-xs font-medium text-gray-600 m-0">Trạng thái</label>
                </div>
              </div>

              <div class="flex justify-end gap-2">
                <Button size="small" label="Hủy" outlined class="rounded" severity="secondary" @click="cancelEditVariant(index)" />
                <Button size="small" label="Lưu" class="!px-4 rounded" @click="saveVariant(index)" />
              </div>
            </div>
          </template>
        </DataTable>
      </div>
    </div>

    <div v-else-if="hasVariants" class="text-center py-6">
      <h4 class="text-base font-medium text-gray-700 mb-1">Chưa có biến thể nào</h4>
      <p class="text-sm text-gray-500">Thêm nhóm và giá trị thuộc tính để tạo biến thể</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { useToast } from "primevue/usetoast";
import type { IProductVariant } from "~/interfaces/product/variant.interface";
import { useProductVariantStore } from "~/stores/product/variant.store";

// Composables
const toast = useToast();
const variantStore = useProductVariantStore();

// Reactive state
const hasVariants = ref(false);
type GroupRow = { uid: string; catalogueId: number | null; attributeIds: number[]; lockGroup: boolean };
const groupRows = ref<GroupRow[]>([]);

const attributeCatalogues = computed(() => variantStore.attributeCatalogues);
const variantGroups = computed(() => variantStore.variantGroups);
const variants = computed(() => variantStore.variants);
const config = useRuntimeConfig();
const expandedRows = ref<any[]>([]);

// Props
interface Props {
  productId?: number;
  productSku?: string;
  initialVariants?: IProductVariant[];
  productSlug?: string;
}

const props = withDefaults(defineProps<Props>(), {
  productSku: "",
  initialVariants: () => [],
  productSlug: "",
});

// Methods
const addRow = () => {
  groupRows.value.push({ uid: cryptoRandom(), catalogueId: null, attributeIds: [], lockGroup: false });
};

const onSelectGroup = async (row: GroupRow) => {
  row.lockGroup = !!row.catalogueId;
  row.attributeIds = [];
  if (row.catalogueId) {
    const found = attributeCatalogues.value.find((c) => c.id === row.catalogueId);
    if (found && !found.attributes) {
      await variantStore.loadAttributesByCatalogue(found.id);
    }
  }
  syncGroupsFromRows();
};

const onSelectAttributes = (row: GroupRow) => {
  syncGroupsFromRows();
};

const getAttributesForRow = (row: GroupRow) => {
  const cat = attributeCatalogues.value.find((c) => c.id === row.catalogueId);
  return cat?.attributes || [];
};

const syncGroupsFromRows = () => {
  variantStore.variantGroups = groupRows.value
    .filter((r) => r.catalogueId && r.attributeIds.length > 0)
    .map((r) => {
      const cat = attributeCatalogues.value.find((c) => c.id === r.catalogueId)!;
      const attrs = (cat.attributes || []).filter((a) => r.attributeIds.includes(a.id));
      return { catalogueId: cat.id, catalogueName: cat.name, attributes: attrs } as any;
    });
  generateVariants();
};

// Khi đã có variants trong store, chủ động suy ra nhóm & thuộc tính đang chọn để sinh biến thể ngay
const initializeGroupsFromExistingVariants = async () => {
  if (variants.value.length === 0) return;
  const groupNameToAttrIds = new Map<string, Set<number>>();
  for (const v of variants.value as any[]) {
    const ad = v.attributeData || {};
    for (const [groupName, attr] of Object.entries(ad) as any) {
      if (!groupNameToAttrIds.has(groupName)) groupNameToAttrIds.set(groupName, new Set<number>());
      const set = groupNameToAttrIds.get(groupName)!;
      if (attr?.id) set.add(Number(attr.id));
    }
  }

  groupRows.value = [];
  for (const [groupName, idSet] of groupNameToAttrIds.entries()) {
    const catalogue = attributeCatalogues.value.find((c) => c.name === groupName);
    if (!catalogue) continue;
    if (!catalogue.attributes) {
      await variantStore.loadAttributesByCatalogue(catalogue.id);
    }
    const attributeIds = (catalogue.attributes || []).filter((a) => idSet.has(a.id)).map((a) => a.id);
    if (attributeIds.length) {
      groupRows.value.push({ uid: cryptoRandom(), catalogueId: catalogue.id, attributeIds, lockGroup: true });
    }
  }
  syncGroupsFromRows();
};

const cryptoRandom = () => Math.random().toString(36).slice(2);

const isCatalogueChosen = (catalogueId: number, currentUid?: string) => {
  return groupRows.value.some((r) => r.catalogueId === catalogueId && r.uid !== currentUid);
};
const isCatalogueOptionDisabled = (catalogueId: number, currentUid?: string) => {
  return isCatalogueChosen(catalogueId, currentUid);
};

const loadAttributeCatalogues = async () => {
  try {
    await variantStore.loadAttributeCatalogues();
  } catch (error) {
    console.error("Error loading attribute catalogues:", error);
    toast.add({
      severity: "error",
      summary: "Lỗi",
      detail: "Không thể tải danh sách nhóm thuộc tính",
    });
  }
};

const generateVariants = () => {
  try {
    variantStore.generateVariants();
    if (variants.value.length > 0 && props.productSku) {
      autoGenerateSkus();
      generateVariantName();
      generateVariantBarcodes();
      variants.value.forEach((v) => ((v as any).url = buildVariantLink(v)));
    }
  } catch (error: any) {
    toast.add({
      severity: "warn",
      summary: "Cảnh báo",
      detail: error.message,
      life: 3000,
    });
  }
};

const autoGenerateSkus = () => {
  if (!props.productSku) return;
  variantStore.autoGenerateSkus(props.productSku);
  variants.value.forEach((variant) => {
    if (variant.sku) {
      variant.sku = variant.sku.replace(/_/g, "-");
    }
  });
};

const generateVariantName = () => {
  variants.value.forEach((variant) => {
    if (variant.attributeData && !(variant as any).name) {
      const attributeNames = Object.entries(variant.attributeData)
        .sort(([a], [b]) => a.localeCompare(b))
        .map(([groupName, attr]) => attr.name)
        .join(" ");

      (variant as any).name = attributeNames;
    }
  });
};

// Tạo mã vạch EAN-13 đơn giản
const computeEAN13Checksum = (digits12: string) => {
  let sum = 0;
  for (let i = 0; i < 12; i++) {
    const n = Number(digits12[i]);
    sum += i % 2 === 0 ? n : n * 3;
  }
  const mod = sum % 10;
  return mod === 0 ? 0 : 10 - mod;
};

const generateVariantBarcodes = () => {
  const nowSeed = Date.now().toString();
  variants.value.forEach((variant, idx) => {
    if (!(variant as any).barcode) {
      const base12 = (nowSeed + String(idx)).replace(/\D/g, "").slice(-12).padStart(12, "0");
      const checksum = computeEAN13Checksum(base12);
      (variant as any).barcode = `${base12}${checksum}`;
    }
  });
};

const buildVariantLink = (variant: any) => {
  const base = `${config.public.baseUrl}/${props.productSlug || "san-pham"}`.replace(/\/$/, "");
  const params = new URLSearchParams();
  params.set("variant", String(variant?.id ?? variant?.code ?? "..."));
  Object.entries(variant.attributeData || {}).forEach(([groupName, attr]: any) => {
    params.set(slugify(groupName), String(attr?.id ?? ""));
  });

  const qs = params.toString();
  return qs ? `${base}?${qs}` : base;
};

const cancelEditVariant = (index: number) => {
  const expandedIndex = expandedRows.value.findIndex((row) => row === variants.value[index]);
  if (expandedIndex !== -1) {
    expandedRows.value.splice(expandedIndex, 1);
  }
};

const saveVariant = (index: number) => {
  const variant = variants.value[index];
  cancelEditVariant(index);
};

onMounted(async () => {
  await loadAttributeCatalogues();

  if (props.initialVariants.length > 0) {
    variantStore.setVariants([...props.initialVariants]);
  }

  // Nếu store đã có biến thể, bật biến thể và khởi tạo nhóm/thuộc tính đang chọn
  if ((variantStore.variants?.length || 0) > 0 || props.initialVariants.length > 0) {
    hasVariants.value = true;
    await initializeGroupsFromExistingVariants();
  }
});

watch(
  variantGroups,
  () => {
    generateVariants();
  },
  { deep: true }
);

watch(
  groupRows,
  async (rows) => {
    for (const r of rows) {
      if (r.catalogueId) {
        const cat = attributeCatalogues.value.find((c) => c.id === r.catalogueId);
        if (cat && !cat.attributes) {
          await variantStore.loadAttributesByCatalogue(cat.id);
        }
      }
    }
  },
  { deep: true }
);

// Khi bật biến thể, đảm bảo có ít nhất 1 dòng mặc định
watch(hasVariants, (enabled) => {
  if (enabled) {
    if (groupRows.value.length === 0) addRow();
  } else {
    groupRows.value = [];
    variantStore.variantGroups = [] as any;
    variantStore.setVariants([] as any);
  }
});
</script>

<style scoped>
:deep(.p-datatable .p-datatable-tbody > tr > td) {
  padding: 0.9rem 0.75rem;
}

:deep(.p-datatable .p-datatable-thead > tr > th) {
  padding: 0.75rem 0.75rem;
  background-color: rgb(243 244 246) !important;
  font-size: 0.8rem;
  font-weight: 600;
}

:deep(.p-datatable .p-datatable-tbody > tr > td .p-inputtext),
:deep(.p-datatable .p-datatable-tbody > tr > td .p-inputnumber) {
  font-size: 0.75rem;
}

.auto-width-table {
  table-layout: auto !important;
  width: 100% !important;
}

.auto-width-table :deep(.p-datatable-table) {
  table-layout: auto !important;
  width: 100% !important;
}

.auto-width-column {
  width: auto !important;
  min-width: fit-content !important;
  white-space: nowrap;
}

.auto-width-column :deep(th),
.auto-width-column :deep(td) {
  width: auto !important;
  min-width: fit-content !important;
  white-space: nowrap;
  padding: 0.5rem 0.75rem;
}
</style>
