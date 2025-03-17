<?php

use App\Auth\Permission;
use App\Auth\PermissionType;
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
        Schema::table('permissions', function (Blueprint $table) {
            // CHANNELS
            $permission_type = PermissionType::create(['name' => 'Channels']);
            Permission::unguard();
            Permission::create(['name' => 'create_channels', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_channels', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_channels', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_channels', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_channels', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // INGESTIONS
            $permission_type = PermissionType::create(['name' => 'Ingestions']);
            Permission::unguard();
            Permission::create(['name' => 'create_ingestions', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_ingestions', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_ingestions', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_ingestions', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_ingestions', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // INGESTION SOURCES
            $permission_type = PermissionType::create(['name' => 'IngestionSources']);
            Permission::unguard();
            Permission::create(['name' => 'create_ingestion_sources', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_ingestion_sources', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_ingestion_sources', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_ingestion_sources', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_ingestion_sources', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            // CHANNELS
            Permission::whereIn('name', [
                'create_channels', 'view_channels', 'update_channels', 'list_channels', 'delete_channels'
            ])->delete();
            PermissionType::where('name', 'Channels')->delete();

            // INGESTIONS
            Permission::whereIn('name', [
                'create_ingestions', 'view_ingestions', 'update_ingestions', 'list_ingestions', 'delete_ingestions'
            ])->delete();
            PermissionType::where('name', 'Ingestions')->delete();

            // INGESTION SOURCES
            Permission::whereIn('name', [
                'create_ingestion_sources', 'view_ingestion_sources', 'update_ingestion_sources', 'list_ingestion_sources', 'delete_ingestion_sources'
            ])->delete();
            PermissionType::where('name', 'IngestionSources')->delete();
        });
    }
};
