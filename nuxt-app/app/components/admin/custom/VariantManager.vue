<template>
  <div class="bg-white rounded border border-gray-200 shadow-sm p-4 space-y-4">
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
          <MultiSelect class="flex-1 h-[38px] text-sm" :options="getAttributesForRow(row)" option-label="name" option-value="id" v-model="row.attributeIds" placeholder="Thuộc tính" display="chip" filter showClear :disabled="!row.catalogueId" @update:model-value="onSelectAttributes(row)">
            <template #value="{ value, placeholder }">
              <div class="flex items-center gap-1 overflow-hidden">
                <template v-if="value && value.length > 0">
                  <span v-for="(item, index) in value.slice(0, 3)" :key="item" class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                    {{ getAttributesForRow(row).find((attr) => attr.id === item)?.name }}
                    <i class="ri-close-line cursor-pointer hover:text-blue-600" @click.stop="removeAttribute(row, item)"></i>
                  </span>
                  <span v-if="getOverflowCount(row) > 0" class="inline-flex items-center bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">+{{ getOverflowCount(row) }}</span>
                </template>
                <span v-else class="text-gray-500">{{ placeholder }}</span>
              </div>
            </template>
            <template #footer>
              <div class="p-3 cursor-pointer flex items-center justify-end font-light text-xs text-emerald-600 gap-1">
                <i class="ri-add-line text-emerald-600"></i>
                <span @click="showCreateAttributeModal = true">Thêm mới</span>
              </div>
            </template>
          </MultiSelect>
          <Button type="button" icon="ri-delete-bin-line" class="rounded-circle" severity="danger" size="small" outlined rounded @click="removeRow(row.uid)" :disabled="groupRows.length <= 1" v-tooltip.bottom="'Xóa dòng'" />
        </div>
      </div>
      <Button type="button" label="Thêm mới" class="rounded font-light text-sm" size="small" icon="ri-add-line" outlined @click="addRow" />
    </div>

    <div v-if="variants.length" class="pt-4 border-t border-gray-100">
      <div class="flex justify-between items-center mb-3">
        <h4 class="text-sm font-medium text-gray-800">Biến thể ({{ variants.length }})</h4>
      </div>

      <div class="overflow-x-auto border border-gray-100 rounded-lg">
        <DataTable :value="variants" dataKey="id" :expandedRows="expandedRows" @row-toggle="expandedRows = $event.data" class="p-datatable-sm auto-width-table" rowHover>
          <Column expander class="p-0 w-[1rem] pl-2">
            <template #header>
              <i :class="allExpanded ? 'ri-arrow-down-s-line' : 'ri-arrow-right-s-line'" class="cursor-pointer text-gray-600 hover:text-gray-800 text-2xl text-center" @click="toggleAllExpanded"></i>
            </template>
          </Column>
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
              <Field :name="`productVariants.${index}.code`" v-slot="{ field }">
                <input type="hidden" v-bind="field" />
              </Field>
              <Field :name="`productVariants.${index}.attributeData`" v-slot="{ field }">
                <input type="hidden" :value="JSON.stringify(data.attributeData)" v-bind="field" />
              </Field>
              <Field :name="`productVariants.${index}.attributes`" v-slot="{ field }">
                <input type="hidden" :value="JSON.stringify(data.attributes)" v-bind="field" />
              </Field>

              <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600">SKU</label>
                    <Field :name="`productVariants.${index}.sku`" v-slot="{ field }">
                      <InputText v-bind="field" class="w-full !h-[38px] text-sm" disabled />
                    </Field>
                  </div>
                  <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600">Tên biến thể</label>
                    <InputText :modelValue="data.name || ''" @update:modelValue="(v)=> updateVariantAt(index, { name: v as any })" class="w-full !h-[38px] text-sm" />
                  </div>
                  <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600">Mã vạch</label>
                    <InputText :modelValue="data.barcode || ''" @update:modelValue="(v)=> updateVariantAt(index, { barcode: v as any })" class="w-full !h-[38px] text-sm" />
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                  <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600">Giá (VNĐ)</label>
                    <InputNumber
                      :modelValue="Number(data.price ?? 0)"
                      @update:modelValue="
                        (val) => {
                          data.price = Number(val ?? 0);
                          emitVariantUpdate();
                        }
                      "
                      input-class="!h-[38px] text-sm"
                      mode="currency"
                      currency="VND"
                      locale="vi-VN"
                      class="w-full"
                    />
                  </div>
                  <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600">Tồn kho</label>
                    <InputNumber
                      :modelValue="Number(data.stock ?? 0)"
                      @update:modelValue="
                        (val) => {
                          data.stock = Number(val ?? 0);
                          emitVariantUpdate();
                        }
                      "
                      mode="decimal"
                      input-class="!h-[38px] text-sm"
                      showButtons
                      :min="0"
                      fluid
                      class="w-full"
                    />
                  </div>
                </div>

                <div class="flex flex-col gap-1">
                  <label class="text-xs font-medium text-gray-600">Đường dẫn</label>
                  <InputText :modelValue="data.url || ''" @update:modelValue="(v)=> updateVariantAt(index, { url: v as any })" class="w-full !h-[38px] text-sm font-mono" placeholder="https://domain/san-pham?variant=..." />
                </div>
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

    <Dialog v-model:visible="showCreateAttributeModal" modal maximizable header="Thêm thuộc tính mới" :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" append-to="body">
      <form @submit.prevent="handleCreateAttribute">
        <div class="mb-3">
          <label class="form-label" for="attributeName">
            Tên thuộc tính:
            <span class="text-danger">*</span>
          </label>
          <input v-model="attributeForm.name" id="attributeName" type="text" placeholder="Nhập tên thuộc tính" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Nhóm thuộc tính:</label>
          <Select v-model="attributeForm.attributeCatalogueId" :options="attributeCatalogues" optionLabel="name" optionValue="id" filter placeholder="Chọn nhóm thuộc tính" class="w-100" />
        </div>
        <div class="mb-3">
          <label class="form-label">Thứ tự</label>
          <InputNumber v-model="attributeForm.order" :min="1" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
        </div>
        <div class="mb-3">
          <label class="form-label">Mô tả</label>
          <textarea v-model="attributeForm.description" rows="4" class="form-control" placeholder="Nhập mô tả thuộc tính" />
        </div>
        <div class="mb-3">
          <label class="form-label">Trạng thái</label>
          <PublishSelect v-model="attributeForm.publish" />
        </div>
        <div class="flex justify-end gap-3 mt-4">
          <Button type="button" variant="cancel" @click="showCreateAttributeModal = false" />
          <Button type="submit" variant="success" :loading="attributeForm.isLoading" />
        </div>
      </form>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { useToast } from "primevue/usetoast";
