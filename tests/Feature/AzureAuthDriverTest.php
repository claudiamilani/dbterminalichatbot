<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\Auth\AuthType;
use App\Auth\ExternalRole;
use App\Auth\User;
use App\LftRouting\RoutingManager;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class AzureAuthDriverTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        AuthType::find(AuthType::AZURE)->saveAsDefault();
        ExternalRole::create(['auth_type_id' => AuthType::AZURE,'external_role_id'=>'TEST_EXTERNAL_ROLE','auto_register_users'=>1]);
    }

    /** @test */
    public function a_user_can_authenticate_azure_backend()
    {
        ExternalRole::first()->roles()->sync([1]);

        Socialite::shouldReceive('driver')->with('mdl-azure')->andReturn(
            $this->mockMdlAzureProvider()
        );
        $response = $this->get(route(RoutingManager::adminAlias() . 'azureRedirect'));
        
        $response->assertRedirect(route(RoutingManager::adminHome()));
        $this->assertAuthenticated('admins');
    }

    /** @test */
    public function a_user_can_authenticate_azure_frontend()
    {
        ExternalRole::first()->roles()->sync([1]);

        if(!config('lft.public_routes.enabled')){
            $this->assertTrue(true);
            return;
        }
        Socialite::shouldReceive('driver')->with('mdl-azure')->andReturn(
            $this->mockMdlAzureProvider()
        );

        $response = $this->get(route(RoutingManager::publicAlias() . 'azureRedirect'));
        $response->assertRedirect(route(RoutingManager::home()));
        $this->assertAuthenticated('web');
    }

    /** @test */
    public function a_user_can_not_authenticate_azure_if_auth_type_disabled(): void
    {
        AuthType::find(AuthType::LOCAL)->saveAsDefault();
        $this->setAuthTypes([
            'id' => AuthType::AZURE,
            'enabled' => 0,
        ]);

        $login_page = $this->get(route(RoutingManager::adminLoginRoute()));
        $login_page->assertDontSeeText("Azure");
        $response = $this->followingRedirects()->post(route(RoutingManager::adminLoginRoute(), ['user' => 'test', 'password' => 'test123!', 'auth_type' => AuthType::AZURE]));
        $response->assertSeeText(trans('auth.ldap_unknown_user'));
    }

    /** @test */
    public function a_user_can_not_auto_register_azure_account_if_auto_reg_disabled()
    {
        AuthType::find(AuthType::LOCAL)->saveAsDefault();
        AuthType::find(AuthType::AZURE)->fill(['auto_register' => 0])->save();

        Socialite::shouldReceive('driver')->with('mdl-azure')->andReturn(
            $this->mockMdlAzureProvider()
        );

        $response = $this->followingRedirects()->get(route(RoutingManager::adminAlias() . 'azureRedirect'));

        $this->assertDatabaseMissing('users', [
            'user' => 'lombardo@medialogic.it'
        ]);

        $this->assertGuest();
        $response->assertSee(trans('auth.account_not_configured'));
    }

    /** @test */
    public function a_user_can_not_login_azure_if_external_role_not_configured()
    {
        ExternalRole::truncate();
        Socialite::shouldReceive('driver')->with('mdl-azure')->andReturn(
            $this->mockMdlAzureProvider()
        );

        $response = $this->followingRedirects()->get(route(RoutingManager::adminAlias() . 'azureRedirect'));

        $this->assertDatabaseHas('users', [
            'user' => 'lombardo@medialogic.it'
        ]);

        $this->assertGuest();
        $response->assertSee(trans('auth.account_not_configured'));
    }

    /** @test */
    public function an_azure_user_can_never_reset_password()
    {
        $user = User::create([
            'user' => 'lombardo@medialogic.it',
            'name' => 'Alberto',
            'surname' => 'Lombardo',
            'enabled' => 1,
            'password' => 'password'
        ]);
        $response = $this->from(route(RoutingManager::adminLoginRoute()))->post(route(RoutingManager::adminAlias() . 'password.email'), ['user' => $user->user]);
        $this->assertDatabaseMissing('password_recoveries', [
            'user' => 'lombardo@medialogic.it'
        ]);
        $response->assertRedirectToRoute(RoutingManager::adminLoginRoute());
    }

    /** @test */
    public function on_azure_user_login_success_login_info_are_updated()
    {
        ExternalRole::first()->roles()->sync([1]);

        Socialite::shouldReceive('driver')->with('mdl-azure')->andReturn(
            $this->mockMdlAzureProvider()
        );
        $response = $this->get(route(RoutingManager::adminAlias() . 'azureRedirect'));
        $response->assertRedirect(route(RoutingManager::adminHome()));
        $this->assertDatabaseHas('users', [

            'user' => 'lombardo@medialogic.it',
            'login_success_on' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    private function mockAzureProvider()
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\MicrosoftProvider');

        $user = ['user' => [
            'mail' => 'lombardo@medialogic.it',
            'givenName' => 'Alberto',
            'surname' => 'Lombardo',
            'groups' => [
                'GROUP1',
                'GROUP2'
            ]
        ]];

        $provider->shouldReceive('user')->andReturn((object)$user);
        $provider->shouldReceive('stateless')->andReturn($provider);

        return $provider;
    }

    private function mockMdlAzureProvider()
    {
        $provider = Mockery::mock('App\Auth\SocialiteProviders\Medialogic\MdlAzureProvider');

        $user = ['user' => [
            'mail' => 'lombardo@medialogic.it',
            'givenName' => 'Alberto',
            'surname' => 'Lombardo',
            'groups' => ['TEST_EXTERNAL_ROLE']
        ],'token' => 'xyz'];

        $provider->shouldReceive('userWithGroups')->andReturn((object)$user);
        $provider->shouldReceive('getProfileImage')->andReturn(['type' => null,'content' => null]);
        $provider->shouldReceive('stateless')->andReturn($provider);

        return $provider;
    }

    private function setAuthTypes(array $params): void
    {
        $auth_type = AuthType::findOrFail($params['id']);
        $auth_type->fill($params);
        $auth_type->save();
    }
}