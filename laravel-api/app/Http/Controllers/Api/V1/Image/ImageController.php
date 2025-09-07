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
        $response = [
            'id' => uniqid('temp_', true),
            'files' => collect($result['files'])->map(function ($file) {
                return [
                    'url' => asset('storage/' . $file['path']),
                    'path' => $file['path'],
                ];
            }),
        ];
        return ApiResource::ok($response, Lang::get('message.upload_successfully'));
    }
}