import { Field } from "vee-validate";
import type { IProductVariant } from "~/interfaces/product/variant.interface";
import { useProductVariantStore } from "~/stores/product/variant.store";
import { attributeService } from "~/services/attribute/attribute.service";
import Button from "~/components/admin/custom/Button.vue";
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import { slugify } from "~/utils/helpers";

const toast = useToast();
const variantStore = useProductVariantStore();

const hasVariants = ref(false);
type GroupRow = { uid: string; catalogueId: number | null; attributeIds: number[]; lockGroup: boolean };
const groupRows = ref<GroupRow[]>([]);

const showCreateAttributeModal = ref(false);
const attributeForm = ref({
  name: "",
  attributeCatalogueId: null,
  order: 1,
  description: "",
  publish: 2,
  isLoading: false,
});

const attributeCatalogues = computed(() => variantStore.attributeCatalogues);
const variantGroups = computed(() => variantStore.variantGroups);
const variants = computed(() => props.modelValue || []);
const config = useRuntimeConfig();
const expandedRows = ref<any>({});
const allExpanded = computed(() => {
  if (variants.value.length === 0) return false;
  return Object.keys(expandedRows.value).length === variants.value.length;
});

interface Props {
  productId?: number;
  productSku?: string;
  productPrice?: number;
  initialVariants?: IProductVariant[];
  productSlug?: string;
  modelValue?: IProductVariant[];
}

