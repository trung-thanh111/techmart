export const useImageUpload = () => {
    const previewUrl = ref<string | null>(null);
    const file = ref<File | null>(null);

    const setInitialPreview = (url?: string | null) => {
        previewUrl.value = url || null;
    };

    const compress = async (input: File, quality = 0.85, maxSize = 1600) => {
        try {
            const bmp = await createImageBitmap(input);
            const scale = Math.min(1, maxSize / Math.max(bmp.width, bmp.height));
            const canvas = document.createElement("canvas");
            canvas.width = Math.round(bmp.width * scale);
            canvas.height = Math.round(bmp.height * scale);
            const ctx = canvas.getContext("2d");
            if (!ctx) return input;
            ctx.drawImage(bmp, 0, 0, canvas.width, canvas.height);
            const blob = await new Promise<Blob | null>((resolve) => canvas.toBlob((b) => resolve(b), "image/jpeg", quality));
            return blob ? new File([blob], input.name.replace(/\.[^.]+$/, ".jpg"), { type: "image/jpeg" }) : input;
        } catch {
            return input;
        }
    };

    const onChange = async (e: Event) => {
        const input = e.target as HTMLInputElement;
        const f = input.files?.[0];
        if (!f) return;
        previewUrl.value = URL.createObjectURL(f);
        file.value = await compress(f);
    };

    return {
        previewUrl,
        file,
        onChange,
        setInitialPreview,
    };
};
