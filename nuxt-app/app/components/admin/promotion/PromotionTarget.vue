<template>
  <div class="card">
    <div class="card-header flex justify-between items-center">
      <h5 class="card-title mb-0">{{ $t("admin.page.promotion.targets") }}</h5>
      <div class="flex items-center gap-2">
        <Button type="button" variant="custom" severity="info" :label="$t('admin.page.promotion.addNew')" @click="addTarget">
          <i class="mdi mdi-plus"></i>
          {{ $t("admin.page.promotion.addNew") }}
        </Button>
        <Button type="button" variant="custom" severity="success" :label="$t('admin.page.promotion.saveChange')" @click="confirmAndClose">
          {{ $t("admin.page.promotion.saveChange") }}
        </Button>
      </div>
    </div>
    <div class="card-body">
      <Accordion :modelValue="activeIndex" @update:modelValue="(val: any) => (activeIndex = val)" expandIcon="pi pi-plus" collapseIcon="pi pi-minus">
        <template v-if="targetsLocal.length === 0">
          <div class="text-center text-muted py-4">
            {{ $t("admin.page.promotion.noTargets") }}
          </div>
        </template>
        <template v-else>
          <AccordionPanel v-for="(target, index) in targetsLocal" :key="target._id || index" :value="index">
            <AccordionHeader>
              <div class="flex items-center gap-2 w-full">
                <span class="font-medium">#{{ index + 1 }} - {{ getTargetLabel(target.targetType) }}</span>
                <div class="ml-auto flex items-center gap-2">
                  <button type="button" @click.stop="removeTarget(index)" class="w-8 h-8 flex items-center justify-center rounded-full text-white bg-red-500 hover:bg-red-700 transition">
                    <i class="mdi mdi-trash-can"></i>
                  </button>
                </div>
              </div>
            </AccordionHeader>
            <AccordionContent>
              <div class="record-body">
                <!-- Row 1: Target Type and Is Included (5:5 split) -->
                <div class="row">
                  <div class="col-md-6">
                    <label class="form-label">{{ $t("admin.page.promotion.targetType") }}</label>
                    <Field :name="`targets.${index}.targetType`" v-slot="{ field, errorMessage, meta }">
                      <Select v-model="field.value" @update:modelValue="field.onChange" :options="PROMOTION_TARGET_TYPES" option-label="label" option-value="value" class="w-full" :placeholder="$t('admin.page.promotion.selectTargetType')" style="height: 38px" />
                      <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                    </Field>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">{{ $t("admin.page.promotion.isIncluded") }}</label>
                    <Field :name="`targets.${index}.isIncluded`" v-slot="{ field, errorMessage, meta }">
                      <Select
                        v-model="field.value"
                        @update:modelValue="field.onChange"
                        :options="[
                          { label: 'Có', value: true },
                          { label: 'Không', value: false },
                        ]"
                        option-label="label"
                        option-value="value"
                        class="w-full"
                        :placeholder="$t('admin.page.promotion.selectInclude')"
                        style="height: 38px"
                      />
                      <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                    </Field>
                  </div>
                </div>

                <!-- Row 2: Entity Search -->
                <div class="row mt-3" v-if="needsTargetId(targetsLocal[index].targetType)">
                  <div class="col-12">
                    <div class="relative">
                      <!-- Hidden input for targetId -->
                      <Field :name="`targets.${index}.targetId`" v-slot="{ field, errorMessage, meta }">
                        <input type="hidden" v-model="field.value" />
                        <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                      </Field>

                      <!-- Search input with button -->
                      <div class="flex gap-2">
                        <div class="flex-1 relative">
                          <input v-model="searchKeywords[index]" @input="updateSearchKeyword(index, ($event.target as HTMLInputElement).value)" @focus="showSuggestions(index)" @blur="hideSuggestions(index)" type="text" class="form-control rounded" style="height: 38px" :placeholder="$t('admin.page.promotion.searchEntity')" />

                          <!-- Suggestions dropdown -->
                          <div v-if="(entitySearchInstances[index] as ExtendedEntitySearchInstance)?.suggestions?.value?.length || ((entitySearchInstances[index] as ExtendedEntitySearchInstance)?.showSuggestions && !(entitySearchInstances[index] as ExtendedEntitySearchInstance)?.keyword?.value)" class="absolute w-full bg-white border rounded shadow-lg mt-1 z-10 max-h-60 overflow-y-auto">
                            <div v-if="!(entitySearchInstances[index] as ExtendedEntitySearchInstance)?.keyword?.value && (entitySearchInstances[index] as ExtendedEntitySearchInstance)?.showSuggestions && !(entitySearchInstances[index] as ExtendedEntitySearchInstance)?.suggestions?.value?.length" class="p-3 text-gray-500">
                              {{ $t("admin.page.promotion.loading") || "Đang tải..." }}
                            </div>
                            <div v-for="suggestion in (entitySearchInstances[index] as ExtendedEntitySearchInstance)?.suggestions?.value || []" :key="suggestion.id" @click="onEntitySelect(index, suggestion)" class="flex items-center gap-2 p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0">
                              <NuxtImg v-if="suggestion.image" :src="suggestion.image" alt="" class="w-8 h-8 object-cover rounded flex-shrink-0" loading="lazy" />
                              <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-700 truncate">{{ suggestion.name }}</p>
                                <p v-if="suggestion.price" class="text-sm font-bold text-red-600">{{ formatPrice(suggestion.price) }}</p>
                              </div>
                            </div>
                          </div>
                        </div>

                        <Button type="button" variant="custom" severity="info" @click="showSuggestions(index)" class="px-3" style="height: 38px; min-width: 38px">
                          <i class="mdi mdi-magnify"></i>
                        </Button>
                      </div>
                    </div>

                    <div v-if="(entitySearchInstances[index] as ExtendedEntitySearchInstance)?.selectedEntity?.value" class="mt-3 p-3 bg-info-300 rounded flex items-center gap-3">
                      <span class="text-sm font-medium text-gray-700">#{{ targetsLocal[index].targetId }}</span>
                      <img v-if="(entitySearchInstances[index] as ExtendedEntitySearchInstance)?.selectedEntity?.value?.image" :src="(entitySearchInstances[index] as ExtendedEntitySearchInstance)?.selectedEntity?.value?.image" alt="" class="w-10 h-8 object-cover rounded" />
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ (entitySearchInstances[index] as ExtendedEntitySearchInstance)?.selectedEntity?.value?.name }}</p>
                        <p v-if="(entitySearchInstances[index] as ExtendedEntitySearchInstance)?.selectedEntity?.value?.price" class="text-sm text-red-600">{{ formatPrice((entitySearchInstances[index] as ExtendedEntitySearchInstance)?.selectedEntity?.value?.price) }}</p>
                      </div>
                      <button @click="onEntityClear(index)" class="w-6 h-6 flex items-center justify-center rounded-full text-white bg-red-500 hover:bg-red-700 transition text-xs">
                        <i class="mdi mdi-close"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Row 3: Metadata -->
                <div class="row mt-3">
                  <div class="col-12">
                    <label class="form-label">{{ $t("admin.page.promotion.metadata") }}</label>
                    <div class="flex items-start gap-2">
                      <div class="flex-1">
                        <template v-if="targetsLocal[index] && typeof targetsLocal[index].metadata === 'object' && targetsLocal[index].metadata !== null">
                          <div class="flex flex-wrap gap-2">
                            <div v-for="(v, k) in targetsLocal[index].metadata" :key="k" class="px-3 py-1 rounded border bg-gray-50">
                              <strong>{{ metadataLabel(String(k)) }}:</strong>
                              <span class="ml-1">{{ String(v) }}</span>
                            </div>
                          </div>
                        </template>
                        <template v-else>
                          <input v-model="metaStrings[index]" type="text" :placeholder="$t('admin.page.promotion.metadata')" class="form-control rounded" style="height: 38px" />
                        </template>
                        <small v-if="metaErrors[index]" class="text-danger">{{ metaErrors[index] }}</small>
                      </div>
                      <div class="flex flex-col gap-2">
                        <button type="button" class="text-blue-900 text-xs" @click="openMetadataDialog(index)">{{ $t("admin.page.promotion.editMetadata") }}</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </AccordionContent>
          </AccordionPanel>
        </template>
      </Accordion>
    </div>
    <!-- Metadata Dialog -->
    <Dialog v-model:visible="showMetadataDialog" :header="$t('admin.page.promotion.editMetadataTitle')" modal class="w-11/12 max-w-2xl">
      <textarea v-model="currentMetadataContent" class="w-full border rounded p-2" rows="8" :placeholder="$t('admin.page.promotion.editMetadataPlaceholder')"></textarea>
      <template #footer>
        <div class="flex justify-end gap-2">
          <Button @click="closeMetadataDialog" severity="secondary">{{ $t("admin.components.buttons.cancel") }}</Button>
          <Button @click="saveMetadataDialog" severity="success">{{ $t("admin.components.buttons.confirm") }}</Button>
        </div>
      </template>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick } from "vue";
