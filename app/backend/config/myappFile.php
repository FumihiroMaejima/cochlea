<?php

return [
    'download' => [
        'storage' => [
            'local'      => 'file/',
            'testing'    => 'file/',
            'staging'    => 'file/',
            'production' => 's3',
        ],
    ],
    'upload' => [
        'storage' => [
            'local'      => [
                'images'     => [
                    'debug' => '/images/debug/',
                ],
            ],
            'testing'    => 'file/',
            'staging'    => 'file/',
            'production' => 's3',
        ],
    ]
];
