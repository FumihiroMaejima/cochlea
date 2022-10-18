<?php

namespace Tests\Feature\Service\Admins;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\ServiceBaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use App\Library\Message\StatusCodeMessages;
use App\Http\Requests\BaseRequest;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\DatabaseMigrations;

class PermissionsServiceTest extends ServiceBaseTestCase
{

    /**
     * roles get request test.
     *
     * @return void
     */
    public function testGetPermissionsList(): void
    {
        $response = $this->get(route('admin.permissions.list'));
        $response->assertStatus(StatusCodeMessages::STATUS_200)
            ->assertJsonCount(4, BaseRequest::RESPONSE_KEY_DATA);
    }
}
