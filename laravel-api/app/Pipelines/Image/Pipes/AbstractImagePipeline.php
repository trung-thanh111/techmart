<?php
namespace App\Pipelines\Image\Pipes;

class AbstractImagePipeline {

    protected $options;
    /**
     * Định nghĩa sẵn construct cho các class con kế thừa k cần khởi tạo lại
     *
     * @param array $options
     */
    public function __construct(
        array $options = []
    ) {
        $this->options = $options;
    }
}