import { Field } from "vee-validate";
import Button from "~/components/admin/custom/Button.vue";
import InputNumber from "primevue/inputnumber";
import Select from "primevue/select";
import Accordion from "primevue/accordion";
import AccordionPanel from "primevue/accordionpanel";
import AccordionHeader from "primevue/accordionheader";
import AccordionContent from "primevue/accordioncontent";
import Dialog from "primevue/dialog";
import { PROMOTION_TARGET_TYPES } from "~/utils/constants";
import { useAppToast } from "~/composables/useAppToast";
import { useI18n } from "vue-i18n";
import { useEntitySearch, type EntityType, type SearchResult } from "~/composables/admin/promotion/useEntitySearch";
import { formatPrice } from "~/utils/helpers";

interface Props {
  modelValue: any[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
  "update:modelValue": [value: any[]];
  save: [value: any[]];
}>();

const { t } = useI18n();

// local targets array synced with parent
const targetsLocal = ref<any[]>(
  (props.modelValue || []).map((target) => ({
    ...target,
    _id: target._id || Date.now() + Math.random(), // ensure unique key for reactivity
  }))
);
watch(
  () => props.modelValue,
  (v) => {
    targetsLocal.value = (v || []).map((target) => ({
      ...target,
      _id: target._id || Date.now() + Math.random(), // ensure unique key for reactivity
    }));
  },
  { deep: true }
);

const addTarget = () => {
  const newRecord = {
    targetType: "order",
    targetId: null,
    isIncluded: true,
    metadata: null,
    _id: Date.now() + Math.random(), // unique key for reactivity
  };
  targetsLocal.value.push(newRecord);
  activeIndex.value = targetsLocal.value.length - 1;
  emit("update:modelValue", targetsLocal.value);
};

const removeTarget = (index: number) => {
  targetsLocal.value.splice(index, 1);
  // adjust active index after removal
  if (activeIndex.value !== null) {
    if (activeIndex.value === index) {
      activeIndex.value = null; // collapse if current panel was removed
    } else if (activeIndex.value > index) {
      activeIndex.value = activeIndex.value - 1; // adjust index if later panels were shifted
    }
  }
  // notify parent immediately
  emit("update:modelValue", targetsLocal.value);
};

// accordion state
const activeIndex = ref<number | null>(null);
const toast = useAppToast();

// metadata string editing helpers
const metaStrings = ref<string[]>([]);
const metaErrors = ref<(string | null)[]>([]);

const syncMetaStrings = () => {
  metaStrings.value = targetsLocal.value.map((t) => {
    if (t && typeof t.metadata === "object" && t.metadata !== null) {
      try {
        return JSON.stringify(t.metadata);
      } catch {
        return String(t.metadata);
      }
    }
    return t && t.metadata != null ? String(t.metadata) : "";
  });
  metaErrors.value = targetsLocal.value.map(() => null);
};

// metadata dialog state
const showMetadataDialog = ref(false);
const currentMetadataContent = ref("");
const currentMetadataIndex = ref(-1);

// entity search instances for each target
const entitySearchInstances = ref<any[]>([]);

// flag to prevent recursive updates between watchers
const isUpdating = ref(false);

// local keywords for inputs (avoid mutating composable internals)
const searchKeywords = ref<string[]>([]);

// Type for extended entity search instance
interface ExtendedEntitySearchInstance {
  keyword: any;
  suggestions: any;
  isLoading: any;
  selectedEntity: any;
  search: (query: string) => void;
  selectEntity: (entity: any) => void;
  clearSelection: () => void;
  getEntityById: (id: number) => Promise<any>;
  showSuggestions: boolean;
}

// computed to check if targetId is needed based on targetType
const needsTargetId = (targetType: string) => {
  return targetType !== "order";
};

// Get entity type from target type
const getEntityType = (targetType: string): EntityType | null => {
  const mapping: Record<string, EntityType> = {
    product: "product",
    category: "category",
    brand: "brand",
    customer_group: "customer_group",
  };
  return mapping[targetType] || null;
};

// Initialize entity search instances
const initializeEntitySearchInstances = () => {
  entitySearchInstances.value = targetsLocal.value.map((target): ExtendedEntitySearchInstance | null => {
    const entityType = getEntityType(target.targetType);
    if (entityType) {
      const instance = useEntitySearch(entityType) as ExtendedEntitySearchInstance;
      // Add showSuggestions property
      instance.showSuggestions = false;
      // Ensure keyword is a ref
      if (!instance.keyword || typeof instance.keyword !== "object" || !("value" in instance.keyword)) {
        // @ts-ignore
        instance.keyword = ref(String(instance.keyword ?? ""));
      }
      // Preload default suggestions (type=all) so dropdown shows immediately on focus
      try {
        instance.search(instance.keyword.value || "");
      } catch (e) {
        // ignore
      }
      return instance;
    }
    return null;
  });
  // ensure searchKeywords length matches targets
  searchKeywords.value = targetsLocal.value.map((t) => "");
};

// Show suggestions dropdown
const showSuggestions = (index: number) => {
  const instance = entitySearchInstances.value[index] as ExtendedEntitySearchInstance;
  console.log("Show suggestions for index:", index, "instance:", instance);
  if (instance) {
    instance.showSuggestions = true;
    // Always trigger search to show all data when no keyword
    const searchQuery = instance.keyword.value || "";
    console.log("Triggering search with query:", searchQuery);
    instance.search(searchQuery);
    // debug: log suggestions after short delay
    setTimeout(() => {
      try {
        // eslint-disable-next-line no-console
        console.log("Post-search suggestions for index", index, ":", (instance.suggestions && instance.suggestions.value) || instance.suggestions);
      } catch (e) {
        // ignore
      }
    }, 400);
  }
};

// Hide suggestions dropdown
const hideSuggestions = (index: number) => {
  const instance = entitySearchInstances.value[index] as ExtendedEntitySearchInstance;
  if (instance) {
    // Delay hiding to allow for clicking on suggestions
    setTimeout(() => {
      instance.showSuggestions = false;
    }, 150);
  }
};

// Update search keyword for entity search
const updateSearchKeyword = (index: number, value: string) => {
  let instance = entitySearchInstances.value[index] as ExtendedEntitySearchInstance;

  // If instance not initialized for this index, try to create one based on target type
  if (!instance) {
    const target = targetsLocal.value[index];
    const entityType = getEntityType(target?.targetType);
    if (entityType) {
      const newInstance = useEntitySearch(entityType) as ExtendedEntitySearchInstance;
      newInstance.showSuggestions = true;
      // ensure keyword is a ref
      // don't touch composable internals - we keep local searchKeywords instead
      entitySearchInstances.value[index] = newInstance;
      instance = newInstance;
    } else {
      return;
    }
  }

  // Ensure keyword is a ref-like object with .value
  // update our local keyword state and trigger search on the composable
  searchKeywords.value[index] = value;
  instance.showSuggestions = true;
  instance.search(value);
};

// Handle entity selection
const onEntitySelect = (index: number, entity: SearchResult) => {
  targetsLocal.value[index].targetId = entity.id;
  const searchInstance = entitySearchInstances.value[index] as ExtendedEntitySearchInstance;
  if (searchInstance) {
    searchInstance.selectEntity(entity);
    searchInstance.showSuggestions = false;
  }
  emit("update:modelValue", targetsLocal.value);
};

// Clear selected entity
const onEntityClear = (index: number) => {
  targetsLocal.value[index].targetId = null;
  const searchInstance = entitySearchInstances.value[index] as ExtendedEntitySearchInstance;
  if (searchInstance) {
    searchInstance.clearSelection();
    searchInstance.showSuggestions = false;
  }
  emit("update:modelValue", targetsLocal.value);
};

// Load selected entity by ID
const loadSelectedEntity = async (index: number, targetId: number, targetType: string) => {
  const entityType = getEntityType(targetType);
  if (!entityType || !targetId) return;

  const searchInstance = entitySearchInstances.value[index];
  if (searchInstance) {
    const entity = await searchInstance.getEntityById(targetId);
    if (entity) {
      searchInstance.selectEntity(entity);
    }
  }
};

watch(
  targetsLocal,
  async () => {
    if (isUpdating.value) return; // prevent recursive updates
    isUpdating.value = true;
    syncMetaStrings();
    initializeEntitySearchInstances();

    // Load selected entities for existing targets
    for (let i = 0; i < targetsLocal.value.length; i++) {
      const target = targetsLocal.value[i];
      if (target.targetId && target.targetType !== "order") {
        await loadSelectedEntity(i, target.targetId, target.targetType);
      }
    }

    nextTick(() => {
      isUpdating.value = false;
    });
  },
  { deep: true, immediate: true }
);

// Watch for target type changes to update entity search instances
watch(
  () => targetsLocal.value.map((t) => t.targetType),
  () => {
    initializeEntitySearchInstances();
  },
  { deep: true }
);

watch(metaStrings, (newVal) => {
  if (isUpdating.value) return; // prevent recursive updates
  isUpdating.value = true;
  newVal.forEach((val, idx) => {
    if (val === undefined) return;
    try {
      const parsed = JSON.parse(val);
      targetsLocal.value[idx].metadata = parsed;
      metaErrors.value[idx] = null;
    } catch {
      targetsLocal.value[idx].metadata = val;
      metaErrors.value[idx] = null;
    }
  });
  emit("update:modelValue", targetsLocal.value);
  nextTick(() => {
    isUpdating.value = false;
  });
});

const confirmAndClose = () => {
  emit("save", targetsLocal.value);
  toast.success("Đã lưu đối tượng áp dụng");
};
// metadata dialog functions
const openMetadataDialog = (index: number) => {
  currentMetadataIndex.value = index;
  currentMetadataContent.value = metaStrings.value[index] ?? "";
  showMetadataDialog.value = true;
};

const closeMetadataDialog = () => {
  showMetadataDialog.value = false;
  currentMetadataIndex.value = -1;
  currentMetadataContent.value = "";
};

const saveMetadataDialog = () => {
  const index = currentMetadataIndex.value;
  if (index >= 0) {
    const raw = currentMetadataContent.value ?? "";
    try {
      const parsed = JSON.parse(raw);
      targetsLocal.value[index].metadata = parsed;
      metaStrings.value[index] = JSON.stringify(parsed);
      metaErrors.value[index] = null;
      toast.success("Đã cập nhật thông tin bổ sung");
      closeMetadataDialog();
    } catch {
      targetsLocal.value[index].metadata = raw;
      metaStrings.value[index] = raw;
      metaErrors.value[index] = null;
      toast.success("Đã lưu thông tin bổ sung (chuỗi)");
      closeMetadataDialog();
    }
    emit("update:modelValue", targetsLocal.value);
  }
};

const metadataLabel = (key: string) => {
  const mapping: Record<string, string> = {
    category_name: t("admin.page.promotion.meta_category_name"),
    category_slug: t("admin.page.promotion.meta_category_slug"),
    product_name: t("admin.page.promotion.meta_product_name"),
    reason: t("admin.page.promotion.meta_reason"),
  };
  return mapping[key] || key;
};

const getTargetLabel = (type: string) => {
  const opt = PROMOTION_TARGET_TYPES.find((o: any) => o.value === type);
  return opt ? opt.label : String(type);
};
</script>
