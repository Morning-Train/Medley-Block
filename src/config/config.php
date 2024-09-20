<?php return [
    'paths' => [
        resource_path("blocks"),
    ],
    'compiled' => env(
        'BLOCK_COMPILED_PATH',
        public_path("build/blocks"),
    ),
    'blocks' => [],
];
