import { formatPrice, formatDate, truncateText } from "~/utils/helpers";

export const useUtils = () => {
  return {
    formatPrice,
    formatDate,
    truncateText,
  };
};
