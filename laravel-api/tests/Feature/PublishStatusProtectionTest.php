<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PromotionRule;
use App\Models\PromotionAction;
use App\Models\User;
use App\Enums\Promotion as PromotionEnum;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PublishStatusProtectionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_see_draft_products_in_list(): void
    {
        $draftProduct = Product::create([
            'name' => 'Draft Secret Product',
            'slug' => 'draft-secret-product-' . uniqid(),
            'sku' => 'DRAFT-SKU-' . uniqid(),
            'price_origin' => 1000000,
            'publish' => 0, // Bản nháp
        ]);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200);
        $ids = collect($response->json('data.data') ?? [])->pluck('id')->toArray();
        $this->assertNotContains($draftProduct->id, $ids);
    }

    public function test_guest_cannot_view_draft_product_by_slug(): void
    {
        $draftProduct = Product::create([
            'name' => 'Draft Secret Product 2',
            'slug' => 'draft-secret-product-2-' . uniqid(),
            'sku' => 'DRAFT-SKU-2-' . uniqid(),
            'price_origin' => 1000000,
            'publish' => 0, // Bản nháp
        ]);

        $response = $this->getJson('/api/v1/products/' . $draftProduct->slug);

        $response->assertStatus(404);
    }

    public function test_guest_cannot_apply_draft_or_private_promotion(): void
    {
        $draftPromo = Promotion::create([
            'code' => 'DRAFTPROMO',
            'name' => 'Draft Promo',
            'slug' => 'draft-promo-' . uniqid(),
            'type' => 'order',
            'priority' => 10,
            'publish' => 0, // Draft
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
        ]);

        $response = $this->postJson('/api/v1/promotion/apply_discount', [
            'code' => 'DRAFTPROMO',
            'subtotal' => 500000,
            'items' => [['id' => 1, 'qty' => 1]],
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'code' => 422,
                'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn/lượt dùng.',
            ]);
    }

    public function test_admin_can_view_draft_products_in_list(): void
    {
        $draftProduct = Product::create([
            'name' => 'Draft Secret Product Admin',
            'slug' => 'draft-secret-product-admin-' . uniqid(),
            'sku' => 'DRAFT-SKU-ADMIN-' . uniqid(),
            'price_origin' => 1000000,
            'publish' => 0,
        ]);

        $admin = User::where('email', 'trungptdev2004@gmail.com')->first();
        auth('admin')->setUser($admin);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200);
        $ids = collect($response->json('data.data') ?? [])->pluck('id')->toArray();
        $this->assertContains($draftProduct->id, $ids);
    }

    public function test_guest_can_list_published_promotions_and_cannot_see_draft_ones(): void
    {
        $publishedPromo = Promotion::create([
            'code' => 'PUBPROMO' . rand(1000, 9999),
            'name' => 'Published Promo',
            'slug' => 'published-promo-' . uniqid(),
            'type' => 'order',
            'priority' => 10,
            'publish' => 2, // Published
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
        ]);

        $draftPromo = Promotion::create([
            'code' => 'DRAFTPROMO' . rand(1000, 9999),
            'name' => 'Draft Promo',
            'slug' => 'draft-promo-' . uniqid(),
            'type' => 'order',
            'priority' => 10,
            'publish' => 0, // Draft
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
        ]);

        $response = $this->getJson('/api/v1/promotions');

        $response->assertStatus(200);
        $ids = collect($response->json('data.data') ?? [])->pluck('id')->toArray();
        $this->assertContains($publishedPromo->id, $ids);
        $this->assertNotContains($draftPromo->id, $ids);
    }
}
