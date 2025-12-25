# Promotion API - Postman Samples

## 📋 Mô tả

Thư mục này chứa các file JSON mẫu để test API Promotion module với Postman.

## 📁 Files

### 1. **Promotion-API-Collection.postman_collection.json**
Collection Postman hoàn chỉnh chứa tất cả API endpoints và data mẫu.

**Cách import:**
1. Mở Postman
2. Click "Import" → "File"
3. Chọn file `Promotion-API-Collection.postman_collection.json`
4. Collection sẽ được import với tất cả requests

### 2. **Individual JSON files** (cho từng loại promotion)
- `flash-sale-postman.json` - Flash Sale giảm 20% điện thoại
- `buy-x-get-y-postman.json` - Buy 2 Get 1 áo thun
- `tiered-discount-postman.json` - Giảm giá theo bậc
- `free-shipping-postman.json` - Miễn phí vận chuyển

**Cách sử dụng:**
1. Copy nội dung file JSON
2. Paste vào Postman request body (raw JSON)
3. Send request

## 🔧 Setup

### 1. **Environment Variables**
Trong Postman, tạo environment với các biến:

```json
{
  "base_url": "http://localhost:8000",
  "auth_token": "your_jwt_token_here",
  "promotion_id": "1"
}
```

### 2. **Authentication**
1. Đầu tiên, login để lấy JWT token:
   ```
   POST {{base_url}}/api/v1/auth/login
   Body: { "email": "...", "password": "..." }
   ```
2. Copy token và set vào `auth_token` variable trong environment

## 📝 Test Cases

### 1. **Flash Sale** (Giảm 20% điện thoại)
- **Type:** `flash_sale`
- **Rules:** Đơn tối thiểu 500k + khung giờ 9h-12h
- **Actions:** Giảm 20% trên tổng đơn hàng, max 500k
- **Targets:** Áp dụng cho category "Điện thoại", loại trừ iPhone 15 Pro Max

### 2. **Buy X Get Y** (Mua 2 tặng 1 áo thun)
- **Type:** `bxgy`
- **Rules:** Mua tối thiểu 2 sản phẩm trong category áo thun
- **Actions:** Buy 2 Get 1 với config chi tiết
- **Targets:** Chỉ áp dụng cho category áo thun

### 3. **Tiered Discount** (Mua nhiều giảm nhiều)
- **Type:** `tiered`
- **Rules:** Không có (áp dụng cho tất cả đơn hàng)
- **Actions:** Giảm giá theo bậc với config tiers
- **Targets:** Áp dụng cho toàn bộ đơn hàng

### 4. **Free Shipping** (Miễn phí vận chuyển)
- **Type:** `free_ship`
- **Rules:** Đơn tối thiểu 300k
- **Actions:** Miễn phí ship, max 50k
- **Targets:** Áp dụng cho toàn bộ đơn hàng

## 🎯 API Endpoints

### Create (POST)
```bash
POST /api/v1/promotions
Authorization: Bearer {{auth_token}}
Content-Type: application/json
Body: <JSON từ các file mẫu>
```

### List (GET)
```bash
GET /api/v1/promotions?perpage=10&sort=id,desc
Authorization: Bearer {{auth_token}}
```

### Show (GET)
```bash
GET /api/v1/promotions/{{promotion_id}}
Authorization: Bearer {{auth_token}}
```

### Update (PUT)
```bash
PUT /api/v1/promotions/{{promotion_id}}
Authorization: Bearer {{auth_token}}
Content-Type: application/json
Body: { "name": "Updated name", "publish": 1 }
```

### Delete (DELETE)
```bash
DELETE /api/v1/promotions/{{promotion_id}}
Authorization: Bearer {{auth_token}}
```

### Bulk Delete (DELETE)
```bash
DELETE /api/v1/promotions/bulkDelete
Authorization: Bearer {{auth_token}}
Content-Type: application/json
Body: { "ids": [1, 2, 3] }
```

## ✅ Validation Rules

- **type:** Phải là một trong các giá trị hợp lệ từ enum
- **publish:** 0=draft, 1=private, 2=public
- **rules:** Array với rule_type, operator, logic_operator hợp lệ
- **actions:** Array với action_type, apply_to hợp lệ
- **targets:** Array với target_type hợp lệ
- **combinable_with:** Array các promotion IDs (nullable)

## 🔍 Response Format

### Success Response
```json
{
  "status": true,
  "code": 200,
  "message": "Data saved successfully",
  "data": {
    "id": 1,
    "name": "Flash Sale - Giảm 20% Điện Thoại",
    "type": "flash_sale",
    "rules": [...],
    "actions": [...],
    "targets": [...]
  },
  "timestamp": "2025-01-01T00:00:00.000000Z"
}
```

### Error Response
```json
{
  "status": false,
  "code": 422,
  "message": "Validation failed",
  "errors": {
    "type": ["Giá trị type không hợp lệ"],
    "rules.0.rule_type": ["Rule type không hợp lệ"]
  },
  "timestamp": "2025-01-01T00:00:00.000000Z"
}
```

## 🎨 Enum Values Reference

### Promotion Types
- `order`, `product`, `flash_sale`, `bundle`, `bxgy`, `free_ship`, `voucher`

### Rule Types
- `min_order_total`, `product_qty`, `product_qty_by_category`, `customer_group`, `time_slot`

### Action Types
- `percent`, `fixed`, `free_shipping`, `bxgy`, `tiered`

### Target Types
- `order`, `product`, `category`, `brand`, `customer_group`, `location`

### Operators
- `=`, `!=`, `>`, `>=`, `<`, `<=`, `IN`, `NOT_IN`, `BETWEEN`

## 🚀 Quick Start

1. **Import collection vào Postman**
2. **Set environment variables**
3. **Login và set auth_token**
4. **Test create promotion với data mẫu**
5. **Test list, show, update, delete**

## 💡 Tips

- Tất cả data mẫu đều có `publish: 2` (public)
- Slug sẽ được tự động generate từ name
- Combinable_with có thể null hoặc array promotion IDs
- Rules và targets có thể empty array
- Actions phải có ít nhất 1 item
