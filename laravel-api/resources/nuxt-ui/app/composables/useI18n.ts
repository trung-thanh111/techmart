import { useI18n, useSwitchLocalePath } from "#imports";

export const useLocale = () => {
    const { locale, setLocale, t, locales } = useI18n();
    const switchLocalePath = useSwitchLocalePath();

    const localeCookie = useCookie("i18n_redirected", {
        default: () => process.env.APP_LOCALE || "vi",
        sameSite: "lax",
        secure: false,
        maxAge: 60 * 60 * 24 * 365, // 1 năm
    });

    const localesWithFlags = computed(() =>
        locales.value.map((filter) => ({
            ...filter,
            flag: filter.code === "vi" ? "/images/flag-vi.jpg" : "/images/flag-en.png",
        })),
    );

    const currentLocale = computed(() => localesWithFlags.value.find((l) => l.code === (locale.value as "vi" | "en")) || localesWithFlags.value[0]);

    const switchLanguage = async (langCode: "vi" | "en") => {
        if (langCode === locale.value) return;
        localeCookie.value = langCode;
        await setLocale(langCode);
        const targetPath = switchLocalePath(langCode);
        if (targetPath) {
            await navigateTo(targetPath, { replace: true });
        }
    };

    onMounted(() => {
        if (localeCookie.value && localeCookie.value !== locale.value) {
            setLocale(localeCookie.value as "vi" | "en");
        }
    });

    return {
        locale,
        locales: localesWithFlags,
        currentLocale,
        switchLanguage,
        t,
    };
};
