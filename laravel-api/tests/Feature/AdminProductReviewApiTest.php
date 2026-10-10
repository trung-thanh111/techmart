<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProductReviewApiTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected string $adminToken;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_review_test@example.com'],
            [
                'name' => 'Admin Review Tester',
                'password' => Hash::make('password123'),
                'publish' => 1,
                'user_catalogue_id' => 2,
            ]
        );

        $this->adminToken = (string) auth('admin')->claims(['guard' => 'admin'])->login($this->adminUser);

        $this->product = Product::firstOrCreate(
            ['slug' => 'test-product-review-admin'],
            [
                'name' => 'Sản phẩm Test Admin Review',
                'sku' => 'TEST-REV-ADM-01',
                'price_origin' => 500000,
                'publish' => 2,
                'product_catalogue_id' => 1,
                'album' => ['products/test-thumb.png'],
            ]
        );
    }

    public function test_guest_cannot_access_admin_reviews_index(): void
    {
        $response = $this->getJson('/api/v1/product_reviews');
        $response->assertStatus(401);
    }

    public function test_client_cannot_access_admin_reviews_index(): void
    {
        $clientUser = User::firstOrCreate(
            ['email' => 'client_review_test@example.com'],
            [
                'name' => 'Client Review Tester',
                'password' => Hash::make('password123'),
                'publish' => 1,
                'user_catalogue_id' => 1,
            ]
        );

        $clientToken = (string) auth('api')->claims(['guard' => 'api'])->login($clientUser);

        $response = $this->withHeader('Authorization', "Bearer {$clientToken}")
            ->getJson('/api/v1/product_reviews');

        $response->assertStatus(401);
    }

    public function test_admin_can_access_reviews_index_with_pagination(): void
    {
        $review1 = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Admin Test User 1',
            'user_email' => 'user1@example.com',
            'rating' => 5,
            'comment' => 'San pham rat tuyet voi',
            'publish' => 2,
        ]);

        $review2 = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Admin Test User 2',
            'user_email' => 'user2@example.com',
            'rating' => 4,
            'comment' => 'Dung tam on',
            'publish' => 1,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/product_reviews');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'product_id',
                        'product',
                        'user_name',
                        'user_email',
                        'rating',
                        'comment',
                        'publish',
                    ],
                ],
                'total',
                'per_page',
            ],
        ]);
    }

    public function test_admin_can_filter_reviews_by_publish_status(): void
    {
        $pendingReview = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Pending Reviewer',
            'user_email' => 'pending@example.com',
            'rating' => 3,
            'comment' => 'Dang cho duyet',
            'publish' => 1,
        ]);

        $approvedReview = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Approved Reviewer',
            'user_email' => 'approved@example.com',
            'rating' => 5,
            'comment' => 'Da duoc duyet',
            'publish' => 2,
        ]);

        // Filter publish = 1 (pending)
        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/product_reviews?publish=1');

        $response->assertStatus(200);
        $items = $response->json('data.data');
        $this->assertTrue(collect($items)->contains('id', $pendingReview->id));
        $this->assertFalse(collect($items)->contains('id', $approvedReview->id));
    }

    public function test_admin_can_filter_reviews_by_rating(): void
    {
        $reviewStar5 = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Star 5 User',
            'user_email' => 'star5@example.com',
            'rating' => 5,
            'comment' => 'Nam sao xuat sac',
            'publish' => 2,
        ]);

        $reviewStar1 = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Star 1 User',
            'user_email' => 'star1@example.com',
            'rating' => 1,
            'comment' => 'Mot sao te qua',
            'publish' => 2,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/product_reviews?rating=5');

        $response->assertStatus(200);
        $items = $response->json('data.data');
        $this->assertTrue(collect($items)->contains('id', $reviewStar5->id));
        $this->assertFalse(collect($items)->contains('id', $reviewStar1->id));
    }

    public function test_admin_can_search_reviews_by_keyword(): void
    {
        $uniqueKeyword = 'SuperUniqueKeywordXyz';
        $review = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Keyword Tester',
            'user_email' => 'keyword@example.com',
            'rating' => 4,
            'comment' => "Danh gia co chua {$uniqueKeyword} ben trong",
            'publish' => 2,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson("/api/v1/product_reviews?keyword={$uniqueKeyword}");

        $response->assertStatus(200);
        $items = $response->json('data.data');
        $this->assertTrue(collect($items)->contains('id', $review->id));
    }

    public function test_admin_can_update_review_status(): void
    {
        $review = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Status Update Tester',
            'user_email' => 'status@example.com',
            'rating' => 5,
            'comment' => 'Can duyet review nay',
            'publish' => 1,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->putJson("/api/v1/product_reviews/{$review->id}/status", [
                'publish' => 2,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.publish', 2);

        $this->assertDatabaseHas('product_reviews', [
            'id' => $review->id,
            'publish' => 2,
        ]);
    }

    public function test_admin_can_bulk_update_review_status(): void
    {
        $review1 = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Bulk User 1',
            'user_email' => 'bulk1@example.com',
            'rating' => 5,
            'comment' => 'Bulk comment 1',
            'publish' => 1,
        ]);

        $review2 = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Bulk User 2',
            'user_email' => 'bulk2@example.com',
            'rating' => 4,
            'comment' => 'Bulk comment 2',
            'publish' => 1,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/product_reviews/bulkStatus', [
                'ids' => [$review1->id, $review2->id],
                'publish' => 2,
            ]);

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('data.updated_count'));

        $this->assertDatabaseHas('product_reviews', [
            'id' => $review1->id,
            'publish' => 2,
        ]);
        $this->assertDatabaseHas('product_reviews', [
            'id' => $review2->id,
            'publish' => 2,
        ]);
    }

    public function test_admin_can_delete_review(): void
    {
        $review = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Delete User',
            'user_email' => 'delete@example.com',
            'rating' => 2,
            'comment' => 'Spam comment to delete',
            'publish' => 1,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->deleteJson("/api/v1/product_reviews/{$review->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('product_reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_admin_can_bulk_delete_reviews(): void
    {
        $review1 = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Bulk Del 1',
            'user_email' => 'bulkdel1@example.com',
            'rating' => 1,
            'comment' => 'Spam 1',
            'publish' => 0,
        ]);

        $review2 = ProductReview::create([
            'product_id' => $this->product->id,
            'user_name' => 'Bulk Del 2',
            'user_email' => 'bulkdel2@example.com',
            'rating' => 1,
            'comment' => 'Spam 2',
            'publish' => 0,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->deleteJson('/api/v1/product_reviews/bulkDelete', [
                'ids' => [$review1->id, $review2->id],
            ]);

        $response->assertStatus(200);
        $this->assertSoftDeleted('product_reviews', ['id' => $review1->id]);
        $this->assertSoftDeleted('product_reviews', ['id' => $review2->id]);
    }
}
