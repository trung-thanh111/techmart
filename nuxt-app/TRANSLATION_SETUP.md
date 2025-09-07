# Frontend Translation System Setup Guide

## Cấu hình Google Translate API cho Nuxt 4

### 1. Thêm vào file .env
```env
# Google Translate API Configuration
GOOGLE_TRANSLATE_API_KEY=your_google_translate_api_key_here
```

### 2. Lấy Google Translate API Key
1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Tạo project mới hoặc chọn project hiện có
3. Enable Google Translate API
4. Tạo API Key trong phần "Credentials"
5. Copy API Key và thêm vào file .env

### 3. Cách sử dụng

#### Sử dụng Composable cơ bản
```javascript
// Trong component Vue
const { 
  currentLanguage, 
  needsTranslation, 
  translateText, 
  translateObject,
  fetchWithTranslation 
} = useTranslation()

// Dịch text đơn lẻ
const translatedText = await translateText('Sản phẩm tốt')

// Dịch object data
const translatedData = await translateObject(productData)

// Gọi API và tự động dịch response
const products = await fetchWithTranslation('/api/v1/products')
```

#### Sử dụng API Translation Composable
```javascript
// Sử dụng các method API đã được wrap
const { apiGet, apiPost, apiPut, apiDelete } = useApiTranslation()

// Tất cả các method này sẽ tự động dịch response
const products = await apiGet('/api/v1/products')
const newProduct = await apiPost('/api/v1/products', productData)
const updatedProduct = await apiPut('/api/v1/products/1', productData)
await apiDelete('/api/v1/products/1')
```

#### Dịch specific fields
```javascript
const { translateFields } = useTranslation()

// Chỉ dịch các field cụ thể
const translatedProduct = await translateFields(product, ['name', 'description'])
```

### 4. Caching System
- **Tự động cache** bản dịch trong memory
- **Cache 30 ngày** cho mỗi bản dịch
- **Auto-clear cache** khi chuyển ngôn ngữ
- **Max 1000 entries** để tránh memory leak

### 5. Performance Features
- **Batch translation** cho multiple texts
- **Memory caching** để tránh gọi API nhiều lần
- **Error handling** và fallback về text gốc
- **Cache statistics** để monitor performance

### 6. Các tính năng chính

#### TranslationService
- `translate(text, targetLanguage)` - Dịch text đơn lẻ
- `translateArray(texts, targetLanguage)` - Dịch array texts
- `translateObject(obj, targetLanguage)` - Dịch object data
- `clearCache()` - Xóa cache
- `getCacheStats()` - Lấy thống kê cache

#### useTranslation Composable
- `translateText(text)` - Dịch text theo ngôn ngữ hiện tại
- `translateObject(obj)` - Dịch object theo ngôn ngữ hiện tại
- `translateApiResponse(response)` - Dịch API response
- `fetchWithTranslation(url, options)` - Gọi API và tự động dịch
- `translateFields(obj, fields)` - Dịch specific fields

#### useApiTranslation Composable
- `apiGet(url, options)` - GET request với auto-translation
- `apiPost(url, body, options)` - POST request với auto-translation
- `apiPut(url, body, options)` - PUT request với auto-translation
- `apiDelete(url, options)` - DELETE request với auto-translation
- `apiRequest(url, options)` - Custom request với auto-translation

### 7. Cấu trúc file
```
app/
├── services/
│   └── TranslationService.ts          # Core translation service
├── composables/
│   ├── useTranslation.ts              # Main translation composable
│   └── useApiTranslation.ts           # API translation composable
├── plugins/
│   └── translation.client.ts          # Global translation service
└── pages/
    └── example-translation.vue        # Example usage
```

### 8. Lưu ý quan trọng
- **Chỉ chạy trên client-side** (có `.client.ts` suffix)
- **Tự động detect ngôn ngữ** từ i18n locale
- **Fallback về tiếng Việt** nếu không dịch được
- **Error handling** đầy đủ với console logging
- **Memory management** với cache size limit

### 9. Testing
Truy cập `/example-translation` để test hệ thống translation với:
- Test dịch text đơn lẻ
- Test load products với auto-translation
- Test cache statistics
- Test chuyển đổi ngôn ngữ
