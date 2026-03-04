<template>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $t("admin.common.edit") }}</h4>
                <div class="page-title-right">
                    <Breadcrumb :crumbs="[{ label: $t('admin.page.promotion.breadcrumb'), to: PROMOTIONINDEX }, { label: $t('admin.common.edit') }]" />
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="align-items-center">
                        <h5>{{ $t("admin.common.titleNote") }}</h5>
                        <span>
                            {{ $t("admin.common.note") }}
                            <p class="fst-italic">{{ $t("admin.common.required") }}</p>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form @submit.prevent="() => handleUpdate(false)" class="row space-y-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $t("admin.page.promotion.basicInfo") }}</h5>
                </div>
                <div class="card-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label" for="name">
                            {{ $t("admin.page.promotion.name") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <input v-bind="field" id="name" type="text" :placeholder="$t('admin.page.promotion.name')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="code">{{ $t("admin.page.promotion.code") }}:</label>
                        <div class="input-group">
                            <Field name="code" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                                <input v-bind="field" id="code" type="text" :placeholder="$t('admin.page.promotion.code')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                            </Field>
                            <button type="button" class="btn btn-outline-secondary" @click="setValues({ ...(values as any), code: generateCouponCode() })">
                                <i class="mdi mdi-refresh"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">
                            {{ $t("admin.common.form.description") }}
                        </label>
                        <Field name="description" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <div class="w-full">
                                <CKEditor v-model="field.value" @update:modelValue="field.onChange" height="h-32" />
                                <small v-if="errorMessage && meta.touched" class="text-danger">
                                    {{ $t(errorMessage) }}
                                </small>
                            </div>
                        </Field>
                    </div>
                </div>
            </div>

            <!-- Rules -->
            <PromotionRule v-model="formValues.rules" @save-record="saveRule" />

            <!-- Action -->
            <PromotionAction v-model="formValues.actions" />

            <!-- Target -->
            <PromotionTarget v-model="formValues.targets" />

            <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
                <Button type="button" variant="cancel" :to="PROMOTIONINDEX" />
                <Button type="button" variant="custom" severity="info" :label="$t('admin.components.buttons.confirm')" :loading="isLoading" @click="() => handleUpdate(false)" />
                <Button type="button" variant="success" :loading="isLoading" @click="() => handleUpdate(true)" />
            </div>
        </div>

        <div class="col-lg-4 mt-0">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $t("admin.common.image") }}</h5>
                </div>
                <div class="card-body">
                    <Field name="image" v-slot="{ field, errorMessage, meta }">
                        <SingleImageUpload v-model="field.value" @update:modelValue="field.onChange" />
                        <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                    </Field>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $t("admin.page.promotion.type") }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="type">
                            {{ $t("admin.page.promotion.type") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="type" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <Select v-model="field.value" @update:modelValue="field.onChange" :options="PROMOTION_TYPES" option-label="label" option-value="value" :placeholder="$t('admin.page.promotion.selectType')" class="w-full rounded py-[1px]" size="small" showClear checkmark />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>
                </div>
            </div>
            <!-- <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.page.promotion.promotionCatalogue") }}</h5>
        </div>
        <div class="card-body">
          <Field name="promotionCatalogueId" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <Select v-bind="field" :options="promotionCatalogueOptions" option-label="name" option-value="value" :placeholder="$t('admin.page.promotion.selectPromotionCatalogue')" showClear class="w-full" />
            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
          </Field>
        </div>
      </div> -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $t("admin.common.form.time") }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="start_date">{{ $t("admin.page.promotion.startDate") }}</label>
                        <Field name="startDate" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <Calendar v-model="field.value" @update:modelValue="field.onChange" dateFormat="yy-mm-dd" :showTime="true" hourFormat="24" class="w-full" input-class="rounded py-[5px] w-full" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="end_date">{{ $t("admin.page.promotion.endDate") }}</label>
                        <Field name="endDate" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <Calendar v-model="field.value" @update:modelValue="field.onChange" dateFormat="yy-mm-dd" :showTime="true" hourFormat="24" class="w-full" input-class="rounded py-[5px] w-full" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $t("admin.common.setting") }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="priority">{{ $t("admin.page.promotion.priority") }}</label>
                        <Field name="priority" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <InputNumber v-model="field.value" @update:modelValue="field.onChange" :min="0" class="w-full rounded py-[5px]" size="small" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>
                    <div class="mb-3">
                        <Field name="canCombine" v-slot="{ field, errorMessage, meta }">
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="field.value" @update:modelValue="field.onChange" :id="`can_combine`" binary />
                                <label class="form-check-label" :for="`can_combine`">
                                    {{ $t("admin.page.promotion.canCombine") }}
                                </label>
                            </div>
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="usage_limit">{{ $t("admin.page.promotion.usageLimit") }}</label>
                        <Field name="usageLimit" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <InputNumber v-model="field.value" @update:modelValue="field.onChange" :min="1" class="w-full rounded py-[5px]" size="small" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="usage_per_customer">{{ $t("admin.page.promotion.usagePerCustomer") }}</label>
                        <Field name="usagePerCustomer" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <InputNumber v-model="field.value" @update:modelValue="field.onChange" :min="1" class="w-full rounded py-[5px]" size="small" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ $t("admin.common.status") }}</label>
                        <Field name="publish" v-slot="{ field, errorMessage, meta }">
                            <PublishSelect :modelValue="field.value" @update:modelValue="field.onChange" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import { usePromotion } from "~/composables/promotion/usePromotion";
