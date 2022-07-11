<?php

return [
    'apiKey' => [
        'public'  => env('STRIPE_PUBLIC_KEY'),
        'private' => env('STRIPE_SECRET_KEY'),
    ],
];
