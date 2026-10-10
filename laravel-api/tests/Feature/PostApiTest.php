<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostCatalogue;
use App\Models\PostTag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_guest_can_list_published_posts(): void
    {
        $response = $this->getJson('/api/v1/posts?publish=2&perpage=5');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        $firstItem = $data[0];
        $this->assertArrayHasKey('name', $firstItem);
        $this->assertArrayHasKey('slug', $firstItem);
        $this->assertArrayHasKey('image', $firstItem);
        $this->assertNotNull($firstItem['image']['url']);
    }

    public function test_guest_can_view_post_by_slug_with_rich_content_and_relations(): void
    {
        $post = Post::where('publish', 2)->whereNotNull('content')->first();
        $this->assertNotNull($post, 'At least one published post must exist');

        $response = $this->getJson('/api/v1/posts/' . $post->slug);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals($post->id, $data['id']);
        $this->assertEquals($post->name, $data['name']);
        $this->assertNotEmpty($data['content']);
        $this->assertArrayHasKey('tableOfContent', $data);
        $this->assertArrayHasKey('image', $data);
        $this->assertNotNull($data['image']['url']);
        $this->assertArrayHasKey('album', $data);
        $this->assertArrayHasKey('relateds', $data);
    }

    public function test_post_image_urls_are_properly_formatted(): void
    {
        $cat = PostCatalogue::firstOrCreate(
            ['slug' => 'test-cat'],
            ['name' => 'Test Cat', 'publish' => 2]
        );

        $postCdn = Post::create([
            'name' => 'Post with CDN Image',
            'slug' => 'post-cdn-' . uniqid(),
            'description' => 'Test CDN image',
            'content' => '<p>Content</p>',
            'image' => 'https://cdn-media.sforum.vn/test.jpg',
            'album' => ['https://cdn-media.sforum.vn/album1.jpg'],
            'post_catalogue_id' => $cat->id,
            'publish' => 2,
        ]);

        $response = $this->getJson('/api/v1/posts/' . $postCdn->slug);
        $response->assertStatus(200);
        $data = $response->json('data');

        // External CDN URL must not be prefixed with /storage
        $this->assertEquals('https://cdn-media.sforum.vn/test.jpg', $data['image']['url']);
        $this->assertCount(1, $data['album']);
        $this->assertEquals('https://cdn-media.sforum.vn/album1.jpg', $data['album'][0]['url']);
    }

    public function test_post_catalogues_return_valid_image_urls(): void
    {
        $response = $this->getJson('/api/v1/post_catalogues');

        $response->assertStatus(200);
        $data = $response->json('data.data') ?? $response->json('data');
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        foreach ($data as $cat) {
            if (!empty($cat['image']['path'])) {
                $this->assertNotNull($cat['image']['url']);
                $this->assertFalse(
                    str_contains($cat['image']['url'], '/storage/https://') ||
                    str_contains($cat['image']['url'], '/storage//')
                );
            }
        }
    }
}
