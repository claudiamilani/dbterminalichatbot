<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\AppConfiguration;
use App\Auth\AuthType;
use App\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCreateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        User::reguard();
    }

    /** @test */
    public function an_admin_can_create_a_user_account(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $response->assertRedirectToRoute('admin::users.index');

        $this->assertDatabaseHas('users', [
            'user' => 'rossi@medialogic.it'
        ]);
    }

    /** @test */
    public function a_user_requires_a_name(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->post(route('admin::users.store'), array_merge($this->data(), ['name' => '', 'password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function a_user_requires_a_surname(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->post(route('admin::users.store'), array_merge($this->data(), ['surname' => '', 'password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $response->assertSessionHasErrors('surname');
    }

    /** @test */
    public function a_user_requires_an_email(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->post(route('admin::users.store'), array_merge($this->data(), ['email' => '', 'password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function a_user_requires_a_username(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->post(route('admin::users.store'), array_merge($this->data(), ['user' => '', 'password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $response->assertSessionHasErrors('user');
    }

    /** @test */
    public function a_user_requires_a_password_if_pwd_change_required_is_false(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => null, 'password_check' => null, 'pwd_change_required' => 0]))->assertStatus(302);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function a_user_does_not_require_a_password_if_pwd_change_required_is_true(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => null, 'password_check' => null, 'pwd_change_required' => 1]))->assertStatus(302);

        $response->assertSessionDoesntHaveErrors();
    }

    /** @test */
    public function a_username_is_unique(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->post(route('admin::users.store'), array_merge($this->data(), ['user' => 'admin', 'password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $response->assertSessionHasErrors(['user' => trans('validation.unique', ['attribute' => 'Utente'])]);
    }

    /** @test */
    public function an_email_is_valid(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->post(route('admin::users.store'), array_merge($this->data(), ['email' => '123', 'password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $response->assertSessionHasErrors(['email' => trans('validation.email', ['attribute' => 'Email'])]);
    }

    /** @test */
    public function password_is_complex(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => 'password', 'password_check' => 'password']))->assertStatus(302);

        $response->assertSessionHasErrors(['password' => AppConfiguration::current()->pwd_complexity_err_msg]);
    }

    /** @test */
    public function password_is_the_same_as_password_check(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => 'M3dialogic$', 'password_check' => 'M3dialogic.']))->assertStatus(302);

        $response->assertSessionHasErrors(['password' => trans('validation.custom.password.same')]);
    }

    private function data(): array
    {
        return [
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'rossi@medialogic.it',
            'user' => 'rossi@medialogic.it',
            'roles' => [3],
            'auth_type_id' => AuthType::LOCAL
        ];
    }
}
