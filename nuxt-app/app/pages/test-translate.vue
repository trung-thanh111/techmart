<template>
  <div class="container mx-auto p-6 max-w-6xl">
    <h1 class="text-3xl font-bold mb-6 text-center">Test API Translate - Database Data</h1>
    
    <!-- API Key Status -->
    <div class="mb-6 p-4 rounded-lg" :class="apiKeyStatus ? 'bg-green-100 border border-green-300' : 'bg-red-100 border border-red-300'">
      <div class="flex items-center">
        <i :class="apiKeyStatus ? 'fas fa-check-circle text-green-600' : 'fas fa-exclamation-circle text-red-600'"></i>
        <span class="ml-2 font-medium">
          {{ apiKeyStatus ? 'API Key configured' : 'API Key not configured' }}
        </span>
      </div>
      <p class="text-sm mt-1 text-gray-600">
        {{ apiKeyStatus ? 'Ready to test translation' : 'Please add APYHUB_API_KEY to your .env file' }}
      </p>
    </div>

    <!-- Test Button -->
    <div class="text-center mb-6">
      <button 
        @click="runAllTests" 
        :disabled="!apiKeyStatus || isLoading"
        class="btn btn-primary text-lg px-8 py-3"
      >
        <i class="fas fa-play mr-2"></i>
        {{ isLoading ? 'Running Tests...' : 'Run All Translation Tests' }}
      </button>
    </div>

    <!-- Loading Indicator -->
    <div v-if="isLoading" class="text-center py-8">
      <div class="inline-flex items-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
        <span class="text-lg">Running translation tests...</span>
      </div>
    </div>

    <!-- Test Results -->
    <div v-if="testResults.length > 0" class="space-y-6">
      <div 
        v-for="(result, index) in testResults" 
        :key="index"
        class="bg-white border rounded-lg p-6 shadow-sm"
      >
        <h3 class="text-xl font-semibold mb-4 flex items-center">
          <i :class="result.type === 'success' ? 'fas fa-check-circle text-green-600' : 'fas fa-times-circle text-red-600'"></i>
          <span class="ml-2">{{ result.title }}</span>
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <h4 class="font-medium text-gray-700 mb-2">Original (Vietnamese)</h4>
            <div class="bg-gray-100 p-3 rounded text-sm">
              <pre>{{ JSON.stringify(result.original, null, 2) }}</pre>
            </div>
          </div>
          <div>
            <h4 class="font-medium text-gray-700 mb-2">Translated (English)</h4>
            <div class="bg-green-100 p-3 rounded text-sm">
              <pre>{{ JSON.stringify(result.translated, null, 2) }}</pre>
            </div>
          </div>
        </div>
        
        <div v-if="result.time" class="mt-3 text-sm text-gray-500">
          Translation time: {{ result.time }}ms
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { TranslationService } from '~/services/TranslationService'

// Page meta
definePageMeta({
  layout: 'default'
})

// Reactive data
const isLoading = ref(false)
const testResults = ref<any[]>([])
const apiKeyStatus = ref(false)

