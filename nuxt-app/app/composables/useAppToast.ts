export const useAppToast = () => {
  return useNuxtApp().$toast as any;
};
