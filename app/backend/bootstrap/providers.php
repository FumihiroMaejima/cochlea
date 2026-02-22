<?php

return [
    App\Providers\AppServiceProvider::class,

    // custom service provider
    App\Providers\RepositoryServiceProvider::class,
    App\Providers\DataBaseQueryServiceProvider::class,

    Maatwebsite\Excel\ExcelServiceProvider::class,
    Laravel\Socialite\SocialiteServiceProvider::class,
];
