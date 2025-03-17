<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\AppConfiguration;
use App\Auth\AuthType;
use App\Auth\User;
use App\LftRouting\RoutingManager;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LocalAuthDriverTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        AppConfiguration::current()->update([
            'allow_pwd_reset' => 1
        ]);
    }

    /** @test */
    public function a_user_can_authenticate_locally_backend(): void
    {
        $response = $this->post(route(RoutingManager::adminLoginRoute()), ['user' => 'admin', 'password' => 'M3dialogic$', 'auth_type' => AuthType::LOCAL])->assertStatus(302);
        $this->assertAuthenticated();
        $response->assertRedirectToRoute(RoutingManager::adminHome());
    }

    /** @test */
    public function a_user_can_authenticate_locally_frontend(): void
    {
        if(!config('lft.public_routes.enabled')){
            $this->assertTrue(true);
            return;
        }
        $response = $this->post(route(RoutingManager::loginRoute()), ['user' => 'admin', 'password' => 'M3dialogic$', 'auth_type' => AuthType::LOCAL])->assertStatus(302);
        $this->assertAuthenticated();
        $response->assertRedirectToRoute(RoutingManager::home());
    }

    /** @test */
    public function a_user_can_unauthenticate_backend(): void
    {
        $admin = User::first();
        $this->actingAs($admin, 'admins');
        $response = $this->post(route(RoutingManager::adminAlias() . 'logout'))->assertStatus(302);
        $this->assertGuest();
        $response->assertRedirectToRoute(RoutingManager::adminLoginRoute());
    }

    /** @test */
    public function a_user_can_unauthenticate_frontend(): void
    {
        if(!config('lft.public_routes.enabled')){
            $this->assertTrue(true);
            return;
        }
        $admin = User::first();
        $this->actingAs($admin, 'web');
        $response = $this->post(route(RoutingManager::publicAlias() . 'logout'))->assertStatus(302);
        $this->assertGuest();
        $response->assertRedirectToRoute(RoutingManager::loginRoute());
    }

    /** @test */
    public function a_user_can_not_authenticate_locally_if_disabled(): void
    {
        $auth_type = AuthType::find(AuthType::LOCAL);
        $auth_type->saveAsDefault();
        $auth_type->update([
            'enabled' => 0,
        ]);
        $login_page = $this->get(route(RoutingManager::adminLoginRoute()));
        $login_page->assertDontSeeText("Localmente");
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute(), ['user' => 'test', 'password' => 'test123!', 'auth_type' => AuthType::LOCAL]));
        if(AuthType::enabled()->count() >= 1){
            $response->assertSeeText(trans('auth.failed'));
        }else{
            $response->assertSee(trans('auth.no_auth_types'),false);
        }

    }

    /** @test */
    public function a_user_can_start_reset_password_when_pwd_reset_true(): void
    {
        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 1
        ]);
        $view = $this->followingRedirects()->get(route(RoutingManager::adminAlias() . 'password.request'));
        $view->assertSeeText(trans('passwords.reset_password_msg'));
    }

    /** @test */
    public function a_user_can_start_reset_password_frontend_when_pwd_reset_true(): void
    {
        if(!config('lft.public_routes.enabled')){
            $this->assertTrue(true);
            return;
        }
        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 1
        ]);
        $view = $this->followingRedirects()->get(route(RoutingManager::publicAlias() . 'password.request'));
        $view->assertSeeText(trans('passwords.reset_password_msg'));
    }

    /** @test */
    public function a_user_can_not_reset_password_locally_if_reset_password_is_false(): void
    {
        AppConfiguration::current()->update([
            'allow_pwd_reset' => 0
        ]);

        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 0
        ]);

        $response = $this->get(route(RoutingManager::adminAlias() . 'password.request'));

        $response->assertDontSeeText(trans('passwords.reset_password'));

        $view = $this->followingRedirects()->post(route(RoutingManager::adminAlias() . 'password.email'), ['user' => 'admin']);

        $view->assertStatus(403);
    }

    /** @test */
    public function a_user_can_not_reset_password_locally_frontend_if_reset_password_is_false(): void
    {
        if(!config('lft.public_routes.enabled')){
            $this->assertTrue(true);
            return;
        }
        AppConfiguration::current()->update([
            'allow_pwd_reset' => 0
        ]);
        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 0
        ]);
        $response = $this->get(route(RoutingManager::publicAlias() . 'password.request'));
        $response->assertDontSeeText(trans('passwords.reset_password'));
        $view = $this->followingRedirects()->post(route(RoutingManager::publicAlias() . 'password.email'), ['user' => 'admin']);
        $view->assertStatus(403);
    }

    /** @test */
    public function a_user_can_not_authenticate_if_provide_wrong_pwd(): void
    {
        $this->get(route(RoutingManager::adminLoginRoute()));
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute(), ['user' => 'admin', 'password' => 'test123!', 'auth_type' => AuthType::LOCAL]));
        $this->assertGuest();
        $response->assertSeeText(trans('auth.failed'));
    }

    /** @test */
    public function a_user_is_notified_with_remaining_attempts_if_provide_wrong_pwd(): void
    {
        $this->get(route(RoutingManager::adminLoginRoute()));
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute(), ['user' => 'admin', 'password' => 'test123!', 'auth_type' => AuthType::LOCAL]));
        $this->assertGuest();
        $response->assertSeeText(trans('auth.failed_with_attempts', ['attempts_left' => 2]));
    }

    /** @test */
    public function on_user_login_success_login_info_are_updated()
    {
        $this->post(route(RoutingManager::adminLoginRoute()), ['user' => 'admin', 'password' => 'M3dialogic$', 'auth_type' => AuthType::LOCAL])->assertStatus(302);
        $this->assertDatabaseHas('users', [

            'user' => 'admin',
            'login_success_on' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $this->assertAuthenticated();

    }

    /** @test */
    public function on_user_login_failed_login_info_are_updated()
    {
        $this->post(route(RoutingManager::adminLoginRoute()), ['user' => 'admin', 'password' => 'test', 'auth_type' => AuthType::LOCAL])->assertStatus(302);
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'user' => 'admin',
            'login_failed_on' => Carbon::now()->format('Y-m-d H:i:s'),
            'failed_login_count' => 1
        ]);

    }

    /** @test */
    public function account_is_locked_after_configured_n_failed_login_tries()
    {
        $user = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'test@medialogic.it',
            'password' => bcrypt('password'),
            'user' => 'test@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);
        $app_configuration = AppConfiguration::current();
        $app_configuration->update([
            'max_failed_login_attempts' => 3
        ]);
        $max_tries = $app_configuration->max_failed_login_attempts;
        $this->get(route(RoutingManager::adminLoginRoute()));

        for($i = 1;$i < $max_tries; $i++) {
            $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute()), ['user' => $user->user, 'password' => 'test', 'auth_type' => AuthType::LOCAL]);

            $response->assertSeeText(trans('auth.failed_with_attempts', ['attempts_left' => $max_tries-$i]));
            $this->assertDatabaseHas('users',[
                'id' => $user->id,
                'failed_login_count' => $i
            ]);
        }
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute()), ['user' =>  $user->user, 'password' => 'test', 'auth_type' => AuthType::LOCAL]);
        $response->assertSeeText(trans('auth.locked'));

        $this->assertDatabaseHas('users',[
            'id' => $user->id,
            'failed_login_count' => $max_tries,
            'locked' => 1
        ]);

    }

    /** @test */
    public function a_user_must_change_pwd_if_pwd_complexity_enabled_and_provides_weak_password(): void
    {
        $user = User::first();
        $user->update(['password' => bcrypt('test')]);
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute()), ['user' => 'admin', 'password' => 'test', 'auth_type' => AuthType::LOCAL]);
        $this->assertAuthenticated('admins');

        $this->assertDatabaseHas('users', [
            'user' => 'admin',
            'pwd_change_required' => 1
        ]);

        $response->assertSeeText(trans('auth.insecure_pwd_should_be_changed'));
    }

    /** @test */
    public function a_user_can_not_login_if_pwd_expired(): void
    {
        $user = User::first();
        $user->update([
            'enabled_to' => Carbon::now()->subMinute()->format('d/m/Y H:i')
        ]);
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute()), ['user' => 'admin', 'password' => 'M3dialogic$', 'auth_type' => AuthType::LOCAL]);
        $response->assertSee(trans('auth.expired'));
    }

    /** @test */
    public function a_user_is_flagged_pwd_change_required_by_check_expired_users_password_command_if_pwd_expires_enabled(): void
    {
        $admin = User::first();
        $this->actingAs($admin, 'admins');
        $this->followingRedirects()->patch(route(RoutingManager::adminAlias() . 'app_configuration.update'), array_merge($this->data(), ['pwd_never_expires' => 0]));
        $this->assertDatabaseHas('app_configurations', [
            'pwd_never_expires' => 0
        ]);

        $admin->update([
            'pwd_changed_at' => Carbon::now()->subDays(7)
        ]);
        //TODO::Wrong call a command directly in test?
        Artisan::call('lft:check-expired-users-password');
        $this->post(route(RoutingManager::adminAlias() . 'logout'))->assertStatus(302);

        $this->assertGuest();

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'pwd_change_required' => 1
        ]);

        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute()), ['user' => 'admin', 'password' => 'M3dialogic$', 'auth_type' => AuthType::LOCAL]);

        $response->assertSee(trans('passwords.reset_password_msg'));
    }

    private function data(): array
    {
        return AppConfiguration::current(true)->toArray();
    }
}
