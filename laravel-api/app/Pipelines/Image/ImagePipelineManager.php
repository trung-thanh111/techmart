<?php

namespace App\Pipelines\Image;

use App\Pipelines\Image\Pipes\GenerateFileNamePipeline;
use App\Pipelines\Image\Pipes\OptimizePipeline;
use App\Pipelines\Image\Pipes\ResizePipeline;
use App\Pipelines\Image\Pipes\StoragePipeline;
use Illuminate\Pipeline\Pipeline;

class ImagePipelineManager
{

    //chú ý thứ tự sắp xếp và giống config upload đã cấu hình !quan trong trong pipeline parttern
    protected $defaultPipeline = [
        'generate_filename' => GenerateFileNamePipeline::class,
        'resize' => ResizePipeline::class,
        'optimize' => OptimizePipeline::class,
        'storage' => StoragePipeline::class
    ];

    public function process($image, string $pipelineKey = 'default', array $overrideOptions = [])
    {
        $pipelineConfig = config("upload.image.pipeline.{$pipelineKey}");
        $pipes = collect($pipelineConfig)->filter(fn($config) => $config['enabled'] ?? true)
            ->map(function ($config, $pipename) use ($overrideOptions) {
                $class = $this->defaultPipeline[$pipename] ?? null;
                if (!$class) return null;

                return new $class(array_merge(
                    $config,
                    $overrideOptions[$pipename] ?? []
                ));
            })->filter()->values()->toArray();

        return app(Pipeline::class)->send($image)->through($pipes)->thenReturn();
    }
}
