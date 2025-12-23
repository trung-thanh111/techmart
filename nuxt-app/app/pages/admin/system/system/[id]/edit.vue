<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.edit") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.system.breadcrumb'), to: SYSTEMINDEX }, { label: $t('admin.common.edit') }]" />
        </div>
      </div>
    </div>
  </div>

  <form @submit.prevent="() => handleUpdate(false)" class="row space-y-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label" for="keyword">
              {{ $t("admin.common.form.keyword") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="keyword" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="keyword" type="text" :placeholder="$t('admin.common.form.keywordPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.content") }}</label>
            <Field name="content" v-slot="{ field }">
              <textarea v-bind="field" rows="6" class="form-control" :placeholder="$t('admin.common.form.contentPlaceholder')" />
            </Field>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="SYSTEMINDEX" />
        <Button type="button" variant="custom" severity="info" :label="$t('admin.components.buttons.confirm')" :loading="isLoading" @click="() => handleUpdate(false)" />
        <Button type="button" variant="success" :loading="isLoading" @click="() => handleUpdate(true)" />
      </div>
    </div>
    <div class="col-lg-4 mt-0">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.setting") }}</h5>
        </div>
        <div class="card-body p-4">
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
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import { useSystem } from "~/composables/admin/system/useSystem";
import { SYSTEMINDEX } from "~/utils/constants";

const { isLoading, show, handleUpdate, setValues } = useSystem(undefined, "update");

const id = Number(useRoute().params.id);

// #region agent log
fetch("http://127.0.0.1:7242/ingest/0cc9012d-e8c7-4b3a-93fd-395427feb6af", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ sessionId: "debug-session", runId: "pre-fix", hypothesisId: "D", location: "pages/admin/system/[id]/edit.vue:setup", message: "calling show from edit", data: { id }, timestamp: Date.now() }) }).catch(() => {});
// #endregion

const resp = await show(id);

// #region agent log
fetch("http://127.0.0.1:7242/ingest/0cc9012d-e8c7-4b3a-93fd-395427feb6af", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ sessionId: "debug-session", runId: "pre-fix", hypothesisId: "E", location: "pages/admin/system/[id]/edit.vue:setup", message: "show returned", data: { resp }, timestamp: Date.now() }) }).catch(() => {});
// #endregion

const record = resp?.data ?? resp ?? null;

setValues({
  keyword: record?.keyword ?? "",
  content: record?.content ?? null,
  publish: record?.publish ?? 0,
});

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
