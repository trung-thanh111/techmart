import { IMAGEDEFAULT } from "~/utils/constants";

/**
 * Status CSS class mapping
 */
export const STATUS_CLASS_MAP: Record<string, string> = {
  pending: "bg-yellow-100 text-yellow-700",
  confirmed: "bg-blue-100 text-blue-700",
  processing: "bg-purple-100 text-purple-700",
  shipped: "bg-cyan-100 text-cyan-700",
  delivered: "bg-green-100 text-green-700",
  cancelled: "bg-red-100 text-red-700",
  refunded: "bg-orange-100 text-orange-700",
  failed: "bg-red-100 text-red-700",
};

/**
 * Get CSS class for order status badge
 */
export const getStatusClass = (status?: string): string => {
  return STATUS_CLASS_MAP[status || ""] || "bg-gray-100 text-gray-700";
};

/**
 * Extract string value from attribute value (handles object/primitive)
 */
export const getAttributeValueString = (val: any): string => {
  if (val === null || val === undefined) return "";
  if (typeof val === "string") return val;
  if (typeof val === "number" || typeof val === "boolean") return String(val);
  if (typeof val === "object" && val.name) return val.name;
  if (typeof val === "object" && val.value) return val.value;
  if (typeof val === "object" && val.label) return val.label;
  return "";
};

/**
 * Format product attributes for display
 */
export const formatItemAttributes = (attrs: any): string => {
  if (!attrs) return "";
  let parsed = attrs;
  if (typeof attrs === "string") {
    try {
      parsed = JSON.parse(attrs);
    } catch {
      return attrs;
    }
  }
  if (Array.isArray(parsed)) {
    return parsed
      .map((a) => {
        const name = a.name || a.attribute_name || "";
        const value = getAttributeValueString(a.value || a.attribute_value);
        return name && value ? `${name}: ${value}` : "";
      })
      .filter(Boolean)
      .join(" | ");
  }
  if (typeof parsed === "object" && parsed !== null) {
    return Object.entries(parsed)
      .map(([k, v]) => {
        const value = getAttributeValueString(v);
        return value ? `${k}: ${value}` : "";
      })
      .filter(Boolean)
      .join(" | ");
  }
  return "";
};

/**
 * Get product image URL with fallback
 */
export const getProductImage = (img?: string | { path?: string; url?: string }): string => {
  if (!img) return IMAGEDEFAULT;
  if (typeof img === "object" && img.url) return img.url;
  if (typeof img === "string" && (img.startsWith("http://") || img.startsWith("https://"))) return img;
  return IMAGEDEFAULT;
};

/**
 * Handle image error - set to default image
 */
export const handleImageError = (e: Event): void => {
  (e.target as HTMLImageElement).src = IMAGEDEFAULT;
};

/**
 * Format date for display (full format with time)
 */
export const formatFullDate = (dateStr?: string): string => {
  if (!dateStr) return "";
  const date = new Date(dateStr);
  return date.toLocaleDateString("vi-VN", { day: "2-digit", month: "2-digit", year: "numeric", hour: "2-digit", minute: "2-digit" });
};
