<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\AppConfiguration;
use App\Auth\AuthType;
use App\Auth\PasswordRecovery;
use App\Auth\User;
use App\LftRouting\RoutingManager;
use App\Mail\ResetPassword;
use Database\Seeders\PasswordRecoverySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordRecoveryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        //We enable the allow_pwd_reset configuration starting with a positive situation.

        $app_config = AppConfiguration::current(true);
        $app_config->update(['allow_pwd_reset' => 1]);
    }

    /** @test */
    public function it_has_a_password_reset_flag_in_the_table(): void
    {
        $this->assertIsBool(AppConfiguration::current(true)->isPasswordResetEnabled());
    }

    /** @test */
    public function an_admin_can_see_the_status_of_pwd_reset(): void
    {
        $this->user = User::first();

        $this->actingAs($this->user, 'admins');

        $response = $this->get(route(RoutingManager::adminAlias() . 'app_configuration.show'));

        $response->assertSee(trans('allow_pwd_reset'),false);
    }

    /** @test */
    public function an_admin_can_set_pwd_reset_false(): void
    {
        $this->user = User::first();

        $this->actingAs($this->user, 'admins');

        $response = $this->patch(route(RoutingManager::adminAlias() . 'app_configuration.update'), array_merge($this->data(), ['allow_pwd_reset' => 0]));
        $response->assertStatus(302);

        $response->assertRedirectToRoute(RoutingManager::adminAlias() . 'app_configuration.show');

        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 0
        ]);
    }

    /** @test */
    public function an_admin_can_set_pwd_reset_true(): void
    {
        $this->user = User::first();

        $this->actingAs($this->user, 'admins');

        $response = $this->patch(route(RoutingManager::adminAlias() . 'app_configuration.update'), array_merge($this->data(), ['allow_pwd_reset' => 1]));
        $response->assertStatus(302);

        $response->assertRedirectToRoute(RoutingManager::adminAlias() . 'app_configuration.show');

        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 1
        ]);
    }

    /** @test */
    public function a_user_cannot_see_reset_password_when_pwd_reset_false(): void
    {
        $app_configuration = AppConfiguration::current();
        $app_configuration->update([
            'allow_pwd_reset' => 0
        ]);
        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 0
        ]);

        $view = $this->get(route(RoutingManager::loginRoute()))->assertStatus(200);

        $view->assertDontSeeText(trans('passwords.reset_password'));
    }

    /** @test */
    public function a_user_cannot_see_reset_password_frontend_when_pwd_reset_false(): void
    {
        if(!config('lft.public_routes.enabled')){
            $this->assertTrue(true);
            return;
        }
        $app_configuration = AppConfiguration::current();
        $app_configuration->update([
            'allow_pwd_reset' => 0
        ]);
        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 0
        ]);
        $view = $this->get(route(RoutingManager::loginRoute()))->assertStatus(200);
        $view->assertDontSeeText(trans('passwords.reset_password'));
    }

    /** @test */
    public function a_user_can_reset_password_when_pwd_reset_true(): void
    {
        $this->assertDatabaseHas('app_configurations', [
            'allow_pwd_reset' => 1
        ]);
        $view = $this->followingRedirects()->get(route(RoutingManager::adminAlias() . 'password.request'));
        $view->assertSeeText(trans('passwords.reset_password_msg'));
    }

    /** @test */
    public function a_user_can_reset_password_frontend_when_pwd_reset_true(): void
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
    public function a_password_recovery_record_is_created_after_password_recovery_request(): void
    {
        $this->followingRedirects()->post(route(RoutingManager::adminAlias() . 'password.email'), ['user' => 'admin']);
        $this->assertDatabaseHas('password_recoveries', [
            'user_id' => 1,
            'user' => 'admin'
        ]);
    }

    /** @test */
    public function a_mail_is_queued_after_pwd_recovery_request(): void
    {
        Mail::fake();
        $reset_view = $this->followingRedirects()->get(route(RoutingManager::adminAlias() . 'password.request'));
        $reset_view->assertSeeText(trans('passwords.reset_password_msg'));
        $response = $this->followingRedirects()->post(route(RoutingManager::adminAlias() . 'password.email'), ['user' => 'admin']);
        Mail::assertQueued(ResetPassword::class);
        $response->assertSeeText(trans('passwords.change.mail_sent'));

    }

    /** @test */
    public function a_user_can_reset_password_with_proper_token(): void
    {
        PasswordRecovery::create([
            'user_id' => 1,
            'user' => "admin",
            'ipv4' => "127.0.0.1",
            'token' => "abc123",
            'email' => 'devphp@medialogic.it'
        ]);

        $this->assertDatabaseHas('password_recoveries', [
            'user_id' => 1
        ]);

        $response = $this->get(route(RoutingManager::adminPwdResetRoute(), ['token' => 'abc123']));
        $response->assertSeeText(trans('passwords.reset_msg'));
        $submit_reset = $this->followingRedirects()->post(route(RoutingManager::adminAlias() . 'password.request'), ['token' => 'abc123', 'user' => 'admin', 'password' => 'M3dialogic2023!', 'password_confirmation' => 'M3dialogic2023!']);
        $submit_reset->assertSeeText(trans('passwords.change.success'));
    }

    /** @test */
    public function a_user_can_not_reset_password_without_proper_token(): void
    {
        PasswordRecovery::create([
            'user_id' => 1,
            'user' => "admin",
            'ipv4' => "127.0.0.1",
            'token' => "abc123",
            'email' => 'devphp@medialogic.it'
        ]);

        $this->assertDatabaseHas('password_recoveries', [
            'user_id' => 1
        ]);

        $submit_reset = $this->from(route(RoutingManager::adminLoginRoute()))->post(route(RoutingManager::adminAlias() . 'password.request'), ['token' => '321Abc', 'user' => 'admin', 'password' => 'M3dialogic2023!', 'password_confirmation' => 'M3dialogic2023!']);

        $this->assertDatabaseHas('password_recoveries', [
            'user_id' => 1
        ]);

        $submit_reset->assertSessionHas('alerts', [
            ["message" => trans('passwords.resets.invalid_token'), 'type' => 'danger']
        ]);

    }

    /** @test */
    public function a_user_can_reset_password_with_proper_complexity(): void
    {
        $user = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'test@medialogic.it',
            'user' => 'test@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);
        PasswordRecovery::create([
            'user_id' => $user->id,
            'user' => "test@medialogic.it",
            'ipv4' => "127.0.0.1",
            'token' => "abc123",
            'email' => 'devphp@medialogic.it'
        ]);

        $this->assertDatabaseHas('password_recoveries', [
            'user_id' => $user->id
        ]);
        $submit_reset = $this->from(route(RoutingManager::adminLoginRoute()))->followingRedirects()->post(route(RoutingManager::adminAlias() . 'password.request'), ['token' => 'abc123', 'user' => 'test@medialogic.it', 'password' => 'M3dialogic2023!', 'password_confirmation' => 'M3dialogic2023!']);

        $this->assertDatabaseMissing('password_recoveries', [
            'user_id' => $user->id
        ]);

        $submit_reset->assertSeeText(trans('passwords.change.success'));
    }

    /** @test */
    public function a_user_can_not_reset_password_without_proper_complexity(): void
    {
        $user = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'test@medialogic.it',
            'user' => 'test@medialogic.it',
            'auth_type_id' => AuthType::LOCAL
        ]);
        PasswordRecovery::create([
            'user_id' => $user->id,
            'user' => "test@medialogic.it",
            'ipv4' => "127.0.0.1",
            'token' => "abc123",
            'email' => 'devphp@medialogic.it'
        ]);

        $this->assertDatabaseHas('password_recoveries', [
            'user_id' => $user->id
        ]);
        $submit_reset = $this->from(route(RoutingManager::adminLoginRoute()))->post(route(RoutingManager::adminAlias() . 'password.request'), ['token' => 'abc123', 'user' => 'test@medialogic.it', 'password' => 'test', 'password_confirmation' => 'test']);

        $submit_reset->assertSessionHasErrors(['password' => AppConfiguration::current()->pwd_complexity_err_msg]);

        $this->assertDatabaseHas('password_recoveries', [
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function a_locked_account_is_enabled_after_pwd_reset_and_app_config_pwd_reset_unlocks_account_enabled(): void
    {
        $app_config = AppConfiguration::current();
        $app_config->update([
            'pwd_reset_unlocks_account' => 1
        ]);
        User::first()->update([
            'locked' => 1
        ]);
        PasswordRecovery::create([
            'user_id' => 1,
            'user' => "admin",
            'ipv4' => "127.0.0.1",
            'token' => "abc123",
            'email' => 'devphp@medialogic.it'
        ]);

        $this->assertDatabaseHas('password_recoveries', [
            'user_id' => 1
        ]);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'locked' => 1
        ]);
        $response = $this->followingRedirects()->post(route(RoutingManager::adminAlias() . 'password.request'), ['token' => 'abc123', 'user' => 'admin', 'password' => 'M3dialogic2023!', 'password_confirmation' => 'M3dialogic2023!']);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'locked' => 0
        ]);

        $response->assertSeeText(trans('passwords.change.success'));
    }

    /** @test */
    public function a_locked_account_is_still_locked_after_pwd_reset_and_app_config_pwd_reset_unlocks_account_disabled(): void
    {
        $app_config = AppConfiguration::current();
        $app_config->update([
            'pwd_reset_unlocks_account' => 0
        ]);
        User::first()->update([
            'locked' => 1
        ]);
        PasswordRecovery::create([
            'user_id' => 1,
            'user' => "admin",
            'ipv4' => "127.0.0.1",
            'token' => "abc123",
            'email' => 'devphp@medialogic.it'
        ]);

        $this->assertDatabaseHas('password_recoveries', [
            'user_id' => 1
        ]);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'locked' => 1
        ]);
        $response = $this->followingRedirects()->post(route(RoutingManager::adminAlias() . 'password.request'), ['token' => 'abc123', 'user' => 'admin', 'password' => 'M3dialogic2023!', 'password_confirmation' => 'M3dialogic2023!']);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'locked' => 1
        ]);

        $response->assertSeeText(trans('passwords.change.success'));
    }

    /** @test */
    public function a_new_pwd_history_record_is_created_after_pwd_reset(): void
    {
        $user = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'test@medialogic.it',
            'user' => 'test@medialogic.it',
            'auth_type_id' => AuthType::LOCAL,
            'locked' => 1
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'locked' => 1
        ]);

        PasswordRecovery::create([
            'user_id' => $user->id,
            'user' => $user->user,
            'ipv4' => "127.0.0.1",
            'token' => "abc123",
            'email' => 'devphp@medialogic.it'
        ]);

        $this->assertDatabaseHas('password_recoveries', [
            'user_id' => $user->id
        ]);


        $response = $this->followingRedirects()->post(route(RoutingManager::adminAlias() . 'password.request'), ['token' => 'abc123', 'user' => 'test@medialogic.it', 'password' => 'M3dialogic2023!', 'password_confirmation' => 'M3dialogic2023!']);


        $this->assertDatabaseHas('password_histories', [
            'user_id' => $user->id
        ]);

        $response->assertSeeText(trans('passwords.change.success'));
    }

    /** @test */
    public function pwd_recoveries_are_removed_when_pwd_reset_is_set_to_false(): void
    {
        $this->user = User::first();
        $this->actingAs($this->user, 'admins');
        $this->seed(PasswordRecoverySeeder::class);
        $this->patch(route(RoutingManager::adminAlias() . 'app_configuration.update'), array_merge($this->data(), ['allow_pwd_reset' => 0]));
        $this->assertDatabaseEmpty('password_recoveries');
    }

    private function data(): array
    {
        return AppConfiguration::current(true)->toArray();
    }
}
