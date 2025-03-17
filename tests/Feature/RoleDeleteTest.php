<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\Auth\Role;
use App\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Role::reguard();
    }

    /** @test */
    public function an_admin_can_delete_a_role(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $this->post(route('admin::roles.store'), $this->data())->assertStatus(302);

        $role = Role::where('name', 'test')->first();

        $response = $this->delete(route('admin::roles.destroy', $role->id))->assertStatus(302);

        $response->assertRedirectToRoute('admin::roles.index');

        $this->assertDatabaseMissing('roles', [
            'name' => 'test'
        ]);
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
