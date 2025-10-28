<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.create") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.banner.breadcrumb'), to: BANNERINDEX }, { label: $t('admin.common.create') }]" />
        </div>
      </div>
    </div>
  </div>

  <form @submit.prevent="handleStore" class="row space-y-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label" for="name">
              {{ $t("admin.common.form.name") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="name" type="text" :placeholder="$t('admin.common.form.namePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
            <div class="d-flex gap-2 text-info mt-1 underline cursor-pointer">
              <span class="mdi mdi-link-variant"></span>
              <p class="genrate_slug_to_name w-full pr-2" v-tooltip.bottom="$t('admin.common.form.urlTooltip')">{{ config.public.baseUrl }}/{{ slug }}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="order">{{ $t("admin.common.form.order") }}:</label>
                <Field name="order" v-slot="{ field, errorMessage, meta }">
                  <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="1" showButtons class="w-full" :placeholder="$t('admin.common.form.orderPlaceholder')" input-class="!rounded py-[7px] w-full" size="small" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">
                    {{ $t(errorMessage) }}
                  </small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="linkUrl">
                  {{ $t("admin.common.form.linkUrl") }}
                </label>
                <Field name="linkUrl" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="linkUrl" type="text" :placeholder="$t('admin.common.form.linkUrlPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="startAt">
                  {{ $t("admin.common.form.startAt") }}
                </label>
                <Field name="startAt" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="startAt" type="datetime-local" class="form-control rounded py-[1px] w-100" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">
                    {{ $t(errorMessage) }}
                  </small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="endAt">
                  {{ $t("admin.common.form.endAt") }}
                </label>
                <Field name="endAt" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="startAt" type="datetime-local" class="form-control rounded py-[1px] w-100" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">
                    {{ $t(errorMessage) }}
                  </small>
                </Field>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.image") }}</h5>
        </div>
        <div class="card-body">
          <MultipleImageUpload v-model="albumData" field-name="album" :max-files="15" @change="onAlbumChange" />
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="BANNERINDEX" />
        <Button variant="success" :loading="isLoading" />
      </div>
    </div>
    <div class="col-lg-4 mt-0">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.setting") }}</h5>
        </div>
        <div class="card-body">
          <div class="form-check d-flex align-items-center gap-2 ps-0 mb-3">
            <Field name="isHot" v-slot="{ field }">
              <AppCheckbox :modelValue="field.value" @update:modelValue="field.onChange" inputId="is_hot" class="scale-125" />
            </Field>
            <label for="is_hot" class="mb-0">
              {{ $t("admin.common.form.isHot") }}
            </label>
          </div>
          <div class="mb-3">
            <label class="form-label">
              {{ $t("admin.common.form.page") }}
              <span class="text-danger">*</span>
            </label>
            <Field name="pageId" v-slot="{ field, errorMessage, meta }">
              <Select v-model="field.value" :options="pageOptions" @update:modelValue="field.onChange" @focus="loadPage" option-label="name" option-value="id" filter showClear :placeholder="$t('admin.common.form.pagePlaceholder')" class="rounded py-[1px] w-100" size="small">
                <template #header>
                  <div class="inline-flex items-center justify-center p-2">
                    <span class="font-medium whitespace-nowrap">{{ $t("admin.common.form.page") }}</span>
                  </div>
                </template>
                <template #footer>
                  <div class="p-3 cursor-pointer flex items-center justify-end font-light text-xs text-emerald-600 gap-1">
                    <i class="ri-add-line text-emerald-600"></i>
                    <span @click="showCreatePageModal = true">Thêm mới</span>
                  </div>
                </template>
              </Select>
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">
              {{ $t("admin.common.form.position") }}
              <span class="text-danger">*</span>
            </label>
            <Field name="positionId" v-slot="{ field, errorMessage, meta }">
              <Select v-model="field.value" :options="positionOptions" @update:modelValue="field.onChange" @focus="loadPosition" option-label="name" option-value="id" filter showClear :placeholder="$t('admin.common.form.positionPlaceholder')" class="rounded py-[1px] w-100" size="small">
                <template #header>
                  <div class="inline-flex items-center justify-center p-2">
                    <span class="font-medium whitespace-nowrap">{{ $t("admin.common.form.position") }}</span>
                  </div>
                </template>
                <template #footer>
                  <div class="p-3 cursor-pointer flex items-center justify-end font-light text-xs text-emerald-600 gap-1">
                    <i class="ri-add-line text-emerald-600"></i>
                    <span @click="showCreatePositionModal = true">Thêm mới</span>
                  </div>
                </template>
              </Select>
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
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

  <!-- Modal tạo Page -->
  <Dialog v-model:visible="showCreatePageModal" modal maximizable :header="$t('admin.common.create')" :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" append-to="body">
    <form @submit.prevent="handleCreatePage">
      <div class="mb-3">
        <label class="form-label" for="pageName">
          {{ $t("admin.common.form.name") }}:
          <span class="text-danger">*</span>
        </label>
        <input v-model="pageForm.name" id="pageName" type="text" :placeholder="$t('admin.common.form.namePlaceholder')" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">{{ $t("admin.common.status") }}</label>
        <PublishSelect v-model="pageForm.publish" />
      </div>
      <div class="flex justify-end gap-3 mt-4">
        <Button type="button" variant="cancel" @click="showCreatePageModal = false" />
        <Button type="submit" variant="success" :loading="pageForm.isLoading" />
      </div>
    </form>
  </Dialog>

  <!-- Modal tạo Position -->
  <Dialog v-model:visible="showCreatePositionModal" modal maximizable :header="$t('admin.common.create')" :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" append-to="body">
    <form @submit.prevent="handleCreatePosition">
      <div class="mb-3">
        <label class="form-label" for="positionName">
          {{ $t("admin.common.form.name") }}:
          <span class="text-danger">*</span>
        </label>
        <input v-model="positionForm.name" id="positionName" type="text" :placeholder="$t('admin.common.form.namePlaceholder')" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">{{ $t("admin.common.status") }}</label>
        <PublishSelect v-model="positionForm.publish" />
      </div>
      <div class="flex justify-end gap-3 mt-4">
        <Button type="button" variant="cancel" @click="showCreatePositionModal = false" />
        <Button type="submit" variant="success" :loading="positionForm.isLoading" />
      </div>
    </form>
  </Dialog>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import { BANNERINDEX } from "~/utils/constants";
import Button from "~/components/admin/custom/Button.vue";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import { useBanner } from "~/composables/admin/banner/useBanner";
import MultipleImageUpload from "~/components/admin/custom/MultipleImageUpload.vue";
import AppCheckbox from "~/components/admin/custom/AppCheckbox.vue";
import { pageService } from "~/services/banner/page.service";
import { positionService } from "~/services/banner/position.service";
import type { AlbumItem } from "~/interfaces/upload/image.interface";

const { handleStore, setValues, values, isLoading, config, slug, pageOptions, positionOptions, loadPage, loadPosition } = useBanner();

const albumData = ref<AlbumItem[] | null>(null);
const onAlbumChange = () => {
  setValues({ ...values, album: albumData.value });
};

// Forms cho dialog
const pageForm = ref({
  name: "",
  publish: 2,
  isLoading: false,
});

const positionForm = ref({
  name: "",
  publish: 2,
  isLoading: false,
});

const showCreatePageModal = ref(false);
const showCreatePositionModal = ref(false);

const handleCreatePage = async () => {
  try {
    pageForm.value.isLoading = true;
    const pageData = {
      name: pageForm.value.name,
      publish: pageForm.value.publish || 2,
    };
    const response = await pageService.store(pageData);
    if (response && typeof response === "object" && "data" in response && response.data?.id) {
      const newPage = {
        id: response.data.id,
        name: response.data.name || "",
      };
      pageOptions.value.push(newPage);
      setValues({ ...values, pageId: response.data.id });

      showCreatePageModal.value = false;
      pageForm.value = {
        name: "",
        publish: 2,
        isLoading: false,
      };
    }
  } catch (error) {
    console.error("Error creating page:", error);
  } finally {
    pageForm.value.isLoading = false;
  }
};

const handleCreatePosition = async () => {
  try {
    positionForm.value.isLoading = true;
    const positionData = {
      name: positionForm.value.name,
      publish: positionForm.value.publish || 2,
    };
    const response = await positionService.store(positionData);
    if (response && typeof response === "object" && "data" in response && response.data?.id) {
      const newPosition = {
        id: response.data.id,
        name: response.data.name || "",
      };
      positionOptions.value.push(newPosition);
      setValues({ ...values, positionId: response.data.id });

      showCreatePositionModal.value = false;
      positionForm.value = {
        name: "",
        publish: 2,
        isLoading: false,
      };
    }
  } catch (error) {
    console.error("Error creating position:", error);
  } finally {
    positionForm.value.isLoading = false;
  }
};

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
