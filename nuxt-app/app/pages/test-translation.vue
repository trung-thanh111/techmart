<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Test Translation System</h1>
    
    <div class="mb-4">
      <button 
        @click="switchLanguage('vi')" 
        :class="currentLanguage === 'vi' ? 'bg-blue-500 text-white' : 'bg-gray-200'"
        class="px-4 py-2 rounded mr-2"
      >
        Tiếng Việt
      </button>
      <button 
        @click="switchLanguage('en')" 
        :class="currentLanguage === 'en' ? 'bg-blue-500 text-white' : 'bg-gray-200'"
        class="px-4 py-2 rounded"
      >
        English
      </button>
    </div>

    <div class="space-y-4">
      <div class="p-4 border rounded">
        <h3 class="font-semibold mb-2">Test 1: Simple Text Translation</h3>
        <p><strong>Original:</strong> {{ testText }}</p>
        <p><strong>Translated:</strong> {{ translatedText }}</p>
        <button 
          @click="translateTest" 
          :disabled="translating"
          class="px-4 py-2 bg-blue-500 text-white rounded disabled:opacity-50"
        >
          {{ translating ? 'Translating...' : 'Translate' }}
        </button>
      </div>

      <div class="p-4 border rounded">
        <h3 class="font-semibold mb-2">Test 2: Object Translation</h3>
        <pre class="bg-gray-100 p-2 rounded text-sm">{{ JSON.stringify(translatedObject, null, 2) }}</pre>
        <button 
          @click="translateObject" 
          :disabled="translating"
          class="px-4 py-2 bg-green-500 text-white rounded disabled:opacity-50 mt-2"
        >
          {{ translating ? 'Translating...' : 'Translate Object' }}
        </button>
      </div>

      <div class="p-4 border rounded">
        <h3 class="font-semibold mb-2">Test 3: Cache Stats</h3>
        <p>Cache size: {{ cacheStats.size }}</p>
        <button 
          @click="clearCache" 
          class="px-4 py-2 bg-red-500 text-white rounded"
        >
          Clear Cache
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const { 
  currentLanguage, 
  needsTranslation, 
  translateText, 
  translateObject: translateObjectData,
  clearCache, 
  getCacheStats 
} = useTranslation()
const { switchLanguage: switchI18n } = useLocale()

const testText = ref('Sản phẩm điện tử chất lượng cao')
const translatedText = ref('')
const translatedObject = ref({})
const translating = ref(false)
const cacheStats = ref({ size: 0, keys: [] })

const testObject = {
  name: 'Laptop Gaming',
  description: 'Máy tính xách tay chơi game hiệu năng cao',
  category: 'Điện tử'
}

const switchLanguage = async (lang) => {
  await switchI18n(lang)
  // Clear previous translations when switching language
  translatedText.value = ''
  translatedObject.value = {}
}

const translateTest = async () => {
  if (!testText.value) return
  
  translating.value = true
  try {
    translatedText.value = await translateText(testText.value)
  } catch (error) {
    console.error('Translation error:', error)
    translatedText.value = 'Translation failed'
  } finally {
    translating.value = false
  }
}

const translateObject = async () => {
  translating.value = true
  try {
    translatedObject.value = await translateObjectData(testObject)
  } catch (error) {
    console.error('Object translation error:', error)
    translatedObject.value = { error: 'Translation failed' }
  } finally {
    translating.value = false
  }
}

const updateCacheStats = () => {
  cacheStats.value = getCacheStats()
}

onMounted(() => {
  updateCacheStats()
})

// Watch for cache changes
watch(() => getCacheStats(), updateCacheStats, { deep: true })
</script>
