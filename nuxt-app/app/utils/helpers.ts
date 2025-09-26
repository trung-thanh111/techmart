export const slugify = (input: string): string => {
  if (!input) return "";
  return input
    .toString()
    .trim()
    .replace(/Đ/g, "D")
    .replace(/đ/g, "d")
    .normalize("NFD")
    .replace(/\p{Diacritic}/gu, "")
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, "")
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-")
    .replace(/^-+|-+$/g, "");
};

export const formatPrice = (price: number): string => {
  return price.toLocaleString("vi-VN") + " ₫";
};

export const formatDate = (date: Date): string => {
  return date.toLocaleDateString("vi-VN");
};

export const truncateText = (text: string, length: number): string => {
  return text.length > length ? text.substring(0, length) + "..." : text;
};

export const closeModal = (modalId: string) => {
  const el = document.getElementById(modalId);
  const bs = (window as any)?.bootstrap;
  if (!el || !bs?.Modal) return;
  const instance = bs.Modal.getInstance(el) || new bs.Modal(el);
  instance.hide();
};

export type SetFieldError = (field: any, message?: any) => void;
// Map lỗi slug từ backend thành lỗi hiển thị dưới field target (name/title)
// Cách dùng: applySlugError(setFieldError, error?.data?.errors, 'title')
export const errorToField = (setFieldError: SetFieldError, apiErrors: Record<string, string> | undefined | null, t: (key: string) => string, targetField: string = "name", message: string = "admin.validation.recordExists") => {
  if (!apiErrors) return;
  const resolveMessage = message;
  for (const [key] of Object.entries(apiErrors)) {
    if (key === "slug") {
      setFieldError(targetField as any, t(resolveMessage));
    }
  }
};

// Build relation filters (dynamic) for HasQuery.
// buildRelationParams({ post_catalogues: 1, post_tags: [2,4] }) →
// { post_catalogues: '{"post_catalogues.id":{"eq":1}}', post_tags: '{"post_tags.id":{"in":"2,4"}}' }
export function buildRelationParams(relationToIds: Record<string, number | number[] | null | undefined>, fieldName: string = "id"): Record<string, string> {
  const result: Record<string, string> = {};
  for (const relationKey of Object.keys(relationToIds)) {
    const value = relationToIds[relationKey];
    if (!value || (Array.isArray(value) && value.length === 0)) continue;
    const op = Array.isArray(value) ? { in: value.join(",") } : { eq: value };
    const payload = { [`${relationKey}.${fieldName}`]: op } as Record<string, any>;
    result[relationKey] = JSON.stringify(payload);
  }
  return result;
}

export const resolveImageUrl = (input?: any, fallback: string = IMAGEDEFAULT): string => {
  const obj = input || {};
  return obj?.logo?.url || obj?.logo?.path || obj?.image?.url || obj?.image?.path || fallback;
};

export const formatPhoneVN = (phone?: string | null): string => {
  if (!phone) return "";
  const cleaned = String(phone).trim();
  return `(+84) ${cleaned.replace(/^0/, "")}`;
};
