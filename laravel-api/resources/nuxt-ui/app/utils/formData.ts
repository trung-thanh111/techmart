export const formData = (payload: Record<string, any>): FormData => {
    const fd = new FormData();
    Object.entries(payload).forEach(([key, value]) => {
        if (value === undefined || value === null) return;
        if (value instanceof File || value instanceof Blob) {
            fd.append(key, value);
        } else if (typeof value === "boolean") {
            fd.append(key, value ? "1" : "0");
        } else if (Array.isArray(value)) {
            if (value.every((v) => typeof v !== "object")) {
                value.forEach((v) => fd.append(`${key}[]`, String(v)));
            } else {
                fd.append(key, JSON.stringify(value));
            }
        } else if (typeof value === "object") {
            fd.append(key, JSON.stringify(value));
        } else {
            fd.append(key, String(value));
        }
    });
    return fd;
};
