<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\Auth\AuthType;
use App\Auth\Drivers\SamlvasAuthDriver;
use App\Auth\ExternalRole;
use App\LftRouting\RoutingManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
;

class SamlvasAuthDriverTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        AuthType::find(AuthType::AZURE)->saveAsDefault();
        ExternalRole::create(['auth_type_id' => AuthType::SAMLVAS, 'external_role_id' => 'TEST_EXTERNAL_ROLE', 'auto_register_users' => 1]);
    }

    /** @test */
    public function a_user_can_authenticate_samlvas_backend()
    {
        ExternalRole::first()->roles()->sync([1]);
        $samlAuthDriverMock = Mockery::mock(SamlvasAuthDriver::class);
        $this->app->bind(SamlvasAuthDriver::class, function () use ($samlAuthDriverMock) {
            return $samlAuthDriverMock;
        });
        $samlAuthDriverMock->shouldReceive('apiCall')->andReturn($this->mockSamlVasResponse());
        $response = $this->get(route(RoutingManager::adminAlias() . 'samlVasRedirect', ['session_id' => 'xyz']));
        $response->assertRedirect(route(RoutingManager::adminHome()));
        $this->assertAuthenticated('admins');
    }
    /** @test */
    public function landing_page_without_session_id_parameter_will_redirect_to_login()
    {
        $response = $this->get(route(RoutingManager::adminAlias() . 'samlVasRedirect'));
        $response->assertRedirect(route(RoutingManager::adminLoginRoute()));
    }

    /** @test */
    public function landing_page_with_session_id_parameter_will_verify_session_id()
    {
        $response = $this->get(route(RoutingManager::adminAlias() . 'samlVasRedirect', ['session_id' => 'xyz']));
        $this->throwException()
    }
    /** @test */

    public function test_saml_vas_redirect_with_valid_session_id()
    {
        $sessionId = str_random(10);
        $driverMock = Mockery::mock(SamlvasAuthDriver::class);
        $driverMock->shouldReceive('apiCall')
            ->once()
            ->with($sessionId)
            ->andReturn(['test']);

        $response = $this->get(route(RoutingManager::adminAlias() . 'samlVasRedirect', ['session_id' => $sessionId]));

        // Verifica il redirect
        $response->assertRedirect(route(RoutingManager::adminHome()));
    }


    private function mockSamlVasResponse()
    {

        $user = [
            'data' => [
                'email' => 'lombardo@medialogic.it',
                'name' => 'Alberto',
                'surname' => 'Lombardo',
                'groups' => ['TEST_EXTERNAL_ROLE'],
                'session_id' => 'xyz'
            ],
        ];

        return $user;

    }

}