<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-6">Demo ProductVariant Component</h2>
    
    <!-- Demo Controls -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-6">
      <h3 class="text-lg font-semibold mb-4">Demo Controls:</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium mb-2">Product ID:</label>
          <InputNumber v-model="demoProductId" :min="0" class="w-full" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">Product SKU:</label>
          <InputText v-model="demoProductSku" placeholder="IPHONE17" class="w-full" />
        </div>
        <div class="flex items-end">
          <Button @click="resetDemo" severity="secondary" class="w-full">Reset Demo</Button>
        </div>
      </div>
    </div>

    <!-- Component Demo -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <h3 class="text-lg font-semibold mb-4">Component Demo:</h3>
      <ProductVariant 
        :product-id="demoProductId" 
        :product-sku="demoProductSku" 
        :initial-variants="demoInitialVariants" 
      />
    </div>

    <!-- Debug Info -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mt-6">
      <h3 class="text-lg font-semibold mb-4">Debug Info:</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <h4 class="font-medium mb-2">Current Props:</h4>
          <pre class="bg-gray-100 dark:bg-gray-700 p-3 rounded text-sm overflow-x-auto">{{ JSON.stringify({
            productId: demoProductId,
            productSku: demoProductSku,
            hasInitialVariants: demoInitialVariants.length > 0
          }, null, 2) }}</pre>
        </div>
        <div>
          <h4 class="font-medium mb-2">Initial Variants:</h4>
          <pre class="bg-gray-100 dark:bg-gray-700 p-3 rounded text-sm overflow-x-auto">{{ JSON.stringify(demoInitialVariants, null, 2) }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ProductVariant from './ProductVariant.vue'
import InputNumber from 'primevue/inputnumber'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import type { IProductVariant } from '~/interfaces/product/variant.interface'

// Demo state
const demoProductId = ref<number | undefined>(undefined)
const demoProductSku = ref('IPHONE17')

const demoInitialVariants = ref<IProductVariant[]>([
  {
    id: 1,
    album: ['image1.jpg', 'image2.jpg'],
    code: '1_2',
    stock: 100,
    sku: 'IPHONE17_VERSION1',
    price: 19800000,
    barcode: '734612036432',
    attributeData: {
      'Màu sắc': { id: 1, name: 'Đỏ' },
      'Dung lượng': { id: 2, name: '128GB' }
    },
    attributes: [1, 2],
    publish: 2,
    productId: 123,
    userId: 1
  }
])

const resetDemo = () => {
  demoProductId.value = undefined
  demoProductSku.value = 'IPHONE17'
  demoInitialVariants.value = []
}
</script>
