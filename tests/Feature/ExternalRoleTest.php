<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\Auth\AuthType;
use App\Auth\ExternalRole;
use App\Auth\Permission;
use App\Auth\Role;
use App\Auth\User;
use App\LftRouting\RoutingManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExternalRoleTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        ExternalRole::create(['auth_type_id' => AuthType::AZURE,'external_role_id'=>'EXTERNAL ROLE','auto_register_users'=>1]);
    }



    /**
     * A basic feature test example.
     */
    /** @test */
    public function a_user_with_permission_can_see_external_roles_index()
    {

        $user = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'mario@medialogic.it',
            'password' => 'M3dialogic$',
            'user' => 'mario@medialogic.it',
            'auth_type_id' => AuthType::LOCAL,
            'enabled' => 1
        ]);

        $role = Role::findOrFail(3);
        $p1 = Permission::where('name', 'list_external_roles')->first();
        $role->permissions()->sync([$p1->id]);
        $user->roles()->save(Role::findOrFail(3));
        app()->getProvider(\App\Providers\AuthServiceProvider::class)->registerPermissions();

        $this->actingAs($user, 'admins');
        $this->assertAuthenticated('admins');

        $response = $this->get(route(RoutingManager::adminAlias() . 'external_roles.index'));
        $response->assertOk();
    }

    /** @test */
    public function a_user_without_permission_can_not_see_external_roles_index()
    {

        $user = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'mario@medialogic.it',
            'user' => 'mario@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);


        $user->assignRole('Registered User');
        app()->getProvider(\App\Providers\AuthServiceProvider::class)->registerPermissions();

        $this->actingAs($user, 'admins');
        $this->get(route(RoutingManager::adminAlias() . 'auth_types.index'))->assertStatus(403);

    }

    /** @test */
    public function a_user_with_permission_can_create_external_role()
    {
        $user = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'mario@medialogic.it',
            'user' => 'mario@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);

        $this->actingAs($user, 'admins');

        $role = Role::findOrFail(3);
        $p1 = Permission::where('name', 'create_external_roles')->first();
        $role->permissions()->sync([$p1->id]);
        $user->assignRole('Registered User');
        app()->getProvider(\App\Providers\AuthServiceProvider::class)->registerPermissions();

        $this->actingAs($user, 'admins');
        $this->assertAuthenticated('admins');

        $external_role = ['auth_type_id'=>AuthType::AZURE, 'external_role_id'=>'TEST_EXTERNAL_ROLE', 'roles'=>1, 'auto_register_users'=>1];

        $response = $this->get(route(RoutingManager::adminAlias() . 'external_roles.create'));
        $response->assertSee(trans('external_roles.create.title'));
        $response = $this->post(route(RoutingManager::adminAlias() . 'external_roles.store', $external_role));

        $this->assertTrue((bool)$external_role = ExternalRole::where('external_role_id','TEST_EXTERNAL_ROLE')->first());

        $this->assertDatabaseHas('external_role_role', [
            'external_role_id' => $external_role->id,
            'role_id' => 1

        ]);
        $response->assertSessionHas('alerts', [
            ["message" => trans('external_roles.create.success'), 'type' => 'success']
        ]);
    }

    /** @test */
    public function a_user_without_permission_can_not_create_external_role()
    {
        $user = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'mario@medialogic.it',
            'user' => 'mario@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);

        $this->actingAs($user, 'admins');

        $user->assignRole('Registered User');
        app()->getProvider(\App\Providers\AuthServiceProvider::class)->registerPermissions();

        $this->actingAs($user, 'admins');
        $this->assertAuthenticated('admins');
        $external_role = ['auth_type_id'=>AuthType::AZURE, 'external_role_id'=>'TEST_EXTERNAL_ROLE', 'roles'=>1, 'auto_register_users'=>1];
        $this->get(route(RoutingManager::adminAlias() . 'external_roles.create'))->assertStatus(403);
        $this->post(route(RoutingManager::adminAlias() . 'external_roles.store', $external_role))->assertStatus(403);
        $this->assertDatabaseMissing('external_roles', [
            'external_role_id' => 'TEST_EXTERNAL_ROLE'
        ]);
        $this->assertDatabaseMissing('external_role_role', [
            'external_role_id' => 5,
            'role_id' => 1
        ]);

    }

    /** @test */
    public function a_user_with_permissions_can_update_external_role()
    {
        $user = User::first();
        $this->actingAs($user, 'admins');
        $external_role = ExternalRole::first();
        $external_role->external_role_id = 'TEST_EXTERNAL_ROLE';
        $response = $this->patch(route(RoutingManager::adminAlias() . 'external_roles.update', array_merge($external_role->toArray(),['roles'=>3])));
        $this->assertDatabaseHas('external_roles', [
            'external_role_id' => 'TEST_EXTERNAL_ROLE',
        ]);

        $response->assertSessionHas('alerts', [
            ["message" => trans('external_roles.edit.success'), 'type' => 'success']
        ]);
    }

    /** @test */
    public function a_user_with_permission_can_delete_external_role()
    {
        $user = User::first();
        $this->actingAs($user, 'admins');
        $external_role = ExternalRole::first();
        $response = $this->delete(route(RoutingManager::adminAlias() . 'external_roles.destroy', $external_role->id));
        $this->assertDatabaseMissing('external_roles', [
            'external_role_id' => $external_role->external_role_id,
        ]);

        $response->assertSessionHas('alerts', [
            ["message" => trans('external_roles.delete.success'), 'type' => 'success']
        ]);
    }

    /** @test */
    public function a_user_without_permission_can_not_delete_external_role()
    {
        $user = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'mario@medialogic.it',
            'user' => 'mario@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);

        $this->actingAs($user, 'admins');

        $user->assignRole('Registered User');
        app()->getProvider(\App\Providers\AuthServiceProvider::class)->registerPermissions();

        $this->actingAs($user, 'admins');
        $this->assertAuthenticated('admins');
        $external_role = ExternalRole::first();
        $this->delete(route(RoutingManager::adminAlias() . 'external_roles.destroy', $external_role->id))->assertStatus(403);
        $this->assertDatabaseHas('external_roles', [
            'external_role_id' => $external_role->external_role_id
        ]);

    }
}
