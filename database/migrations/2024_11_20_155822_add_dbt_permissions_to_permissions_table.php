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

            // VENDORS
            $permission_type = PermissionType::create(['name' => 'Vendors']);
            Permission::unguard();
            Permission::create(['name' => 'create_vendors', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_vendors', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_vendors', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_vendors', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_vendors', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // ATTR CATEGORIES
            $permission_type = PermissionType::create(['name' => 'AttrCategories']);
            Permission::unguard();
            Permission::create(['name' => 'create_attr_categories', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_attr_categories', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_attr_categories', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_attr_categories', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_attr_categories', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // ATTRIBUTES
            $permission_type = PermissionType::create(['name' => 'Attributes']);
            Permission::unguard();
            Permission::create(['name' => 'create_attributes', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_attributes', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_attributes', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_attributes', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_attributes', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // DOCUMENT TYPES
            $permission_type = PermissionType::create(['name' => 'DocumentTypes']);
            Permission::unguard();
            Permission::create(['name' => 'create_document_types', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_document_types', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_document_types', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_document_types', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_document_types', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // DOCUMENTS
            $permission_type = PermissionType::create(['name' => 'Documents']);
            Permission::unguard();
            Permission::create(['name' => 'create_documents', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_documents', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_documents', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_documents', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_documents', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // OTAS
            $permission_type = PermissionType::create(['name' => 'Otas']);
            Permission::unguard();
            Permission::create(['name' => 'create_otas', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_otas', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_otas', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_otas', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_otas', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // TERMINALS
            $permission_type = PermissionType::create(['name' => 'Terminals']);
            Permission::unguard();
            Permission::create(['name' => 'create_terminals', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_terminals', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_terminals', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_terminals', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_terminals', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // TACS
            $permission_type = PermissionType::create(['name' => 'Tacs']);
            Permission::unguard();
            Permission::create(['name' => 'create_tacs', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_tacs', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_tacs', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_tacs', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_tacs', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // TERMINAL PICTURES
            $permission_type = PermissionType::create(['name' => 'TerminalPictures']);
            Permission::unguard();
            Permission::create(['name' => 'create_terminal_pictures', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_terminal_pictures', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_terminal_pictures', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_terminal_pictures', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_terminal_pictures', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // TERMINAL CONFIGS
            $permission_type = PermissionType::create(['name' => 'TerminalConfigs']);
            Permission::unguard();
            Permission::create(['name' => 'create_terminal_configs', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_terminal_configs', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_terminal_configs', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_terminal_configs', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_terminal_configs', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // ATTRIBUTE VALUES
            $permission_type = PermissionType::create(['name' => 'AttributeValues']);
            Permission::unguard();
            Permission::create(['name' => 'create_attribute_values', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_attribute_values', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // ATTRIBUTE HEADER MAPPINGS
            $permission_type = PermissionType::create(['name' => 'AttributeHeaderMappings']);
            Permission::unguard();
            Permission::create(['name' => 'create_attribute_header_mappings', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_attribute_header_mappings', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_attribute_header_mappings', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_attribute_header_mappings', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_attribute_header_mappings', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // LEGACY IMPORTS
            $permission_type = PermissionType::create(['name' => 'LegacyImports']);
            Permission::unguard();
            Permission::create(['name' => 'create_legacy_imports', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_legacy_imports', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_legacy_imports', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_legacy_imports', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_legacy_imports', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

            // TRANSPOSE CONFIGS
            $permission_type = PermissionType::create(['name' => 'TransposeConfigs']);
            Permission::unguard();
            Permission::create(['name' => 'create_transpose_configs', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_transpose_configs', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'update_transpose_configs', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_transpose_configs', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_transpose_configs', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            // VENDORS
            Permission::whereIn('name', [
                'create_vendors', 'view_vendors', 'update_vendors', 'list_vendors', 'delete_vendors'
            ])->delete();
            PermissionType::where('name', 'Vendors')->delete();

            // ATTR CATEGORIES
            Permission::whereIn('name', [
                'create_attr_categories', 'view_attr_categories', 'update_attr_categories', 'list_attr_categories', 'delete_attr_categories'
            ])->delete();
            PermissionType::where('name', 'AttrCategories')->delete();

            // ATTRIBUTES
            Permission::whereIn('name', [
                'create_attributes', 'view_attributes', 'update_attributes', 'list_attributes', 'delete_attributes'
            ])->delete();
            PermissionType::where('name', 'Attributes')->delete();

            // DOCUMENT TYPES
            Permission::whereIn('name', [
                'create_document_types', 'view_document_types', 'update_document_types', 'list_document_types', 'delete_document_types'
            ])->delete();
            PermissionType::where('name', 'DocumentTypes')->delete();

            // DOCUMENTS
            Permission::whereIn('name', [
                'create_documents', 'view_documents', 'update_documents', 'list_documents', 'delete_documents'
            ])->delete();
            PermissionType::where('name', 'Documents')->delete();

            // OTAS
            Permission::whereIn('name', [
                'create_otas', 'view_otas', 'update_otas', 'list_otas', 'delete_otas'
            ])->delete();
            PermissionType::where('name', 'Otas')->delete();

            // TERMINALS
            Permission::whereIn('name', [
                'create_terminals', 'view_terminals', 'update_terminals', 'list_terminals', 'delete_terminals'
            ])->delete();
            PermissionType::where('name', 'Terminals')->delete();

            // TACS
            Permission::whereIn('name', [
                'create_tacs', 'view_tacs', 'update_tacs', 'list_tacs', 'delete_tacs'
            ])->delete();
            PermissionType::where('name', 'Tacs')->delete();

            // TERMINAL PICTURES
            Permission::whereIn('name', [
                'create_terminal_pictures', 'view_terminal_pictures', 'update_terminal_pictures', 'list_terminal_pictures', 'delete_terminal_pictures'
            ])->delete();
            PermissionType::where('name', 'TerminalPictures')->delete();

            // TERMINAL CONFIGS
            Permission::whereIn('name', [
                'create_terminal_configs', 'view_terminal_configs', 'update_terminal_configs', 'list_terminal_configs', 'delete_terminal_configs'
            ])->delete();
            PermissionType::where('name', 'TerminalConfigs')->delete();

            // ATTRIBUTE VALUES
            Permission::whereIn('name', [
                'create_attribute_values', 'update_attribute_values'
            ])->delete();
            PermissionType::where('name', 'AttributeValues')->delete();

            // ATTRIBUTE HEADER MAPPINGS
            Permission::whereIn('name', [
                'create_attribute_header_mappings', 'view_attribute_header_mappings', 'update_attribute_header_mappings', 'list_attribute_header_mappings', 'delete_attribute_header_mappings'
            ])->delete();
            PermissionType::where('name', 'AttributeHeaderMappings')->delete();

            // LEGACY IMPORTS
            Permission::whereIn('name', [
                'create_legacy_imports', 'view_legacy_imports', 'update_legacy_imports', 'list_legacy_imports', 'delete_legacy_imports'
            ])->delete();
            PermissionType::where('name', 'LegacyImports')->delete();

            // TRANSPOSE CONFIGS
            Permission::whereIn('name', [
                'create_transpose_configs', 'view_transpose_configs', 'update_transpose_configs', 'list_transpose_configs', 'delete_transpose_configs'
            ])->delete();
            PermissionType::where('name', 'TransposeConfigs')->delete();
        });
    }
};
