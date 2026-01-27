<template>
    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h5 class="card-title mb-0">{{ $t("admin.page.promotion.rules") }}</h5>
            <div class="flex items-center gap-2">
                <Button type="button" variant="custom" severity="info" :label="$t('admin.page.promotion.addNew')" @click="addRule">
                    <i class="mdi mdi-plus"></i>
                    {{ $t("admin.page.promotion.addNew") }}
                </Button>
                <Button type="button" variant="custom" severity="success" :label="$t('admin.page.promotion.saveChange')" @click="confirmAndClose">
                    {{ $t("admin.page.promotion.saveChange") }}
                </Button>
            </div>
        </div>
        <div class="card-body p-2">
            <Accordion :modelValue="activeIndex" @update:modelValue="(val: any) => (activeIndex = val)" expandIcon="pi pi-plus" collapseIcon="pi pi-minus">
                <template v-if="rules.length === 0">
                    <div class="text-center text-muted py-4">
                        {{ $t("admin.page.promotion.noRules") }}
                    </div>
                </template>
                <template v-else>
                    <AccordionPanel v-for="(rule, index) in rules" :key="index" :value="index">
                        <AccordionHeader>
                            <div class="flex items-center gap-2 w-full">
                                <span class="font-medium">#{{ index + 1 }} - {{ getRuleTypeLabel(rule.ruleType) }}</span>
                                <div class="ml-auto flex items-center gap-2">
                                    <button type="button" @click.stop="removeRule(index)" class="w-8 h-8 flex items-center justify-center rounded-full text-white bg-red-500 hover:bg-red-700 transition">
                                        <i class="mdi mdi-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </AccordionHeader>
                        <AccordionContent>
                            <div class="mb-3 relative">
                                <div class="record-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">{{ $t("admin.page.promotion.ruleGroup") }}</label>
                                            <Field :name="`rules.${index}.ruleGroup`" v-slot="{ field, errorMessage, meta }">
                                                <InputNumber v-model="field.value" @update:modelValue="field.onChange" :min="0" class="w-full" input-class="rounded" size="small" style="height: 38px" />
                                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                            </Field>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">{{ $t("admin.page.promotion.logicOperator") }}</label>
                                            <Field :name="`rules.${index}.logicOperator`" v-slot="{ field, errorMessage, meta }">
                                                <Select v-model="field.value" @update:modelValue="field.onChange" :options="PROMOTION_LOGIC_OPERATORS" option-label="label" option-value="value" class="w-full" :placeholder="$t('admin.page.promotion.selectLogicOperator')" style="height: 38px" />
                                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                            </Field>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">{{ $t("admin.page.promotion.ruleType") }}</label>
                                            <Field :name="`rules.${index}.ruleType`" v-slot="{ field, errorMessage, meta }">
                                                <Select
                                                    v-model="field.value"
                                                    @update:modelValue="
                                                        (val) => {
                                                            field.onChange(val);
                                                            onRuleTypeChange(index, val);
                                                        }
                                                    "
                                                    :options="PROMOTION_RULE_TYPES"
                                                    option-label="label"
                                                    option-value="value"
                                                    class="w-full h-[38px]"
                                                    :placeholder="$t('admin.page.promotion.selectRuleType')"
                                                />
                                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                            </Field>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label class="form-label">{{ $t("admin.page.promotion.operator") }}</label>
                                            <Field :name="`rules.${index}.operator`" v-slot="{ field, errorMessage, meta }">
                                                <Select v-model="field.value" @update:modelValue="field.onChange" :options="PROMOTION_OPERATORS" option-label="label" option-value="value" class="w-full" :placeholder="$t('admin.page.promotion.selectOperator')" style="height: 38px" />
                                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                            </Field>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">{{ $t("admin.page.promotion.value") }}</label>
                                            <Field :name="`rules.${index}.value`" v-slot="{ field, errorMessage, meta }">
                                                <template v-if="rule.ruleType === 'min_order_total'">
                                                    <InputNumber v-model="field.value" @update:modelValue="field.onChange" mode="currency" currency="VND" locale="vi-VN" class="w-full h-[38px] rounded" :min="0" />
                                                </template>
                                                <template v-else-if="rule.ruleType === 'time_slot'">
                                                    <div class="flex gap-2">
                                                        <input :value="(field.value && field.value.from) || ''" @input="(e) => updateNestedFieldFromEvent(field, 'from', e)" class="form-control rounded h-[38px]" placeholder="Từ (HH:MM)" />
                                                        <input :value="(field.value && field.value.to) || ''" @input="(e) => updateNestedFieldFromEvent(field, 'to', e)" class="form-control rounded h-[38px]" placeholder="Đến (HH:MM)" />
                                                    </div>
                                                </template>
                                                <template v-else-if="rule.ruleType === 'product_qty_by_category'">
                                                    <div class="flex gap-2">
                                                        <input :value="(field.value && field.value.category_id) || ''" @input="(e) => updateNestedFieldFromEventNumber(field, 'category_id', e)" class="form-control rounded h-[38px]" placeholder="ID danh mục" />
                                                        <input :value="(field.value && field.value.min_qty) || ''" @input="(e) => updateNestedFieldFromEventNumber(field, 'min_qty', e)" class="form-control rounded h-[38px]" placeholder="SL tối thiểu" />
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <input :value="field.value" @input="(e) => updateFieldFromEvent(field, e)" type="text" class="form-control rounded h-[38px]" :class="errorMessage && meta.touched ? 'border-red-500' : ''" placeholder="Nhập giá trị điều kiện" />
                                                </template>
                                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                            </Field>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <label class="form-label">{{ $t("admin.page.promotion.description") }}</label>
                                            <Field :name="`rules.${index}.description`" v-slot="{ field, errorMessage, meta }">
                                                <CKEditor v-model="field.value" @update:modelValue="field.onChange" class="h-32" />
                                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                            </Field>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </AccordionContent>
                    </AccordionPanel>
                </template>
            </Accordion>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { Field } from "vee-validate";
