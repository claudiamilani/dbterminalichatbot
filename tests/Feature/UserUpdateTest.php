<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\Auth\AuthType;
use App\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function  setUp(): void
    {
        parent::setUp();

        User::reguard();
    }

    /** @test */
    public function an_admin_can_update_a_user_account(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $user = User::where('user', 'rossi@medialogic.it')->first();

        $response = $this->patch(route('admin::users.update', $user->id), array_merge($this->data(), ['user' => 'rossi', 'password_change_required' => 0]))->assertStatus(302);

        $response->assertRedirectToRoute('admin::users.index');

        $this->assertDatabaseHas('users', [
            'user' => 'rossi'
        ]);
    }

    /** @test */
    public function a_user_requires_a_name(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $user = User::where('user', 'rossi@medialogic.it')->first();

        $response = $this->patch(route('admin::users.update', $user->id), array_merge($this->data(), ['name' => '', 'password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function a_user_requires_a_surname(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $user = User::where('user', 'rossi@medialogic.it')->first();

        $response = $this->patch(route('admin::users.update', $user->id), array_merge($this->data(), ['surname' => '', 'password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $response->assertSessionHasErrors('surname');
    }

    /** @test */
    public function a_user_requires_an_email(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $user = User::where('user', 'rossi@medialogic.it')->first();

        $response = $this->patch(route('admin::users.update', $user->id), array_merge($this->data(), ['email' => '', 'password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function a_user_requires_a_username(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $user = User::where('user', 'rossi@medialogic.it')->first();

        $response = $this->patch(route('admin::users.update', $user->id), array_merge($this->data(), ['user' => '', 'password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $response->assertSessionHasErrors('user');
    }

    /** @test */
    public function a_user_can_change_their_password(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $user = User::where('user', 'rossi@medialogic.it')->first();

        $response = $this->patch(route('admin::users.update', $user->id), array_merge($this->data(), ['current_password' => 'M3dialogic$', 'password' => 'Password123!', 'password_check' => 'Password123!']))->assertStatus(302);

        $response->assertRedirectToRoute('admin::users.index');
    }

    /** @test */
    public function password_has_not_already_been_used(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $user = User::where('user', 'rossi@medialogic.it')->first();

        $response = $this->patch(route('admin::users.update', $user->id), array_merge($this->data(), ['current_password' => 'M3dialogic$', 'password' => 'Password123!', 'password_check' => 'Password123!']))->assertStatus(302);

        $response->assertRedirectToRoute('admin::users.index');

        $response = $this->patch(route('admin::users.update', $user->id), array_merge($this->data(), ['current_password' => 'Password123!', 'password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $response->assertSessionHasErrors(['password' => trans('auth.pwd_history_error')]);
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
