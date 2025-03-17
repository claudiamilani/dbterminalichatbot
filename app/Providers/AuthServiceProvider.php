<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

namespace App\Providers;

use App\AppConfiguration;
use App\Auth\AdminGuard;
use App\Auth\AuthType;
use App\Auth\PasswordRecovery;
use App\Auth\Permission;
use App\Auth\User;
use App\Auth\Role;
use App\Auth\UserSession;
use App\DBT\Models\AttributeHeaderMapping;
use App\DBT\Models\LegacyImport;
use App\DBT\Models\TerminalConfig;
use App\DBT\Models\AttributeValue;
use App\DBT\Models\TerminalPicture;
use App\DBT\Models\Ota;
use App\DBT\Models\TransposeConfig;
use App\DBT\Models\Vendor;
use App\DBT\Models\Channel;
use App\DBT\Models\Document;
use App\DBT\Models\DocumentType;
use App\DBT\Models\AttrCategory;
use App\DBT\Models\DbtAttribute;
use App\DBT\Models\Terminal;
use App\DBT\Models\Tac;
use App\DBT\Models\Ingestion;
use App\DBT\Models\IngestionSource;
use App\DBT\TransposeRequest;
use App\Policies\AppConfigurationPolicy;
use App\Policies\AuthTypePolicy;
use App\Policies\DBT\AttributeHeaderMappingPolicy;
use App\Policies\DBT\AttributeValuePolicy;
use App\Policies\DBT\LegacyImportPolicy;
use App\Policies\DBT\TerminalConfigPolicy;
use App\Policies\DBT\TerminalPicturePolicy;
use App\Policies\DBT\OtaPolicy;
use App\Policies\DBT\TransposeConfigPolicy;
use App\Policies\DBT\TransposeRequestPolicy;
use App\Policies\DBT\VendorPolicy;
use App\Policies\DBT\AttrCategoryPolicy;
use App\Policies\DBT\AttributePolicy;
use App\Policies\DBT\ChannelPolicy;
use App\Policies\DBT\DocumentPolicy;
use App\Policies\DBT\DocumentTypePolicy;
use App\Policies\DBT\TerminalPolicy;
use App\Policies\DBT\TacPolicy;
use App\Policies\DBT\IngestionPolicy;
use App\Policies\DBT\IngestionSourcePolicy;
use App\Policies\PasswordRecoveryPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\UserSessionPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use PDOException;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        AppConfiguration::class => AppConfigurationPolicy::class,
        PasswordRecovery::class => PasswordRecoveryPolicy::class,
        UserSession::class => UserSessionPolicy::class,
        AuthType::class => AuthTypePolicy::class,
        Vendor::class => VendorPolicy::class,
        Channel::class => ChannelPolicy::class,
        Document::class => DocumentPolicy::class,
        DocumentType::class => DocumentTypePolicy::class,
        DbtAttribute::class => AttributePolicy::class,
        AttrCategory::class => AttrCategoryPolicy::class,
        AttributeValue::class => AttributeValuePolicy::class,
        Terminal::class => TerminalPolicy::class,
        TerminalPicture::class => TerminalPicturePolicy::class,
        TerminalConfig::class => TerminalConfigPolicy::class,
        Tac::class => TacPolicy::class,
        Ingestion::class => IngestionPolicy::class,
        IngestionSource::class => IngestionSourcePolicy::class,
        Ota::class => OtaPolicy::class,
        LegacyImport::class => LegacyImportPolicy::class,
        TransposeConfig::class => TransposeConfigPolicy::class,
        AttributeHeaderMapping::class => AttributeHeaderMappingPolicy::class,
        TransposeRequest::class => TransposeRequestPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPermissions();
        // add custom guard
        Auth::extend('admins', function ($app, $name, array $config) {
            return new AdminGuard(Auth::createUserProvider($config['provider']), $app->make('request'));
        });
    }

    public function registerPermissions(): void
    {
        try{
            if (Schema::hasTable('permissions')) {
                foreach (Permission::with('roles')->get() as $permission) {
                    Gate::define($permission->name, function ($user) use ($permission) {
                        return $user->hasRole($permission->roles);
                    });
                }
            }
        }catch(PDOException $e){
            // This method should just register permissions and should not report any db connection issues
        }
    }
}
