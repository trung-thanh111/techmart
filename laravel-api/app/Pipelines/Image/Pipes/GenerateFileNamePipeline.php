<?php
namespace App\Pipelines\Image\Pipes;

use Illuminate\Support\Str;

class GenerateFileNamePipeline extends AbstractImagePipeline{

    public function handle($image, \Closure $next){
        if(!isset($image->filename)){
            $originalFileName = $image->originalFile->getClientOriginalName();
            $extension = $image->originalFile->getClientOriginalExtension();
            $image->filename = Str::uuid().'.'.$extension;
            $image->originalName = $originalFileName;
        }

        return $next($image);
    }

}
