<template>
    <div class="ckeditor-custom-container">
        <client-only>
            <ckeditor v-model="model" :editor="ClassicEditor" :config="editorConfig" />
        </client-only>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { Ckeditor } from "@ckeditor/ckeditor5-vue";
import "ckeditor5/ckeditor5.css";

import { ClassicEditor, Essentials, Paragraph, Heading, Bold, Italic, Strikethrough, Link, List, Code, CodeBlock, Alignment, Image, ImageToolbar, ImageCaption, ImageStyle, ImageUpload, BlockQuote, HorizontalLine } from "ckeditor5";

const props = defineProps<{
    modelValue: string | null | undefined;
    placeholder?: string | null;
    height?: string | number;
}>();

const emit = defineEmits<{ (e: "update:modelValue", value: string): void }>();

const editorHeight = computed(() => {
    if (!props.height) return "160px";
    if (typeof props.height === "number") return `${props.height}px`;
    if (props.height === "h-16") return "100px";
    if (props.height === "h-32") return "160px";
    if (/^\d+$/.test(props.height)) return `${props.height}px`;
    return props.height;
});

const editorConfig = computed(() => ({
    licenseKey: "GPL",
    language: "en",
    placeholder: props.placeholder ?? "",
    plugins: [Essentials, Paragraph, Heading, Bold, Italic, Strikethrough, Link, List, Code, CodeBlock, Alignment, Image, ImageToolbar, ImageCaption, ImageStyle, ImageUpload, BlockQuote, HorizontalLine],
    toolbar: ["heading", "|", "bold", "italic", "strikethrough", "link", "bulletedList", "numberedList", "|", "code", "codeBlock", "alignment", "insertImage", "blockQuote", "horizontalLine", "|", "undo", "redo"],
}));

const model = computed<string>({
    get: () => (props.modelValue as string) ?? "",
    set: (val) => emit("update:modelValue", val ?? ""),
});
</script>

<style scoped>
:deep(.ck.ck-toolbar) {
    border: none !important;
    border-bottom: 1px solid #e5e7eb !important;
    border-radius: 6px 6px 0 0 !important;
    background: #fff;
    box-shadow: none;
}

:deep(.ck.ck-editor__main > .ck-editor__editable) {
    border: none !important;
    border-radius: 0 0 6px 6px !important;
}

:deep(.ck.ck-editor__top .ck-sticky-panel__content) {
    border-radius: 6px 6px 0 0 !important;
    border: 1px solid #e5e7eb !important;
}

:deep(.ck.ck-editor__main) {
    border: 1px solid #e5e7eb !important;
    border-top: none !important;
    border-radius: 0 0 6px 6px !important;
}

:deep(.ck-editor) {
    height: auto !important;
}

:deep(.ck-editor__main) {
    height: auto !important;
}

:deep(.ck-editor__editable_inline),
:deep(.ck-content) {
    min-height: v-bind(editorHeight) !important;
    height: auto !important;
    max-height: 1500px !important;
    overflow-y: auto !important;
    resize: vertical !important;
}

:deep(.ck.ck-dropdown__panel) {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
}

:deep(.ck.ck-button) {
    border-radius: 4px;
    padding: 4px 6px;
}

:deep(.ck.ck-button:hover) {
    background: #f3f4f6;
}

:deep(.ck.ck-button:focus),
:deep(.ck.ck-button.ck-on) {
    box-shadow: none !important;
}
</style>
