<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // port設定がある為、ローカルではこける。
        // $response = $this->get(route('api.sample.test.route'));
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
