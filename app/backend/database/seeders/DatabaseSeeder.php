<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(Masters\AdminsTableSeeder::class);
        // $this->call(Logs\AdminsLogTableSeeder::class);
        $this->call(Masters\BannersTableSeeder::class);
        $this->call(Masters\BannerBlockContentsTableSeeder::class);
        $this->call(Masters\BannerBlocksTableSeeder::class);
        $this->call(Masters\CoinsTableSeeder::class);
        $this->call(Masters\EventsTableSeeder::class);
        $this->call(Masters\HomeContentsGroupsTableSeeder::class);
        $this->call(Masters\HomeContentsTableSeeder::class);
        $this->call(Masters\InformationsTableSeeder::class);
        $this->call(Masters\ManufacturersTableSeeder::class);
        $this->call(Masters\ProductsTableSeeder::class);
        $this->call(Masters\PermissionsTableSeeder::class);
        $this->call(Masters\QuestionnairesTableSeeder::class);
        $this->call(Masters\RolesTableSeeder::class);
        $this->call(Masters\RolePermissionsTableSeeder::class);
        $this->call(Masters\AdminsRolesTableSeeder::class);
        $this->call(Masters\ServiceTermsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
