<?php
namespace App\Services\Impl;

use App\Services\Interfaces\ImageServiceInterface;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use App\Pipelines\Image\ImagePipelineManager;

class ImageService implements ImageServiceInterface {

    private $config;

    private $uploadedFiles = [];
    private $errors = [];
    private $imagePipelineManager;


    public function __construct(
        ImagePipelineManager $imagePipelineManager
    ) {
        $this->config = config('upload');
        $this->imagePipelineManager = $imagePipelineManager;
    }

    public function upload($files, $folder, $pipeline, $overrideOptions)
    {
        if(isset($files)){
            $this->uploadedFiles = [];
            $this->errors = [];
            if(is_array($files) && count($files)){
                return $this->multipleUpload($files, $folder, $pipeline, $overrideOptions);
            }
            return $this->singleUpload($files, $folder, $pipeline, $overrideOptions);
        }
    }

    private function singleUpload($file, $folder, $pipeline, $overrideOptions){
        try {
            $result = $this->handleUpload($file, $folder, $pipeline, $overrideOptions);
            $this->uploadedFiles = $result;
        } catch (\Throwable $th) {
            $this->errors = [
                'file' => $file->getClientOriginalName(),
                'error' => $th->getMessage()
            ];
            Log::error(Lang::get('message.upload_image_error'), $this->errors);
        }
        return $this->generateResponse();
    }

    private function multipleUpload($files, $folder, $pipeline, $overrideOptions){
        foreach ($files as $key => $file) {
            try {
                $result = $this->handleUpload($file, $folder, $pipeline, $overrideOptions);
                $this->uploadedFiles[] = $result;
            } catch (\Throwable $th) {
                $this->errors = [
                    'file' => $file->getClientOriginalName(),
                    'error' => $th->getMessage()
                ];
                Log::error(Lang::get('message.upload_image_error'), end($this->errors));
            }
        }
        return $this->generateResponse();
    }

    // dùng thư viện intervention quản lý hình ảnh
    private function handleUpload($file, $folder, $pipeline, $overrideOptions){
        $overrideOptions['storage'] = array_merge(
            $overrideOptions ?? [],
            ['path' => $this->biuldPath($folder)]
        );
        $image = ImageManager::gd()->read($file);
        $image->originalFile = $file;
        // upload theo quy trình
        $process = $this->imagePipelineManager->process($image, $pipeline, $overrideOptions);
        return [
            'path' => $process->path
        ];
    }

    private function generateResponse() {
        $response = [
            'success' => count($this->errors) === 0,
            'files' => $this->uploadedFiles
        ];
        if(!empty($this->errors)){
            $response['error'] = $this->errors;
        }

        return $response;
    }

    private function biuldPath($folder){
        return trim("{$this->config['image']['base_path']}/{$folder}/");
    }


}
