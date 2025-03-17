<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\AppConfiguration;
use App\Auth\Role;
use App\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_cannot_list_users(): void
    {
        $user = User::factory()->make();

        $this->actingAs($user, 'admins')->get(route('admin::users.index'))->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_edit_other_users(): void
    {
        $mario = User::factory()->create();

        $luigi = User::factory()->create();

        $this->actingAs($mario, 'admins')->get(route('admin::users.edit', $luigi->id))->assertStatus(403);
    }

    /** @test */
    public function a_user_can_edit_their_own_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'admins')->get(route('admin::users.edit', $user->id))->assertStatus(200);
    }

    /** @test */
    public function an_admin_can_list_users(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins')->get(route('admin::users.index'))->assertStatus(200);
    }

    /** @test */
    public function an_admin_can_view_a_user(): void
    {
        $admin = User::first();

        $user = User::factory()->create();

        $this->actingAs($admin, 'admins')->get(route('admin::users.edit', $user->id))->assertStatus(200);
    }

    /** @test */
    public function a_user_cannot_list_roles(): void
    {
        $user = User::factory()->make();

        $this->actingAs($user, 'admins')->get(route('admin::roles.index'))->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_list_pwd_resets(): void
    {
        $user = User::factory()->make();

        $this->actingAs($user, 'admins')->get(route('admin::pending_pwd_resets.index'))->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_edit_a_role(): void
    {
        $user = User::factory()->make();

        $this->actingAs($user, 'admins')->get(route('admin::roles.edit', Role::find(1)->id))->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_update_a_role(): void
    {
        $user = User::factory()->make();

        $this->actingAs($user, 'admins')->patch(route('admin::roles.update', Role::find(1)->id))->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_view_app_configuration(): void
    {
        $user = User::factory()->make();

        $this->actingAs($user, 'admins')->get(route('admin::app_configuration.show', AppConfiguration::current()->id))->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_edit_app_configuration(): void
    {
        $user = User::factory()->make();

        $this->actingAs($user, 'admins')->get(route('admin::app_configuration.edit', AppConfiguration::current()->id))->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_update_app_configuration(): void
    {
        $user = User::factory()->make();

        $this->actingAs($user, 'admins')->patch(route('admin::app_configuration.update', AppConfiguration::current()->id))->assertStatus(403);
    }

    /** @test */
    public function an_admin_can_list_roles(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins')->get(route('admin::roles.index'))->assertStatus(200);
    }

    /** @test */
    public function an_admin_can_list_pwd_resets(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins')->get(route('admin::pending_pwd_resets.index'))->assertStatus(200);
    }

    /** @test */
    public function an_admin_can_view_app_configuration(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins')->get(route('admin::app_configuration.show', AppConfiguration::current()->id))->assertStatus(200);
    }

    /** @test */
    public function an_admin_can_update_app_configuration(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins')->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['allow_pwd_reset' => 1]))->assertStatus(302);

        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 1
        ]);
    }

    /** @test */
    public function an_admin_can_edit_a_role(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins')->get(route('admin::roles.edit', Role::find(1)->id))->assertStatus(200);
    }

    private function data(): array
    {
        return AppConfiguration::current(true)->toArray();
    }
}
