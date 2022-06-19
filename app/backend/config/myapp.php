<?php

return [
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
