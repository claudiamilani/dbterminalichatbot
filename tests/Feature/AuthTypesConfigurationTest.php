<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\Auth\AuthType;
use App\Auth\Permission;
use App\Auth\Role;
use App\Auth\User;
use App\LftRouting\RoutingManager;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTypesConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

    }

    /** @test */
    public function a_user_with_permission_can_see_auth_types_index()
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
        $p1 = Permission::where('name', 'update_auth_types')->first();
        $p2 = Permission::where('name', 'list_auth_types')->first();
        $role->permissions()->sync([$p1->id, $p2->id]);
        $user->roles()->save(Role::findOrFail(3));
        app()->getProvider(AuthServiceProvider::class)->registerPermissions();

        $this->actingAs($user, 'admins');
        $this->assertAuthenticated('admins');

        $response = $this->get(route(RoutingManager::adminAlias() . 'auth_types.index'));
        $response->assertOk();
    }

    /** @test */
    public function a_user_without_permission_can_not_see_auth_types_index()
    {

        $user = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'mario@medialogic.it',
            'user' => 'mario@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);


        $user->assignRole('Registered User');
        app()->getProvider(AuthServiceProvider::class)->registerPermissions();

        $this->actingAs($user, 'admins');
        $this->get(route(RoutingManager::adminAlias() . 'auth_types.index'))->assertStatus(403);

    }

    /** @test */
    public function a_user_with_permission_can_enable_auth_type()
    {
        $user = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'mario@medialogic.it',
            'user' => 'mario@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);

        $this->actingAs($user, 'admins');
        $auth_type = AuthType::first();
        $auth_type->update([
            'enabled' => 0
        ]);
        $this->assertDatabaseHas('auth_types', [
            'id' => AuthType::LOCAL,
            'enabled' => 0
        ]);
        $role = Role::findOrFail(3);
        $p1 = Permission::where('name', 'update_auth_types')->first();
        $p2 = Permission::where('name', 'list_auth_types')->first();
        $role->permissions()->sync([$p1->id, $p2->id]);
        $user->assignRole('Registered User');
        app()->getProvider(AuthServiceProvider::class)->registerPermissions();

        $this->actingAs($user, 'admins');
        $this->assertAuthenticated('admins');

        $auth_type->enabled = 1;

        $response = $this->patch(route(RoutingManager::adminAlias() . 'auth_types.update', $auth_type->toArray()));

        $this->assertDatabaseHas('auth_types', [
            'id' => AuthType::LOCAL,
            'enabled' => 1
        ]);

        $response->assertSessionHas('alerts', [
            ["message" => trans('auth_types.edit.success'), 'type' => 'success']
        ]);
    }

    /** @test */
    public function a_user_with_permissions_can_set_default_auth_type()
    {
        $user = User::first();
        $this->actingAs($user, 'admins');
        $auth_type = AuthType::where('id', AuthType::AZURE)->first();
        $auth_type->default = 1;
        $auth_type->enabled = 1;
        $response = $this->patch(route(RoutingManager::adminAlias() . 'auth_types.update', $auth_type->toArray()));
        $this->assertDatabaseHas('auth_types', [
            'id' => AuthType::AZURE,
            'default' => 1
        ]);
        $this->assertDatabaseMissing('auth_types', [
            'id' => AuthType::LOCAL,
            'default' => 1
        ]);
        $response->assertSessionHas('alerts', [
            ["message" => trans('auth_types.edit.success'), 'type' => 'success']
        ]);
    }

    /** @test */
    public function a_user_with_permission_can_enable_auto_register_for_auth_type()
    {
        $user = User::first();
        $this->actingAs($user, 'admins');
        $auth_type = AuthType::where('id', AuthType::LDAP)->first();
        $auth_type->update([
            'auto_register' => 0
        ]);
        $this->assertDatabaseHas('auth_types', [
            'id' => AuthType::LDAP,
            'auto_register' => 0
        ]);
        $auth_type->auto_register = 1;
        $response = $this->patch(route(RoutingManager::adminAlias() . 'auth_types.update', $auth_type->toArray()));

        $this->assertDatabaseHas('auth_types', [
            'id' => AuthType::LDAP,
            'auto_register' => 1
        ]);

        $response->assertSessionHas('alerts', [
            ["message" => trans('auth_types.edit.success'), 'type' => 'success']
        ]);
    }

    /** @test */
    public function a_user_with_permission_can_never_enable_auto_register_for_local_auth_type()
    {
        $user = User::first();
        $this->actingAs($user, 'admins');
        $auth_type = AuthType::first();
        $auth_type->auto_register = 1;
        $response = $this->patch(route(RoutingManager::adminAlias() . 'auth_types.update', $auth_type->toArray()));

        $this->assertDatabaseMissing('auth_types', [
            'id' => AuthType::LOCAL,
            'auto_register' => 1
        ]);
        $response->assertSessionHasErrors('auto_register', [trans('auth_types.edit.no_auto_register')]);
    }

    /** @test */
    public function only_one_auth_type_can_be_configured_as_default_at_the_same_time()
    {
        $user = User::first();
        $this->actingAs($user, 'admins');
        // Assuming we have set LOCAL as default
        AuthType::find(AuthType::LOCAL)->saveAsDefault();
        $auth_type = AuthType::find(AuthType::AZURE);
        $auth_type->default = 1;
        $auth_type->enabled = 1;
        $this->assertDatabaseHas('auth_types', [
            'id' => AuthType::LOCAL,
            'default' => 1
        ]);
        $response = $this->patch(route(RoutingManager::adminAlias() . 'auth_types.update', $auth_type->toArray()));
        $this->assertDatabaseHas('auth_types', [
            'id' => AuthType::AZURE,
            'default' => 1
        ]);
        $this->assertDatabaseMissing('auth_types', [
            'id' => AuthType::LOCAL,
            'default' => 1
        ]);
        $response->assertSessionHas('alerts', [
            ["message" => trans('auth_types.edit.success'), 'type' => 'success']
        ]);
    }

    /** @test */
    public function at_least_one_auth_type_need_to_be_enabled()
    {
        AuthType::query()->update([
            'enabled' => 0
        ]);

        $user = User::first();
        $this->actingAs($user, 'admins');
        $auth_type = AuthType::first();
        $auth_type->enabled = 0;

        $response = $this->patch(route(RoutingManager::adminAlias() . 'auth_types.update', $auth_type->toArray()));

        $response->assertSessionHasErrors('enabled', [
            trans('auth_types.edit.need_one_enabled')
        ]);

    }
}
