<?php

namespace Database\Seeders\Post;

use App\Models\Post;
use App\Models\PostCatalogue;
use App\Models\PostTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catNews = PostCatalogue::where('slug', 'tin-tuc-cong-nghe')->first() ?? PostCatalogue::first();
        $catReview = PostCatalogue::where('slug', 'danh-gia-san-pham')->first() ?? $catNews;
        $tag = PostTag::first();

        $posts = [
            [
                'name' => 'Đánh giá chi tiết iPhone 15 Pro Max: Đỉnh cao công nghệ và khung viền Titan',
                'description' => 'iPhone 15 Pro Max mang đến trải nghiệm đỉnh cao với khung titan siêu nhẹ, chip A17 Pro mạnh mẽ và camera tiềm vọng 5x.',
                'content' => '<p>iPhone 15 Pro Max là chiếc flagship đáng mua nhất trong năm nay với hàng loạt cải tiến đáng giá từ thiết kế đến hiệu năng...</p>',
                'image' => '/images/posts/iphone-15-review.jpg',
                'is_hot' => 1,
                'order' => 1,
                'post_catalogue_id' => $catReview ? $catReview->id : 1,
                'tag_id' => $tag ? $tag->id : 1,
                'publish' => 2,
                'user_id' => 1,
            ],
            [
                'name' => 'So sánh Samsung Galaxy S24 Ultra và iPhone 15 Pro Max: Kẻ tám lạng người nửa cân',
                'description' => 'Trận chiến không khoan nhượng giữa hai đại diện xuất sắc nhất của thế giới Android và iOS hiện nay.',
                'content' => '<p>Galaxy S24 Ultra nổi bật với AI thông minh tích hợp, bút S-Pen tiện dụng và màn hình chống chói ấn tượng...</p>',
                'image' => '/images/posts/compare-s24-ip15.jpg',
                'is_hot' => 1,
                'order' => 2,
                'post_catalogue_id' => $catReview ? $catReview->id : 1,
                'tag_id' => $tag ? $tag->id : 1,
                'publish' => 2,
                'user_id' => 1,
            ],
            [
                'name' => 'Xu hướng trí tuệ nhân tạo trên điện thoại di động năm 2026',
                'description' => 'Trí tuệ nhân tạo (AI) đang dần định hình lại cách chúng ta tương tác với smartphone hàng ngày.',
                'content' => '<p>Từ việc dịch thuật trực tiếp cuộc gọi, chỉnh sửa ảnh thông minh đến trợ lý ảo đa phương thức...</p>',
                'image' => '/images/posts/ai-trends.jpg',
                'is_hot' => 0,
                'order' => 3,
                'post_catalogue_id' => $catNews ? $catNews->id : 1,
                'tag_id' => $tag ? $tag->id : 1,
                'publish' => 2,
                'user_id' => 1,
            ],
        ];

        foreach ($posts as $item) {
            $post = Post::firstOrCreate(
                ['slug' => Str::slug($item['name'])],
                array_merge($item, [
                    'slug' => Str::slug($item['name']),
                    'published_at' => now(),
                ])
            );

            if ($item['post_catalogue_id']) {
                $post->post_catalogues()->syncWithoutDetaching([$item['post_catalogue_id']]);
            }
            if ($item['tag_id']) {
                $post->post_tags()->syncWithoutDetaching([$item['tag_id']]);
            }
        }

        $this->command->info('Posts seeded successfully!');
    }
}
