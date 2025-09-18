export interface UploadedFile {
    url: string;
    path: string;
}

export interface TempUploadResponse {
    id: string;
    files: UploadedFile[];
}
