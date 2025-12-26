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
          <AccordionPanel v-for="(target, index) in targetsLocal" :key="index" :value="index">
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
                  <div class="col-md-4">
                    <label class="form-label">{{ $t("admin.page.promotion.targetType") }}</label>
                    <Field :name="`targets.${index}.targetType`" v-slot="{ field, errorMessage, meta }">
                      <Select v-model="field.value" @update:modelValue="field.onChange" :options="PROMOTION_TARGET_TYPES" option-label="label" option-value="value" class="w-full" :placeholder="$t('admin.page.promotion.selectTargetType')" style="height: 38px" />
                      <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                    </Field>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ $t("admin.page.promotion.targetId") }}</label>
                    <Field :name="`targets.${index}.targetId`" v-slot="{ field, errorMessage, meta }">
                      <InputNumber v-model="field.value" @update:modelValue="field.onChange" :min="0" class="w-full" input-class="rounded" size="small" style="height: 38px" />
                      <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                    </Field>
                  </div>
                  <div class="col-md-4">
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

                <div class="row mt-3">
                  <!-- Row 2: metadata full width -->
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
                        <button type="button" class="text-blue-900 text-sm" @click="openModal(index)">{{ $t("admin.page.promotion.editMetadata") }}</button>
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
    <!-- Modal editors for metadata -->
    <div v-for="(_, mi) in targetsLocal" :key="'modal-' + mi">
      <div v-if="showModal[mi]" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded p-4 w-11/12 max-w-2xl">
          <h5 class="mb-3">{{ $t("admin.page.promotion.editMetadataTitle") }}</h5>
          <textarea v-model="modalContent[mi]" class="w-full border rounded p-2" rows="8" :placeholder="$t('admin.page.promotion.editMetadataPlaceholder')"></textarea>
          <div class="flex justify-end gap-2 mt-3">
            <button class="btn btn-secondary" @click="closeModal(mi)">{{ $t("admin.components.buttons.cancel") }}</button>
            <button class="btn btn-primary" @click="saveModal(mi)">{{ $t("admin.components.buttons.confirm") }}</button>
          </div>
        </div>
      </div>
    </div>
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
import { PROMOTION_TARGET_TYPES } from "~/utils/constants";
import { useAppToast } from "~/composables/useAppToast";
import { useI18n } from "vue-i18n";

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
const targetsLocal = ref<any[]>(props.modelValue || []);
watch(
  () => props.modelValue,
  (v) => {
    targetsLocal.value = v || [];
  },
  { deep: true }
);

const addTarget = () => {
  const newRecord = {
    targetType: "order",
    targetId: null,
    isIncluded: true,
    metadata: null,
  };
  targetsLocal.value = [...targetsLocal.value, newRecord];
  activeIndex.value = targetsLocal.value.length - 1;
  emit("update:modelValue", targetsLocal.value);
};

const removeTarget = (index: number) => {
  targetsLocal.value = targetsLocal.value.filter((_, i) => i !== index);
  // collapse after removal
  activeIndex.value = null;
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

// modal editor state (declare before any callers)
const showModal = ref<boolean[]>([]);
const modalContent = ref<string[]>([]);

const ensureModalArrays = () => {
  while (showModal.value.length < targetsLocal.value.length) showModal.value.push(false);
  while (showModal.value.length > targetsLocal.value.length) showModal.value.pop();
  while (modalContent.value.length < targetsLocal.value.length) modalContent.value.push("");
  while (modalContent.value.length > targetsLocal.value.length) modalContent.value.pop();
};

watch(
  targetsLocal,
  () => {
    syncMetaStrings();
    ensureModalArrays();
  },
  { deep: true, immediate: true }
);

watch(metaStrings, (newVal) => {
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
});

const confirmAndClose = () => {
  emit("save", targetsLocal.value);
  toast.success("Đã lưu đối tượng áp dụng");
};
const openModal = (index: number) => {
  ensureModalArrays();
  modalContent.value[index] = metaStrings.value[index] ?? "";
  showModal.value[index] = true;
};

const closeModal = (index: number) => {
  showModal.value[index] = false;
};

const saveModal = (index: number) => {
  const raw = modalContent.value[index] ?? "";
  try {
    const parsed = JSON.parse(raw);
    targetsLocal.value[index].metadata = parsed;
    metaStrings.value[index] = JSON.stringify(parsed);
    metaErrors.value[index] = null;
    toast.success("Đã cập nhật thông tin bổ sung");
    showModal.value[index] = false;
  } catch {
    targetsLocal.value[index].metadata = raw;
    metaStrings.value[index] = raw;
    metaErrors.value[index] = null;
    toast.success("Đã lưu thông tin bổ sung (chuỗi)");
    showModal.value[index] = false;
  }
  emit("update:modelValue", targetsLocal.value);
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
