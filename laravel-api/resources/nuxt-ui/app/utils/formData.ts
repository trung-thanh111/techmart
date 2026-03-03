export const formData = (payload: Record<string, any>): FormData => {
    const fd = new FormData();

    const appendRecursive = (data: any, rootKey: string) => {
        if (data === undefined || data === null) return;

        if (data instanceof File || data instanceof Blob) {
            fd.append(rootKey, data);
        } else if (Array.isArray(data)) {
            data.forEach((item, index) => {
                appendRecursive(item, `${rootKey}[${index}]`);
            });
        } else if (typeof data === "object" && !(data instanceof Date)) {
            Object.entries(data).forEach(([key, value]) => {
                appendRecursive(value, `${rootKey}[${key}]`);
            });
        } else if (typeof data === "boolean") {
            fd.append(rootKey, data ? "1" : "0");
        } else {
            fd.append(rootKey, String(data));
        }
    };

    Object.entries(payload).forEach(([key, value]) => {
        appendRecursive(value, key);
    });

    return fd;
};