const props = withDefaults(defineProps<Props>(), {
  productSku: "",
  productPrice: 0,
  initialVariants: () => [],
  productSlug: "",
  modelValue: () => [],
});

const emit = defineEmits<{
  "update:modelValue": [value: IProductVariant[]];
}>();

const addRow = () => {
  groupRows.value.push({ uid: cryptoRandom(), catalogueId: null, attributeIds: [], lockGroup: false });
};

const toggleAllExpanded = () => {
  if (allExpanded.value) {
    // Thu gọn tất cả
    expandedRows.value = [];
  } else {
    // Mở rộng tất cả
    expandedRows.value = variants.value.reduce((acc: any, variant: any) => {
      acc[variant.id || variant.code || Math.random()] = true;
      return acc;
    }, {});
  }
};

const removeRow = (uid: string) => {
  const idx = groupRows.value.findIndex((r) => r.uid === uid);
  if (idx === -1) return;
  groupRows.value.splice(idx, 1);
  syncGroupsFromRows();
};

const updateVariantAt = (index: number, changes: Partial<IProductVariant>) => {
  const next = (variants.value as IProductVariant[]).map((v, i) => (i === index ? { ...v, ...changes } : v));
  emit("update:modelValue", normalizeVariants(next as any));
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

const getOverflowCount = (row: GroupRow) => {
  const count = row.attributeIds.length;
  return count > 3 ? count - 3 : 0;
};

const removeAttribute = (row: GroupRow, attributeId: number) => {
  row.attributeIds = row.attributeIds.filter((id) => id !== attributeId);
  syncGroupsFromRows();
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
    toast.add({
      severity: "error",
      summary: "Lỗi",
      detail: "Không thể tải danh sách nhóm thuộc tính",
    });
  }
};

const handleCreateAttribute = async () => {
  try {
    attributeForm.value.isLoading = true;
    const attributeData = {
      name: attributeForm.value.name,
      attribute_catalogue_id: attributeForm.value.attributeCatalogueId,
      order: attributeForm.value.order || 1,
      description: attributeForm.value.description || "",
      publish: attributeForm.value.publish || 2,
    };
    const response = await attributeService.store(attributeData);
    if (response && typeof response === "object" && "data" in response && response.data?.id) {
      if (attributeForm.value.attributeCatalogueId) {
        await variantStore.loadAttributesByCatalogue(attributeForm.value.attributeCatalogueId);
      }
      showCreateAttributeModal.value = false;
      attributeForm.value = {
        name: "",
        attributeCatalogueId: null,
        order: 1,
        description: "",
        publish: 2,
        isLoading: false,
      };
    }
  } catch (error) {
    console.error("Error creating attribute:", error);
  } finally {
    attributeForm.value.isLoading = false;
  }
};

