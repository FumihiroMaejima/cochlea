<?php

return [
    'seeder' => [
        'password' => [
            'testuser'  => env('TEST_USR_SEEDER_PASSWORD', 'password'),
            'testadmin' => env('TEST_ADMIN_SEEDER_PASSWORD', 'password')
        ],
        'authority' => [
            'rolesNameList'       => ['マスター', '管理者', '開発者', 'マネージャー', '一般'],
            'rolesCodeList'       => ['master', 'administrator', 'develop', 'manager', 'general'],
            'rolesDetailList'     => ['masterロール', 'administratorロール', 'develop権限ロール', 'managerロール', 'generalロール'],
            'permissionsNameList' => ['作成', '読取', '更新', '削除'],
            'roles' => [
                (object)['key' => 1, 'name' => 'マスター'],
                (object)['key' => 2, 'name' => '管理者'],
                (object)['key' => 3, 'name' => '開発者'],
                (object)['key' => 4, 'name' => 'マネージャー'],
                (object)['key' => 5, 'name' => '一般']
            ],
            'permissions' => [
                (object)['key' => 1, 'name' => '作成'],
                (object)['key' => 2, 'name' => '読取'],
                (object)['key' => 3, 'name' => '更新'],
                (object)['key' => 4, 'name' => '削除']
            ]
        ]
    ],
    'test' => [
        'admin' => [
            'login' => [
                'email'    => 'test' . 'admin1' . '@example.com',
                'password' => env('TEST_ADMIN_SEEDER_PASSWORD', 'password')
            ]
        ],
        'member' => [
            'create' => [
                'success' => [
                    'name'                  => 'test name',
                    'email'                 => 'testadmin'. '12345XXX' . '@example.com',
                    'roleId'                => 1,
                    'password'              => 'testpassword' . '12345',
                    'password_confirmation' => 'testpassword' . '12345'
                ]
            ]
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
        ]
    ],
    'headers' => [
        'id'        => 'X-Auth-ID',
        'authority' => 'X-Auth-Authority'
    ],
    'executionRole' => [
        'services' => [
            'admins'      => ['master', 'administrator', 'develop'],
            'permissions' => ['master', 'administrator', 'develop'],
            'roles'       => ['master', 'administrator', 'develop'],
        ]
    ],
    'file' => [
        'download' => [
            'storage' => [
                'local'      => 'file/',
                'testing'    => 'file/',
                'staging'    => 'file/',
                'production' => 's3',
            ],
        ]
        ],
    'slack' => [
        'channel' => env('APP_SLACK_CHANNEL', 'channel_title'),
        'name'    => env('APP_SLACK_NAME', 'bot-name'),
        'icon'    => env('APP_SLACK_ICON', ':ghost:'),
        'url'     => env('APP_SLACK_WEBHOOK_URL', 'https://hooks.slack.com/services/test'),
    ],
    'service' => []
];
