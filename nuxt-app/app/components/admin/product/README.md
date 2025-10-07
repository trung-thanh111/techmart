# ProductVariant Component

Component Vue 3 để quản lý biến thể sản phẩm với giao diện 2 cột, hỗ trợ tạo tự động các combination từ attribute groups.

## Tính năng

- ✅ UI layout 2 cột (Attribute Catalogues + Attributes)
- ✅ Chọn nhóm thuộc tính và thuộc tính
- ✅ Tự động sinh combination (cartesian product)
- ✅ Inline edit với DataTable
- ✅ Validation giới hạn số lượng biến thể
- ✅ Auto-generate SKU
- ✅ Hỗ trợ camelCase từ frontend
- ✅ Preview attribute data
- ✅ Confirmation dialog khi regenerate

## Cách sử dụng

### Tạo sản phẩm mới (chưa có ID)
```vue
<template>
  <ProductVariant :product-sku="'IPHONE17'" />
</template>

<script setup>
import ProductVariant from '~/components/admin/product/ProductVariant.vue'
</script>
```

### Sản phẩm đã có ID
```vue
<template>
  <ProductVariant 
    :product-id="123" 
    :product-sku="'IPHONE17'" 
    :initial-variants="existingVariants" 
  />
</template>

<script setup>
import ProductVariant from '~/components/admin/product/ProductVariant.vue'
import type { IProductVariant } from '~/interfaces/product/variant.interface'

const existingVariants = ref<IProductVariant[]>([])
</script>
```

## Props

| Prop              | Type   | Required | Description                              |
| ----------------- | ------ | -------- | ---------------------------------------- |
| `productId`       | Number | ❌       | ID của sản phẩm (optional khi tạo mới)  |
| `productSku`      | String | ❌       | SKU của sản phẩm để generate default SKU |
| `initialVariants` | Array  | ❌       | Danh sách biến thể hiện có               |

## API Endpoints

### Backend Routes

- `GET /api/v1/attribute_catalogues` - Lấy danh sách nhóm thuộc tính
- `GET /api/v1/attribute_catalogues/{id}` - Lấy thuộc tính của nhóm
- `GET /api/v1/products/{id}/variants` - Lấy biến thể của sản phẩm
- `PUT /api/v1/products/{id}` - Cập nhật sản phẩm với biến thể

### Request Format

```json
{
  "name": "Iphone 17 ProMax",
  "sku": "IPHONE17",
  "price_origin": 10000000,
  "description": "test description",
  "content": "test content",
  "metaTitle": "Test Meta Title",
  "metaDescription": "Test Meta Description",
  "productCatalogueId": 6,
  "brandId": 1,
  "publish": 2,
  "productCatalogues": [6],
  "productVariants": [
    {
      "code": "1_2",
      "stock": 100,
      "sku": "IPHONE17_VERSION1",
      "price": 19800000,
      "barcode": "734612036432",
      "publish": 2,
      "attributeData": {
        "Màu sắc": { "id": 1, "name": "Đỏ" },
        "Dung lượng": { "id": 2, "name": "128GB" }
      },
      "attributes": [1, 2]
    }
  ]
}
```

## Helper Functions

### `cartesianProduct(groups: VariantGroup[]): ProductVariant[]`

Tạo tất cả combination từ các nhóm thuộc tính.

### `generateCodeFromIds(ids: number[]): string`

Tạo code từ danh sách ID thuộc tính.

### `generateDefaultSku(productSku: string, code: string): string`

Tạo SKU mặc định cho biến thể.

### `validateCombinationLimit(groups: VariantGroup[], maxVariants = 300)`

Kiểm tra giới hạn số lượng biến thể.

## Store Usage

```typescript
import { useProductVariantStore } from "~/stores/product/variant.store";

const variantStore = useProductVariantStore();

// Load attribute catalogues
await variantStore.loadAttributeCatalogues();

// Save variants
await variantStore.saveVariants(productId, productSku);
```

## Validation Rules

- Tối đa 300 biến thể (có thể config)
- Mỗi biến thể phải có `attributes` và `attributeData`
- SKU phải unique trong hệ thống
- Code phải unique trong sản phẩm

## Error Handling

- **409 Conflict**: SKU đã tồn tại
- **422 Unprocessable Entity**: Validation errors
- **500 Internal Server Error**: Server errors

## Testing

Chạy unit tests:

```bash
npm run test variantHelpers.test.ts
```

## Dependencies

- Vue 3 (Composition API)
- PrimeVue components
- Tailwind CSS
- Pinia (store)
- TypeScript
