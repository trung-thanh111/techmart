<template>
    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h5 class="card-title mb-0">{{ $t("admin.page.promotion.actions") }}</h5>
            <div class="flex items-center gap-2">
                <Button type="button" variant="custom" severity="info" :label="$t('admin.page.promotion.addNew')" @click="addAction">
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
                        {{ $t("admin.page.promotion.noActions") }}
                    </div>
                </template>
                <template v-else>
                    <AccordionPanel v-for="(field, index) in fields" :key="field.key" :value="index">
                        <AccordionHeader>
                            <div class="flex items-center gap-2 w-full">
                                <span class="font-medium">#{{ index + 1 }} - {{ getActionTypeLabel(field.value.actionType) }}</span>
                                <div class="ml-auto flex items-center gap-2">
                                    <button type="button" @click.stop="removeAction(index)" class="w-8 h-8 flex items-center justify-center rounded-full text-white bg-red-500 hover:bg-red-700 transition">
                                        <i class="mdi mdi-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </AccordionHeader>
                        <AccordionContent>
                            <div class="record-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">{{ $t("admin.page.promotion.executionOrder") }}</label>
                                        <Field :name="`actions.${index}.executionOrder`" v-slot="{ field: f, errorMessage, meta }">
                                            <InputNumber v-model="f.value" @update:modelValue="f.onChange" :min="0" class="w-full" input-class="rounded" size="small" style="height: 38px" />
                                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                        </Field>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ $t("admin.page.promotion.actionType") }}</label>
                                        <Field :name="`actions.${index}.actionType`" v-slot="{ field: f, errorMessage, meta }">
                                            <Select v-model="f.value" @update:modelValue="f.onChange" :options="PROMOTION_ACTION_TYPES" option-label="label" option-value="value" class="w-full" :placeholder="$t('admin.page.promotion.selectActionType')" style="height: 38px" />
                                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                        </Field>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ $t("admin.page.promotion.actionValue") }}</label>
                                        <Field :name="`actions.${index}.actionValue`" v-slot="{ field: f, errorMessage, meta }">
                                            <InputNumber v-model="f.value" @update:modelValue="f.onChange" :min="0" class="w-full" input-class="rounded" size="small" style="height: 38px" />
                                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                        </Field>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $t("admin.page.promotion.maxDiscount") }}</label>
                                        <Field :name="`actions.${index}.maxDiscount`" v-slot="{ field: f, errorMessage, meta }">
                                            <InputNumber v-model="f.value" @update:modelValue="f.onChange" mode="currency" currency="VND" locale="vi-VN" :min="0" class="w-full" input-class="!rounded" size="small" style="height: 38px" />
                                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                        </Field>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $t("admin.page.promotion.minDiscount") }}</label>
                                        <Field :name="`actions.${index}.minDiscount`" v-slot="{ field: f, errorMessage, meta }">
                                            <InputNumber v-model="f.value" @update:modelValue="f.onChange" mode="currency" currency="VND" locale="vi-VN" :min="0" class="w-full" input-class="!rounded" size="small" style="height: 38px" />
                                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                        </Field>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $t("admin.page.promotion.applyTo") }}</label>
                                        <Field :name="`actions.${index}.applyTo`" v-slot="{ field: f, errorMessage, meta }">
                                            <Select v-model="f.value" @update:modelValue="f.onChange" :options="PROMOTION_APPLY_TO" option-label="label" option-value="value" class="w-full" :placeholder="$t('admin.page.promotion.selectApplyTo')" style="height: 38px" />
                                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                        </Field>
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
import InputNumber from "primevue/inputnumber";
import Select from "primevue/select";
import Accordion from "primevue/accordion";
import AccordionPanel from "primevue/accordionpanel";
import AccordionHeader from "primevue/accordionheader";
import AccordionContent from "primevue/accordioncontent";
import { PROMOTION_ACTION_TYPES, PROMOTION_APPLY_TO } from "~/utils/constants";
import { useAppToast } from "~/composables/useAppToast";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const { fields, push, remove } = useFieldArray<any>("actions");

const addAction = () => {
    push({
        executionOrder: fields.value.length,
        actionType: "percent",
        actionValue: 0,
        maxDiscount: 0,
        minDiscount: 0,
        applyTo: "cart_subtotal",
        config: null,
    });
    // open newly added panel
    activeIndex.value = fields.value.length - 1;
};

const removeAction = (index: number) => {
    remove(index);
    activeIndex.value = null;
};

// accordion state
const activeIndex = ref<number | null>(null);
const toast = useAppToast();

const confirmAndClose = () => {
    toast.success(t("admin.saveSuccess"));
};

const getActionTypeLabel = (type: string) => {
    const opt = PROMOTION_ACTION_TYPES.find((o: any) => o.value === type);
    return opt ? opt.label : type;
};
</script>
