<template>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $t("admin.common.edit") }}</h4>
                <div class="page-title-right">
                    <Breadcrumb :crumbs="[{ label: $t('admin.page.menu.breadcrumb'), to: MENUINDEX }, { label: $t('admin.common.edit') }]" />
                </div>
            </div>
        </div>
    </div>

    <form @submit.prevent="() => handleUpdate(false)" class="row space-y-4" v-if="!isLoadingData">
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
                            <InputText v-bind="field" type="text" class="form-control" placeholder="Nhập tên menu" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">
                                {{ $t(errorMessage) }}
                            </small>
                        </Field>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ $t("admin.common.form.slug") }}:</label>
                        <input :value="slug" class="form-control" readonly placeholder="Slug tự sinh" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ $t("admin.common.form.position") }}:</label>
                        <Field name="position" v-slot="{ field, errorMessage, meta }">
                            <Select v-model="field.value" @update:modelValue="field.onChange" :options="positionOptions" option-label="label" option-value="value" showClear class="!rounded w-full p-[1px]" size="small" />
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

        <div class="col-lg-8 mt-0">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $t("admin.page.menu.items") }}</h5>
                </div>
                <div class="card-body">
                    <MenuManager :items="menuItemsNested" @update:items="handleMenuItemsUpdate" />
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
                <Button type="button" variant="cancel" :to="MENUINDEX" />
                <Button type="button" variant="custom" severity="info" :label="$t('admin.components.buttons.confirm')" :loading="isLoading" @click="() => handleUpdate(false)" />
                <Button type="button" variant="success" :loading="isLoading" @click="() => handleUpdate(true)" />
            </div>
        </div>
    </form>

    <!-- Loading state -->
    <div v-else class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">{{ $t("admin.common.loading") }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { Field } from "vee-validate";
import Button from "~/components/custom/Button.vue";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import PublishSelect from "~/components/custom/PublishSelect.vue";
import { useMenu } from "~/composables/menu/useMenu";
import MenuManager from "~/components/menu/MenuManager.vue";
import { MENUINDEX } from "~/utils/constants";

const { isLoading, values, slug, positionOptions, setValues, handleUpdate, show } = useMenu(undefined, "update");

const isLoadingData = ref(true);
const id = Number(useRoute().params.id);
const menuItemsNested = ref<any[]>([]);

/**
 * Convert flat menu items (với parent_id) sang nested structure
 */
const convertFlatToNested = (flatItems: any[]): any[] => {
    if (!Array.isArray(flatItems) || flatItems.length === 0) {
        return [];
    }

    const itemMap = new Map<number, any>();
    const rootItems: any[] = [];

    flatItems.forEach((item) => {
        const nestedItem = {
            id: item.id,
            name: item.name || "",
            slug: item.slug || "",
            order: item.order || 0,
            publish: item.publish || 2,
            __localId: item.id || `n-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
            children: [],
        };

        itemMap.set(item.id, nestedItem);
    });

    flatItems.forEach((item) => {
        const nestedItem = itemMap.get(item.id);
        if (!nestedItem) return;

        const parentId = item.parent_id || item.parentId || 0;

        if (parentId === 0 || parentId === null) {
            rootItems.push(nestedItem);
        } else {
            const parent = itemMap.get(parentId);
            if (parent) {
                if (!Array.isArray(parent.children)) {
                    parent.children = [];
                }
                parent.children.push(nestedItem);
            } else {
                rootItems.push(nestedItem);
            }
        }
    });

    const sortByOrder = (items: any[]) => {
        items.sort((a, b) => (a.order || 0) - (b.order || 0));
        items.forEach((item) => {
            if (Array.isArray(item.children) && item.children.length > 0) {
                sortByOrder(item.children);
            }
        });
    };

    sortByOrder(rootItems);

    return rootItems;
};

const handleMenuItemsUpdate = (nestedData: any[]) => {
    menuItemsNested.value = nestedData;

    setValues({
        ...values,
        menuItems: nestedData,
    });
};

onMounted(async () => {
    if (!id || isNaN(id)) {
        navigateTo(MENUINDEX);
        return;
    }

    isLoadingData.value = true;

    try {
        const response = await show(id);

        if (!response?.data) {
            throw new Error("No data returned");
        }

        const menuData = response.data;
        const flatMenuItems = menuData.menuItems || [];
        const nestedMenuItems = convertFlatToNested(flatMenuItems);

        menuItemsNested.value = nestedMenuItems;

        const validPositions = ["header", "footer", "sidebar", "profile", "mobile"];
        const position = validPositions.includes(menuData.position as any) ? menuData.position : "header";

        setValues({
            name: menuData.name || "",
            slug: menuData.slug || null,
            position: position as "header" | "footer" | "sidebar" | "profile" | "mobile",
            publish: menuData.publish || 2,
            menuItems: nestedMenuItems,
        });
    } catch (error: any) {
        console.error("❌ Error loading menu:", error);
        navigateTo(MENUINDEX);
    } finally {
        isLoadingData.value = false;
    }
});

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>
