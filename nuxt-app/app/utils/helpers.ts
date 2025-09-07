export const slugify = (input: string): string => {
  return input
    .toLowerCase()
    .normalize("NFD")
    .replace(/\p{Diacritic}/gu, "")
    .replace(/[^a-z0-9\s-]/g, "")
    .trim()
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-");
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
