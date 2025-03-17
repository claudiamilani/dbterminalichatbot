<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

use App\Auth\ExternalRole;
use App\Auth\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('external_role_role', function (Blueprint $table) {
            $table->unsignedBigInteger('external_role_id')->index();
            $table->unsignedBigInteger('role_id')->index();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->timestamps();
        });



        foreach(config('lft.defaults.external_roles.'.config('app.env')) as $k => $v){
            foreach($v as $local_role ){
                Role::upsert([['name' => $local_role]],['name']);
            }
            $ext_role = ExternalRole::firstOrCreate(
                ['auth_type_id' => 3,'external_role_id' => $k]
            );
            $ext_role->roles()->sync(Role::whereIn('name',$v)->pluck('id'));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_role_role');
    }
};
