<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Jobs;

use App\Auth\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Socialite\Facades\Socialite;

class UpdateAzureUserProfileImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $token;
    private User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $encrypted_token)
    {
        $this->user = $user;
        $this->token = $encrypted_token;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token = decrypt($this->token);
        $profile_image = Socialite::driver('mdl-azure')->getProfileImage($token);
        if($this->user->isAzure()){
            $this->user->authType->driverInstance->saveProfileImage($this->user,$profile_image);
        }
    }
}
