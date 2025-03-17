<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\AppConfiguration;
use App\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppConfigUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function max_failed_login_attempts_is_required(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['max_failed_login_attempts' => null]))->assertStatus(302);

        $response->assertSessionHasErrors('max_failed_login_attempts');
    }

    /** @test */
    public function max_failed_login_attempts_is_zero_or_more(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['max_failed_login_attempts' => -1]))->assertStatus(302);

        $response->assertSessionHasErrors(['max_failed_login_attempts' => trans('app_configuration.attributes.max_failed_login_attempts') . ' deve essere un numero maggiore o uguale a 0.']);
    }

    /** @test */
    public function failed_login_reset_interval_is_required(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['failed_login_reset_interval' => null]))->assertStatus(302);

        $response->assertSessionHasErrors('failed_login_reset_interval');
    }

    /** @test */
    public function failed_login_reset_interval_is_one_or_more(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['failed_login_reset_interval' => 0]))->assertStatus(302);

        $response->assertSessionHasErrors(['failed_login_reset_interval' => trans('app_configuration.attributes.failed_login_reset_interval') . ' deve essere un numero maggiore o uguale a 1.']);
    }

    /** @test */
    public function pwd_min_length_is_required(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['pwd_min_length' => null]))->assertStatus(302);

        $response->assertSessionHasErrors('pwd_min_length');
    }

    /** @test */
    public function pwd_min_length_is_eight_or_more(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['pwd_min_length' => 7]))->assertStatus(302);

        $response->assertSessionHasErrors(['pwd_min_length' => trans('app_configuration.attributes.pwd_min_length') . ' deve essere un numero maggiore o uguale a 8.']);
    }

    /** @test */
    public function pwd_regexp_is_required(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['pwd_regexp' => null]))->assertStatus(302);

        $response->assertSessionHasErrors('pwd_regexp');
    }

    /** @test */
    public function pwd_regexp_is_valid(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['pwd_regexp' => '1234']))->assertStatus(302);

        $response->assertSessionHasErrors(['pwd_regexp' => trans('app_configuration.attributes.pwd_regexp') . ' non è una espressione regolare valida.']);
    }

    /** @test */
    public function pwd_history_is_required(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['pwd_history' => null]))->assertStatus(302);

        $response->assertSessionHasErrors('pwd_history');
    }

    /** @test */
    public function pwd_history_is_zero_or_more(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['pwd_history' => -1]))->assertStatus(302);

        $response->assertSessionHasErrors(['pwd_history' => trans('app_configuration.attributes.pwd_history') . ' deve essere un numero maggiore a 0 e minore a 99']);
    }

    /** @test */
    public function pwd_history_is_less_than_99(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['pwd_history' => 100]))->assertStatus(302);

        $response->assertSessionHasErrors(['pwd_history' => trans('app_configuration.attributes.pwd_history') . ' deve essere un numero maggiore a 0 e minore a 99']);
    }

    /** @test */
    public function pwd_expires_in_is_required(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['pwd_expires_in' => null]))->assertStatus(302);

        $response->assertSessionHasErrors('pwd_expires_in');
    }

    /** @test */
    public function pwd_expires_in_is_zero_or_more(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['pwd_expires_in' => -1]))->assertStatus(302);

        $response->assertSessionHasErrors(['pwd_expires_in' => trans('app_configuration.attributes.pwd_expires_in') . ' deve essere un numero maggiore a 0 e minore a 99']);
    }

    /** @test */
    public function pwd_expires_in_is_less_than_99(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['pwd_expires_in' => 100]))->assertStatus(302);

        $response->assertSessionHasErrors(['pwd_expires_in' => trans('app_configuration.attributes.pwd_expires_in') . ' deve essere un numero maggiore a 0 e minore a 99']);
    }

    /** @test */
    public function pwd_complexity_err_msg_is_required(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $response = $this->patch(route('admin::app_configuration.update'), array_merge($this->data(), ['pwd_complexity_err_msg' => null]))->assertStatus(302);

        $response->assertSessionHasErrors('pwd_complexity_err_msg');
    }

    private function data(): array
    {
        return AppConfiguration::current(true)->toArray();
    }
}
