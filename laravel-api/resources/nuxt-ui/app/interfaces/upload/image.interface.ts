export interface AlbumItem {
    path: string;
}

export interface ImageItem {
    id: string;
    url: string;
    path: string;
    file?: File;
}

export interface UploadResponseFile {
    url: string;
    path: string;
}

export interface UploadResponse {
    id: string;
    files: UploadResponseFile[];
}
