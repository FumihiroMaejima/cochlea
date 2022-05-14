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
        ]
    ],
    'test' => [],
    'headers' => [
        'id'        => 'X-Auth-ID',
        'authority' => 'X-Auth-Authority'
    ],
    'executionRole' => [
        'services' => [
            'admins' => ['master', 'administrator', 'develop'],
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
