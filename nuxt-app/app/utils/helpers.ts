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
export const errorToField = (
  setFieldError: SetFieldError,
  apiErrors: Record<string, string> | undefined | null,
  t: (key: string) => string,
  targetField: string = 'name',
  message: string = 'admin.validation.recordExists'
) => {
  if (!apiErrors) return;
  const resolveMessage = message;
  for (const [key] of Object.entries(apiErrors)) {
    if (key === 'slug') {
      setFieldError(targetField as any, t(resolveMessage));
    }
  }
};