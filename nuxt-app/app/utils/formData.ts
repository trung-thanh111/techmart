export const formData = (payload: Record<string, any>): FormData => {
    const fd = new FormData();
    Object.entries(payload).forEach(([key, value]) => {
        if (value === undefined || value === null) return;
        if (value instanceof File || value instanceof Blob) {
            fd.append(key, value as Blob);
        } else if (Array.isArray(value)) {
            value.forEach((v) => fd.append(`${key}[]`, v as any));
        } else if (typeof value === 'object') {
            fd.append(key, JSON.stringify(value));
        } else {
            fd.append(key, String(value));
        }
    });
    return fd;
};


