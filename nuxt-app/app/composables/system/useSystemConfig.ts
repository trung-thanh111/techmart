import { computed } from "vue";
import { systemService } from "~/services/system/system.service";
import type { ISystemPublicConfigs } from "~/interfaces/system/system.interface";

export const useSystemConfig = () => {
  const configs = useState<ISystemPublicConfigs | null>("system_configs", () => null);
  const loading = useState<boolean>("system_configs_loading", () => false);

  const fetchConfigs = async (force = false): Promise<ISystemPublicConfigs | null> => {
    if (configs.value && !force) return configs.value;
    try {
      loading.value = true;
      const res = await systemService.getPublicConfigs();
      if (res?.data) {
        configs.value = res.data;
      }
    } catch (err) {
      console.error("Failed to load system configs:", err);
    } finally {
      loading.value = false;
    }
    return configs.value;
  };

  const general = computed(() => configs.value?.general || {});
  const contact = computed(() => configs.value?.contact || {});
  const seo = computed(() => configs.value?.seo || {});
  const social = computed(() => configs.value?.social || {});
  const script = computed(() => configs.value?.script || {});

  const companyName = computed(() => general.value.company_name || "");
  const shortName = computed(() => general.value.short_name || "");
  const slogan = computed(() => general.value.slogan || "");
  const logo = computed(() => general.value.logo || "");
  const favicon = computed(() => general.value.favicon || "");
  const copyright = computed(() => general.value.copyright || "");
  const taxCode = computed(() => general.value.tax_code || "");
  const workingHours = computed(() => general.value.working_hours || "");
  const intro = computed(() => general.value.intro || "");

  const hotline = computed(() => contact.value.hotline || "");
  const hotlineSecondary = computed(() => contact.value.hotline_secondary || "");
  const complaintsHotline = computed(() => contact.value.complaints_hotline || "");
  const email = computed(() => contact.value.email || "");
  const emailComplaint = computed(() => contact.value.email_complaint || "");
  const address = computed(() => contact.value.address || "");
  const mapIframe = computed(() => contact.value.map_iframe || "");

  const facebook = computed(() => social.value.facebook || "");
  const youtube = computed(() => social.value.youtube || "");
  const tiktok = computed(() => social.value.tiktok || "");
  const zalo = computed(() => social.value.zalo || "");
  const messenger = computed(() => social.value.messenger || "");
  const instagram = computed(() => social.value.instagram || "");

  const headScript = computed(() => script.value.head_script || "");
  const bodyScript = computed(() => script.value.body_script || "");

  return {
    configs,
    loading,
    fetchConfigs,
    general,
    contact,
    seo,
    social,
    script,
    companyName,
    shortName,
    slogan,
    logo,
    favicon,
    copyright,
    taxCode,
    workingHours,
    intro,
    hotline,
    hotlineSecondary,
    complaintsHotline,
    email,
    emailComplaint,
    address,
    mapIframe,
    facebook,
    youtube,
    tiktok,
    zalo,
    messenger,
    instagram,
    headScript,
    bodyScript,
  };
};
