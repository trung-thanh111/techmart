<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\AttributeCatalogue;
use App\Models\Product;
use App\Models\ProductVariant;
use Tests\TestCase;

class ProductVariantAttributeApiTest extends TestCase
{
    public function test_product_variants_have_proper_attribute_data_and_pivot_relations(): void
    {
        $product = Product::where('slug', 'iphone-16-pro-max')->with(['product_variants.variant_attributes'])->first();
        $this->assertNotNull($product, 'iPhone 16 Pro Max must exist');
        $this->assertNotEmpty($product->product_variants, 'Product must have variants');

        $variant = $product->product_variants->first();
        $this->assertNotNull($variant);
        $this->assertIsArray($variant->attribute_data);

        // Attribute data keys must be catalogue names (not numeric indices or IDs)
        $keys = array_keys($variant->attribute_data);
        foreach ($keys as $key) {
            $this->assertFalse(is_numeric($key), "Key '{$key}' must be catalogue name, not numeric");
        }

        // Must have Dung lượng and Màu sắc
        $this->assertArrayHasKey('Dung lượng', $variant->attribute_data);
        $this->assertArrayHasKey('Màu sắc', $variant->attribute_data);

        $storageVal = $variant->attribute_data['Dung lượng'];
        $this->assertIsArray($storageVal);
        $this->assertArrayHasKey('id', $storageVal);
        $this->assertArrayHasKey('name', $storageVal);
        $this->assertEquals('256GB', $storageVal['name']);

        $colorVal = $variant->attribute_data['Màu sắc'];
        $this->assertIsArray($colorVal);
        $this->assertArrayHasKey('id', $colorVal);
        $this->assertArrayHasKey('name', $colorVal);
        $this->assertEquals('Titan Sa Mạc', $colorVal['name']);

        // Check variant code is formatted as sorted attribute IDs
        $attrIds = [$storageVal['id'], $colorVal['id']];
        sort($attrIds);
        $expectedCode = implode('_', $attrIds);
        $this->assertEquals($expectedCode, $variant->code);

        // Check pivot table
        $pivotAttrIds = $variant->variant_attributes->pluck('id')->all();
        sort($pivotAttrIds);
        $this->assertEquals($attrIds, $pivotAttrIds);
    }

    public function test_public_product_show_endpoint_returns_structured_attribute_data(): void
    {
        $response = $this->getJson('/api/v1/products/iphone-16-pro-max');
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertNotNull($data);
        $this->assertEquals('iphone-16-pro-max', $data['slug']);

        $variants = $data['productVariants'] ?? [];
        $this->assertNotEmpty($variants);

        $firstVariant = $variants[0];
        $this->assertArrayHasKey('attributeData', $firstVariant);
        $attrData = $firstVariant['attributeData'];
        $this->assertIsArray($attrData);

        $this->assertArrayHasKey('Dung lượng', $attrData);
        $this->assertArrayHasKey('Màu sắc', $attrData);
        $this->assertEquals('256GB', $attrData['Dung lượng']['name']);
        $this->assertEquals('Titan Sa Mạc', $attrData['Màu sắc']['name']);

        // Check attributes array is returned in variant resource
        $this->assertArrayHasKey('attributes', $firstVariant);
        $this->assertIsArray($firstVariant['attributes']);
        $this->assertCount(2, $firstVariant['attributes']);
    }
}
