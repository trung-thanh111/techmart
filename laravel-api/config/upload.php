<?php
return [
    'image' => [
        'disk' => 'public', // nơi lưu, s3,...
        'max_size' => 5 * 1024, // 5mb
        'allowed_type' => [
            'jpeg',
            'jpg',
            'png',
            'svg',
            'gif',
            'webp',
            'avif',
        ],
        'base_path' => 'uploads',
        'pipeline' => [
            'default' => [
                'generate_filename' => [
                    'enabled' => true,
                ],
                'resize' => [
                    'enabled' => false,
                    'width' => 300,
                    'height' => 300
                ],
                'optimize' => [
                    'enabled' => true,
                    'quality' => 95
                ],
                'storage' => [
                    'enabled' => true
                ]

            ],
            'product' => [],
            'blog' => [],
        ],
    ],
    'file' => [],
    'media' => [],
];


// Design parttern: pipelines parttern (data sẽ đi theo từng đường ống 1 cho đến cuối cùng -> data truyền từ đầu đến cuối cùng)
// hay sử dụng trong middleware
// ---> | ---> | ---> | ---> | --->
