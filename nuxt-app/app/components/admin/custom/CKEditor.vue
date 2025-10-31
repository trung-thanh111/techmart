<template>
  <client-only>
    <ckeditor v-model="model" :editor="ClassicEditor" :config="editorConfig" />
  </client-only>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { Ckeditor } from "@ckeditor/ckeditor5-vue";
import "ckeditor5/ckeditor5.css";

import { ClassicEditor, Essentials, Paragraph, Heading, Bold, Italic, Strikethrough, Link, List, Code, CodeBlock, Alignment, Image, ImageToolbar, ImageCaption, ImageStyle, ImageUpload, BlockQuote, HorizontalLine } from "ckeditor5";

const props = defineProps<{ modelValue: string | null | undefined }>();

const emit = defineEmits<{ (e: "update:modelValue", value: string): void }>();

const editorConfig = computed(() => ({
  licenseKey: "GPL",
  language: "en",
  plugins: [Essentials, Paragraph, Heading, Bold, Italic, Strikethrough, Link, List, Code, CodeBlock, Alignment, Image, ImageToolbar, ImageCaption, ImageStyle, ImageUpload, BlockQuote, HorizontalLine],
  toolbar: ["heading", "|", "bold", "italic", "strikethrough", "link", "bulletedList", "numberedList", "|", "code", "codeBlock", "alignment", "insertImage", "blockQuote", "horizontalLine", "|", "undo", "redo"],
}));

// v-model proxy (two-way)
const model = computed<string>({
  get: () => (props.modelValue as string) ?? "",
  set: (val) => emit("update:modelValue", val ?? ""),
});
</script>
<style scoped>
.ck.ck-toolbar {
  border: none;
  border-bottom: 1px solid #e5e7eb !important;
  border-radius: 6px;
  background: #fff;
  box-shadow: none;
}
.ck.ck-toolbar__separator {
  display: inline-block !important;
  width: 1px !important;
  height: 20px !important;
  margin: 0 4px;
}

.ck.ck-dropdown__panel {
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
}
.ck.ck-button:focus,
.ck.ck-button.ck-on {
  box-shadow: none !important;
}
.ck.ck-button {
  border-radius: 4px;
  padding: 4px 6px;
}
.ck.ck-button:hover {
  background: #f3f4f6;
}
.ck.ck-list__item .ck-button:hover {
  background: #f3f4f6;
}
</style>
<style>
.ck-editor__editable_inline,
.ck-content {
  max-height: 500px !important;
  overflow-y: auto !important;
  height: auto !important;
  min-height: unset !important;
}
</style>
