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
                <template v-if="fields.length === 0">
                    <div class="text-center text-muted py-4">
                        {{ $t("admin.page.promotion.noTargets") }}
                    </div>
                </template>
                <template v-else>
                    <AccordionPanel v-for="(field, index) in fields" :key="field.key" :value="index">
                        <AccordionHeader>
                            <div class="flex items-center gap-2 w-full">
                                <span class="font-medium">#{{ index + 1 }} - {{ getTargetLabel(field.value.targetType) }}</span>
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
                                        <Field :name="`targets.${index}.targetType`" v-slot="{ field: f, errorMessage, meta }">
                                            <Select
                                                v-model="f.value"
                                                @update:modelValue="
                                                    (val) => {
                                                        f.onChange(val);
                                                        onTargetTypeChange(index, val);
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
                                        <Field :name="`targets.${index}.isIncluded`" v-slot="{ field: f, errorMessage, meta }">
                                            <Select
                                                v-model="f.value"
                                                @update:modelValue="f.onChange"
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
                                <div class="row mt-3" v-if="needsTargetId(field.value.targetType)">
                                    <div class="col-12">
                                        <div class="relative">
                                            <Field :name="`targets.${index}.targetId`" v-slot="{ field: f, errorMessage, meta }">
                                                <input type="hidden" :value="f.value" />
                                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                            </Field>
                                            <div class="flex gap-2 h-[38px]">
                                                <div class="flex-1 relative">
                                                    <input :value="searchKeywords[index] || ''" @input="updateSearchKeyword(index, ($event.target as HTMLInputElement).value)" @focus="showSuggestions(index)" @blur="hideSuggestions(index)" type="text" class="form-control rounded h-[38px]" :placeholder="$t('admin.page.promotion.searchEntity')" />
                                                    <div v-if="shouldShowSuggestions(index)" class="absolute w-full bg-white border rounded shadow-lg z-10 overflow-y-auto max-h-60">
                                                        <div v-if="isInstanceLoading(entitySearchInstances[index]) && getSuggestionsArray(entitySearchInstances[index]).length === 0" class="p-3 text-gray-500">
                                                            {{ $t("admin.page.promotion.loading") }}
                                                        </div>
                                                        <div v-for="suggestion in getSuggestionsArray(entitySearchInstances[index])" :key="suggestion.id" @mousedown.prevent="onEntitySelect(index, suggestion)" class="flex gap-2 p-1 hover:bg-gray-50 cursor-pointer border-b last:border-b-0">
                                                            <NuxtImg v-if="suggestion.image" :src="suggestion.image" alt="" class="w-10 h-10 object-cover rounded flex-shrink-0" loading="lazy" />
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm font-medium text-gray-700 truncate mb-1">{{ suggestion.name }}</p>
                                                                <p v-if="suggestion.price" class="text-xs font-bold text-red-600 mb-0">{{ formatPrice(suggestion.price) }}</p>
                                                                <p v-else-if="suggestion.metaDescription" class="text-xs text-gray-600 mb-0">{{ suggestion.metaDescription }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <Button type="button" variant="custom" severity="info" @click="showSuggestions(index)" :icon="'mdi mdi-magnify'" class="h-[38px]" />
                                            </div>
                                        </div>
                                        <div v-if="field.value.targetId" class="mt-3 bg-info-100 p-2 rounded">
                                            <div class="text-sm text-gray-600 mb-2 font-medium">{{ $t("admin.page.promotion.selectedEntityLabel") }}</div>
                                            <div class="p-2 bg-white rounded flex items-center gap-2 border">
                                                <span class="text-sm font-medium text-gray-700 me-2">#{{ field.value.targetId }}</span>
                                                <NuxtImg v-if="selectedEntities[index]?.image" :src="selectedEntities[index]?.image" alt="" class="w-10 h-10 object-cover rounded" loading="lazy" />
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate mb-1">{{ selectedEntities[index]?.name || $t("admin.page.promotion.loading") }}</p>
                                                    <p v-if="selectedEntities[index]?.price" class="text-xs text-red-600 mb-0">
                                                        {{ formatPrice(selectedEntities[index]?.price || 0) }}
                                                    </p>
                                                </div>
                                                <button @click="onEntityClear(index)" class="w-8 h-8 flex items-center justify-center rounded-full text-white bg-red-500 hover:bg-red-700 transition text-xs">
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
                                                <template v-if="field.value.metadata && typeof field.value.metadata === 'object'">
                                                    <div class="flex flex-wrap gap-2">
                                                        <div v-for="(v, k) in field.value.metadata" :key="k" class="px-3 py-1 rounded border bg-gray-50 text-sm">
                                                            <strong>{{ metadataLabel(String(k)) }}:</strong>
                                                            <span class="ml-1">{{ String(v) }}</span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <input :value="metaStrings[index] || ''" @input="(e) => updateMetaString(index, (e.target as HTMLInputElement).value)" type="text" :placeholder="$t('admin.page.promotion.metadata')" class="form-control rounded" style="height: 38px" />
                                                </template>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" @click="openMetadataDialog(index)">
                                                {{ $t("admin.page.promotion.editMetadata") }}
                                            </button>
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
import { ref, watch, nextTick, onMounted } from "vue";
import { Field, useFieldArray } from "vee-validate";
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

const { t } = useI18n();
const toast = useAppToast();

const { fields, push, remove, update } = useFieldArray<any>("targets");

const activeIndex = ref<number | null>(null);
const searchKeywords = ref<string[]>([]);
const entitySearchInstances = ref<any[]>([]);
const selectedEntities = ref<(SearchResult | null)[]>([]);
const showSuggestionsByBlur = ref<boolean[]>([]);
const metaStrings = ref<string[]>([]);

const addTarget = () => {
    push({
        targetType: "product",
        targetId: null,
        isIncluded: true,
        metadata: null,
    });
    activeIndex.value = fields.value.length - 1;
};

const removeTarget = (index: number) => {
    remove(index);
    if (activeIndex.value === index) activeIndex.value = null;
    else if (activeIndex.value !== null && activeIndex.value > index) activeIndex.value--;
};

const onTargetTypeChange = (index: number, val: string) => {
    const field = fields.value[index];
    if (!field) return;
    const newVal = { ...field.value, targetType: val, targetId: null, metadata: null };
    update(index, newVal);

    // reset search for this index
    searchKeywords.value[index] = "";
    selectedEntities.value[index] = null;
    initializeSearchInstance(index, val);
};

const needsTargetId = (targetType: string | null | undefined) => {
    return !!targetType && targetType !== "order" && targetType !== "location";
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

const initializeSearchInstance = (index: number, targetType: string) => {
    const type = getEntityType(targetType);
    if (type) {
        const instance = useEntitySearch(type);
        entitySearchInstances.value[index] = instance;

        // Initial load if we have a targetId
        const targetId = fields.value[index]?.value?.targetId;
        if (targetId) {
            loadEntity(index, targetId);
        }
    } else {
        entitySearchInstances.value[index] = null;
    }
};

const loadEntity = async (index: number, id: number) => {
    const instance = entitySearchInstances.value[index];
    if (!instance) return;
    try {
        const entity = await instance.getEntityById(id);
        if (entity) selectedEntities.value[index] = entity;
    } catch (e) {
        console.error("Failed to load entity", id, e);
    }
};

const updateSearchKeyword = (index: number, val: string) => {
    searchKeywords.value[index] = val;
    const instance = entitySearchInstances.value[index];
    if (instance) {
        instance.search(val);
        showSuggestionsByBlur.value[index] = true;
    }
};

const showSuggestions = (index: number) => {
    showSuggestionsByBlur.value[index] = true;
};

const hideSuggestions = (index: number) => {
    setTimeout(() => {
        showSuggestionsByBlur.value[index] = false;
    }, 200);
};

const shouldShowSuggestions = (index: number) => {
    return showSuggestionsByBlur.value[index];
};

const getSuggestionsArray = (instance: any) => {
    if (!instance) return [];
    return instance.suggestions?.value || instance.suggestions || [];
};

const isInstanceLoading = (instance: any) => {
    if (!instance) return false;
    return instance.isLoading?.value || instance.isLoading || false;
};

const onEntitySelect = (index: number, entity: SearchResult) => {
    selectedEntities.value[index] = entity;
    searchKeywords.value[index] = entity.name;
    showSuggestionsByBlur.value[index] = false;

    const field = fields.value[index];
    if (field) {
        update(index, { ...field.value, targetId: entity.id });
    }
};

const onEntityClear = (index: number) => {
    selectedEntities.value[index] = null;
    searchKeywords.value[index] = "";
    const field = fields.value[index];
    if (field) {
        update(index, { ...field.value, targetId: null });
    }
};

// Metadata logic
const showMetadataDialog = ref(false);
const currentMetadataContent = ref("");
const currentMetadataIndex = ref(-1);

const openMetadataDialog = (index: number) => {
    currentMetadataIndex.value = index;
    const meta = fields.value[index]?.value?.metadata;
    currentMetadataContent.value = typeof meta === "object" && meta !== null ? JSON.stringify(meta, null, 2) : String(meta || "");
    showMetadataDialog.value = true;
};

const closeMetadataDialog = () => {
    showMetadataDialog.value = false;
};

const saveMetadataDialog = () => {
    const index = currentMetadataIndex.value;
    if (index === -1) return;

    let meta: any = currentMetadataContent.value;
    try {
        meta = JSON.parse(currentMetadataContent.value);
    } catch (e) {
        // use as string if not valid JSON
    }

    const field = fields.value[index];
    if (field) {
        update(index, { ...field.value, metadata: meta });
    }
    showMetadataDialog.value = false;
    toast.success(t("admin.saveSuccess"));
};

const updateMetaString = (index: number, val: string) => {
    metaStrings.value[index] = val;
    const field = fields.value[index];
    if (field) {
        let meta = val;
        try {
            meta = JSON.parse(val);
        } catch (e) {}
        update(index, { ...field.value, metadata: meta });
    }
};

const metadataLabel = (key: string) => {
    const mapping: Record<string, string> = {
        category_name: t("admin.page.promotion.meta_category_name"),
        product_name: t("admin.page.promotion.meta_product_name"),
        reason: t("admin.page.promotion.meta_reason"),
    };
    return mapping[key] || key;
};

const getTargetLabel = (type: string) => {
    const opt = PROMOTION_TARGET_TYPES.find((o: any) => o.value === type);
    return opt ? opt.label : type || t("admin.page.promotion.selectTargetType");
};

const confirmAndClose = () => {
    toast.success(t("admin.saveSuccess"));
};

// Initialize search instances on mount or when fields change (e.g. initial setValues from usePromotion)
watch(
    () => fields.value.length,
    (newLen, oldLen) => {
        if (newLen > (oldLen || 0)) {
            for (let i = Math.max(0, oldLen || 0); i < newLen; i++) {
                const target = fields.value[i].value;
                if (target && target.targetType) {
                    initializeSearchInstance(i, target.targetType);
                }
            }
        }
    },
    { immediate: true },
);

// One-time initialization for existing fields
onMounted(async () => {
    await nextTick();
    fields.value.forEach((field, index) => {
        if (field.value.targetType) {
            initializeSearchInstance(index, field.value.targetType);
        }
    });
});
</script>
