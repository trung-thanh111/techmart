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
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $t("admin.page.promotion.targetType") }}</label>
                                        <Field :name="`targets.${index}.targetType`" v-slot="{ field, errorMessage, meta }">
                                            <Select
                                                v-model="field.value"
                                                @update:modelValue="
                                                    (val) => {
                                                        field.onChange(val);
                                                        targetsLocal[index].targetType = val;
                                                        emit('update:modelValue', targetsLocal);
                                                    }
                                                "
                                                :options="PROMOTION_TARGET_TYPES"
                                                option-label="label"
                                                option-value="value"
                                                class="w-full"
                                                :placeholder="$t('admin.page.promotion.selectTargetType')"
                                                style="height: 38px"
                                            />
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
                                <div class="row mt-3" v-if="needsTargetId(targetsLocal[index].targetType)">
                                    <div class="col-12">
                                        <div class="relative">
                                            <Field :name="`targets.${index}.targetId`" v-slot="{ field, errorMessage, meta }">
                                                <input type="hidden" v-model="field.value" />
                                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                            </Field>
                                            <div class="flex gap-2 h-[38px]">
                                                <div class="flex-1 relative">
                                                    <input v-model="searchKeywords[index]" @input="updateSearchKeyword(index, ($event.target as HTMLInputElement).value)" @focus="showSuggestions(index)" @blur="hideSuggestions(index)" type="text" class="form-control rounded h-[38px]" :placeholder="$t('admin.page.promotion.searchEntity')" />
                                                    <div v-if="shouldShowSuggestions(index)" class="absolute w-full bg-white border rounded shadow-lg z-10 overflow-y-auto">
                                                        <div v-if="isInstanceLoading(entitySearchInstances[index] as ExtendedEntitySearchInstance) && getSuggestionsArray(entitySearchInstances[index] as ExtendedEntitySearchInstance).length === 0" class="p-3 text-gray-500">
                                                            {{ $t("admin.page.promotion.loading") }}
                                                        </div>
                                                        <div v-for="suggestion in getSuggestionsArray(entitySearchInstances[index] as ExtendedEntitySearchInstance)" :key="suggestion.id" @mousedown.prevent="onEntitySelect(index, suggestion)" @click.prevent="onEntitySelect(index, suggestion)" class="flex gap-2 p-1 hover:bg-gray-50 cursor-pointer border-b last:border-b-0">
                                                            <NuxtImg v-if="suggestion.image" :src="suggestion.image" alt="" class="w-10 h-10 object-cover rounded flex-shrink-0" loading="lazy" />
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm font-medium text-gray-700 truncate mb-1">{{ suggestion.name }}</p>
                                                                <p v-if="suggestion.price" class="text-xs font-bold text-red-600 mb-0">{{ formatPrice(suggestion.price) }}</p>
                                                                <p v-else-if="suggestion.metaImportant" class="text-xs text-gray-600 mb-0">{{ suggestion.metaImportant }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <Button type="button" variant="custom" severity="info" @click="showSuggestions(index)" :icon="'mdi mdi-magnify'" class="h-[38px]" />
                                            </div>
                                        </div>
                                        <div v-if="(entitySearchInstances[index] as ExtendedEntitySearchInstance)?.selectedEntity?.value || targetsLocal[index].targetId || selectedEntities[index]" class="mt-3 bg-info-100 roundedp-1">
                                            <div class="text-sm text-gray-600 mb-2 font-medium">{{ $t("admin.page.promotion.selectedEntityLabel") || "Đối tượng đã chọn:" }}</div>
                                            <div class="p-2 bg-info-300 rounded flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-700 me-2">#{{ targetsLocal[index].targetId }}</span>
                                                <NuxtImg v-if="(selectedEntities[index] && selectedEntities[index]?.image) || (entitySearchInstances[index] as ExtendedEntitySearchInstance)?.selectedEntity?.value?.image" :src="(selectedEntities[index] && selectedEntities[index]?.image) || (entitySearchInstances[index] as ExtendedEntitySearchInstance)?.selectedEntity?.value?.image" alt="" class="w-10 h-10 object-cover rounded" loading="lazy" />
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate mb-1">{{ (selectedEntities[index] && selectedEntities[index]?.name) || (entitySearchInstances[index] as ExtendedEntitySearchInstance)?.selectedEntity?.value?.name || "" }}</p>
                                                    <p v-if="(selectedEntities[index] && selectedEntities[index]?.price) || (entitySearchInstances[index] as ExtendedEntitySearchInstance)?.selectedEntity?.value?.price" class="text-xs text-red-600 mb-0">
                                                        {{ formatPrice((selectedEntities[index] && selectedEntities[index]?.price) || (entitySearchInstances[index] as ExtendedEntitySearchInstance)?.selectedEntity?.value?.price || 0) }}
                                                    </p>
                                                </div>
                                                <button @click="onEntityClear(index)" class="w-8 h-8 flex items-center justify-center rounded-full text-white bg-red-500 hover:bg-red-700 transition text-xs" aria-label="remove">
                                                    <i class="mdi mdi-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        <Dialog v-model:visible="showMetadataDialog" :header="$t('admin.page.promotion.editMetadataTitle')" modal class="w-11/12 max-w-2xl">
            <CKEditor v-model="currentMetadataContent" :placeholder="$t('admin.page.promotion.editMetadataPlaceholder')" height="h-32" />
            <template #footer>
                <div class="flex justify-end gap-2">
                    <Button @click="closeMetadataDialog" variant="custom" severity="secondary" :label="$t('admin.components.buttons.cancel')"></Button>
                    <Button @click="saveMetadataDialog" variant="success" :label="$t('admin.components.buttons.confirm')"></Button>
                </div>
            </template>
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick } from "vue";
import { Field, useForm } from "vee-validate";
import Button from "~/components/custom/Button.vue";
import Select from "primevue/select";
import Accordion from "primevue/accordion";
import AccordionPanel from "primevue/accordionpanel";
import AccordionHeader from "primevue/accordionheader";
import AccordionContent from "primevue/accordioncontent";
import Dialog from "primevue/dialog";
import { PROMOTION_TARGET_TYPES } from "~/utils/constants";
import { useAppToast } from "~/composables/useAppToast";
import { useI18n } from "vue-i18n";
import { useEntitySearch, type EntityType, type SearchResult } from "~/composables/promotion/useEntitySearch";
import { formatPrice } from "~/utils/helpers";
import CKEditor from "~/components/custom/CKEditor.vue";

