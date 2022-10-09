<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        // port設定がある為、ローカルではこける。
        $response = $this->get('/');
        // $response = $this->get(route('api.sample.test.route'));

        $response->assertStatus(200);
    }
}
