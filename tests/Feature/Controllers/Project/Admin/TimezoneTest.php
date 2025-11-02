<?php

namespace Tests\Feature\Controllers\Project\Admin;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Database\Testsupport\Authentication;
use Tests\TestCase;

class TimezoneTest extends TestCase
{
    use RefreshDatabase, Authentication;
    
    public function test_admin_can_get_timezone_list(): void
    {
        $this->seed([
            \Database\Seeders\Thesaurus\TimezoneSeeder::class,
        ]);
        $this->seedUsers();
        
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->getJson(RouteServiceProvider::URL_ADMIN_TIMEZONE."?name=nov");

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has(4)
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('name', 'Africa/Porto-Novo')
                            ->has('id')
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('name', 'Asia/Novokuznetsk')
                            ->has('id')
                            ->etc()
                    )
                    ->has('2', fn (AssertableJson $json) =>
                        $json->where('name', 'Asia/Novosibirsk')
                            ->has('id')
                            ->etc()
                    )
                    ->has('3', fn (AssertableJson $json) =>
                        $json->where('name', 'Europe/Ulyanovsk')
                            ->has('id')
                            ->etc()
                    )
            );
    }
}
