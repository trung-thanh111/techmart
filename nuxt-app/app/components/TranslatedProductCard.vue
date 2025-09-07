<template>
  <div class="border rounded-lg p-4 shadow hover:shadow-lg transition-shadow">
    <div v-if="loading" class="animate-pulse">
      <div class="h-6 bg-gray-200 rounded mb-2"></div>
      <div class="h-4 bg-gray-200 rounded mb-2"></div>
      <div class="h-4 bg-gray-200 rounded w-1/2"></div>
    </div>
    
    <div v-else>
      <h3 class="font-semibold text-lg mb-2">{{ translatedProduct.name }}</h3>
      <p class="text-gray-600 mb-2">{{ translatedProduct.description }}</p>
      <p class="text-green-600 font-bold">{{ formatPrice(translatedProduct.price) }}</p>
      
      <!-- Translation indicator -->
      <div v-if="needsTranslation && isTranslated" class="mt-2 text-xs text-blue-500">
        <i class="fas fa-language mr-1"></i>
        Translated
      </div>
    </div>
  </div>
</template>

<script setup>
interface Props {
  product: {
    id: number
    name: string
    description: string
    price: number
  }
}

const props = defineProps<Props>()

const { 
  currentLanguage, 
  needsTranslation, 
  translateFields 
} = useTranslation()

const translatedProduct = ref(props.product)
const loading = ref(false)
const isTranslated = ref(false)

const translateProduct = async () => {
  if (!needsTranslation.value) {
    translatedProduct.value = props.product
    isTranslated.value = false
    return
  }

  loading.value = true
  try {
    const translated = await translateFields(props.product, ['name', 'description'])
    translatedProduct.value = translated
    isTranslated.value = true
  } catch (error) {
    console.error('Translation error:', error)
    translatedProduct.value = props.product
    isTranslated.value = false
  } finally {
    loading.value = false
  }
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
}

// Watch for language changes
watch(currentLanguage, translateProduct)

// Initial translation
onMounted(translateProduct)
</script>