interface Props {
    modelValue: any[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
    "update:modelValue": [value: any[]];
    save: [value: any[]];
}>();

const { t } = useI18n();
const { setFieldValue } = useForm();
const targetsLocal = ref<any[]>(
    (props.modelValue || []).map((target) => ({
        ...target,
        _id: target._id || Date.now() + Math.random(),
    })),
);
watch(
    () => props.modelValue,
    (v) => {
        const prevSelected = selectedEntities.value ? selectedEntities.value.slice() : [];
        targetsLocal.value = (v || []).map((target) => ({
            ...target,
            _id: target._id || Date.now() + Math.random(),
        }));
        selectedEntities.value = targetsLocal.value.map((t, i) => {
            const id = t?.targetId;
            if (id == null) return null;
            const found = prevSelected.find((s) => s && Number(s.id) === Number(id));
            return found ?? null;
        });
        hideSuggestionsByBlur.value = targetsLocal.value.map((t, i) => hideSuggestionsByBlur.value[i] ?? false);
    },
    { deep: true },
);

const addTarget = () => {
    const newRecord = {
        targetType: "",
        targetId: null,
        isIncluded: true,
        metadata: null,
        _id: Date.now() + Math.random(),
    };
    targetsLocal.value.push(newRecord);
    activeIndex.value = targetsLocal.value.length - 1;
    emit("update:modelValue", targetsLocal.value);
};

const removeTarget = (index: number) => {
    targetsLocal.value.splice(index, 1);
    if (activeIndex.value !== null) {
        if (activeIndex.value === index) {
            activeIndex.value = null; // collapse if current panel was removed
        } else if (activeIndex.value > index) {
            activeIndex.value = activeIndex.value - 1; // adjust index if later panels were shifted
        }
    }
    emit("update:modelValue", targetsLocal.value);
};

const activeIndex = ref<number | null>(null);
const toast = useAppToast();

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

const showMetadataDialog = ref(false);
const currentMetadataContent = ref("");
const currentMetadataIndex = ref(-1);
const entitySearchInstances = ref<any[]>([]);
const selectedEntities = ref<(SearchResult | null)[]>([]);
const hideSuggestionsByBlur = ref<boolean[]>([]);
const skipLoadAfterSelect = ref<boolean[]>([]);
const isUpdating = ref(false);
const searchKeywords = ref<string[]>([]);

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

const needsTargetId = (targetType: string | null | undefined) => {
    return !!targetType && targetType !== "order";
};

const getEntityType = (targetType: string): EntityType | null => {
    const mapping: Record<string, EntityType> = {
        product: "product",
        category: "category",
        brand: "brand",
        customer_group: "customer_group",
    };
    return mapping[targetType] || null;
};

// ensure per-index helper arrays keep their previous values and match targets length
const ensurePerIndexArrays = (len: number) => {
    searchKeywords.value = Array.from({ length: len }, (_, i) => searchKeywords.value[i] ?? "");
    selectedEntities.value = Array.from({ length: len }, (_, i) => selectedEntities.value[i] ?? null);
    hideSuggestionsByBlur.value = Array.from({ length: len }, (_, i) => hideSuggestionsByBlur.value[i] ?? false);
    skipLoadAfterSelect.value = Array.from({ length: len }, (_, i) => skipLoadAfterSelect.value[i] ?? false);
};

const initializeEntitySearchInstances = () => {
    entitySearchInstances.value = targetsLocal.value.map((target): ExtendedEntitySearchInstance | null => {
        const entityType = getEntityType(target.targetType);
        if (entityType) {
            const instance = useEntitySearch(entityType) as ExtendedEntitySearchInstance;
            instance.showSuggestions = false;
            if (!instance.keyword || typeof instance.keyword !== "object" || !("value" in instance.keyword)) {
                // @ts-ignore
                instance.keyword = ref(String(instance.keyword ?? ""));
            }
            try {
                instance.search(instance.keyword.value || "");
            } catch (e) {
                // ignore
            }
            return instance;
        }
        return null;
    });
    ensurePerIndexArrays(targetsLocal.value.length);
};

const showSuggestions = (index: number) => {
    const instance = entitySearchInstances.value[index] as ExtendedEntitySearchInstance;
    if (!instance) return;
    hideSuggestionsByBlur.value[index] = false;
    instance.showSuggestions = true;
    instance.search(instance.keyword.value || "");
};

const hideSuggestions = (index: number) => {
    const instance = entitySearchInstances.value[index] as ExtendedEntitySearchInstance;
    if (instance) {
        setTimeout(() => {
            instance.showSuggestions = false;
            hideSuggestionsByBlur.value[index] = true;
        }, 150);
    }
};

// Helpers to normalize composable return shapes (ref vs raw)
const getSuggestionsArray = (instance: any) => {
    if (!instance) return [];
    const s = instance.suggestions;
    if (!s) return [];
    return "value" in s ? s.value : s;
};

const isInstanceLoading = (instance: any) => {
    if (!instance) return false;
    const l = instance.isLoading;
    if (l === undefined) return false;
    return typeof l === "object" && "value" in l ? l.value : !!l;
};

const shouldShowSuggestions = (index: number) => {
    const instance = entitySearchInstances.value[index];
    if (!instance) return false;
    const suggestions = getSuggestionsArray(instance);
    if (hideSuggestionsByBlur.value[index]) return false;
    return suggestions.length > 0 || (instance.showSuggestions && (isInstanceLoading(instance) || !instance.keyword?.value));
};

const updateSearchKeyword = (index: number, value: string) => {
    let instance = entitySearchInstances.value[index] as ExtendedEntitySearchInstance;

    if (!instance) {
        const target = targetsLocal.value[index];
        const entityType = getEntityType(target?.targetType);
        if (entityType) {
            const newInstance = useEntitySearch(entityType) as ExtendedEntitySearchInstance;
            newInstance.showSuggestions = true;
            entitySearchInstances.value[index] = newInstance;
            instance = newInstance;
        } else {
            return;
        }
    }

    searchKeywords.value[index] = value;
    hideSuggestionsByBlur.value[index] = false;
    instance.showSuggestions = true;
    instance.search(value);
};

// centralize selected entity setting to keep composable and local cache in sync
const setSelectedEntity = (index: number, entity: SearchResult | null) => {
    const inst = entitySearchInstances.value[index] as ExtendedEntitySearchInstance | null;
    if (inst) {
        try {
            if (entity) inst.selectEntity(entity);
            else if (typeof inst.clearSelection === "function") inst.clearSelection();
        } catch {
            try {
                if (inst.selectedEntity && "value" in inst.selectedEntity) inst.selectedEntity.value = entity;
            } catch {
                // ignore
            }
        }
    }
    selectedEntities.value[index] = entity;
    try {
        setFieldValue && setFieldValue(`targets.${index}.targetId`, entity ? entity.id : null);
    } catch {
        // ignore
    }
};

const onEntitySelect = (index: number, entity: SearchResult) => {
    targetsLocal.value[index].targetId = entity.id;
    const searchInstance = entitySearchInstances.value[index] as ExtendedEntitySearchInstance;
    if (searchInstance) {
        searchInstance.showSuggestions = false;
    }
    hideSuggestionsByBlur.value[index] = false;
    skipLoadAfterSelect.value[index] = true;
    setSelectedEntity(index, entity);
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
    try {
        setFieldValue && setFieldValue(`targets.${index}.targetId`, null);
    } catch (e) {
        // ignore
    }
    selectedEntities.value[index] = null;
    emit("update:modelValue", targetsLocal.value);
};

const loadSelectedEntity = async (index: number, targetId: number, targetType: string) => {
    const entityType = getEntityType(targetType);
    if (!entityType || !targetId) return;

    const searchInstance = entitySearchInstances.value[index];
    if (searchInstance) {
        try {
            const entity = await searchInstance.getEntityById(targetId);
            if (entity) {
                setSelectedEntity(index, entity);
            } else {
                const suggestions = getSuggestionsArray(searchInstance);
                const found = suggestions.find((s: any) => Number(s.id) === Number(targetId));
                if (found) {
                    setSelectedEntity(index, found);
                } else {
                    setSelectedEntity(index, { id: targetId, name: `#${targetId}` } as any);
                }
            }
        } catch (err: any) {
            console.error("Failed to load selected entity for index", index, "id", targetId, err);
            try {
                const suggestions = getSuggestionsArray(searchInstance);
                const found = suggestions.find((s: any) => Number(s.id) === Number(targetId));
                if (found) {
                    setSelectedEntity(index, found);
                    return;
                }
            } catch {
                // ignore
            }
            setSelectedEntity(index, { id: targetId, name: `#${targetId}` } as any);
            return;
        }
    }
};

watch(
    targetsLocal,
    async () => {
        if (isUpdating.value) return;
        isUpdating.value = true;
        syncMetaStrings();
        initializeEntitySearchInstances();

        for (let i = 0; i < targetsLocal.value.length; i++) {
            const target = targetsLocal.value[i];
            if (skipLoadAfterSelect.value[i]) {
                skipLoadAfterSelect.value[i] = false;
                continue;
            }
            if (target.targetId && target.targetType !== "order") {
                try {
                    await loadSelectedEntity(i, target.targetId, target.targetType);
                } catch (err) {
                    console.error("Error loading selected entity for target", i, err);
                }
            }
        }

        nextTick(() => {
            isUpdating.value = false;
        });
    },
    { deep: true, immediate: true },
);

watch(
    () => targetsLocal.value.map((t) => t.targetType),
    (newVal, oldVal) => {
        initializeEntitySearchInstances();
        try {
            for (let i = 0; i < (newVal || []).length; i++) {
                const prev = (oldVal || [])[i];
                const curr = newVal[i];
                if (prev !== curr) {
                    if (targetsLocal.value[i]) {
                        targetsLocal.value[i].targetId = null;
                    }
                    selectedEntities.value[i] = null;
                    const inst = entitySearchInstances.value[i];
                    try {
                        if (inst) {
                            if (typeof inst.clearSelection === "function") inst.clearSelection();
                            if (inst.selectedEntity && "value" in inst.selectedEntity) inst.selectedEntity.value = null;
                        }
                    } catch (e) {
                        // ignore
                    }
                }
            }
            emit("update:modelValue", targetsLocal.value);
        } catch (e) {
            // ignore
        }
    },
    { deep: true },
);

watch(metaStrings, (newVal) => {
    if (isUpdating.value) return;
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