// Mock database data
const mockDatabaseData = {
  products: [
    {
      id: 1,
      name: 'iPhone 15 Pro Max',
      description: 'Điện thoại thông minh cao cấp với camera chuyên nghiệp',
      content: 'iPhone 15 Pro Max là flagship mới nhất của Apple với nhiều tính năng vượt trội. Camera 48MP với zoom quang học 5x, chip A17 Pro mạnh mẽ, màn hình Super Retina XDR 6.7 inch.',
      meta_title: 'iPhone 15 Pro Max - Điện thoại cao cấp chính hãng',
      meta_description: 'Mua iPhone 15 Pro Max chính hãng với giá tốt nhất tại TechMart. Camera 48MP, chip A17 Pro, màn hình 6.7 inch.',
      meta_keyword: 'iphone, điện thoại, apple, smartphone, camera 48mp',
      made_in: 'Trung Quốc'
    },
    {
      id: 2,
      name: 'Samsung Galaxy S24 Ultra',
      description: 'Điện thoại Android cao cấp với bút S Pen',
      content: 'Galaxy S24 Ultra mang đến trải nghiệm sáng tạo với S Pen tích hợp. Camera 200MP với zoom quang học 10x, chip Snapdragon 8 Gen 3, màn hình Dynamic AMOLED 2X 6.8 inch.',
      meta_title: 'Samsung Galaxy S24 Ultra - Android cao cấp với S Pen',
      meta_description: 'Galaxy S24 Ultra với S Pen và camera 200MP. Màn hình 6.8 inch, chip Snapdragon 8 Gen 3 mạnh mẽ.',
      meta_keyword: 'samsung, galaxy, android, s pen, camera 200mp',
      made_in: 'Hàn Quốc'
    }
  ],
  brands: [
    {
      id: 1,
      name: 'Apple',
      description: 'Tập đoàn công nghệ hàng đầu thế giới chuyên sản xuất điện thoại, máy tính và thiết bị điện tử',
      country: 'Mỹ'
    },
    {
      id: 2,
      name: 'Samsung',
      description: 'Nhà sản xuất điện tử lớn nhất Hàn Quốc với các sản phẩm từ điện thoại đến tủ lạnh',
      country: 'Hàn Quốc'
    }
  ],
  catalogues: [
    {
      id: 1,
      name: 'Điện thoại thông minh',
      description: 'Các dòng điện thoại smartphone cao cấp từ các thương hiệu nổi tiếng',
      meta_title: 'Điện thoại thông minh - Smartphone chính hãng',
      meta_description: 'Tổng hợp các dòng điện thoại thông minh tốt nhất với giá cả hợp lý'
    },
    {
      id: 2,
      name: 'Laptop và máy tính',
      description: 'Máy tính xách tay và desktop chuyên nghiệp cho công việc và giải trí',
      meta_title: 'Laptop và máy tính - Computer chuyên nghiệp',
      meta_description: 'Laptop gaming, văn phòng và máy tính để bàn với hiệu năng cao'
    }
  ],
  posts: [
    {
      id: 1,
      title: 'Hướng dẫn chọn mua điện thoại phù hợp',
      description: 'Những tiêu chí quan trọng khi chọn mua điện thoại thông minh',
      content: 'Việc chọn mua điện thoại thông minh phù hợp không chỉ dựa vào giá cả mà còn cần xem xét nhiều yếu tố khác như hiệu năng, camera, pin, thiết kế...',
      meta_title: 'Hướng dẫn chọn mua điện thoại phù hợp - TechMart',
      meta_description: 'Tư vấn chọn mua điện thoại thông minh phù hợp với nhu cầu và ngân sách',
      meta_keyword: 'hướng dẫn, chọn mua, điện thoại, smartphone'
    }
  ]
}

// Initialize
onMounted(() => {
  checkApiKey()
})

// Check if API key is configured
const checkApiKey = () => {
  const config = useRuntimeConfig()
  apiKeyStatus.value = !!config.public.apyHubApiKey
}

// Translation service
let translationService: TranslationService | null = null

// Initialize translation service
const initTranslationService = () => {
  if (!translationService) {
    translationService = new TranslationService()
  }
  return translationService
}

// Test single text translation
const testSingleTranslation = async () => {
  const service = initTranslationService()
  const text = 'Chào mừng bạn đến với TechMart - Cửa hàng công nghệ hàng đầu Việt Nam'
  
  const start = Date.now()
  const translated = await service.translate(text, 'en')
  const time = Date.now() - start
  
  return {
    type: 'success',
    title: 'Single Text Translation',
    original: { text },
    translated: { text: translated },
    time
  }
}

// Test batch translation
const testBatchTranslation = async () => {
  const service = initTranslationService()
  const texts = [
    'Điện thoại thông minh',
    'Laptop gaming',
    'Máy tính để bàn',
    'Phụ kiện điện tử',
    'Thiết bị âm thanh'
  ]
  
  const start = Date.now()
  const translated = await service.translateArray(texts, 'en')
  const time = Date.now() - start
  
  return {
    type: 'success',
    title: 'Batch Translation',
    original: { texts },
    translated: { texts: translated },
    time
  }
}

