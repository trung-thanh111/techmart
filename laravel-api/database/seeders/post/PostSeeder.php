<?php

namespace Database\Seeders\Post;

use App\Models\Post;
use App\Models\PostCatalogue;
use App\Models\PostTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = base_path('scraped_sforum_posts.json');
        
        // Clean existing post relations and records
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('related_posts')->truncate();
        DB::table('post_catalogue_post')->truncate();
        DB::table('post_tag_post')->truncate();
        DB::table('posts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $fallbackPosts = [
            [
                'name' => 'Đánh giá chi tiết iPhone 15 Pro Max: Đỉnh cao công nghệ và khung viền Titan',
                'slug' => 'danh-gia-chi-tiet-iphone-15-pro-max',
                'description' => 'iPhone 15 Pro Max mang đến trải nghiệm đỉnh cao với khung titan siêu nhẹ, chip A17 Pro mạnh mẽ và camera tiềm vọng 5x.',
                'content' => '<p>iPhone 15 Pro Max là chiếc flagship đáng mua nhất trong năm nay với hàng loạt cải tiến đáng giá từ thiết kế đến hiệu năng...</p>',
                'image' => 'https://cdn-media.sforum.vn/storage/app/media/wp-content/uploads/2024/01/danh-gia-iphone-15-pro-max-thumb.jpg',
                'album' => [
                    'https://cdn-media.sforum.vn/storage/app/media/wp-content/uploads/2024/01/danh-gia-iphone-15-pro-max-1.jpg',
                    'https://cdn-media.sforum.vn/storage/app/media/wp-content/uploads/2024/01/danh-gia-iphone-15-pro-max-2.jpg',
                ],
                'cat_slug' => 'danh-gia-san-pham',
                'tags' => ['iphone-15-series', 'review-camera'],
                'is_hot' => 1,
                'view_count' => 1520,
                'like_count' => 180,
                'order' => 1,
            ],
        ];

        $postDataList = [];
        if (File::exists($jsonPath)) {
            $jsonContent = File::get($jsonPath);
            $decoded = json_decode($jsonContent, true);
            if (is_array($decoded) && count($decoded) > 0) {
                $postDataList = $decoded;
            }
        }

        if (empty($postDataList)) {
            $postDataList = $fallbackPosts;
        }

        $allCatalogues = PostCatalogue::all()->keyBy('slug');
        $defaultCatalogue = $allCatalogues->first();
        $allTags = PostTag::all()->keyBy('slug');

        $createdPosts = [];

        foreach ($postDataList as $index => $item) {
            $name = trim($item['name'] ?? 'Bài viết công nghệ');
            $slug = !empty($item['slug']) ? Str::slug($item['slug']) : Str::slug($name);
            $catSlug = $item['cat_slug'] ?? '';
            
            $catalogue = $allCatalogues->get($catSlug) ?? $defaultCatalogue;
            $catalogueId = $catalogue ? $catalogue->id : 1;

            $tagSlugs = $item['tags'] ?? [];
            $matchingTags = collect($tagSlugs)->map(fn($t) => $allTags->get($t))->filter();
            $primaryTagId = $matchingTags->isNotEmpty() ? $matchingTags->first()->id : null;

            $description = mb_substr($item['description'] ?? '', 0, 480);
            $metaDescription = !empty($description) ? $description : mb_substr(strip_tags($item['content'] ?? ''), 0, 200);
            $publishedAt = now()->subDays(rand(1, 20))->subHours(rand(1, 23));

            $post = Post::create([
                'name' => mb_substr($name, 0, 250),
                'slug' => $slug,
                'description' => $description,
                'content' => $item['content'] ?? '',
                'image' => $item['image'] ?? null,
                'album' => $item['album'] ?? [],
                'table_of_content' => 1,
                'is_hot' => !empty($item['is_hot']) ? 1 : 0,
                'order' => $item['order'] ?? ($index + 1),
                'view_count' => $item['view_count'] ?? rand(500, 3500),
                'like_count' => $item['like_count'] ?? rand(25, 280),
                'post_catalogue_id' => $catalogueId,
                'tag_id' => $primaryTagId,
                'publish' => 2,
                'user_id' => 1,
                'meta_title' => mb_substr($name, 0, 250),
                'meta_keyword' => implode(', ', $tagSlugs),
                'meta_description' => $metaDescription,
                'published_at' => $publishedAt,
                'created_at' => $publishedAt,
                'updated_at' => $publishedAt,
            ]);

            // Sync catalogue
            $post->post_catalogues()->sync([$catalogueId]);

            // Sync tags
            if ($matchingTags->isNotEmpty()) {
                $post->post_tags()->sync($matchingTags->pluck('id')->toArray());
            }

            $createdPosts[] = $post;
        }

        // Attach related posts for each post
        foreach ($createdPosts as $currentPost) {
            $relatedCandidates = collect($createdPosts)
                ->filter(fn($p) => $p->id !== $currentPost->id);

            // Prefer same catalogue
            $sameCatalogue = $relatedCandidates->filter(fn($p) => $p->post_catalogue_id === $currentPost->post_catalogue_id);
            $selected = $sameCatalogue->count() >= 3 
                ? $sameCatalogue->random(min(4, $sameCatalogue->count())) 
                : $relatedCandidates->random(min(4, $relatedCandidates->count()));

            if ($selected->isNotEmpty()) {
                $currentPost->relateds()->sync($selected->pluck('id')->toArray());
            }
        }

        $this->command->info('Successfully seeded ' . count($createdPosts) . ' CellphoneS Sforum posts with full content and relations!');
    }
}
