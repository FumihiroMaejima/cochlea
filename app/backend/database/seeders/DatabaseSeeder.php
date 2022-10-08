<?php

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
        $this->call(Masters\CoinsTableSeeder::class);
        $this->call(Masters\EventsTableSeeder::class);
        $this->call(Masters\InformationsTableSeeder::class);
        $this->call(Masters\ManufacturersTableSeeder::class);
        $this->call(Masters\ProductsTableSeeder::class);
        $this->call(Masters\PermissionsTableSeeder::class);
        $this->call(Masters\RolesTableSeeder::class);
        $this->call(Masters\RolePermissionsTableSeeder::class);
        $this->call(Masters\AdminsRolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
