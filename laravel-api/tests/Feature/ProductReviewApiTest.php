<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductReviewApiTest extends TestCase
{
    use DatabaseTransactions;

    protected Product $product;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::firstOrCreate(
            ['slug' => 'test-review-product'],
            [
                'name' => 'Test Review Product',
                'sku' => 'REV-PROD-SKU',
                'price_origin' => 150000,
                'publish' => 2,
            ]
        );

        $this->user = User::firstOrCreate(
            ['email' => 'reviewer_user@example.com'],
            ['name' => 'Reviewer User', 'password' => Hash::make('password123'), 'publish' => 1]
        );
    }

    public function test_guest_can_get_product_reviews_with_summary(): void
    {
        ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'User One',
            'rating' => 5,
            'comment' => 'San pham rat tot',
            'publish' => 2,
        ]);

        ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'User Two',
            'rating' => 5,
            'comment' => 'Chat luong vuot mong doi',
            'publish' => 2,
        ]);

        ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'User Three',
            'rating' => 3,
            'comment' => 'Tam on trong tam gia',
            'publish' => 2,
        ]);

        $response = $this->getJson("/api/v1/products/{$this->product->id}/reviews");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'summary' => [
                    'total_reviews',
                    'average_rating',
                    'star_counts',
                    'star_percentages',
                ],
                'reviews',
                'pagination',
            ],
        ]);

        $summary = $response->json('data.summary');
        $this->assertEquals(3, $summary['total_reviews']);
        $this->assertEquals(4.3, $summary['average_rating']);
        $this->assertEquals(2, $summary['star_counts']['5']);
        $this->assertEquals(1, $summary['star_counts']['3']);
    }

    public function test_guest_can_submit_product_review(): void
    {
        $payload = [
            'product_id' => $this->product->id,
            'rating' => 5,
            'comment' => 'May dung rat muot, pin trau, gia re.',
            'user_name' => 'Nguyen Van Test',
            'user_email' => 'test_guest@example.com',
        ];

        $response = $this->postJson("/api/v1/products/{$this->product->id}/reviews", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $this->product->id,
            'user_name' => 'Nguyen Van Test',
            'rating' => 5,
        ]);
    }

    public function test_authenticated_user_can_submit_review(): void
    {
        $token = JWTAuth::fromUser($this->user);

        $payload = [
            'product_id' => $this->product->id,
            'rating' => 4,
            'comment' => 'Chat luong cao, giao hang sieu nhanh.',
        ];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/products/{$this->product->id}/reviews", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'rating' => 4,
        ]);
    }

    public function test_review_validation_errors(): void
    {
        $payload = [
            'product_id' => $this->product->id,
            'rating' => 6, // Không hợp lệ
            'comment' => 'hi', // Quá ngắn (< 3 ký tự)
        ];

        $response = $this->postJson("/api/v1/products/{$this->product->id}/reviews", $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['rating', 'comment']);
    }

    public function test_can_like_review(): void
    {
        $review = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'User Like Test',
            'rating' => 5,
            'comment' => 'Danh gia rat hay',
            'publish' => 2,
            'likes_count' => 0,
        ]);

        $response = $this->postJson("/api/v1/product-reviews/{$review->id}/like");

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('data.likes_count'));
    }
}