// Test product translation
const testProductTranslation = async () => {
  const service = initTranslationService()
  const product = mockDatabaseData.products[0]
  
  const start = Date.now()
  const translated = await service.translateObject(product, 'en')
  const time = Date.now() - start
  
  return {
    type: 'success',
    title: 'Product Translation',
    original: product,
    translated,
    time
  }
}

// Test brand translation
const testBrandTranslation = async () => {
  const service = initTranslationService()
  const brand = mockDatabaseData.brands[0]
  
  const start = Date.now()
  const translated = await service.translateObject(brand, 'en')
  const time = Date.now() - start
  
  return {
    type: 'success',
    title: 'Brand Translation',
    original: brand,
    translated,
    time
  }
}

// Test catalogue translation
const testCatalogueTranslation = async () => {
  const service = initTranslationService()
  const catalogue = mockDatabaseData.catalogues[0]
  
  const start = Date.now()
  const translated = await service.translateObject(catalogue, 'en')
  const time = Date.now() - start
  
  return {
    type: 'success',
    title: 'Catalogue Translation',
    original: catalogue,
    translated,
    time
  }
}

// Test post translation
const testPostTranslation = async () => {
  const service = initTranslationService()
  const post = mockDatabaseData.posts[0]
  
  const start = Date.now()
  const translated = await service.translateObject(post, 'en')
  const time = Date.now() - start
  
  return {
    type: 'success',
    title: 'Post Translation',
    original: post,
    translated,
    time
  }
}

// Test cache functionality
const testCache = async () => {
  const service = initTranslationService()
  const text = 'Test cache functionality with ApyHub API'
  
  // First translation (should call API)
  const start1 = Date.now()
  const result1 = await service.translate(text, 'en')
  const time1 = Date.now() - start1
  
  // Second translation (should use cache)
  const start2 = Date.now()
  const result2 = await service.translate(text, 'en')
  const time2 = Date.now() - start2
  
  return {
    type: 'success',
    title: 'Cache Test',
    original: { 
      text,
      first_call_time: time1,
      second_call_time: time2,
      cache_stats: service.getCacheStats()
    },
    translated: { 
      first_result: result1,
      second_result: result2,
      cache_working: time2 < time1
    },
    time: time1 + time2
  }
}

// Run all tests
const runAllTests = async () => {
  if (!apiKeyStatus.value) {
    alert('Please configure APYHUB_API_KEY in your .env file')
    return
  }
  
  isLoading.value = true
  testResults.value = []
  
  try {
    console.log('🚀 Starting Translation Tests with ApyHub API')
    
    // Run all tests
    const tests = [
      testSingleTranslation,
      testBatchTranslation,
      testProductTranslation,
      testBrandTranslation,
      testCatalogueTranslation,
      testPostTranslation,
      testCache
    ]
    
    for (const test of tests) {
      try {
        const result = await test()
        testResults.value.push(result)
        console.log(`✅ ${result.title} completed in ${result.time}ms`)
        
        // Add delay between tests to avoid rate limiting
        await new Promise(resolve => setTimeout(resolve, 1000))
      } catch (error) {
        console.error(`❌ Test failed:`, error)
        testResults.value.push({
          type: 'error',
          title: 'Test Error',
          original: { error: error.message },
          translated: null,
          time: 0
        })
      }
    }
    
    console.log('🎉 All tests completed!')
    
  } catch (error) {
    console.error('❌ Test suite failed:', error)
    testResults.value.push({
      type: 'error',
      title: 'Test Suite Error',
      original: { error: error.message },
      translated: null,
      time: 0
    })
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.btn {
  @apply px-6 py-3 rounded-lg font-medium transition-colors duration-200;
}

.btn-primary {
  @apply bg-blue-600 text-white hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed;
}
</style>
