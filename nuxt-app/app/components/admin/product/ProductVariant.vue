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
        <div v-for="(row, idx) in groupRows" :key="row.uid" class="flex items-center gap-3 hover:border-blue-300 transition">
          <Select class="w-1/3 h-[42px]" :options="attributeCatalogues" option-label="name" option-value="id" v-model="row.catalogueId" placeholder="Nhóm thuộc tính" :option-disabled="(opt) => isCatalogueOptionDisabled(opt.id, row.uid)" checkmark @update:model-value="onSelectGroup(row)" />
          <MultiSelect class="flex-1 h-[42px] text-[16px]" :options="getAttributesForRow(row)" option-label="name" option-value="id" v-model="row.attributeIds" placeholder="Thuộc tính" display="chip" filter showClear :disabled="!row.catalogueId" @update:model-value="onSelectAttributes(row)" />

          <Button icon="ri-delete-bin-line" size="small" severity="danger" text rounded @click="removeRow(idx)" />
        </div>
      </div>
      <Button label="Thêm mới" class="rounded" size="small" icon="ri-add-line" outlined @click="addRow" />

      <!-- Tag nhóm đã chọn -->
      <div v-if="variantGroups.length" class="pt-3 border-t border-gray-100">
        <div class="text-xs text-gray-500 mb-2">Đã chọn:</div>
        <div class="flex flex-wrap gap-2">
          <Tag v-for="group in variantGroups" :key="group.catalogueId" :value="`${group.catalogueName} (${group.attributes.length})`" severity="info" class="text-xs" />
        </div>
      </div>
    </div>

    <!-- Variant Table -->
    <div v-if="variants.length" class="pt-4 border-t border-gray-100">
      <div class="flex justify-between items-center mb-3">
        <h4 class="text-sm font-medium text-gray-800">Danh sách biến thể ({{ variants.length }})</h4>
        <div class="flex gap-2">
          <Button @click="autoGenerateSkus" size="small" icon="ri-barcode-line" severity="secondary" label="Tự động tạo SKU" />
        </div>
      </div>

      <DataTable :value="variants" class="p-datatable-sm border border-gray-100 rounded-lg" :paginator="true" :rows="10" rowHover>
        <Column field="sku" header="SKU" />
        <Column field="code" header="Mã" />
        <Column field="attributeData" header="Thuộc tính">
          <template #body="{ data }">
            <div class="flex flex-wrap gap-1">
              <Tag v-for="(attr, group) in data.attributeData" :key="group" :value="`${group}: ${attr.name}`" severity="secondary" class="text-[11px]" />
            </div>
          </template>
        </Column>

        <Column field="price" header="Giá">
          <template #body="{ data }">
            <InputNumber v-model="data.price" input-class="!h-[34px]" mode="currency" currency="VND" locale="vi-VN" class="w-[140px]" />
          </template>
        </Column>

        <Column field="stock" header="Tồn kho">
          <template #body="{ data }">
            <InputNumber v-model="data.stock" input-class="!h-[34px]" show-buttons class="w-[100px]" />
          </template>
        </Column>

        <Column>
          <template #body="{ index }">
            <Button icon="ri-delete-bin-line" size="small" severity="danger" text rounded @click="removeVariant(index)" />
          </template>
        </Column>
      </DataTable>
    </div>

    <!-- Save button -->
    <div v-if="variants.length" class="pt-4 border-t border-gray-100 flex justify-end">
      <Button label="Lưu các phiên bản" icon="ri-save-3-line" class="!h-10 !px-6" :loading="saving" @click="saveVariants" />
    </div>

    <!-- Empty state -->
    <div v-else-if="hasVariants" class="text-center py-6">
      <h4 class="text-base font-medium text-gray-700 mb-1">Chưa có biến thể nào</h4>
      <p class="text-sm text-gray-500">Thêm nhóm và giá trị thuộc tính để tạo biến thể</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { useToast } from "primevue/usetoast";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import Checkbox from "primevue/checkbox";
import Dropdown from "primevue/dropdown";
import MultiSelect from "primevue/multiselect";
import Tag from "primevue/tag";
import Dialog from "primevue/dialog";
import type { IAttributeCatalogue, IAttribute, IProductVariant } from "~/interfaces/product/variant.interface";
import { generateDefaultSku } from "~/utils/variantHelpers";
import { useProductVariantStore } from "~/stores/product/variant.store";

// Props
interface Props {
  productId?: number;
  productSku?: string;
  initialVariants?: IProductVariant[];
}

const props = withDefaults(defineProps<Props>(), {
  productSku: "",
  initialVariants: () => [],
});

// Composables
const toast = useToast();
const variantStore = useProductVariantStore();

// Reactive state
const hasVariants = ref(false);
type GroupRow = { uid: string; catalogueId: number | null; attributeIds: number[]; lockGroup: boolean };
const groupRows = ref<GroupRow[]>([]);
const saving = ref(false);
const showWarningDialog = ref(false);
const warningMessage = ref("");
const pendingRegenerate = ref(false);

// Computed
const productId = computed(() => props.productId);
const selectedCatalogue = computed(() => variantStore.selectedCatalogue);
const canAddGroup = computed(() => variantStore.canAddGroup);
const attributeCatalogues = computed(() => variantStore.attributeCatalogues);
const selectedCatalogueId = computed(() => variantStore.selectedCatalogueId);
const selectedAttributes = computed(() => variantStore.selectedAttributes);
const variantGroups = computed(() => variantStore.variantGroups);
const variants = computed(() => variantStore.variants);