const generateVariants = () => {
  try {
    const existingVariants = [...variants.value];

    variantStore.generateVariants();
    const newVariants = variantStore.variants;

    const oldGroupNames = new Set<string>();
    existingVariants.forEach((variant) => {
      if (variant.attributeData) {
        Object.keys(variant.attributeData).forEach((groupName) => {
          oldGroupNames.add(groupName);
        });
      }
    });

    const newGroupNames = new Set<string>();
    newVariants.forEach((variant) => {
      if (variant.attributeData) {
        Object.keys(variant.attributeData).forEach((groupName) => {
          newGroupNames.add(groupName);
        });
      }
    });

    const commonGroupNames = new Set([...oldGroupNames].filter((name) => newGroupNames.has(name)));
    const addedGroups = Array.from(newGroupNames).filter((name) => !oldGroupNames.has(name));

    const mergedVariants: IProductVariant[] = [];

    if (existingVariants.length > 0 && addedGroups.length > 0) {
      existingVariants.forEach((oldVariant) => {
        if (!oldVariant.attributeData) {
          mergedVariants.push(oldVariant);
          return;
        }

        const newGroupAttributes: Record<string, any[]> = {};
        addedGroups.forEach((groupName) => {
          const groupAttrs = variantGroups.value.find((g) => g.catalogueName === groupName)?.attributes || [];
          newGroupAttributes[groupName] = groupAttrs;
        });

        const newAttributeCombinations = generateAttributeCombinations(newGroupAttributes);

        newAttributeCombinations.forEach((combination) => {
          const newAttributeData = {
            ...oldVariant.attributeData,
            ...combination,
          };

          const preservedPrice = Number(oldVariant.price) || 0;
          const preservedStock = Number(oldVariant.stock) || 0;

          const newVariant = {
            code: oldVariant.code,
            attributeData: JSON.parse(JSON.stringify(newAttributeData)),
            attributes: oldVariant.attributes ? JSON.parse(JSON.stringify(oldVariant.attributes)) : undefined,
            name: generateVariantNameFromAttributes(newAttributeData),
            sku: generateNewSku(oldVariant.sku as string, combination),
            barcode: oldVariant.barcode || generateNewBarcode(),
            price: preservedPrice,
            stock: preservedStock,
            url: buildVariantLink({ attributeData: newAttributeData }),
            id: undefined,
          };

          mergedVariants.push(newVariant);
        });
      });
    } else {
      newVariants.forEach((newVariant) => {
        if (!newVariant.attributeData) {
          mergedVariants.push(newVariant);
          return;
        }

        const matchingOldVariant = existingVariants.find((oldVariant) => {
          if (!oldVariant.attributeData) return false;
          return JSON.stringify(oldVariant.attributeData) === JSON.stringify(newVariant.attributeData);
        });

        if (matchingOldVariant) {
          mergedVariants.push(matchingOldVariant);
        } else {
          if (props.productSku) {
            autoGenerateSkus([newVariant]);
            generateVariantName([newVariant]);
            generateVariantBarcodes([newVariant]);
            (newVariant as any).url = buildVariantLink(newVariant);
          }
          if (props.productPrice && props.productPrice > 0) {
            (newVariant as any).price = props.productPrice;
          }
          mergedVariants.push(newVariant);
        }
      });
    }

    if (Object.keys(expandedRows.value).length === 0) {
      expandedRows.value = {};
    }

    emit("update:modelValue", normalizeVariants(mergedVariants as any));
  } catch (error: any) {
    toast.add({
      severity: "warn",
      summary: "Cảnh báo",
      detail: error.message,
      life: 3000,
    });
  }
};

const generateAttributeCombinations = (groupAttributes: Record<string, any[]>) => {
  const groups = Object.keys(groupAttributes);
  if (groups.length === 0) return [{}];

  const validGroups = groups.filter((group) => groupAttributes[group] && groupAttributes[group].length > 0);

  if (validGroups.length === 0) return [{}];
  const combinations: Record<string, any>[] = [];

  const generateCombinations = (current: Record<string, any>, remainingGroups: string[]) => {
    if (remainingGroups.length === 0) {
      combinations.push({ ...current });
      return;
    }

    const currentGroup = remainingGroups[0];
    const attributes = groupAttributes[currentGroup as string];

    if (attributes && attributes.length > 0) {
      attributes.forEach((attr: any) => {
        generateCombinations({ ...current, [currentGroup as string]: attr }, remainingGroups.slice(1));
      });
    }
  };

  generateCombinations({}, validGroups);
  return combinations;
};

const generateVariantNameFromAttributes = (attributeData: Record<string, any>) => {
  const attributeNames = Object.entries(attributeData)
    .sort(([a], [b]) => a.localeCompare(b))
    .map(([groupName, attr]: [string, any]) => attr.name)
    .join(" ");
  return attributeNames;
};

const generateNewSku = (oldSku: string, combination: Record<string, any>) => {
  if (!oldSku) return "";

  const baseSku = oldSku.split("-").slice(0, -1).join("-");
  const newAttrIds = Object.values(combination)
    .map((attr: any) => attr.id)
    .join("-");
  return `${baseSku}-${newAttrIds}`;
};

