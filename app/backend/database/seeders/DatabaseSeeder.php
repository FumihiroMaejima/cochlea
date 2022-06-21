<?php

namespace Database\Seeders;

use App\Models\RolePermissions;
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
        $this->call(AdminsTableSeeder::class);
         // $this->call(AdminsLogTableSeeder::class);
        $this->call(Master\ManufacturersTableSeeder::class);
        $this->call(Master\ProductsTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(RolePermissionsTableSeeder::class);
        $this->call(AdminsRolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
