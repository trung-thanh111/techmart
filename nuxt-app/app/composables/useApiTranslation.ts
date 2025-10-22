/**
 * Composable for API calls with automatic translation
 */
export const useApiTranslation = () => {
  const { currentLanguage, needsTranslation, translateApiResponse } = useTranslation()

  /**
   * Make API request with automatic translation
   */
  const apiGet = async (url: string, options: any = {}) => {
    const response = await $fetch(url, {
      method: 'GET',
      ...options
    })
    
    if (needsTranslation.value) {
      return await translateApiResponse(response)
    }
    
    return response
  }

  /**
   * Make POST API request with automatic translation
   */
  const apiPost = async (url: string, body: any = {}, options: any = {}) => {
    const response = await $fetch(url, {
      method: 'POST',
      body,
      ...options
    })
    
    if (needsTranslation.value) {
      return await translateApiResponse(response)
    }
    
    return response
  }

  /**
   * Make PUT API request with automatic translation
   */
  const apiPut = async (url: string, body: any = {}, options: any = {}) => {
    const response = await $fetch(url, {
      method: 'PUT',
      body,
      ...options
    })
    
    if (needsTranslation.value) {
      return await translateApiResponse(response)
    }
    
    return response
  }

  /**
   * Make DELETE API request with automatic translation
   */
  const apiDelete = async (url: string, options: any = {}) => {
    const response = await $fetch(url, {
      method: 'DELETE',
      ...options
    })
    
    if (needsTranslation.value) {
      return await translateApiResponse(response)
    }
    
    return response
  }

  /**
   * Make API request with custom method and automatic translation
   */
  const apiRequest = async (url: string, options: any = {}) => {
    const response = await $fetch(url, options)
    
    if (needsTranslation.value) {
      return await translateApiResponse(response)
    }
    
    return response
  }

  return {
    apiGet,
    apiPost,
    apiPut,
    apiDelete,
    apiRequest
  }
}
