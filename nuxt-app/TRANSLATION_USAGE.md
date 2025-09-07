# Translation System Usage Guide

## Cách sử dụng hệ thống Translation

### 1. Cấu hình cơ bản
Thêm vào file `.env`:
```env
GOOGLE_TRANSLATE_API_KEY=your_api_key_here
```

### 2. Sử dụng trong Component

#### Cách 1: Sử dụng useTranslation composable
```vue
<template>
  <div>
    <h1>{{ translatedTitle }}</h1>
    <p>{{ translatedDescription }}</p>
  </div>
</template>

<script setup>
const { translateText, translateObject } = useTranslation()

const title = ref('Tiêu đề sản phẩm')
const description = ref('Mô tả sản phẩm')
const translatedTitle = ref('')
const translatedDescription = ref('')

// Dịch text đơn lẻ
const translateContent = async () => {
  translatedTitle.value = await translateText(title.value)
  translatedDescription.value = await translateText(description.value)
}

onMounted(translateContent)
</script>
```

#### Cách 2: Sử dụng useApiTranslation composable
```vue
<template>
  <div v-for="product in products" :key="product.id">
    <h3>{{ product.name }}</h3>
    <p>{{ product.description }}</p>
  </div>
</template>

<script setup>
const { apiGet } = useApiTranslation()

const products = ref([])

const loadProducts = async () => {
  // Tự động dịch response
  const response = await apiGet('/api/v1/products')
  products.value = response.data.data || []
}

onMounted(loadProducts)
</script>
```

#### Cách 3: Sử dụng TranslatedProductCard component
```vue
<template>
  <TranslatedProductCard 
    v-for="product in products" 
    :key="product.id"
    :product="product" 
  />
</template>

<script setup>
const products = ref([
  {
    id: 1,
    name: 'Sản phẩm A',
    description: 'Mô tả sản phẩm A',
    price: 100000
  }
])
</script>
```

### 3. Các function chính

#### useTranslation()
- `translateText(text)` - Dịch text đơn lẻ
- `translateObject(obj)` - Dịch object data
- `translateApiResponse(response)` - Dịch API response
- `translateFields(obj, fields)` - Dịch specific fields
- `clearCache()` - Xóa cache
- `getCacheStats()` - Lấy thống kê cache

#### useApiTranslation()
- `apiGet(url, options)` - GET request với auto-translation
- `apiPost(url, body, options)` - POST request với auto-translation
- `apiPut(url, body, options)` - PUT request với auto-translation
- `apiDelete(url, options)` - DELETE request với auto-translation

### 4. Lưu ý quan trọng

1. **Chỉ chạy trên client-side** - Tất cả translation chỉ hoạt động trong browser
2. **Tự động detect ngôn ngữ** - Dựa vào i18n locale hiện tại
3. **Caching thông minh** - Tự động cache bản dịch để tăng performance
4. **Error handling** - Tự động fallback về text gốc nếu dịch lỗi
5. **Memory management** - Tự động xóa cache cũ khi đầy

### 5. Test hệ thống

Truy cập `/example-translation` để test:
- Test dịch text đơn lẻ
- Test load products với auto-translation
- Test cache statistics
- Test chuyển đổi ngôn ngữ

### 6. Performance Tips

1. **Sử dụng batch translation** cho multiple texts
2. **Cache được tự động quản lý** - không cần lo lắng
3. **Chỉ dịch khi cần thiết** - tự động skip nếu đang dùng tiếng Việt
4. **Error recovery** - hệ thống tự động fallback
