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
  const logo = obj?.logo;
  const image = obj?.image;
  const hasPath = (val: any) => typeof val === "string" && val.trim().length > 0;

  if (logo && hasPath(logo?.path)) {
    return logo?.url || logo.path;
  }
  if (image && hasPath(image?.path)) {
    return image?.url || image.path;
  }
  return fallback;
};

export const formatPhoneVN = (phone?: string | null): string => {
  if (!phone) return "";
  const cleaned = String(phone).trim();
  return `(+84) ${cleaned.replace(/^0/, "")}`;
};

/**
 * Generate SKU
 * @param isVariant - true nếu là phiên bản, false nếu là sản phẩm gốc
 * @returns Generated SKU string
 */
export const generateSKU = (): string => {
  const prefix = "PD-";
  const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  let randomLetters = "";
  for (let i = 0; i < 6; i++) {
    randomLetters += letters.charAt(Math.floor(Math.random() * letters.length));
  }
  const randomNumbers = Math.floor(1000 + Math.random() * 9000).toString();
  return prefix + randomLetters + randomNumbers;
};
