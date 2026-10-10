<?php

namespace App\Traits;

use App\Enums\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasUpload
{

    // sẽ lấy phần trước @ của email làm tên folder lưu ảnh của từng user
    protected function generateUploadBasePath()
    {
        $auth = auth(Common::API);
        return Str::before($auth->user()->email, '@');
    }

    protected function uploadImage(Request $request, string $folder = '', $pipeline = 'default', string $name = ''): self
    {
        if ($request->hasFile($name)) {
            // khởi tạo config -> nếu k muốn dùng mặc định trong config.upload
            $config = [
                'files' => $request->file($name),
                'folder' => "{$this->baseFolder}/{$folder}",
                'pipeline' => $pipeline,
                "overrideOptions" => [
                    "optimize" => [
                        "quality" => 85,
                    ]
                ],
            ];
            $process = $this->imageService->upload(...$config); // dùng spread operator truyền các tham số key của config
            if (isset($process['files']['path'])) {
                $this->modelData[$name] = $process['files']['path'];
            }
        }
        return $this;
    }

    protected function uploadAlbum(Request $request, string $folder = '', $pipeline = 'default', string $name = ''):self {
        if ($request->has($name)) {
            // khởi tạo config -> nếu k muốn dùng mặc định trong config.upload
            $config = [
                'files' => $request->file($name),
                'folder' => "{$this->baseFolder}/{$folder}",
                'pipeline' => $pipeline,
                "overrideOptions" => [
                    "optimize" => [
                        "quality" => 85,
                    ]
                ],
            ];
            $process = $this->imageService->upload(...$config); // dùng spread operator truyền các tham số key của config
            $this->modelData[$name] = [...$this->existingImages, ...$process['files']];
        }else{
            $this->modelData[$name] = $this->existingImages;
        }

        return $this;
    }

    // trường hợp update ảnh thì mới cần xóa ảnh cũ
    protected function removeImages(Request $request, ?int $id = null): self
    {
        if ($id) {
            $record = $this->show($id);
            $this->existingImages = $record->album;
            if ($request->has('remove_images')) {
                $removeImages = $request->input('remove_images', []);
                if (count($removeImages)) {
                    if(!is_null($record) && isset($record->album) && count($record->album)){
                        $images = array_filter($record->album, function ($item) use ($removeImages){
                            // lọc lấy ra những ảnh không nằm trong mảng $removeImages
                            return !in_array($item['path'], $removeImages);
                        });
                        // cập nhật lại mảng ảnh đã tồn tại
                        $this->existingImages = $images;
                    }
                }
            }
            Log::info(Lang::get('message.existing_image'), $this->existingImages);
        }
        return $this;
    }

    public function moveImage(Request $request, string $folder = '', string $name = 'album'): self{
        if(!$request->{$name} || !is_array($request->{$name})){
            return $this;
        }
        $albumData = $request->input($name, []);
        $moveImages = [];
        foreach($albumData as $key => $val){
            if(isset($val['path']) && strpos($val['path'], 'temp/') !== false){
                $oldPath = str_replace('storage/', '', $val['path']);
                $fileName = basename($oldPath);
                $newPath = config('upload.image.base_path'). '/' . $this->generateUploadBasePath() . '/' . $folder . '/' . $fileName;
                if(Storage::disk('public')->exists($oldPath) && Storage::disk('public')->move($oldPath, $newPath)){
                    $moveImages[] = ['path' => $newPath];
                }
            }else{
                $moveImages[] = $val;
            }
        }
        $this->model->update([$name => $moveImages]);
        return $this;
    }
}
