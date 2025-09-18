import type { TempUploadResponse } from '~/interfaces/upload/image.interface';
import { useApi } from '~/composables/useApi';
import type { ApiResponse } from '~/composables/useApi';

const ENDPOINT = '/api/v1';

export const uploadService = {
    /**
     * Upload temp files
     * @param files File[] | FileList
     * @param sessionId string
     */
    temp: async (files: File[] | FileList, sessionId: string): Promise<TempUploadResponse> => {
        const { privateFetch } = useApi();

        const fd = new FormData();
        Array.from(files).forEach((file, index) => {
            fd.append(`file[${index}]`, file);
        });
        fd.append('sessionId', sessionId);

        const res = await privateFetch<ApiResponse<TempUploadResponse>>(`${ENDPOINT}/upload-temp`, {
            method: 'POST',
            body: fd,
        });
        return res?.data as TempUploadResponse;
    },
};
