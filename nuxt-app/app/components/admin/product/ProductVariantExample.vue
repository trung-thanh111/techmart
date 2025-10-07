<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-6">Ví dụ sử dụng ProductVariant Component</h2>

    <!-- Example Usage -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-6">
      <h3 class="text-lg font-semibold mb-4">Cách sử dụng:</h3>
      <pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-sm overflow-x-auto"><code>&lt;ProductVariant
  :product-id="123"
  :product-sku="'PROD-001'"
  :initial-variants="existingVariants"
/&gt;</code></pre>
    </div>

    <!-- API Payload Example -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-6">
      <h3 class="text-lg font-semibold mb-4">API Payload Example:</h3>
      <pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-sm overflow-x-auto"><code>{
  "productVariants": [
    {
      "album": ["image1.jpg", "image2.jpg"],
      "code": "1_3_7",
      "stock": 100,
      "sku": "PROD-001-1_3_7",
      "price": 1500000,
      "barcode": "1234567890123",
      "attributeData": {
        "Màu sắc": { "id": 1, "name": "Đỏ" },
        "Dung lượng": { "id": 4, "name": "128GB" }
      },
      "attributes": [1, 4],
      "publish": 2
    }
  ]
}</code></pre>
    </div>

    <!-- Expected Response -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-6">
      <h3 class="text-lg font-semibold mb-4">Expected API Response:</h3>
      <pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-sm overflow-x-auto"><code>{
  "status": true,
  "message": "Cập nhật dữ liệu thành công",
  "data": {
    "id": 123,
    "name": "iPhone 15 Pro",
    "product_variants": [
      {
        "id": 1,
        "album": ["image1.jpg", "image2.jpg"],
        "code": "1_3_7",
        "stock": 100,
        "sku": "PROD-001-1_3_7",
        "price": 1500000,
        "barcode": "1234567890123",
        "attribute_data": {
          "Màu sắc": { "id": 1, "name": "Đỏ" },
          "Dung lượng": { "id": 4, "name": "128GB" }
        },
        "publish": 2,
        "variant_attributes": [
          { "id": 1, "name": "Đỏ" },
          { "id": 4, "name": "128GB" }
        ]
      }
    ]
  }
}</code></pre>
    </div>

    <!-- Test Cases -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
      <h3 class="text-lg font-semibold mb-4">Test Cases:</h3>
      <div class="space-y-4">
        <div class="border-l-4 border-green-500 pl-4">
          <h4 class="font-medium text-green-700 dark:text-green-400">Test Case 1: Tạo biến thể mới</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            - Chọn nhóm "Màu sắc" với thuộc tính "Đỏ", "Xanh"
            <br />
            - Chọn nhóm "Dung lượng" với thuộc tính "128GB", "256GB"
            <br />
            - Kết quả: 4 biến thể (2x2 combinations)
          </p>
        </div>

        <div class="border-l-4 border-blue-500 pl-4">
          <h4 class="font-medium text-blue-700 dark:text-blue-400">Test Case 2: Giới hạn số lượng</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            - Thêm 3 nhóm với 10 thuộc tính mỗi nhóm
            <br />
            - Kết quả: Warning hiển thị (10x10x10 = 1000 > 300)
          </p>
        </div>

        <div class="border-l-4 border-yellow-500 pl-4">
          <h4 class="font-medium text-yellow-700 dark:text-yellow-400">Test Case 3: SKU trùng lặp</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            - Tạo biến thể với SKU đã tồn tại
            <br />
            - Kết quả: Error 409 với message "SKU đã tồn tại trong hệ thống"
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Example component showing how to use ProductVariant
</script>
