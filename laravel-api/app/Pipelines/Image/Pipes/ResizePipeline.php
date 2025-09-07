<?php
namespace App\Pipelines\Image\Pipes;

class ResizePipeline extends AbstractImagePipeline{

    public function handle($image, \Closure $next){

        return $next($image);
    }
}
