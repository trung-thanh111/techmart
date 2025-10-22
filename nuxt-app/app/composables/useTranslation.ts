import { TranslationService } from '~/services/TranslationService'

export const useTranslation = () => {
  const { locale } = useI18n()
  const translationService = new TranslationService()

  /**
   * Get current language code
   */
  const currentLanguage = computed(() => {
    return locale.value as 'vi' | 'en'
  })

  /**
   * Check if translation is needed
   */
  const needsTranslation = computed(() => {
    return currentLanguage.value !== 'vi'
  })

  /**
   * Translate text
   */
  const translateText = async (text: string): Promise<string> => {
    if (!needsTranslation.value || !text?.trim()) {
      return text
    }
    return await translationService.translate(text, currentLanguage.value)
  }

  /**
   * Translate array of texts
   */
  const translateArray = async (texts: string[]): Promise<string[]> => {
    if (!needsTranslation.value || !texts.length) {
      return texts
    }
    return await translationService.translateArray(texts, currentLanguage.value)
  }

  /**
   * Translate object data
   */
  const translateObject = async (obj: any): Promise<any> => {
    if (!needsTranslation.value || !obj) {
      return obj
    }
    return await translationService.translateObject(obj, currentLanguage.value)
  }

  /**
   * Translate API response data
   */
  const translateApiResponse = async (response: any): Promise<any> => {
    if (!needsTranslation.value || !response) {
      return response
    }

    // Handle different response structures
    if (response.data) {
      if (Array.isArray(response.data)) {
        // Handle array of items
        response.data = await translateObject(response.data)
      } else if (response.data.data && Array.isArray(response.data.data)) {
        // Handle paginated data
        response.data.data = await translateObject(response.data.data)
      } else {
        // Handle single item
        response.data = await translateObject(response.data)
      }
    } else if (Array.isArray(response)) {
      // Handle direct array response
      response = await translateObject(response)
    } else {
      // Handle single object response
      response = await translateObject(response)
    }

    return response
  }

  /**
   * Make API request and auto-translate response
   */
  const fetchWithTranslation = async (url: string, options: any = {}) => {
    try {
      const response = await $fetch(url, options)
      return await translateApiResponse(response)
    } catch (error) {
      console.error('API request failed:', error)
      throw error
    }
  }

  /**
   * Translate specific fields of an object
   */
  const translateFields = async (obj: any, fields: string[]): Promise<any> => {
    if (!needsTranslation.value || !obj) {
      return obj
    }

    const translated = { ...obj }
    
    for (const field of fields) {
      if (obj[field] && typeof obj[field] === 'string') {
        translated[field] = await translateText(obj[field])
      }
    }

    return translated
  }

  /**
   * Clear translation cache
   */
  const clearCache = () => {
    translationService.clearCache()
  }

  /**
   * Get cache statistics
   */
  const getCacheStats = () => {
    return translationService.getCacheStats()
  }

  /**
   * Watch for language changes and clear cache
   */
  watch(currentLanguage, () => {
    // Clear cache when language changes to ensure fresh translations
    clearCache()
  })

  return {
    currentLanguage,
    needsTranslation,
    translateText,
    translateArray,
    translateObject,
    translateApiResponse,
    translateFields,
    fetchWithTranslation,
    clearCache,
    getCacheStats
  }
}