import { PROMOTIONINDEX } from "~/utils/constants";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import Button from "~/components/custom/Button.vue";
import PromotionRule from "~/components/promotion/PromotionRule.vue";
import PromotionAction from "~/components/promotion/PromotionAction.vue";
import PromotionTarget from "~/components/promotion/PromotionTarget.vue";
import SingleImageUpload from "~/components/custom/SingleImageUpload.vue";
import CKEditor from "~/components/custom/CKEditor.vue";
import PublishSelect from "~/components/custom/PublishSelect.vue";
import { promotionService } from "~/services/promotion/promotion.service";
import { useAppToast } from "~/composables/useAppToast";

const route = useRoute();
const id = Number(route.params.id);

const { values, errors, isLoading, show, handleUpdate, setValues, promotionCatalogueOptions, PROMOTION_TYPES, PROMOTION_PUBLISH_STATUS, PROMOTION_RULE_TYPES, PROMOTION_OPERATORS, PROMOTION_ACTION_TYPES, PROMOTION_APPLY_TO, PROMOTION_TARGET_TYPES, PROMOTION_LOGIC_OPERATORS, generateCouponCode } = usePromotion(undefined, "update");
const formValues = values as any;

onMounted(async () => {
    const promotionResponse = await show(id);
    const promotionData = (promotionResponse?.data as any) || null;

    if (promotionData) {
        const parseDate = (v: any) => {
            if (!v) return null;
            const d = v instanceof Date ? v : new Date(String(v));
            return Number.isNaN(d.getTime()) ? null : d;
        };

        setValues({
            name: promotionData.name ?? "",
            code: promotionData.code ?? "",
            description: promotionData.description ?? "",
            image: promotionData.image ?? null,
            type: (promotionData.type as any) ?? "order",
            priority: promotionData.priority ?? 0,
            promotionCatalogueId: promotionData.promotionCatalogue?.id ?? promotionData.promotionCatalogueId ?? null,
            canCombine: promotionData.canCombine ?? false,
            combinableWith: promotionData.combinableWith ?? null,
            usageLimit: promotionData.usageLimit ?? null,
            usagePerCustomer: promotionData.usagePerCustomer ?? null,
            startDate: parseDate(promotionData.startDate),
            endDate: parseDate(promotionData.endDate),
            publish: promotionData.publish ?? 2,
            // relations
            rules: promotionData.rules ?? [],
            actions: promotionData.actions ?? [],
            targets: promotionData.targets ?? [],
        });
    }
});

const toast = useAppToast();

const saveRule = async (index: number, record: any) => {
    try {
        const idNum = Number(id);
        const payload = { rules: formValues.rules } as any;
        const response = await promotionService.update(idNum, payload as any);
        toast.success(response?.message || "Đã lưu điều kiện");
        // refresh rules from server if available
        const refreshed = await show(idNum);
        const data = refreshed?.data || {};
        if (data.rules) setValues({ ...(values as any), rules: data.rules } as any);
    } catch (err: any) {
        toast.error(err?.response?.data?.message || "Lưu thất bại");
        console.error("Save rule error:", err);
    }
};

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>