import Button from "~/components/custom/Button.vue";
import CKEditor from "~/components/custom/CKEditor.vue";
import InputNumber from "primevue/inputnumber";
import Select from "primevue/select";
import Accordion from "primevue/accordion";
import AccordionPanel from "primevue/accordionpanel";
import AccordionHeader from "primevue/accordionheader";
import AccordionContent from "primevue/accordioncontent";
import { PROMOTION_RULE_TYPES, PROMOTION_OPERATORS, PROMOTION_LOGIC_OPERATORS } from "~/utils/constants";
import { useAppToast } from "~/composables/useAppToast";
import { promotionService } from "~/services/promotion/promotion.service";
import { useRoute } from "vue-router";

interface Props {
    modelValue: any[];
    promotionId?: number | string | null;
}

const props = defineProps<Props>();
const route = useRoute();
const emit = defineEmits<{
    "update:modelValue": [value: any[]];
    save: [value: any[]];
    "save-record": [index: number, record: any];
}>();

const rules = ref<any[]>(props.modelValue || []);
import { watch } from "vue";

// keep local rules in sync with parent prop
watch(
    () => props.modelValue,
    (v) => {
        rules.value = v || [];
    },
    { deep: true },
);

const activeIndex = ref<number | null>(null);

const addRule = async () => {
    const newRecord = {
        ruleGroup: rules.value.length,
        logicOperator: null,
        ruleType: null,
        operator: null,
        value: null,
        description: null,
    };
    rules.value = [...rules.value, newRecord];
    activeIndex.value = rules.value.length - 1;
    emit("update:modelValue", rules.value);
};

const removeRule = async (index: number) => {
    rules.value = rules.value.filter((_, i) => i !== index);
    // collapse after removal
    activeIndex.value = null;
    // notify parent of change
    emit("update:modelValue", rules.value);
};
const toast = useAppToast();

const confirmAndClose = () => {
    emit("save", rules.value);
    toast.success("Đã lưu điều kiện");
};
const onRuleTypeChange = (index: number, newType: string) => {
    // initialize appropriate default value shape when changing type
    const r = rules.value[index];
    if (!r) return;
    if (newType === "min_order_total") {
        r.value = r.value || 0;
    } else if (newType === "time_slot") {
        r.value = r.value && typeof r.value === "object" ? r.value : { from: "", to: "" };
    } else if (newType === "product_qty_by_category") {
        r.value = r.value && typeof r.value === "object" ? r.value : { category_id: null, min_qty: null };
    } else {
        r.value = r.value ?? "";
    }
    // update
    rules.value = [...rules.value];
    emit("update:modelValue", rules.value);
};

const saveRecord = async (index: number) => {
    const record = rules.value[index];
    // persist then emit
    await persistRules();
    emit("save-record", index, record);
    toast.success("Đã lưu điều kiện này");
    // close the panel after saving
    activeIndex.value = null;
};
const getPromotionId = () => {
    if (props.promotionId) return Number(props.promotionId);
    return Number(route?.params?.id);
};

const persistRules = async () => {
    const pid = getPromotionId();
    if (!pid || Number.isNaN(pid)) return;
    try {
        const payload = { rules: rules.value } as any;
        const resp = await promotionService.update(pid, payload as any);
        toast.success(resp?.message || "Đã lưu điều kiện");
        // if server returns updated promotion, sync rules
        const refreshed = await promotionService.show(pid);
        const refreshedData = (refreshed?.data as any) || {};
        if (refreshedData && refreshedData.rules) {
            rules.value = refreshedData.rules;
            emit("update:modelValue", rules.value);
        }
    } catch (err: any) {
        console.error("Persist rules error:", err);
        toast.error(err?.response?.data?.message || "Lưu điều kiện thất bại");
    }
};
const updateNestedFieldFromEvent = (field: any, key: string, ev?: Event) => {
    const v = ev && (ev.target as HTMLInputElement)?.value;
    const newVal = { ...(field.value || {}) };
    newVal[key] = v;
    field.onChange(newVal);
};

const updateNestedFieldFromEventNumber = (field: any, key: string, ev?: Event) => {
    const raw = ev && (ev.target as HTMLInputElement)?.value;
    const num = raw ? Number(raw) : null;
    const newVal = { ...(field.value || {}) };
    newVal[key] = isNaN(num as number) ? null : num;
    field.onChange(newVal);
};

const updateFieldFromEvent = (field: any, ev?: Event) => {
    const v = ev && (ev.target as HTMLInputElement)?.value;
    field.onChange(v);
};
const getRuleTypeLabel = (type: string) => {
    const opt = PROMOTION_RULE_TYPES.find((o: any) => o.value === type);
    return opt ? opt.label : type;
};
</script>
