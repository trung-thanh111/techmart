<?php

namespace App\Http\Controllers\Api\V1\Image;

use App\Enums\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadTempRequest;
use App\Http\Resources\ApiResource;
use App\Services\Impl\ImageService;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    private $imageService;
    private $auth;
    private $basePath;

    public function __construct(
        ImageService $imageService

    ) {
        $this->imageService = $imageService;
        $this->auth = auth(Common::API);
        $this->basePath = Str::before($this->auth->user()->email, '@');
    }

    public function uploadTemp(UploadTempRequest $request)
    {
        $config = [
            'files' => $request->file('file'),
            'folder' => "{$this->basePath}/temp/{$request->input('session_id')}",
            'pipeline' => 'default',
            'overrideOptions' => [],
        ];
        $result = $this->imageService->upload(...$config);
        $rawFiles = $result['files'] ?? [];
        $filesList = isset($rawFiles['path']) ? [$rawFiles] : (is_array($rawFiles) ? $rawFiles : []);
        $response = [
            'id' => uniqid('temp_', true),
            'files' => collect($filesList)->map(function ($file) {
                $path = is_array($file) ? ($file['path'] ?? '') : (string) $file;
                return [
                    'url' => asset('storage/' . $path),
                    'path' => $path,
                ];
            }),
        ];
        return ApiResource::ok($response, Lang::get('message.upload_successfully'));
    }
}
