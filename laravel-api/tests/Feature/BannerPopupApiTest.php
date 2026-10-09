<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\BannerPage;
use App\Models\BannerPosition;
use App\Models\Popup;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BannerPopupApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure baseline data exists
        BannerPage::firstOrCreate(
            ['code' => 'home'],
            ['name' => 'Trang chủ', 'slug' => 'trang-chu', 'publish' => 2]
        );
        BannerPosition::firstOrCreate(
            ['code' => 'main_slider'],
            ['name' => 'Slider chính', 'slug' => 'slider-chinh', 'publish' => 2]
        );
    }

    public function test_guest_can_list_published_banners_filtered_by_page_and_position(): void
    {
        $homePage = BannerPage::where('code', 'home')->first();
        $mainPos = BannerPosition::where('code', 'main_slider')->first();

        $banner = Banner::create([
            'name' => 'Test Slider Banner',
            'slug' => 'test-slider-banner-' . uniqid(),
            'album' => ['/images/slide/hero-slide-left-1.webp'],
            'page_id' => $homePage->id,
            'position_id' => $mainPos->id,
            'link_url' => '/shop',
            'order' => 1,
            'is_hot' => true,
            'publish' => 2,
        ]);

        $response = $this->getJson("/api/v1/banners?page_id={$homePage->id}&position_id={$mainPos->id}&publish=2");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $data = $response->json('data.data') ?? $response->json('data');
        $this->assertNotEmpty($data);

        $ids = collect($data)->pluck('id')->all();
        $this->assertContains($banner->id, $ids);
    }

    public function test_banner_api_does_not_return_draft_banners_when_filtering_public(): void
    {
        $homePage = BannerPage::where('code', 'home')->first();
        $mainPos = BannerPosition::where('code', 'main_slider')->first();

        $draftBanner = Banner::create([
            'name' => 'Draft Banner',
            'slug' => 'draft-banner-' . uniqid(),
            'album' => ['/images/slide/test-draft.jpg'],
            'page_id' => $homePage->id,
            'position_id' => $mainPos->id,
            'publish' => 0, // Draft
        ]);

        $response = $this->getJson('/api/v1/banners?publish=2');

        $response->assertStatus(200);
        $data = $response->json('data.data') ?? $response->json('data');
        $ids = collect($data)->pluck('id')->all();

        $this->assertNotContains($draftBanner->id, $ids);
    }

    public function test_guest_can_view_single_banner_details(): void
    {
        $homePage = BannerPage::where('code', 'home')->first();
        $mainPos = BannerPosition::where('code', 'main_slider')->first();

        $banner = Banner::create([
            'name' => 'Detail Test Banner',
            'slug' => 'detail-test-banner-' . uniqid(),
            'album' => ['/images/slide/hero-slide-left-2.webp'],
            'page_id' => $homePage->id,
            'position_id' => $mainPos->id,
            'link_url' => '/shop',
            'publish' => 2,
        ]);

        $response = $this->getJson("/api/v1/banners/{$banner->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'data' => [
                'id' => $banner->id,
                'name' => 'Detail Test Banner',
                'pageId' => $homePage->id,
                'positionId' => $mainPos->id,
            ],
        ]);
    }

    public function test_guest_can_list_published_popups(): void
    {
        $popup = Popup::create([
            'title' => 'Test Promo Popup',
            'slug' => 'test-promo-popup-' . uniqid(),
            'type' => 'promotion',
            'content' => 'Test content',
            'image' => '/images/promotions/promo-1.webp',
            'link' => '/shop',
            'button_text' => 'Xem ngay',
            'show_delay' => 1000,
            'priority' => true,
            'publish' => 2,
        ]);

        $response = $this->getJson('/api/v1/popups?publish=2');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $data = $response->json('data.data') ?? $response->json('data');
        $this->assertNotEmpty($data);

        $ids = collect($data)->pluck('id')->all();
        $this->assertContains($popup->id, $ids);
    }

    public function test_popup_api_does_not_return_draft_popups_when_filtering_public(): void
    {
        $draftPopup = Popup::create([
            'title' => 'Draft Popup',
            'slug' => 'draft-popup-' . uniqid(),
            'type' => 'notification',
            'content' => 'Draft notification',
            'publish' => 0, // Draft
        ]);

        $response = $this->getJson('/api/v1/popups?publish=2');

        $response->assertStatus(200);
        $data = $response->json('data.data') ?? $response->json('data');
        $ids = collect($data)->pluck('id')->all();

        $this->assertNotContains($draftPopup->id, $ids);
    }

    public function test_guest_can_view_single_popup_details(): void
    {
        $popup = Popup::create([
            'title' => 'Single Popup Details',
            'slug' => 'single-popup-details-' . uniqid(),
            'type' => 'promotion',
            'content' => 'Single popup content',
            'image' => '/images/promotions/promo-1.webp',
            'link' => '/shop',
            'button_text' => 'Chi tiết',
            'show_delay' => 2000,
            'priority' => true,
            'publish' => 2,
        ]);

        $response = $this->getJson("/api/v1/popups/{$popup->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'data' => [
                'id' => $popup->id,
                'title' => 'Single Popup Details',
                'type' => 'promotion',
                'buttonText' => 'Chi tiết',
                'showDelay' => 2000,
                'priority' => true,
            ],
        ]);
    }
}
