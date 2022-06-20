<?php

namespace Tests;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Trait\HelperTrait;
use Database\Seeders\AdminsTableSeeder;
use Database\Seeders\AdminsRolesTableSeeder;
use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\RolePermissionsTableSeeder;
use Database\Seeders\RolesTableSeeder;

/**
 * Serviceクラスのテスト用Baseクラス
 */
class ServiceBaseTestCase extends TestCase
{
    use HelperTrait;

    protected $initialized = false;

    /**
     * 初期化処理
     *
     * @return array
     */
    protected function init(): array
    {
        // $this->refreshDatabase();
        // $this->refreshTestDatabase();
        // $this->runDatabaseMigrations();

        $this->artisan('migrate:fresh');
        $this->seed([
            AdminsTableSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            RolePermissionsTableSeeder::class,
            AdminsRolesTableSeeder::class,
        ]);

        $response = $this->json('POST', route('auth.admin.login'), [
            'email'    => Config::get('myappTest.test.admin.login.email'),
            'password' => Config::get('myappTest.test.admin.login.password')
        ], ['Content-Type' => 'application/json'])->json();

        return [
            'token'          => $response['access_token'],
            'user_id'        => $response['user']['id'],
            'user_authority' => $response['user']['authority']
        ];
    }

    /**
     * setUpは各テストメソッドが実行される前に実行する
     * 親クラスのsetUpを必ず実行する
     */
    protected function setUp(): void
    {
        parent::setUp();
    }
}
