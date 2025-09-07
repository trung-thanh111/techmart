# ApyHub Translation API Setup

## Cấu hình API Key

1. Truy cập [ApyHub Translate API](https://apyhub.com/utility/sharpapi-translate-text)
2. Đăng ký tài khoản và lấy API Key
3. Thêm API Key vào file `.env`:

```env
# ApyHub API Key for translation service
APYHUB_API_KEY=your_apyhub_api_key_here
```

## Thay đổi đã thực hiện

### 1. TranslationService.ts
- Đổi API endpoint từ Google Translate sang ApyHub
- Cập nhật request format để sử dụng Bearer token authentication
- Cập nhật response handling cho format mới của ApyHub
- Batch translation được xử lý bằng cách gọi API từng text một (ApyHub không hỗ trợ batch)

### 2. nuxt.config.ts
- Thay đổi `googleTranslateApiKey` thành `apyHubApiKey`
- Cập nhật environment variable từ `GOOGLE_TRANSLATE_API_KEY` thành `APYHUB_API_KEY`

## Cách sử dụng

```typescript
import { TranslationService } from '~/services/TranslationService'

const translationService = new TranslationService()

// Dịch text đơn
const translatedText = await translationService.translate('Xin chào', 'en')

// Dịch array
const translatedTexts = await translationService.translateArray(['Xin chào', 'Cảm ơn'], 'en')

// Dịch object
const translatedObject = await translationService.translateObject({
  name: 'Tên sản phẩm',
  description: 'Mô tả sản phẩm'
}, 'en')
```

## Lưu ý

- API Key cần được cấu hình trong file `.env`
- ApyHub sử dụng Bearer token authentication
- Batch translation sẽ gọi API từng text một, có thể chậm hơn so với Google Translate
- Cache vẫn hoạt động bình thường để tối ưu performance
