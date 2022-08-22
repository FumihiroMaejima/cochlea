<?php

return [
    'test' => [
        'admin' => [
            'login' => [
                'email'    => 'test' . 'admin1' . '@example.com',
                'password' => env('TEST_ADMIN_SEEDER_PASSWORD', 'password')
            ],
            'create' => [
                'success' => [
                    'name'                  => 'test name',
                    'email'                 => 'testadmin'. '12345XXX' . '@example.com',
                    'roleId'                => 1,
                    'password'              => 'testpassword' . '12345',
                    'password_confirmation' => 'testpassword' . '12345'
                ]
            ],
        ],
        'roles' => [
            'create' => [
                'success' => [
                    'name'        => 'test name',
                    'code'        => 'test_code',
                    'detail'      => 'role`s detail.',
                    'permissions' => [1,2,3],
                ]
            ]
        ],
        'coins' => [
            'create' => [
                'success' => [
                    'name'      => 'test name',
                    'detail'    => 'role`s detail.',
                    'price'     => 350,
                    'cost'      => 350,
                    'start_at'  => '2022/05/10 00:00:00',
                    'end_at'    => '2022/08/10 23:59:59',
                    'image'     => null,
                    'coins'     => [2,3],
                ]
            ]
        ],
    ],
];
