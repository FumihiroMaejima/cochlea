<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Admins\AdminsRepository;
use App\Repositories\Admins\AdminsRepositoryInterface;
use App\Repositories\Admins\Permissions\PermissionsRepository;
use App\Repositories\Admins\Permissions\PermissionsRepositoryInterface;
use App\Repositories\Admins\RolePermissions\RolePermissionsRepository;
use App\Repositories\Admins\RolePermissions\RolePermissionsRepositoryInterface;
use App\Repositories\Admins\Roles\RolesRepository;
use App\Repositories\Admins\Roles\RolesRepositoryInterface;

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
        $this->app->bind(PermissionsRepositoryInterface::class, PermissionsRepository::class);
        $this->app->bind(RolePermissionsRepositoryInterface::class, RolePermissionsRepository::class);
        $this->app->bind(RolesRepositoryInterface::class, RolesRepository::class);
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
