<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

use App\AppConfiguration;
use App\Auth\Permission;
use App\Auth\PermissionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('app_configurations', function (Blueprint $table) {
            $table->id();
            $table->text('pwdr_mail_obj_u')->comment('Object mail reset utente backend')->nullable();
            $table->text('pwdr_mail_body_u')->comment('Body mail reset utente backend')->nullable();
            $table->unsignedInteger('max_failed_login_attempts')->default(3);
            $table->unsignedInteger('failed_login_reset_interval')->default(1);
            $table->unsignedInteger('pwd_reset_unlocks_account')->default(0);
            $table->unsignedInteger('pwd_min_length')->default(8);
            $table->unsignedTinyInteger('pwd_history')->default(3);
            $table->unsignedInteger('pwd_expires_in')->default(1);
            $table->unsignedInteger('pwd_never_expires')->default(1);
            $table->string('pwd_regexp')->default('/^.*(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d])(?=.*[!$#%@^&+=]).*$/')->nullable();
            $table->string('pwd_complexity_err_msg')->default('La password deve contenere almeno una lettera maiuscola, un numero, un carattere speciale tra !$#%@^&+= ed essere lunga almeno 8 caratteri.')->nullable();
            $table->string('manual_file_name')->nullable();
            $table->string('manual_file_path')->nullable();
            $table->timestamps();
        });

        $permission_type = PermissionType::create(['name' => 'AppConfiguration']);
        Permission::unguard();
        Permission::create(['name' => 'view_app_configuration','label' => 'View','permission_type_id' => $permission_type->id]);
        Permission::create(['name' => 'update_app_configuration','label' => 'Update','permission_type_id' => $permission_type->id]);
        Permission::reguard();
        AppConfiguration::create();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('app_configurations');
        Permission::whereIn('name',
            [
                'view_app_configuration',
                'update_app_configuration',
            ])->delete();
        PermissionType::where('name','AppConfiguration')->delete();
    }
}
