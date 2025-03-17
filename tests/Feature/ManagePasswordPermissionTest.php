<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\AppConfiguration;
use App\Auth\AuthType;
use App\Auth\Permission;
use App\Auth\Role;
use App\Auth\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagePasswordPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        AppConfiguration::current()->update([
            'allow_pwd_reset' => 1
        ]);
    }

    // LOCAL
    /** @test */
    public function a_user_can_reset_his_own_password(): void
    {
        // Assuming and allowed pwd reset in app configuration
        AppConfiguration::current()->fill(['allow_pwd_reset' => true])->save();

        $user = User::create([
           'name' => 'Mario',
           'surname' => 'Rossi',
           'email' => 'mario@medialogic.it',
           'user' => 'mario@medialogic.it',
           'auth_type_id' => AuthType::LOCAL
        ]);

        $user->roles()->save(Role::findOrFail(3));

        $this->actingAs($user, 'admins');
        $response = $this->get(route('admin::users.edit', $user->id));
        $response->assertSee(trans('users.attributes.pwd_change_required_on'));
    }

    /** @test */
    public function a_user_with_permission_can_reset_a_non_admin_users_password(): void
    {
        // Assuming and allowed pwd reset in app configuration
        AppConfiguration::current()->fill(['allow_pwd_reset' => true])->save();

        $mario = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'mario@medialogic.it',
            'user' => 'mario@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);

        $role = Role::findOrFail(3);
        $p1 = Permission::where('name', 'update_users')->first();
        $p2 = Permission::where('name', 'manage_users_password')->first();
        $role->permissions()->sync([$p1->id, $p2->id]);
        app()->getProvider(AuthServiceProvider::class)->registerPermissions();

        $this->assertDatabaseHas('permission_role', ['permission_id' => $p1->id,'role_id' => $role->id]);
        $this->assertDatabaseHas('permission_role', ['permission_id' => $p2->id,'role_id' => $role->id]);

        $mario->roles()->save($role);

        $luigi = User::create([
            'name' => 'Luigi',
            'surname' => 'Rossi',
            'email' => 'luigi@medialogic.it',
            'user' => 'luigi@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);

        $luigi->roles()->save($role);

        $this->actingAs($mario, 'admins');
        $response = $this->get(route('admin::users.edit', $luigi->id));
        $response->assertOk();

        $response->assertSee(trans('users.attributes.pwd_change_required_on'));
    }

    /** @test */
    public function a_user_with_permission_cannot_reset_an_admin_users_password(): void
    {
        $admin = User::first();

        $mario = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'mario@medialogic.it',
            'user' => 'mario@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);

        $role = Role::findOrFail(3);
        $p1 = Permission::where('name', 'update_users')->first();
        $p2 = Permission::where('name', 'manage_users_password')->first();
        $role->permissions()->sync([$p1->id, $p2->id]);
        app()->getProvider(AuthServiceProvider::class)->registerPermissions();

        $this->assertDatabaseHas('permission_role', ['permission_id' => $p1->id,'role_id' => $role->id]);
        $this->assertDatabaseHas('permission_role', ['permission_id' => $p2->id,'role_id' => $role->id]);

        $mario->roles()->save($role);

        $this->actingAs($mario, 'admins');
        $response = $this->get(route('admin::users.edit', $admin->id));

        $response->assertDontSee(trans('users.attributes.pwd_change_required_off'));
    }

    /** @test */
    public function an_admin_can_reset_any_users_password(): void
    {
        $admin = User::first();
        // Assuming and allowed pwd reset in app configuration
        AppConfiguration::current()->fill(['allow_pwd_reset' => true])->save();

        $user = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'rossi@medialogic.it',
            'user' => 'rossi@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);

        $this->actingAs($admin, 'admins');
        $response = $this->get(route('admin::users.edit', $user->id));
        $response->assertSee(trans('users.attributes.pwd_change_required_on'));
    }
}
