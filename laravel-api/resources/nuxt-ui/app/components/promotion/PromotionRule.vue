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
                <template v-if="fields.length === 0">
                    <div class="text-center text-muted py-4">
                        {{ $t("admin.page.promotion.noRules") }}
                    </div>
                </template>
                <template v-else>
                    <AccordionPanel v-for="(field, index) in fields" :key="field.key" :value="index">
                        <AccordionHeader>
                            <div class="flex items-center gap-2 w-full">
                                <span class="font-medium">#{{ index + 1 }} - {{ getRuleTypeLabel(field.value.ruleType) }}</span>
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
                                            <Field :name="`rules.${index}.ruleGroup`" v-slot="{ field: f, errorMessage, meta }">
                                                <InputNumber v-model="f.value" @update:modelValue="f.onChange" :min="0" class="w-full" input-class="rounded" size="small" style="height: 38px" />
                                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                            </Field>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">{{ $t("admin.page.promotion.logicOperator") }}</label>
                                            <Field :name="`rules.${index}.logicOperator`" v-slot="{ field: f, errorMessage, meta }">
                                                <Select v-model="f.value" @update:modelValue="f.onChange" :options="PROMOTION_LOGIC_OPERATORS" option-label="label" option-value="value" class="w-full" :placeholder="$t('admin.page.promotion.selectLogicOperator')" style="height: 38px" />
                                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                            </Field>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">{{ $t("admin.page.promotion.ruleType") }}</label>
                                            <Field :name="`rules.${index}.ruleType`" v-slot="{ field: f, errorMessage, meta }">
                                                <Select
                                                    v-model="f.value"
                                                    @update:modelValue="
                                                        (val) => {
                                                            f.onChange(val);
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
                                            <Field :name="`rules.${index}.operator`" v-slot="{ field: f, errorMessage, meta }">
                                                <Select v-model="f.value" @update:modelValue="f.onChange" :options="PROMOTION_OPERATORS" option-label="label" option-value="value" class="w-full" :placeholder="$t('admin.page.promotion.selectOperator')" style="height: 38px" />
                                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                            </Field>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">{{ $t("admin.page.promotion.value") }}</label>
                                            <Field :name="`rules.${index}.value`" v-slot="{ field: f, errorMessage, meta }">
                                                <template v-if="field.value.ruleType === 'min_order_total'">
                                                    <InputNumber v-model="f.value" @update:modelValue="f.onChange" mode="currency" currency="VND" locale="vi-VN" class="w-full h-[38px] rounded" :min="0" />
                                                </template>
                                                <template v-else-if="field.value.ruleType === 'time_slot'">
                                                    <div class="flex gap-2">
                                                        <input :value="(f.value && f.value.from) || ''" @input="(e) => updateNestedFieldFromEvent(f, 'from', e)" class="form-control rounded h-[38px]" placeholder="Từ (HH:MM)" />
                                                        <input :value="(f.value && f.value.to) || ''" @input="(e) => updateNestedFieldFromEvent(f, 'to', e)" class="form-control rounded h-[38px]" placeholder="Đến (HH:MM)" />
                                                    </div>
                                                </template>
                                                <template v-else-if="field.value.ruleType === 'product_qty_by_category'">
                                                    <div class="flex gap-2">
                                                        <input :value="(f.value && f.value.category_id) || ''" @input="(e) => updateNestedFieldFromEventNumber(f, 'category_id', e)" class="form-control rounded h-[38px]" placeholder="ID danh mục" />
                                                        <input :value="(f.value && f.value.min_qty) || ''" @input="(e) => updateNestedFieldFromEventNumber(f, 'min_qty', e)" class="form-control rounded h-[38px]" placeholder="SL tối thiểu" />
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <input :value="f.value" @input="(e) => updateFieldFromEvent(f, e)" type="text" class="form-control rounded h-[38px]" :class="errorMessage && meta.touched ? 'border-red-500' : ''" placeholder="Nhập giá trị điều kiện" />
                                                </template>
                                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                            </Field>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <label class="form-label">{{ $t("admin.page.promotion.description") }}</label>
                                            <Field :name="`rules.${index}.description`" v-slot="{ field: f, errorMessage, meta }">
                                                <CKEditor v-model="f.value" @update:modelValue="f.onChange" class="h-32" />
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
import { Field, useFieldArray } from "vee-validate";
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
import { useI18n } from "vue-i18n";
import { promotionService } from "~/services/promotion/promotion.service";
import { useRoute } from "vue-router";

interface Props {
    promotionId?: number | string | null;
}

const props = defineProps<Props>();
const route = useRoute();
const emit = defineEmits<{
    save: [value: any[]];
    "save-record": [index: number, record: any];
}>();

const { t } = useI18n();

const { fields, push, remove, update } = useFieldArray<any>("rules");

const activeIndex = ref<number | null>(null);

const addRule = async () => {
    push({
        ruleGroup: fields.value.length,
        logicOperator: "AND",
        ruleType: "min_order_total",
        operator: ">=",
        value: 0,
        description: null,
    });
    activeIndex.value = fields.value.length - 1;
};

const removeRule = async (index: number) => {
    remove(index);
    // collapse after removal
    activeIndex.value = null;
};
const toast = useAppToast();

const confirmAndClose = () => {
    toast.success(t("admin.saveSuccess"));
};

const onRuleTypeChange = (index: number, newType: string) => {
    // initialize appropriate default value shape when changing type
    const field = fields.value[index];
    if (!field) return;
    const r = { ...field.value };
    if (newType === "min_order_total") {
        r.value = r.value || 0;
    } else if (newType === "time_slot") {
        r.value = r.value && typeof r.value === "object" ? r.value : { from: "", to: "" };
    } else if (newType === "product_qty_by_category") {
        r.value = r.value && typeof r.value === "object" ? r.value : { category_id: null, min_qty: null };
    } else {
        r.value = r.value ?? "";
    }
    update(index, r);
};

const saveRecord = async (index: number) => {
    const record = fields.value[index].value;
    // persist then emit
    await persistRules();
    emit("save-record", index, record);
    toast.success(t("admin.saveSuccess"));
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
        const payload = { rules: fields.value.map((f) => f.value) } as any;
        const resp = await promotionService.update(pid, payload as any);
        toast.success(resp?.message || t("admin.saveSuccess"));
        // if server returns updated promotion, the parent will usually handle the sync
    } catch (err: any) {
        console.error("Persist rules error:", err);
        toast.error(err?.response?.data?.message || t("admin.saveFailed"));
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
