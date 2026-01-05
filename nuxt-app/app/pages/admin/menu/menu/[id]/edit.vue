<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.edit") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.menu.breadcrumb'), to: '/admin/menu/menu' }, { label: $t('admin.common.edit') }]" />
        </div>
      </div>
    </div>
  </div>
  <form @submit.prevent="() => handleUpdate(false)" class="row space-y-4">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.info") }}</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">
              {{ $t("admin.common.form.name") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="name" v-slot="{ field, errorMessage, meta }">
              <input v-bind="field" type="text" class="form-control" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.slug") }}:</label>
            <input :value="slug" class="form-control" readonly />
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.position") }}:</label>
            <Field name="position" v-slot="{ field }">
              <Select v-model="field.value" @update:modelValue="field.onChange" :options="positions" option-label="label" option-value="value" showClear class="w-full" />
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.status") }}</label>
            <Field name="publish" v-slot="{ field }">
              <PublishSelect :modelValue="field.value" @update:modelValue="field.onChange" />
            </Field>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.page.menu.items") }}</h5>
        </div>
        <div class="card-body">
          <MenuManager :items="values.menuItems" @update:items="values.menuItems = $event" />
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="MENUINDEX" />
        <Button type="button" variant="custom" severity="info" :label="$t('admin.components.buttons.confirm')" :loading="isLoading" @click="() => handleUpdate(false)" />
        <Button type="button" variant="success" :loading="isLoading" @click="() => handleUpdate(true)" />
      </div>
    </div>
  </form>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import Button from "~/components/admin/custom/Button.vue";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import { useMenu } from "~/composables/admin/menu/useMenu";
import MenuManager from "~/components/admin/menu/MenuManager.vue";
import { MENUINDEX } from "~/utils/constants";

const { isLoading, values, slug, positionOptions, setValues, handleUpdate, show } = useMenu(undefined, "update");
const positions = positionOptions;
const id = Number(useRoute().params.id);

// Function to convert flat menu items to nested structure
const convertFlatToNested = (flatItems: any[]) => {
  const itemMap = new Map();
  const rootItems: any[] = [];

  // First pass: create all items in map
  flatItems.forEach((item, index) => {
    const nestedItem = {
      id: item.id,
      parentId: item.parentId || 0,
      name: item.name,
      slug: item.slug,
      icon: item.icon,
      order: item.order,
      publish: item.publish,
      __localId: item.id || `n-${index}`,
      children: [],
    };
    itemMap.set(item.id, nestedItem);
  });

  // Second pass: build tree structure
  flatItems.forEach((item) => {
    const nestedItem = itemMap.get(item.id);
    if ((item.parentId || 0) === 0) {
      // Root item
      rootItems.push(nestedItem);
    } else {
      // Child item
      const parent = itemMap.get(item.parentId);
      if (parent) {
        parent.children.push(nestedItem);
      }
    }
  });

  return rootItems;
};

onMounted(async () => {
  if (id) {
    const response = await show(id);
    if (response?.data) {
      // Transform menuItems from API response to form format
      const menuData = response.data;
      const flatMenuItems = (menuData.menuItems || []).map((item: any) => ({
        id: item.id,
        parentId: item.parentId || 0,
        name: item.name,
        slug: item.slug,
        icon: item.icon,
        order: item.order,
        publish: item.publish,
      }));

      // Convert flat to nested structure for MenuManager component
      const nestedMenuItems = convertFlatToNested(flatMenuItems);

      // Validate position value
      const validPositions: ("header" | "footer" | "sidebar" | "profile" | "mobile")[] = ["header", "footer", "sidebar", "profile", "mobile"];
      const position = validPositions.includes(menuData.position as any) ? (menuData.position as "header" | "footer" | "sidebar" | "profile" | "mobile") : "header";

      // Set form values
      setValues({
        name: menuData.name,
        slug: menuData.slug,
        position: position,
        publish: menuData.publish,
        menuItems: nestedMenuItems,
      });
    }
  }
});

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
