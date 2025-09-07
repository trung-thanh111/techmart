<?php
namespace App\Pipelines\Image\Pipes;

use Illuminate\Support\Facades\Storage;

class StoragePipeline extends AbstractImagePipeline{

    protected $disk;

    public function handle($image, \Closure $next){
        $this->disk = $this->options['disk'] ?? config('upload.image.disk');
        $image = match($this->disk){
            'public' => $this->localUpload($image),
            // 's3' => $this->s3Upload($image),
        };
        return $next($image);
    }

    public function localUpload($image){
        $path = trim($this->options['path'].$image->filename, '/');
        Storage::disk($this->disk)->put($path, (string)$image->encode($image->encoder));
        $image->path = $path;
        return $image;
    }
}
