<script setup lang="ts">
import { computed } from "vue";
import { useI18n } from "vue-i18n";
const { t } = useI18n();

const props = withDefaults(
  defineProps<{
    type?: "button" | "submit" | "reset";
    label?: string;
    loading?: boolean;
    class?: string;
    severity?: "primary" | "secondary" | "success" | "info" | "warning" | "help" | "danger";
    to?: string;
    variant?: "success" | "cancel" | "delete" | "duplicate" | "custom";
    icon?: string;
    iconPos?: "left" | "right";
  }>(),
  {
    type: "submit",
    loading: false,
    severity: "primary",
    variant: "custom",
    iconPos: "left",
  }
);

const emit = defineEmits<{
  click: [event: MouseEvent];
}>();

const buttonConfig = computed(() => {
  const configs = {
    success: {
      label: t("admin.components.buttons.confirm"),
      severity: "success" as const,
      class: "py-1.5 px-3 border-0 rounded-sm text-[12px]",
    },
    cancel: {
      label: t("admin.components.buttons.cancel"),
      severity: "secondary" as const,
      class: "bg-[#e2e8f090] py-1.5 px-3 border-0 rounded-sm text-[12px]",
    },
    delete: {
      label: t("admin.components.buttons.delete"),
      severity: "danger" as const,
      class: "py-1.5 px-3 border-0 rounded-sm text-[12px]",
    },
    duplicate: {
      label: t("admin.components.buttons.duplicate"),
      severity: "info" as const,
      class: "py-1.5 px-3 border-0 rounded-sm text-[12px]",
    },
    custom: {
      label: props.label || "",
      severity: props.severity,
      class: props.class || "py-1.5 px-3 border-0 rounded-sm text-[12px]",
    },
  };

  return configs[props.variant];
});

const finalLabel = computed(() => {
  return props.variant === "custom" && props.label ? props.label : buttonConfig.value.label;
});

const finalSeverity = computed(() => {
  return props.variant === "custom" && props.severity ? props.severity : buttonConfig.value.severity;
});

const finalClass = computed(() => {
  return props.variant === "custom" && props.class ? props.class : buttonConfig.value.class;
});

const onClick = async (event: MouseEvent) => {
  if (props.to) {
    await navigateTo(props.to);
  }
  emit("click", event);
};
</script>

<template>
  <div>
    <Button :type="type" :label="finalLabel" :severity="finalSeverity" :loading="loading" :icon="icon" :iconPos="iconPos" :class="finalClass" :pt="{ loadingicon: 'w-4 h-4 mr-0.5' }" @click="onClick" />
  </div>
</template>
