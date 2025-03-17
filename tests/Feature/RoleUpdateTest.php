<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\Auth\Role;
use App\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Role::reguard();
    }

    /** @test */
    public function an_admin_can_update_a_role(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->post(route('admin::roles.store'), $this->data())->assertStatus(302);

        $response->assertRedirectToRoute('admin::roles.index');

        $role = Role::where('name', 'test')->first();

        $response = $this->patch(route('admin::roles.update', $role->id), array_merge($this->data(), ['name' => 'test 123']))->assertStatus(302);

        $response->assertRedirectToRoute('admin::roles.index');

        $this->assertDatabaseHas('roles', [
            'name' => 'test 123'
        ]);
    }

    /** @test */
    public function a_role_requires_a_name(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $this->post(route('admin::roles.store'), array_merge($this->data(), ['name' => 'test']))->assertStatus(302);

        $role = Role::where('name', 'test')->first();

        $response = $this->patch(route('admin::roles.update', $role->id), array_merge($this->data(), ['name' => '']))->assertStatus(302);

        $response->assertSessionHasErrors('name');
    }

    private function data(): array
    {
        return [
            'name' => 'test',
            'types' => [1],
            'permissions' => [1,2,3]
        ];
    }
}
