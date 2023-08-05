<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Masters\AdminsRepository;
use App\Repositories\Masters\AdminsRepositoryInterface;
use App\Repositories\Masters\AdminsRoles\AdminsRolesRepository;
use App\Repositories\Masters\AdminsRoles\AdminsRolesRepositoryInterface;
use App\Repositories\Masters\Banners\BannersBlockContentsRepository;
use App\Repositories\Masters\Banners\BannersBlockContentsRepositoryInterface;
use App\Repositories\Masters\Banners\BannersBlocksRepository;
use App\Repositories\Masters\Banners\BannersBlocksRepositoryInterface;
use App\Repositories\Masters\Banners\BannersRepository;
use App\Repositories\Masters\Banners\BannersRepositoryInterface;
use App\Repositories\Masters\Coins\CoinsRepository;
use App\Repositories\Masters\Coins\CoinsRepositoryInterface;
use App\Repositories\Masters\Contacts\ContactsRepository;
use App\Repositories\Masters\Contacts\ContactsRepositoryInterface;
use App\Repositories\Masters\Events\EventsRepository;
use App\Repositories\Masters\Events\EventsRepositoryInterface;
use App\Repositories\Masters\HomeContents\HomeContentsGroupsRepository;
use App\Repositories\Masters\HomeContents\HomeContentsGroupsRepositoryInterface;
use App\Repositories\Masters\HomeContents\HomeContentsRepository;
use App\Repositories\Masters\HomeContents\HomeContentsRepositoryInterface;
use App\Repositories\Masters\Images\ImagesRepository;
use App\Repositories\Masters\Images\ImagesRepositoryInterface;
use App\Repositories\Masters\Informations\InformationsRepository;
use App\Repositories\Masters\Informations\InformationsRepositoryInterface;
use App\Repositories\Masters\Permissions\PermissionsRepository;
use App\Repositories\Masters\Permissions\PermissionsRepositoryInterface;
use App\Repositories\Masters\RolePermissions\RolePermissionsRepository;
use App\Repositories\Masters\RolePermissions\RolePermissionsRepositoryInterface;
use App\Repositories\Masters\Roles\RolesRepository;
use App\Repositories\Masters\Roles\RolesRepositoryInterface;
use App\Repositories\Masters\ServiceTerms\ServiceTermsRepository;
use App\Repositories\Masters\ServiceTerms\ServiceTermsRepositoryInterface;
use App\Repositories\Logs\UserCoinPaymentLog\UserCoinPaymentLogRepository;
use App\Repositories\Logs\UserCoinPaymentLog\UserCoinPaymentLogRepositoryInterface;
use App\Repositories\Users\UserAuthCodes\UserAuthCodesRepository;
use App\Repositories\Users\UserAuthCodes\UserAuthCodesRepositoryInterface;
use App\Repositories\Users\UserCoinPaymentStatus\UserCoinPaymentStatusRepository;
use App\Repositories\Users\UserCoinPaymentStatus\UserCoinPaymentStatusRepositoryInterface;
use App\Repositories\Users\UserCoinHistories\UserCoinHistoriesRepository;
use App\Repositories\Users\UserCoinHistories\UserCoinHistoriesRepositoryInterface;
use App\Repositories\Users\UserCoins\UserCoinsRepository;
use App\Repositories\Users\UserCoins\UserCoinsRepositoryInterface;
use App\Repositories\Users\UserReadInformations\UserReadInformationsRepository;
use App\Repositories\Users\UserReadInformations\UserReadInformationsRepositoryInterface;
use App\Repositories\Users\Users\UsersRepository;
use App\Repositories\Users\Users\UsersRepositoryInterface;

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
        $this->app->bind(BannersBlockContentsRepositoryInterface::class, BannersBlockContentsRepository::class);
        $this->app->bind(BannersBlocksRepositoryInterface::class, BannersBlocksRepository::class);
        $this->app->bind(BannersRepositoryInterface::class, BannersRepository::class);
        $this->app->bind(CoinsRepositoryInterface::class, CoinsRepository::class);
        $this->app->bind(ContactsRepositoryInterface::class, ContactsRepository::class);
        $this->app->bind(EventsRepositoryInterface::class, EventsRepository::class);
        $this->app->bind(HomeContentsGroupsRepositoryInterface::class, HomeContentsGroupsRepository::class);
        $this->app->bind(HomeContentsRepositoryInterface::class, HomeContentsRepository::class);
        $this->app->bind(ImagesRepositoryInterface::class, ImagesRepository::class);
        $this->app->bind(InformationsRepositoryInterface::class, InformationsRepository::class);
        $this->app->bind(PermissionsRepositoryInterface::class, PermissionsRepository::class);
        $this->app->bind(RolePermissionsRepositoryInterface::class, RolePermissionsRepository::class);
        $this->app->bind(RolesRepositoryInterface::class, RolesRepository::class);
        $this->app->bind(ServiceTermsRepositoryInterface::class, ServiceTermsRepository::class);
        $this->app->bind(UserCoinPaymentLogRepositoryInterface::class, UserCoinPaymentLogRepository::class);
        $this->app->bind(UserAuthCodesRepositoryInterface::class, UserAuthCodesRepository::class);
        $this->app->bind(UserCoinPaymentStatusRepositoryInterface::class, UserCoinPaymentStatusRepository::class);
        $this->app->bind(UserCoinHistoriesRepositoryInterface::class, UserCoinHistoriesRepository::class);
        $this->app->bind(UserCoinsRepositoryInterface::class, UserCoinsRepository::class);
        $this->app->bind(UserReadInformationsRepositoryInterface::class, UserReadInformationsRepository::class);
        $this->app->bind(UsersRepositoryInterface::class, UsersRepository::class);
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
