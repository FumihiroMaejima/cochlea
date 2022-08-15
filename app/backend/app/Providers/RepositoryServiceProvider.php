<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Admins\AdminsRepository;
use App\Repositories\Admins\AdminsRepositoryInterface;
use App\Repositories\Admins\AdminsRoles\AdminsRolesRepository;
use App\Repositories\Admins\AdminsRoles\AdminsRolesRepositoryInterface;
use App\Repositories\Admins\Coins\CoinsRepository;
use App\Repositories\Admins\Coins\CoinsRepositoryInterface;
use App\Repositories\Admins\Images\ImagesRepository;
use App\Repositories\Admins\Images\ImagesRepositoryInterface;
use App\Repositories\Admins\Permissions\PermissionsRepository;
use App\Repositories\Admins\Permissions\PermissionsRepositoryInterface;
use App\Repositories\Admins\RolePermissions\RolePermissionsRepository;
use App\Repositories\Admins\RolePermissions\RolePermissionsRepositoryInterface;
use App\Repositories\Admins\Roles\RolesRepository;
use App\Repositories\Admins\Roles\RolesRepositoryInterface;
use App\Repositories\Logs\UserCoinPaymentLog\UserCoinPaymentLogRepository;
use App\Repositories\Logs\UserCoinPaymentLog\UserCoinPaymentLogRepositoryInterface;
use App\Repositories\Users\UserCoinPaymentStatus\UserCoinPaymentStatusRepository;
use App\Repositories\Users\UserCoinPaymentStatus\UserCoinPaymentStatusRepositoryInterface;
use App\Repositories\Users\UserCoinHistories\UserCoinHistoriesRepository;
use App\Repositories\Users\UserCoinHistories\UserCoinHistoriesRepositoryInterface;
use App\Repositories\Users\UserCoins\UserCoinsRepository;
use App\Repositories\Users\UserCoins\UserCoinsRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AdminsRepositoryInterface::class, AdminsRepository::class);
        $this->app->bind(AdminsRolesRepositoryInterface::class, AdminsRolesRepository::class);
        $this->app->bind(CoinsRepositoryInterface::class, CoinsRepository::class);
        $this->app->bind(ImagesRepositoryInterface::class, ImagesRepository::class);
        $this->app->bind(PermissionsRepositoryInterface::class, PermissionsRepository::class);
        $this->app->bind(RolePermissionsRepositoryInterface::class, RolePermissionsRepository::class);
        $this->app->bind(RolesRepositoryInterface::class, RolesRepository::class);
        $this->app->bind(UserCoinPaymentLogRepositoryInterface::class, UserCoinPaymentLogRepository::class);
        $this->app->bind(UserCoinPaymentStatusRepositoryInterface::class, UserCoinPaymentStatusRepository::class);
        $this->app->bind(UserCoinHistoriesRepositoryInterface::class, UserCoinHistoriesRepository::class);
        $this->app->bind(UserCoinsRepositoryInterface::class, UserCoinsRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
