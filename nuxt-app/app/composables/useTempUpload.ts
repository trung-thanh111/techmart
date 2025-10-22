import { uploadService } from "~/services/upload/image.service";
import type { UploadedFile } from "~/interfaces/upload/image.interface";

export const useTempUpload = () => {
  const sessionId = ref<string>(generateSessionId());
  const isUploading = ref(false);
  const uploadedFiles = ref<UploadedFile[]>([]);

  const resetSession = () => {
    sessionId.value = generateSessionId();
    uploadedFiles.value = [];
  };

  const upload = async (files: File[] | FileList) => {
    if (!files || (Array.isArray(files) && files.length === 0)) return [] as UploadedFile[];
    isUploading.value = true;
    try {
      const res = await uploadService.temp(files, sessionId.value);
      // server returns path without leading storage/, ensure strip just in case
      const normalized = (res.files || []).map((f) => ({
        url: f.url,
        path: stripStoragePrefix(f.path),
      }));
      uploadedFiles.value = [...uploadedFiles.value, ...normalized];
      return normalized;
    } finally {
      isUploading.value = false;
    }
  };

  return {
    sessionId,
    isUploading,
    uploadedFiles,
    upload,
    resetSession,
  };
};

function generateSessionId(): string {
  // session_upload_YYYYMMDDHHmmss_random
  const now = new Date();
  const pad = (n: number) => `${n}`.padStart(2, "0");
  const stamp = `${now.getFullYear()}${pad(now.getMonth() + 1)}${pad(now.getDate())}${pad(now.getHours())}${pad(now.getMinutes())}${pad(now.getSeconds())}`;
  const rand = Math.random().toString(36).slice(2, 8);
  return `session_${stamp}_${rand}`;
}

function stripStoragePrefix(p: string): string {
  if (!p) return p;
  return p.replace(/^storage\//, "");
}


