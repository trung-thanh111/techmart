export default defineNuxtPlugin((nuxtApp) => {
  const cookie = useCookie('i18n_redirected')
  if (cookie.value && cookie.value !== 'vi') {
    cookie.value = 'vi'
  }
  const { $i18n } = nuxtApp as any
  if ($i18n?.setLocale && $i18n.locale.value !== 'vi') {
    $i18n.setLocale('vi')
  }
})
