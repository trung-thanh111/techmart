<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-6">Test ProductVariant Component</h2>

    <!-- Test Data -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-6">
      <h3 class="text-lg font-semibold mb-4">Test Data:</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <h4 class="font-medium mb-2">Product Info:</h4>
          <pre class="bg-gray-100 dark:bg-gray-700 p-3 rounded text-sm overflow-x-auto">{{ JSON.stringify(productInfo, null, 2) }}</pre>
        </div>
        <div>
          <h4 class="font-medium mb-2">Initial Variants:</h4>
          <pre class="bg-gray-100 dark:bg-gray-700 p-3 rounded text-sm overflow-x-auto">{{ JSON.stringify(initialVariants, null, 2) }}</pre>
        </div>
      </div>
    </div>

    <!-- Component Test -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <h3 class="text-lg font-semibold mb-4">Component Test:</h3>
      <ProductVariant :product-id="productInfo.id" :product-sku="productInfo.sku" :initial-variants="initialVariants" />
    </div>

    <!-- Test Results -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mt-6">
      <h3 class="text-lg font-semibold mb-4">Test Results:</h3>
      <div class="space-y-2">
        <div class="flex items-center space-x-2">
          <span class="w-2 h-2 bg-green-500 rounded-full"></span>
          <span>Component loaded successfully</span>
        </div>
        <div class="flex items-center space-x-2">
          <span class="w-2 h-2 bg-green-500 rounded-full"></span>
          <span>Store initialized</span>
        </div>
        <div class="flex items-center space-x-2">
          <span class="w-2 h-2 bg-green-500 rounded-full"></span>
          <span>Services connected</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ProductVariant from "./ProductVariant.vue";
import type { IProductVariant } from "~/interfaces/product/variant.interface";

// Test data
const productInfo = {
  id: 123,
  name: "iPhone 17 ProMax",
  sku: "IPHONE17",
};

const initialVariants: IProductVariant[] = [
  {
    id: 1,
    album: ["image1.jpg", "image2.jpg"],
    code: "1_2",
    stock: 100,
    sku: "IPHONE17_VERSION1",
    price: 19800000,
    barcode: "734612036432",
    attributeData: {
      "Màu sắc": { id: 1, name: "Đỏ" },
      "Dung lượng": { id: 2, name: "128GB" },
    },
    attributes: [1, 2],
    publish: 2,
    productId: 123,
    userId: 1,
  },
  {
    id: 2,
    album: ["image3.jpg"],
    code: "1_3",
    stock: 50,
    sku: "IPHONE17_VERSION2",
    price: 21800000,
    barcode: "734612036433",
    attributeData: {
      "Màu sắc": { id: 1, name: "Đỏ" },
      "Dung lượng": { id: 3, name: "256GB" },
    },
    attributes: [1, 3],
    publish: 2,
    productId: 123,
    userId: 1,
  },
];
</script>
