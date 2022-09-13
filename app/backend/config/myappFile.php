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
    ],
    'service' => [
        'admins' => [
            'coins' => [
                'template' => [
                    (object)[
                        'name'   => 'test enemies',
                        'detail' => 'test detail',
                        'price'  => 100,
                        'cost'   => 100,
                    ]
                ]
            ]
        ]
    ]
];
