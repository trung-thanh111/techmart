<template>
    <div v-if="!editorReady" class="h-[300px] bg-gray-100 animate-pulse rounded flex items-center justify-center">
        <span class="text-gray-500">Đang tải editor...</span>
    </div>
    <component v-else :is="editorComponent" v-model="modelValue" :editor="Editor" :config="mergedConfig" />
</template>

<script setup lang="ts">
import { ref, shallowRef, onMounted, computed } from "vue";

const props = defineProps<{
    modelValue: string;
    config?: Record<string, any>;
}>();

const emit = defineEmits(["update:modelValue"]);

const modelValue = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

const editorReady = ref(false);
const Editor = shallowRef<any>(null);
const editorComponent = shallowRef<any>(null);

const mergedConfig = computed(() => ({
    toolbar: ["heading", "|", "bold", "italic", "link", "bulletedList", "numberedList", "|", "blockQuote", "insertTable", "undo", "redo"],
    ...props.config,
}));

onMounted(async () => {
    // Dynamic import - chỉ load khi component mount
    const [{ ClassicEditor }, { Ckeditor }] = await Promise.all([import("ckeditor5"), import("@ckeditor/ckeditor5-vue")]);

    Editor.value = ClassicEditor;
    editorComponent.value = Ckeditor;
    editorReady.value = true;
});
</script>
