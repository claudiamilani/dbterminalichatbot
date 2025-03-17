<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

namespace Tests\Feature;

use App\Auth\AuthType;
use App\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function  setUp(): void
    {
        parent::setUp();

        User::reguard();
    }

    /** @test */
    public function an_admin_can_delete_a_user_account(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admins');

        $this->post(route('admin::users.store'), array_merge($this->data(), ['password' => 'M3dialogic$', 'password_check' => 'M3dialogic$']))->assertStatus(302);

        $user = User::where('user', 'rossi@medialogic.it')->first();

        $response = $this->delete(route('admin::users.destroy', $user->id))->assertStatus(302);

        $response->assertRedirectToRoute('admin::users.index');

        $this->assertDatabaseMissing('users', [
            'user' => 'rossi@medialogic.it'
        ]);
    }

    private function data(): array
    {
        return [
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'rossi@medialogic.it',
            'user' => 'rossi@medialogic.it',
            'roles' => [3],
            'auth_type_id' => AuthType::LOCAL
        ];
    }
}
