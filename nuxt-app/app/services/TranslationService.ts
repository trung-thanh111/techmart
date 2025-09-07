interface TranslationCache {
  [key: string]: {
    text: string
    timestamp: number
    ttl: number
  }
}

interface ApyHubTranslateResponse {
  data: {
    translated_text: string
  }
  // Alternative response format
  translated_text?: string
  result?: string
}

export class TranslationService {
  private apiKey: string
  private baseUrl = 'https://api.apyhub.com/utility/sharpapi-translate-text'
  private cache: TranslationCache = {}
  private cacheTtl = 60 * 60 * 24 * 30 // 30 days in seconds
  private maxCacheSize = 1000

  constructor() {
    this.apiKey = useRuntimeConfig().public.apyHubApiKey as string
  }

  /**
   * Translate text from Vietnamese to target language
   */
  async translate(text: string, targetLanguage: string = 'en'): Promise<string> {
    if (!this.needsTranslation(targetLanguage) || !text?.trim()) {
      return text
    }

    const cacheKey = this.getCacheKey(text, targetLanguage)
    
    // Check cache first
    if (this.cache[cacheKey]) {
      const cached = this.cache[cacheKey]
      if (Date.now() - cached.timestamp < cached.ttl * 1000) {
        return cached.text
      } else {
        delete this.cache[cacheKey]
      }
    }

    try {
      console.log('🔄 Translating:', text, 'to', targetLanguage)
      console.log('🔑 API Key:', this.apiKey.substring(0, 10) + '...')
      
      const response = await $fetch<ApyHubTranslateResponse>(this.baseUrl, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.apiKey}`,
          'Content-Type': 'application/json'
        },
        body: {
          text: text,
          target_language: targetLanguage,
          source_language: 'vi'
        }
      })

      console.log('📥 API Response:', response)
      
      // Handle different response formats
      let translatedText = text
      if (response.data?.translated_text) {
        translatedText = response.data.translated_text
      } else if (response.translated_text) {
        translatedText = response.translated_text
      } else if (response.result) {
        translatedText = response.result
      } else {
        console.warn('⚠️ No translated text found in response, using original')
      }
      
      console.log('✅ Translation result:', translatedText)
      
      // Cache the result
      this.setCache(cacheKey, translatedText)
      
      return translatedText
    } catch (error) {
      console.error('❌ Translation error:', error)
      console.error('Error details:', JSON.stringify(error, null, 2))
      return text // Fallback to original text
    }
  }

  /**
   * Translate array of texts
   */
  async translateArray(texts: string[], targetLanguage: string = 'en'): Promise<string[]> {
    if (!this.needsTranslation(targetLanguage) || !texts.length) {
      return texts
    }

    const results: string[] = []
    const textsToTranslate: string[] = []
    const textToIndex: number[] = []

    // Prepare texts for batch translation
    texts.forEach((text, index) => {
      if (text?.trim()) {
        textsToTranslate.push(text)
        textToIndex.push(index)
      }
    })

    if (!textsToTranslate.length) {
      return texts
    }

    // Batch translate
    const translatedBatch = await this.batchTranslate(textsToTranslate, targetLanguage)

    // Map results back to original array
    texts.forEach((text, index) => {
      if (!text?.trim()) {
        results[index] = text
      } else {
        const batchIndex = textToIndex.indexOf(index)
        results[index] = translatedBatch[batchIndex] ?? text
      }
    })

    return results
  }

  /**
   * Translate object data recursively
   */
  async translateObject(obj: any, targetLanguage: string = 'en'): Promise<any> {
    if (!this.needsTranslation(targetLanguage) || !obj) {
      return obj
    }

    if (Array.isArray(obj)) {
      return Promise.all(obj.map(item => this.translateObject(item, targetLanguage)))
    }

    if (typeof obj === 'object' && obj !== null) {
      const translated: any = {}
      
      for (const [key, value] of Object.entries(obj)) {
        if (this.isTranslatableField(key, value)) {
          translated[key] = await this.translate(value as string, targetLanguage)
        } else if (typeof value === 'object') {
          translated[key] = await this.translateObject(value, targetLanguage)
        } else {
          translated[key] = value
        }
      }
      
      return translated
    }

    return obj
  }

  /**
   * Batch translate multiple texts
   */
  private async batchTranslate(texts: string[], targetLanguage: string): Promise<string[]> {
    const cacheKey = this.getBatchCacheKey(texts, targetLanguage)
    
    // Check cache first
    if (this.cache[cacheKey]) {
      const cached = this.cache[cacheKey]
      if (Date.now() - cached.timestamp < cached.ttl * 1000) {
        return JSON.parse(cached.text)
      } else {
        delete this.cache[cacheKey]
      }
    }

    try {
      // ApyHub doesn't support batch translation, so we'll translate one by one
      const translatedTexts: string[] = []
      
      for (const text of texts) {
        const response = await $fetch<ApyHubTranslateResponse>(this.baseUrl, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${this.apiKey}`,
            'Content-Type': 'application/json'
          },
          body: {
            text: text,
            target_language: targetLanguage,
            source_language: 'vi'
          }
        })
        
        // Handle different response formats
        let translatedText = text
        if (response.data?.translated_text) {
          translatedText = response.data.translated_text
        } else if (response.translated_text) {
          translatedText = response.translated_text
        } else if (response.result) {
          translatedText = response.result
        }
        
        translatedTexts.push(translatedText)
      }
      
      // Cache the result
      this.setCache(cacheKey, JSON.stringify(translatedTexts))
      
      return translatedTexts
    } catch (error) {
      console.error('Batch translation error:', error)
      return texts // Fallback to original texts
    }
  }

  /**
   * Check if translation is needed
   */
  private needsTranslation(targetLanguage: string): boolean {
    return targetLanguage !== 'vi' && !!this.apiKey
  }

  /**
   * Check if field should be translated
   */
  private isTranslatableField(key: string, value: any): boolean {
    const translatableFields = [
      'name', 'title', 'description', 'content', 'meta_title',
      'meta_description', 'meta_keyword', 'slug'
    ]
    
    return translatableFields.includes(key) && 
           typeof value === 'string' && 
           value.trim().length > 0
  }

  /**
   * Generate cache key for single text
   */
  private getCacheKey(text: string, targetLanguage: string): string {
    return `translation:${this.hashString(text + ':' + targetLanguage)}`
  }

  /**
   * Generate cache key for batch translation
   */
  private getBatchCacheKey(texts: string[], targetLanguage: string): string {
    return `translation_batch:${this.hashString(texts.join('|') + ':' + targetLanguage)}`
  }

  /**
   * Simple hash function
   */
  private hashString(str: string): string {
    let hash = 0
    for (let i = 0; i < str.length; i++) {
      const char = str.charCodeAt(i)
      hash = ((hash << 5) - hash) + char
      hash = hash & hash // Convert to 32-bit integer
    }
    return Math.abs(hash).toString(36)
  }

  /**
   * Set cache with size limit
   */
  private setCache(key: string, text: string): void {
    // Remove oldest entries if cache is full
    if (Object.keys(this.cache).length >= this.maxCacheSize) {
      const oldestKey = Object.keys(this.cache)[0]
      if (oldestKey) {
        delete this.cache[oldestKey]
      }
    }

    this.cache[key] = {
      text,
      timestamp: Date.now(),
      ttl: this.cacheTtl
    }
  }

  /**
   * Clear cache
   */
  clearCache(): void {
    this.cache = {}
  }

  /**
   * Get cache stats
   */
  getCacheStats(): { size: number, keys: string[] } {
    return {
      size: Object.keys(this.cache).length,
      keys: Object.keys(this.cache)
    }
  }
}
