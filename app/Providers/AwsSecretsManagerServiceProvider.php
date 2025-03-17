<?php

namespace App\Providers;

use App\Aws\AwsSecretsManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AwsSecretsManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (config('aws-secrets-manager.enable_secrets_manager')) {
            $secretsManager = new AwsSecretsManager();
            $secretsManager->loadSecrets();
        }
    }
}
