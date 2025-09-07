export default defineNuxtPlugin(async () => {
  // Global translation service instance
  const { TranslationService } = await import('~/services/TranslationService')
  const translationService = new TranslationService()

  return {
    provide: {
      translation: translationService
    }
  }
})