const generateNewBarcode = () => {
  const nowSeed = Date.now().toString();
  const base12 = nowSeed.replace(/\D/g, "").slice(-12).padStart(12, "0");
  const checksum = computeEAN13Checksum(base12);
  return `${base12}${checksum}`;
};

const autoGenerateSkus = (variantsList?: IProductVariant[]) => {
  if (!props.productSku) return;
  const targetVariants = variantsList || variants.value;
  variantStore.autoGenerateSkus(props.productSku);
  targetVariants.forEach((variant) => {
    if (variant.sku) {
      variant.sku = variant.sku.replace(/_/g, "-");
    }
  });
};

const generateVariantName = (variantsList?: IProductVariant[]) => {
  const targetVariants = variantsList || variants.value;
  targetVariants.forEach((variant) => {
    if (variant.attributeData && !(variant as any).name) {
      const attributeNames = Object.entries(variant.attributeData)
        .sort(([a], [b]) => a.localeCompare(b))
        .map(([groupName, attr]) => attr.name)
        .join(" ");

      (variant as any).name = attributeNames;
    }
  });
};

const computeEAN13Checksum = (digits12: string) => {
  let sum = 0;
  for (let i = 0; i < 12; i++) {
    const n = Number(digits12[i]);
    sum += i % 2 === 0 ? n : n * 3;
  }
  const mod = sum % 10;
  return mod === 0 ? 0 : 10 - mod;
};

const generateVariantBarcodes = (variantsList?: IProductVariant[]) => {
  const nowSeed = Date.now().toString();
  const targetVariants = variantsList || variants.value;
  targetVariants.forEach((variant, idx) => {
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
  Object.entries(variant.attributeData || {}).forEach(([groupName, attr]: [string, any]) => {
    params.set(slugify(groupName), String(attr?.id ?? ""));
  });

  const qs = params.toString();
  return qs ? `${base}?${qs}` : base;
};

const emitVariantUpdate = () => {
  emit("update:modelValue", normalizeVariants([...variants.value] as any));
};

const normalizeVariants = (list: IProductVariant[]) => {
  return (list as any[]).map((v) => {
    const p = { ...v } as any;
    p.price = Number(p.price ?? 0);
    p.stock = Number(p.stock ?? 0);
    if ("publish" in p) delete p.publish;
    return p as IProductVariant;
  });
};

onMounted(async () => {
  await loadAttributeCatalogues();

  if (props.modelValue && props.modelValue.length > 0) {
    variantStore.setVariants([...props.modelValue]);
    hasVariants.value = true;
    await initializeGroupsFromExistingVariants();
  } else if (props.initialVariants.length > 0) {
    variantStore.setVariants([...props.initialVariants]);
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

watch(hasVariants, (enabled) => {
  if (enabled) {
    if (groupRows.value.length === 0 && variants.value.length === 0) {
      addRow();
    }
  } else {
    groupRows.value = [];
    variantStore.variantGroups = [];
    variantStore.setVariants([]);
    emit("update:modelValue", []);
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

/* Fix MultiSelect layout overflow */
:deep(.p-multiselect) {
  max-width: 100%;
  overflow: hidden;
}

:deep(.p-multiselect .p-multiselect-label-container) {
  max-width: 100%;
  overflow: hidden;
  display: flex;
  flex-wrap: nowrap;
}

:deep(.p-multiselect .p-multiselect-label) {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  flex: 1;
  min-width: 0;
}

:deep(.p-multiselect .p-multiselect-token) {
  max-width: 100px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  flex-shrink: 0;
}

:deep(.p-multiselect .p-multiselect-token-label) {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 80px;
}

:deep(.p-multiselect .p-multiselect-token-icon) {
  flex-shrink: 0;
}

:deep(.p-multiselect-panel) {
  max-width: 100%;
  overflow-x: auto;
}

/* Custom value display for MultiSelect */
:deep(.p-multiselect .p-multiselect-label-container) {
  overflow: hidden;
}
</style>
