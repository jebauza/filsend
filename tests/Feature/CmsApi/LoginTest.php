<?php

namespace Tests\Feature\CmsApi;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function post_cmsapi_auth_login_ok()
    {
        $user = factory(User::class)->create(['password' => Hash::make('password')]);

        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])
                            ->postJson(route('cmsapi.auth.login'), [
                                'email' => $user->email,
                                'password' => 'password',
                            ]);

        $response->assertStatus(200)
                    ->assertJson([
                        'msg' => __('Successfully authenticated'),
                        'authUser' => $user->load('permissions')->toArray(),
                        'userPermissions' => $user->getAllPermissions()->toArray()
                    ]);

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function post_cmsapi_auth_login_error_msg()
    {
        $user = factory(User::class)->create(['password' => Hash::make('password')]);

        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])
                            ->postJson(route('cmsapi.auth.login'), [
                                'email' => $user->email,
                                'password' => 'wrong_password',
                            ]);

        $response->assertStatus(422)
                    ->assertJson(['errors' => ['email' => [__('auth.failed')]]]);
    }

    /** @test */
    public function get_cmsapi_auth_logout_ok()
    {
        $user = factory(User::class)->create(['password' => Hash::make('password')]);

        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])
                            ->actingAs($user)->getJson(route('cmsapi.auth.logout'));

        $response->assertStatus(204);

        $this->assertGuest();
    }

    /** @test */
    public function get_refresh_auth_user_ok()
    {
        $user = factory(User::class)->create(['password' => Hash::make('password')]);

        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])
                            ->actingAs($user)->getJson(route('cmsapi.auth.get-refresh-auth-user'));

        $response->assertStatus(200)
                    ->assertJson([
                        'authUser' => $user->toArray(),
                        'userPermissions' => $user->getAllPermissions()->pluck('name')->toArray()
                    ]);

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function get_cmsapi_not_ajax_request_error()
    {
        $auth_user = factory(User::class)->create(['password' => Hash::make('password')]);

        $response = $this->actingAs($auth_user)->getJson(route('cmsapi.auth.get-refresh-auth-user'));

        $response->assertStatus(302);

        $this->assertAuthenticatedAs($auth_user);
    }
}
