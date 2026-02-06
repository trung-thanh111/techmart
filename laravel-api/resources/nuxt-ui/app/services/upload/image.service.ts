import type { UploadResponse } from "~/interfaces/upload/image.interface";
import { useApi } from "~/composables/useApi";
import type { ApiResponse } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    uploadTemp: `${API_VERSION.version1}/upload-temp`,
} as const;

export const uploadService = {
    /**
     * Upload temp files
     * @param files File[] | FileList
     * @param sessionId string
     */
    temp: async (files: File[] | FileList, sessionId: string): Promise<UploadResponse> => {
        const { privateFetch } = useApi();

        const fd = new FormData();
        Array.from(files).forEach((file, index) => {
            fd.append(`file[${index}]`, file);
        });
        fd.append("sessionId", sessionId);

        const res = await privateFetch<ApiResponse<UploadResponse>>(endpoint.uploadTemp, {
            method: "POST",
            body: fd,
        });
        return res?.data as UploadResponse;
    },

    /**
     * Generate unique session ID for upload
     */
    generateSessionId: (): string => {
        return `session_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    },

    /**
     * Validate file before upload
     * @param file File
     * @param maxSize number in bytes (default: 5MB)
     * @param allowedTypes string[] (default: image types)
     */
    validateFile: (file: File, maxSize: number = 5 * 1024 * 1024, allowedTypes: string[] = ["image/jpeg", "image/png", "image/webp", "image/jpg"]): { valid: boolean; error?: string } => {
        // Check file type
        if (!allowedTypes.includes(file.type)) {
            return {
                valid: false,
                error: `File ${file.name} không đúng định dạng. Chỉ chấp nhận: ${allowedTypes.map((t) => t.replace("image/", "").toUpperCase()).join(", ")}`,
            };
        }

        // Check file size
        if (file.size > maxSize) {
            return {
                valid: false,
                error: `File ${file.name} quá lớn. Kích thước tối đa: ${Math.round(maxSize / (1024 * 1024))}MB`,
            };
        }

        return { valid: true };
    },

    /**
     * Validate multiple files
     * @param files File[] | FileList
     * @param maxSize number in bytes
     * @param allowedTypes string[]
     */
    validateFiles: (files: File[] | FileList, maxSize: number = 5 * 1024 * 1024, allowedTypes: string[] = ["image/jpeg", "image/png", "image/webp", "image/jpg"]): { valid: boolean; errors: string[]; validFiles: File[] } => {
        const errors: string[] = [];
        const validFiles: File[] = [];

        Array.from(files).forEach((file) => {
            const validation = uploadService.validateFile(file, maxSize, allowedTypes);
            if (validation.valid) {
                validFiles.push(file);
            } else {
                errors.push(validation.error!);
            }
        });

        return {
            valid: errors.length === 0,
            errors,
            validFiles,
        };
    },
};
