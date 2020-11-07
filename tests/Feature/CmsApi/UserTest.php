<?php

namespace Tests\Feature\CmsApi;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_user_index_paginate_and_filters_ok()
    {
        Artisan::call('launch:deploy');
        $users = factory(User::class, 10)->create();
        $auth_user = $users->first();
        $search_user = $users->last();
        $auth_user->syncPermissions(['users.index']);

        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->json('GET', route('cmsapi.user.index'), [
                        'page' => 1,
                        'email' => $search_user->email
                    ]);

        $response->assertStatus(200)
                    ->assertJson([
                        'current_page' => 1,
                        'data' => [$search_user->toArray()],
                        'total' => 1
                    ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function get_user_index_paginate_permission_error()
    {
        $users = factory(User::class, 2)->create();
        $auth_user = $users->first();

        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->json('GET', route('cmsapi.user.index'), [
                        'page' => 1,
                        'email' => $users->last()->email
                    ]);

        $response->assertStatus(403);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function post_user_store_ok()
    {
        Artisan::call('launch:deploy');
        $auth_user = factory(User::class)->create();
        $auth_user->syncPermissions(['users.store']);

        $new_user = [
            'firstname' => 'Name_Test',
            'secondname' => 'Secondname_Test',
            'lastname' => 'Lastname_Test',
            'username' => 'username_Test',
            'email' => 'email_test@test.com',
            'password' => Hash::make('password'),
            'image' => '',
            'roles' => [],
            'permissions' => [],
        ];
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->postJson(route('cmsapi.user.store'), $new_user);

        $response->assertStatus(201)
                ->assertJson([
                    'msg' => __('Save successfully'),
                    'user' => [
                        'firstname' => $new_user['firstname'],
                        'secondname' => 'Secondname_Test',
                        'lastname' => 'Lastname_Test',
                        'username' => 'username_Test',
                        'email' => $new_user['email']
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'firstname' => $new_user['firstname'],
            'email' => $new_user['email']
        ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function post_user_store_permission_error()
    {
        $auth_user = factory(User::class)->create();

        $new_user = [
            'firstname' => 'Name_Test',
            'secondname' => 'Secondname_Test',
            'lastname' => 'Lastname_Test',
            'username' => 'username_Test',
            'email' => 'email_test@test.com',
            'password' => Hash::make('password'),
            'image' => '',
            'roles' => [],
            'permissions' => [],
        ];
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->postJson(route('cmsapi.user.store'), $new_user);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('users', [
            'firstname' => $new_user['firstname'],
            'email' => $new_user['email']
        ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function post_user_store_validate_error()
    {
        Artisan::call('launch:deploy');
        $auth_user = factory(User::class)->create();
        $auth_user->syncPermissions(['users.store']);

        $new_user = [
            'firstname' => Str::random(256),
            'secondname' => Str::random(256),
            'username' => 'username_Test',
            'email' => 'email_testtest.com',
            'password' => Hash::make('password'),
            'image' => '',
            'roles' => [],
            'permissions' => [],
        ];
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->postJson(route('cmsapi.user.store'), $new_user);

        $response->assertStatus(422)
                ->assertJson(['message' => 'The given data was invalid.'])
                ->assertJsonStructure([
                    'message',
                    'errors' => [
                        'firstname',
                        'secondname',
                        'lastname',
                        'email'
                    ]
                ]);

        $this->assertDatabaseMissing('users', [
            'firstname' => $new_user['firstname'],
            'email' => $new_user['email']
        ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function post_user_update_ok()
    {
        Artisan::call('launch:deploy');
        $auth_user = factory(User::class)->create();
        $auth_user->syncPermissions(['users.update']);

        $update_user = factory(User::class)->create();
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->postJson(route('cmsapi.user.update', ['user_id' => $update_user->id]), [
                        'firstname' => $update_user->firstname,
                        'secondname' => $update_user->secondname,
                        'lastname' => $update_user->lastname,
                        'username' => 'username_new',
                        'email' => 'email_new@test.com'
                    ]);

        //dd($response->getData());

        $response->assertStatus(200)
                ->assertJson(['msg' => __('Save successfully')])
                ->assertJsonStructure(['msg', 'user']);

        $this->assertDatabaseHas('users', [
            'username' => 'username_new',
            'email' => 'email_new@test.com'
        ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function post_user_update_permission_error()
    {
        $auth_user = factory(User::class)->create();

        $update_user = factory(User::class)->create();
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->postJson(route('cmsapi.user.update', ['user_id' => $update_user->id]), [
                        'firstname' => $update_user->firstname,
                        'secondname' => $update_user->secondname,
                        'lastname' => $update_user->lastname,
                        'username' => 'username_new',
                        'email' => 'email_new@test.com'
                    ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('users', [
            'username' => 'username_new',
            'email' => 'email_new@test.com'
        ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function post_user_update_validate_error()
    {
        Artisan::call('launch:deploy');
        $auth_user = factory(User::class)->create();
        $auth_user->syncPermissions(['users.store']);

        $update_user = factory(User::class)->create();
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->postJson(route('cmsapi.user.store', ['user_id' => $update_user->id]), [
                        'firstname' => Str::random(256),
                        'secondname' => Str::random(256),
                        'username' => 'username_Test',
                        'email' => 'email_testtest.com',
                        'image' => '',
                        'roles' => [],
                        'permissions' => [],
                    ]);

        $response->assertStatus(422)
                ->assertJson(['message' => 'The given data was invalid.'])
                ->assertJsonStructure([
                    'message',
                    'errors' => [
                        'firstname',
                        'secondname',
                        'lastname',
                        'email'
                    ]
                ]);

        $this->assertDatabaseMissing('users', [
            'username' => 'username_Test',
            'email' => 'email_testtest.com'
        ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function post_user_update_missing_user_id_error()
    {
        Artisan::call('launch:deploy');
        $auth_user = factory(User::class)->create();
        $auth_user->syncPermissions(['users.update']);

        $update_user = factory(User::class)->create();
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->postJson(route('cmsapi.user.update', ['user_id' => ($update_user->id + 100)]), [
                        'firstname' => $update_user->firstname,
                        'secondname' => $update_user->secondname,
                        'lastname' => $update_user->lastname,
                        'username' => 'username_new',
                        'email' => 'email_new@test.com'
                    ]);

        //dd($response->getData());

        $response->assertStatus(404)
                    ->assertJsonStructure(['message', 'exception'])
                    ->assertJson([
                            'exception' => "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException",
                        ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function put_user_set_state_activate_ok()
    {
        Artisan::call('launch:deploy');
        $auth_user = factory(User::class)->create();
        $auth_user->syncPermissions(['users.activate']);

        $update_user = factory(User::class)->create(['state' => 'I']);
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->putJson(route('cmsapi.user.set-state', ['user_id' => $update_user->id]), [
                        'state' => 'A'
                    ]);

        $update_user->refresh();
        $response->assertStatus(200)
                ->assertJson([
                    'msg' => __('User :state successfully', ['state'=>__($update_user->state == 'A' ? 'activated' : 'deactivated')]),
                    'user' => $update_user->toArray()
                ]);

        $this->assertDatabaseHas('users', [
            'email' => $update_user->email,
            'state' => 'A'
        ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function put_user_set_state_deactivate_ok()
    {
        Artisan::call('launch:deploy');
        $auth_user = factory(User::class)->create();
        $auth_user->syncPermissions(['users.deactivate']);

        $update_user = factory(User::class)->create(['state' => 'A']);
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->putJson(route('cmsapi.user.set-state', ['user_id' => $update_user->id]), [
                        'state' => 'I'
                    ]);

        $update_user->refresh();
        $response->assertStatus(200)
                ->assertJson([
                    'msg' => __('User :state successfully', ['state'=>__($update_user->state == 'A' ? 'activated' : 'deactivated')]),
                    'user' => $update_user->toArray()
                ]);

        $this->assertDatabaseHas('users', [
            'email' => $update_user->email,
            'state' => 'I'
        ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function put_user_set_state_permission_error()
    {
        $auth_user = factory(User::class)->create();

        $update_user = factory(User::class)->create();
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->putJson(route('cmsapi.user.set-state', ['user_id' => $update_user->id]), [
                        'state' => 'I'
                    ]);

        $response->assertStatus(403)
                ->assertJsonStructure([
                    'message'
                ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function put_user_set_state_validate_error()
    {
        Artisan::call('launch:deploy');
        $auth_user = factory(User::class)->create();
        $auth_user->syncPermissions(['users.deactivate']);

        $update_user = factory(User::class)->create(['state' => 'A']);
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->putJson(route('cmsapi.user.set-state', ['user_id' => $update_user->id]), [
                        'state' => 'Q'
                    ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'message',
                    'errors' => [
                        'state'
                    ]
                ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function put_user_set_state_can_activate_gate_error()
    {
        Artisan::call('launch:deploy');
        $auth_user = factory(User::class)->create();
        $auth_user->syncPermissions(['users.deactivate']);

        $update_user = factory(User::class)->create(['state' => 'I']);
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->putJson(route('cmsapi.user.set-state', ['user_id' => $update_user->id]), [
                        'state' => 'A'
                    ]);

        $response->assertStatus(403)
                ->assertJson(['msg_error' => __('Forbidden access')]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function put_user_set_state_can_deactivate_gate_error()
    {
        Artisan::call('launch:deploy');
        $auth_user = factory(User::class)->create();
        $auth_user->syncPermissions(['users.activate']);

        $update_user = factory(User::class)->create(['state' => 'A']);
        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->putJson(route('cmsapi.user.set-state', ['user_id' => $update_user->id]), [
                        'state' => 'I'
                    ]);

        $response->assertStatus(403)
                ->assertJson(['msg_error' => __('Forbidden access')]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function put_user_set_state_missing_user_id_error()
    {
        Artisan::call('launch:deploy');
        $auth_user = factory(User::class)->create();
        $auth_user->syncPermissions(['users.activate']);

        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                    ->putJson(route('cmsapi.user.set-state', ['user_id' => ($auth_user->id+100)]), [
                        'state' => 'A'
                    ]);

        $response->assertStatus(404)
                    ->assertJsonStructure(['message', 'exception'])
                    ->assertJson([
                            'exception' => "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException",
                        ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function get_user_show_ok()
    {
        $auth_user = factory(User::class)->create();

        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                        ->json('GET', route('cmsapi.user.show', ['user_id' => $auth_user->id]));

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $auth_user->id,
                    'firstname' => $auth_user->firstname,
                    'secondname' => $auth_user->secondname,
                    'lastname' => $auth_user->lastname,
                    'username' => $auth_user->username,
                    'email' => $auth_user->email,
                    'state' => $auth_user->state,
                ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function get_user_show_missing_user_id_error()
    {
        $auth_user = factory(User::class)->create();

        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                        ->json('GET', route('cmsapi.user.show', ['user_id' => ($auth_user->id + 100)]));

        $response->assertStatus(404)
                    ->assertJsonStructure(['message', 'exception'])
                    ->assertJson([
                            'exception' => "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException",
                        ]);

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function get_user_get_permissions_ok()
    {
        Artisan::call('launch:deploy');
        $auth_user = factory(User::class)->create();
        $auth_user->syncPermissions(['users.index', 'users.store']);

        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                        ->json('GET', route('cmsapi.user.get-permissions', ['user_id' => $auth_user->id]));

        $response->assertStatus(200)
                ->assertJson($auth_user->getAllPermissions()->toArray());

        $this->assertAuthenticatedAs($auth_user);
    }

    /** @test */
    public function get_user_get_permissions_missing_user_id_error()
    {
        $auth_user = factory(User::class)->create();

        $response = $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest',])->actingAs($auth_user)
                        ->json('GET', route('cmsapi.user.get-permissions', ['user_id' => ($auth_user->id + 100)]));

        $response->assertStatus(404)
                    ->assertJsonStructure(['message', 'exception'])
                    ->assertJson([
                            'exception' => "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException",
                        ]);

        $this->assertAuthenticatedAs($auth_user);
    }
}
