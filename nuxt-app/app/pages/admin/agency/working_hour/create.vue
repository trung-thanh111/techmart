<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.create") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.agencyWorkingHour.breadcrumb'), to: AGENCYWORKINGHOURINDEX }, { label: $t('admin.common.create') }]" />
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
              <p class="fst-italic">
                {{ $t("admin.common.required") }}
              </p>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <form @submit.prevent="handleStore" class="row space-y-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label" for="dayOfWeek">
              {{ $t("admin.common.form.dayOfWeek") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="dayOfWeek" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="dayOfWeek" type="text" :placeholder="$t('admin.common.form.dayOfWeekPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="openTime">
                  {{ $t("admin.common.form.openTime") }}:
                  <span class="text-danger">*</span>
                </label>
                <Field name="openTime" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="openTime" type="time" class="form-control rounded py-[1px] w-100" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">
                    {{ $t(errorMessage) }}
                  </small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="closeTime">
                  {{ $t("admin.common.form.closeTime") }}:
                  <span class="text-danger">*</span>
                </label>
                <Field name="closeTime" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="closeTime" type="time" class="form-control rounded py-[1px] w-100" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">
                    {{ $t(errorMessage) }}
                  </small>
                </Field>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="AGENCYWORKINGHOURINDEX" />
        <Button variant="success" :loading="isLoading" />
      </div>
    </div>
    <div class="col-lg-4 mt-0">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.setting") }}</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.status") }}</label>
            <Field name="publish" v-slot="{ field, errorMessage, meta }">
              <PublishSelect :modelValue="field.value" @update:modelValue="field.onChange" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import Button from "~/components/admin/custom/Button.vue";
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import { AGENCYWORKINGHOURINDEX } from "~/utils/constants";
import { useAgencyWorkingHour } from "~/composables/admin/agency/useWorkingHour";

const { isLoading, handleStore } = useAgencyWorkingHour();

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
