<?php

namespace Tests\Feature;

use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SystemConfigApiTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_system_test@example.com'],
            [
                'name' => 'Admin System Tester',
                'password' => Hash::make('password123'),
                'publish' => 1,
                'user_catalogue_id' => 2,
            ]
        );

        $this->adminToken = (string) auth('admin')->claims(['guard' => 'admin'])->login($this->adminUser);
    }

    public function test_guest_can_get_public_configs(): void
    {
        $response = $this->getJson('/api/v1/systems/public-configs');

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertIsArray($data);
        $this->assertArrayHasKey('general', $data);
        $this->assertArrayHasKey('contact', $data);
        $this->assertArrayHasKey('seo', $data);
        $this->assertArrayHasKey('social', $data);
        $this->assertArrayHasKey('script', $data);
        $this->assertArrayHasKey('map', $data);

        $this->assertNotEmpty($data['general']['company_name']);
        $this->assertNotEmpty($data['contact']['hotline']);
        $this->assertNotEmpty($data['seo']['meta_title']);
    }

    public function test_admin_can_get_system_structure_with_fields(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/v1/systems/structure');

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertIsArray($data);
        $this->assertArrayHasKey('general', $data);
        $this->assertArrayHasKey('fields', $data['general']);
        $this->assertArrayHasKey('company_name', $data['general']['fields']);

        $field = $data['general']['fields']['company_name'];
        $this->assertArrayHasKey('label', $field);
        $this->assertArrayHasKey('type', $field);
        $this->assertArrayHasKey('keyword', $field);
        $this->assertEquals('general_company_name', $field['keyword']);
    }

    public function test_guest_cannot_save_dynamic_settings(): void
    {
        $response = $this->postJson('/api/v1/systems/save-settings', [
            'settings' => ['general_slogan' => 'Unauthenticated Slogan'],
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_can_save_dynamic_settings(): void
    {
        $uniqueSlogan = 'Slogan Test ' . uniqid();
        $uniqueHotline = '0987' . rand(100000, 999999);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/v1/systems/save-settings', [
                'settings' => [
                    'general_slogan' => $uniqueSlogan,
                    'contact_hotline' => $uniqueHotline,
                ],
            ]);

        $response->assertStatus(200);

        // Verify in database
        $this->assertDatabaseHas('systems', [
            'keyword' => 'general_slogan',
            'content' => $uniqueSlogan,
            'publish' => 2,
        ]);
        $this->assertDatabaseHas('systems', [
            'keyword' => 'contact_hotline',
            'content' => $uniqueHotline,
            'publish' => 2,
        ]);

        // Verify public configs reflects the updated value
        $publicRes = $this->getJson('/api/v1/systems/public-configs');
        $publicRes->assertStatus(200);
        $this->assertEquals($uniqueSlogan, $publicRes->json('data.general.slogan'));
        $this->assertEquals($uniqueHotline, $publicRes->json('data.contact.hotline'));
    }

    public function test_policy_lookup_by_keyword_works(): void
    {
        $response = $this->getJson('/api/v1/policy/privacy_policy');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('privacy_policy', $data['keyword']);
        $this->assertNotEmpty($data['content']);
    }

    public function test_admin_can_upload_temp_image(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->image('test-logo.png', 200, 200);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->post('/api/v1/upload-temp', [
                'file' => $file,
                'session_id' => 'session_' . time(),
            ]);

        $response->assertStatus(200);
        $files = $response->json('data.files');
        $this->assertNotEmpty($files);
        $this->assertArrayHasKey('url', $files[0]);
        $this->assertArrayHasKey('path', $files[0]);
    }
}
