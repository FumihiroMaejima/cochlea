<?php

return [
    'headers' => [
        'id'            => 'X-Auth-ID',
        'authority'     => 'X-Auth-Authority',
        'authorization' => 'Authorization',
    ],
    'executionRole' => [
        'services' => [
            'admins'      => ['master', 'administrator', 'develop'],
            'permissions' => ['master', 'administrator', 'develop'],
            'roles'       => ['master', 'administrator', 'develop'],
            'coins'       => ['master', 'administrator', 'develop'],
            'debug'       => ['master', 'administrator', 'develop'],
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
    'service' => [],
    'database' => [
        'logs' => [
            'baseConnectionName' => env('DB_LOGS_BASE_CONNECTION'),
        ],
        'users' => [
            'baseConnectionName' => env('DB_USER_BASE_CONNECTION'),
            'shardCount'         => 12,
            'modBaseNumber'      => 3,
            'nodeNumber1'        => 1,
            'nodeNumber2'        => 2,
            'nodeNumber3'        => 3,
            'node1ShardIds'      => [1, 4, 7, 10],
            'node2ShardIds'      => [2, 5, 8, 11],
            'node3ShardIds'      => [3, 6, 9, 12],
        ]
    ],
];