// Methods
const addRow = () => {
  groupRows.value.push({ uid: cryptoRandom(), catalogueId: null, attributeIds: [], lockGroup: false });
};

const removeRow = (idx: number) => {
  groupRows.value.splice(idx, 1);
  syncGroupsFromRows();
};

const onSelectGroup = async (row: GroupRow) => {
  row.lockGroup = !!row.catalogueId; // khóa chọn group sau khi đã chọn
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
  // Build variantGroups from rows
  variantStore.variantGroups = groupRows.value
    .filter((r) => r.catalogueId && r.attributeIds.length > 0)
    .map((r) => {
      const cat = attributeCatalogues.value.find((c) => c.id === r.catalogueId)!;
      const attrs = (cat.attributes || []).filter((a) => r.attributeIds.includes(a.id));
      return { catalogueId: cat.id, catalogueName: cat.name, attributes: attrs } as any;
    });
  generateVariants();
};

const cryptoRandom = () => Math.random().toString(36).slice(2);

// Disable catalogue options already chosen in other rows
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
    
const selectCatalogue = async (catalogue: IAttributeCatalogue) => {
  await variantStore.selectCatalogue(catalogue);
};

const isCatalogueDisabled = (catalogueId: number) => {
  return variantStore.isCatalogueDisabled(catalogueId);
};

const isAttributeSelected = (attributeId: number) => {
  return selectedAttributes.value.includes(attributeId);
};

const toggleAttribute = (attribute: IAttribute) => {
  variantStore.toggleAttribute(attribute);
};

const addGroupToVariants = () => {
  // Deprecated in step UI; rows drive groups
};

const removeGroup = (catalogueId: number) => {
  // Deprecated in step UI
};

const generateVariants = () => {
  try {
    variantStore.generateVariants();
  } catch (error: any) {
    toast.add({
      severity: "warn",
      summary: "Cảnh báo",
      detail: error.message,
      life: 3000,
    });
  }
};

const regenerateVariants = () => {
  if (variants.value.length > 0) {
    // Check if any variants have been modified
    const hasModifiedVariants = variants.value.some((v) => v.sku !== generateDefaultSku(props.productSku || "", v.code) || v.price !== null || v.stock !== 0 || v.barcode !== null || v.album.length > 0);

    if (hasModifiedVariants) {
      warningMessage.value = "Việc tạo lại biến thể sẽ xóa tất cả dữ liệu đã chỉnh sửa. Bạn có chắc chắn?";
      pendingRegenerate.value = true;
      showWarningDialog.value = true;
      return;
    }
  }

  generateVariants();
};

const confirmRegenerate = () => {
  showWarningDialog.value = false;
  if (pendingRegenerate.value) {
    generateVariants();
    pendingRegenerate.value = false;
  }
};

const autoGenerateSkus = () => {
  if (!props.productSku) return;

  variantStore.autoGenerateSkus(props.productSku);

  toast.add({
    severity: "success",
    summary: "Thành công",
    detail: "Đã tự động tạo SKU cho tất cả biến thể",
  });
};

const onCellEditComplete = (event: any) => {
  const { data, field, newValue } = event;
  data[field] = newValue;
};

const removeVariant = (index: number) => {
  variantStore.removeVariant(index);
};

const saveVariants = async () => {
  if (variants.value.length === 0) return;

  if (!props.productId) {
    toast.add({
      severity: "warn",
      summary: "Cảnh báo",
      detail: "Vui lòng lưu sản phẩm trước khi tạo biến thể",
    });
    return;
  }

  saving.value = true;

  try {
    await variantStore.saveVariants(props.productId, props.productSku || "");

    toast.add({
      severity: "success",
      summary: "Thành công",
      detail: `Đã lưu ${variants.value.length} biến thể`,
    });
  } catch (error: any) {
    console.error("Error saving variants:", error);

    let errorMessage = "Không thể lưu biến thể";
    if (error.status === 409) {
      errorMessage = "SKU đã tồn tại trong hệ thống";
    } else if (error.data?.message) {
      errorMessage = error.data.message;
    }

    toast.add({
      severity: "error",
      summary: "Lỗi",
      detail: errorMessage,
    });
  } finally {
    saving.value = false;
  }
};

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement;
  img.style.display = "none";
};

// Lifecycle
onMounted(() => {
  loadAttributeCatalogues();

  if (props.initialVariants.length > 0) {
    variantStore.setVariants([...props.initialVariants]);
  }
});

// Watch for changes
watch(
  variantGroups,
  () => {
    generateVariants();
  },
  { deep: true }
);

// Khi chọn nhóm -> load attributes ngay (behaviour requested)
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
.product-variant-manager {
  @apply space-y-4;
}

:deep(.p-datatable .p-datatable-tbody > tr > td) {
  padding: 0.5rem;
}

:deep(.p-datatable .p-datatable-thead > tr > th) {
  padding: 0.75rem 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
}

:deep(.p-datatable .p-datatable-tbody > tr > td .p-inputtext),
:deep(.p-datatable .p-datatable-tbody > tr > td .p-inputnumber) {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
}
</style>
