<?php
namespace App\Pipelines\Image\Pipes;

use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\GifEncoder;
use Intervention\Image\Encoders\AvifEncoder;
use Intervention\Image\Encoders\WebpEncoder;

class OptimizePipeline extends AbstractImagePipeline{

    public function handle($image, \Closure $next){
        $quality = $this->options['quality'];
        $mime = $image->origin()->mediaType();

        $encoder = match($mime){
            'image/jpeg' => new JpegEncoder(),
            'image/png' => new PngEncoder(),
            'image/gif' => new GifEncoder(),
            'image/webp' => new WebpEncoder(),
            'image/avif' => new AvifEncoder()
        };
        $image->encoder = $encoder;

        return $next($image);
    }
}
