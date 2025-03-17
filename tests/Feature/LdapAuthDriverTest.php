<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use Adldap\AdldapInterface;
use App\AppConfiguration;
use App\Auth\AuthType;
use App\Auth\PasswordRecovery;
use App\Auth\User;
use App\LftRouting\RoutingManager;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use Tests\TestCase;

class LdapAuthDriverTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockLdapProvider();
        AuthType::find(AuthType::LDAP)->saveAsDefault();

        AppConfiguration::current()->update([
            'allow_pwd_reset' => 1
        ]);

        $this->user = User::create([
            'name' => 'Test',
            'surname' => 'Test',
            'email' => 'tesone@medialogic.it',
            'password' => bcrypt('Lombardo2023!!'),
            'user' => 'ftesone',
            'auth_type_id' => AuthType::LDAP,
            'enabled' => 1
        ]);
    }


    /** @test */
    public function a_user_can_authenticate_ldap_backend(): void
    {
        $this->mockLdapProvider(false);
        $user = $this->user;
        $this->assertDatabaseHas('users', ['user' => 'ftesone', 'auth_type_id' => AuthType::LDAP]);
        $response = $this->post(route(RoutingManager::adminLoginRoute(), ['user' => $user->user, 'password' => 'Lombardo2023!!', 'auth_type' => AuthType::LDAP]));
        $response->assertRedirectToRoute(RoutingManager::adminHome());
        $this->assertAuthenticated('admins');

    }

    /** @test */
    public function a_user_can_authenticate_ldap_frontend(): void
    {
        if(!config('lft.public_routes.enabled')){
            $this->assertTrue(true);
            return;
        }
        $this->mockLdapProvider(false);
        $user = $this->user;
        $this->assertDatabaseHas('users', ['user' => 'ftesone', 'auth_type_id' => AuthType::LDAP]);
        $response = $this->post(route(RoutingManager::loginRoute(), ['user' => $user->user, 'password' => 'Lombardo2023!!', 'auth_type' => AuthType::LDAP]));
        $response->assertRedirectToRoute(RoutingManager::home());
        $this->assertAuthenticated('web');

    }

    /** @test */
    public function a_user_can_not_authenticate_ldap_if_disabled(): void
    {
        AuthType::find(AuthType::LOCAL)->saveAsDefault();
        $this->setAuthTypes([
            'id' => AuthType::LDAP,
            'enabled' => 0,
        ]);
        $user = $this->user;
        $login_page = $this->get(route(RoutingManager::adminLoginRoute()));
        $login_page->assertDontSeeText("LDAP");
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute(), ['user' => $user->user, 'password' => 'test123!', 'auth_type' => AuthType::LDAP]));
        $response->assertSeeText(trans('auth.ldap_unknown_user'));
    }

    /** @test */
    public function a_user_can_auto_register_backend_ldap_account_if_auto_reg_enabled()
    {
        $this->mockLdapProvider(false);
        $auth_type = AuthType::where('id', AuthType::LDAP)->first();
        $auth_type->update([
            'enabled' => 1,
            'auto_register' => 1,
        ]);
        $response = $this->post(route(RoutingManager::adminLoginRoute()), ['user' => 'test_auto_reg', 'password' => 'M3dialogic2023!', 'auth_type' => AuthType::LDAP]);
        $this->assertDatabaseHas('users', [
            'user' => 'test_auto_reg',
            'auth_type_id' => AuthType::LDAP,
        ]);

        $response->assertRedirectToRoute(RoutingManager::adminHome());
    }

    /** @test */
    public function a_user_can_auto_register_frontend_ldap_account_if_auto_reg_enabled()
    {
        $this->mockLdapProvider(false);
        $auth_type = AuthType::where('id', AuthType::LDAP)->first();
        $auth_type->update([
            'enabled' => 1,
            'auto_register' => 1,
        ]);
        $response = $this->post(route(RoutingManager::loginRoute()), ['user' => 'test_auto_reg_frontend', 'password' => 'M3dialogic2023!', 'auth_type' => AuthType::LDAP]);
        $this->assertDatabaseHas('users', [
            'user' => 'test_auto_reg_frontend',
            'auth_type_id' => AuthType::LDAP
        ]);
        $response->assertRedirectToRoute(RoutingManager::home());
    }

    /** @test */
    public function a_user_can_not_auto_register_ldap_account_if_auto_reg_disabled()
    {

        $this->mockLdapProvider(false);
        $auth_type = AuthType::where('id', AuthType::LDAP)->first();
        $auth_type->update([
            'enabled' => 0,
        ]);

        $response = $this->post(route(RoutingManager::adminLoginRoute()), ['user' => 'test', 'password' => 'M3dialogic2023!', 'auth_type' => AuthType::LDAP]);

        $this->assertDatabaseMissing('users', [
            'user' => 'test'
        ]);
        $this->assertGuest();

        $response->assertDontSeeText('Ldap');
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
    public function a_user_can_not_start_reset_password_if_reset_password_is_false(): void
    {
        AppConfiguration::current()->update([
            'allow_pwd_reset' => 0
        ]);

        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 0
        ]);

        $response = $this->get(route(RoutingManager::adminAlias() . 'password.request'));

        $response->assertDontSeeText(trans('passwords.reset_password'));

        $view = $this->followingRedirects()->post(route(RoutingManager::adminAlias() . 'password.email'), ['user' => 'test@medialogic.it']);

        $view->assertStatus(403);
    }

    /** @test */
    public function a_user_can_not_start_reset_password_frontend_if_reset_password_is_false(): void
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
        $view = $this->followingRedirects()->post(route(RoutingManager::publicAlias() . 'password.email'), ['user' => 'test@medialogic.it']);
        $view->assertStatus(403);
    }

    /** @test */
    public function a_user_can_reset_password_if_reset_password_is_true(): void
    {
        $this->mockLdapProvider();
        $user = $this->user;
        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 1
        ]);
        $reset_view = $this->followingRedirects()->get(route(RoutingManager::adminAlias() . 'password.request'));
        $reset_view->assertSeeText(trans('passwords.reset_password_msg'));
        $response = $this->followingRedirects()->post(route(RoutingManager::adminAlias() . 'password.email'), ['user' => 'ftesone']);
        $response->assertSeeText(trans('passwords.change.mail_sent'));
        $this->assertDatabaseHas('password_recoveries', [
            'user_id' => $user->id,
        ]);

        $token = PasswordRecovery::where('user_id', $user->id)->first()->token;
        $submit_reset = $this->followingRedirects()->post(route(RoutingManager::adminAlias() . 'password.execute_reset'), ['token' => $token, 'user' => 'ftesone', 'password' => 'M3dialogic2023!', 'password_confirmation' => 'M3dialogic2023!']);
        $submit_reset->assertSeeText(trans('passwords.change.success'));

    }

    /** @test */
    public function a_user_can_reset_password_frontend_if_reset_password_is_true(): void
    {
        if(!config('lft.public_routes.enabled')){
            $this->assertTrue(true);
            return;
        }
        $this->mockLdapProvider();
        $user = $this->user;
        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 1
        ]);
        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 1
        ]);
        $reset_view = $this->followingRedirects()->get(route(RoutingManager::publicAlias() . 'password.request'));
        $reset_view->assertSeeText(trans('passwords.reset_password_msg'));
        $response = $this->followingRedirects()->post(route(RoutingManager::publicAlias() . 'password.email'), ['user' => 'ftesone']);
        $response->assertSeeText(trans('passwords.change.mail_sent'));
        $this->assertDatabaseHas('password_recoveries', [
            'user_id' => $user->id,
        ]);

        $token = PasswordRecovery::where('user_id', $user->id)->first()->token;
        $submit_reset = $this->followingRedirects()->post(route(RoutingManager::publicAlias() . 'password.execute_reset'), ['token' => $token, 'user' => 'ftesone', 'password' => 'M3dialogic2023!', 'password_confirmation' => 'M3dialogic2023!']);

        $submit_reset->assertSeeText(trans('passwords.change.success'));

    }

    /** @test */
    public function a_user_can_not_authenticate_if_provide_wrong_pwd(): void
    {
        $user = $this->user;
        $this->get(route(RoutingManager::adminLoginRoute()));
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute(), ['user' => $user->user, 'password' => 'test123!', 'auth_type' => AuthType::LDAP]));
        $this->assertGuest();
        $response->assertSeeText(trans('auth.failed'));
    }

    /** @test */
    public function a_user_is_notified_with_remaining_attempts_if_provide_wrong_pwd(): void
    {
        $this->get(route(RoutingManager::adminLoginRoute()));
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute(), ['user' => 'ftesone', 'password' => 'test123!', 'auth_type' => AuthType::LDAP]));
        $this->assertGuest();
        $response->assertSeeText(trans('auth.failed_with_attempts', ['attempts_left' => 2]));
    }

    /** @test */
    public function on_user_login_success_login_info_are_updated()
    {
        $this->mockLdapProvider(false);

        $user = $this->user;
        $this->post(route(RoutingManager::adminLoginRoute()), ['user' => $user->user, 'password' => 'Lombardo2023!!', 'auth_type' => AuthType::LDAP]);
        $this->assertAuthenticated('admins');
        $this->assertDatabaseHas('users', [

            'user' => 'ftesone',
            'login_success_on' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

    }

    /** @test */
    public function on_user_login_failed_login_info_are_updated()
    {
        $user = $this->user;
        $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute(), ['user' => $user->user, 'password' => 'test123!', 'auth_type' => AuthType::LDAP]));
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'user' => 'ftesone',
            'login_failed_on' => Carbon::now()->format('Y-m-d H:i:s'),
            'failed_login_count' => 1
        ]);

    }

    /** @test */
    public function account_is_locked_after_configured_n_failed_login_tries()
    {
        $this->mockLdapProvider();
        $user = $this->user;
        $app_configuration = AppConfiguration::current();
        $app_configuration->update([
            'max_failed_login_attempts' => 3
        ]);
        $max_tries = $app_configuration->max_failed_login_attempts;
        $this->get(route(RoutingManager::adminLoginRoute()));

        for ($i = 1; $i < $max_tries; $i++) {
            $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute()), ['user' => $user->user, 'password' => 'test', 'auth_type' => AuthType::LDAP]);

            $response->assertSeeText(trans('auth.failed_with_attempts', ['attempts_left' => $max_tries - $i]));
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'failed_login_count' => $i
            ]);
        }
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute()), ['user' => $user->user, 'password' => 'test', 'auth_type' => AuthType::LDAP]);
        $response->assertSeeText(trans('auth.locked'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'failed_login_count' => $max_tries,
            'locked' => 1
        ]);

    }

    /** @test */
    public function a_user_must_change_pwd_if_pwd_complexity_enabled_and_provides_weak_password(): void
    {
        $this->mockLdapProvider(false);

        $user = $this->user;
        $user->update(['password' => bcrypt('test')]);
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute()), ['user' => $user->user, 'password' => 'test', 'auth_type' => AuthType::LDAP]);
        $this->assertAuthenticated('admins');

        $this->assertDatabaseHas('users', [
            'user' => $user->user,
            'pwd_change_required' => 1
        ]);

        $response->assertSeeText(trans('auth.insecure_pwd_should_be_changed'));
    }

    /** @test */
    public function a_user_can_not_login_if_pwd_expired(): void
    {
        $this->mockLdapProvider(false);
        $user = $this->user;
        $user->update([
            'enabled_to' => Carbon::now()->subMinute()->format('d/m/Y H:i')
        ]);
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute()), ['user' => $user->user, 'password' => 'Lombardo2023!!', 'auth_type' => AuthType::LDAP]);

        $response->assertSee(trans('auth.expired'));
    }

    /** @test */
    public function a_user_is_flagged_pwd_change_required_by_check_expired_users_password_command_if_pwd_expires_enabled(): void
    {
        $this->mockLdapProvider(false);
        $app_config = AppConfiguration::current();
        $app_config->update([
            'pwd_never_expires' => 0
        ]);
        $user = $this->user;
        $this->assertDatabaseHas('app_configurations', [
            'pwd_never_expires' => 0
        ]);

        $user->update([
            'pwd_changed_at' => Carbon::now()->subDays(14)
        ]);

        $this->assertDatabaseHas('users', [
            'user' => $user->user,
            'pwd_changed_at' => Carbon::now()->subDays(14)
        ]);

        Artisan::call('lft:check-expired-users-password');

        $this->assertGuest();

        $this->assertDatabaseHas('users', [
            'user' => $user->user,
            'pwd_change_required' => 1
        ]);

        $response = $this->followingRedirects()->from(route(RoutingManager::adminLoginRoute()))->post(route(RoutingManager::adminLoginRoute()), ['user' => $user->user,'password' => 'Lombardo2023!!', 'auth_type' => AuthType::LDAP]);
        $response->assertSee(trans('passwords.reset_password_msg'),false);
    }

    private function mockLdapProvider($pwd_reset = true)
    {
        // Via mockery facade we can mock every laravel facade
        $mock = Mockery::mock(AdldapInterface::class);

        // we prepare the dataset we expect from the LDAP provider
        $user = [
            'user' => "ftesone",
            'mail' => ["tesone@medialogic.it"],
            'givenname' => ["Francesco"],
            'sn' => ["Tesone"],
        ];

        // We tell the app to refer for AdldapInterface, used by adldap library, to the mocked one for this instance.
        $this->app->instance(AdldapInterface::class, $mock);
        if ($pwd_reset) {
            $mock->shouldReceive('attempt')->andReturn(false);
            $mock->shouldReceive('setPassword', 'save')->andReturn(true);
        }
        // Via shouldreceive we define how mocked class reacts to LDAP methods called in the flow,
        // specifying what they return, basically we are returning the mocked class itself instead of the original one.
        $mock->shouldReceive('auth', 'connect', 'attempt', 'search', 'users', 'findOrFail', 'where')->andReturn($mock);
        // we only return the prepared dataset for the "first" method call.
        $mock->shouldReceive('first')->andReturn((object)$user);


        return $mock;
    }

    private function setAuthTypes(array $params)
    {

        $auth_type = AuthType::findOrFail($params['id']);
        $auth_type->fill($params);
        $auth_type->save();
    }
}
