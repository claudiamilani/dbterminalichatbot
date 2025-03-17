<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Roles Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    'title' => 'Roles',
    'menu_title' => 'Roles',
    'create' => [
        'title' => 'Create new Role',
        'success' => 'Role created successfully.',
        'error' => 'An error occurred. Role not created.',
    ],
    'edit' => [
        'title' => 'Edit Role',
        'success' => 'Role updated successfully.',
        'error' => 'An error occurred. Role not updated.',
        'success_but_in_use' => 'Role is actually utilized preventing name or description being updated. Only permissions were saved.',
        'in_use' => 'You cannot change the name or description when the role is in use.'

    ],
    'delete' => [
        'title' => 'Delete Role',
        'confirm_msg' => "Please confirm Role deletion.",
        'success' => "Role successfully deleted.",
        'error' => "An error occurred. Role not deleted.",
    ],
    'attributes' => [
        'name' => 'Name',
        'description' => 'Description',
        'permissions' => 'Permissions',
        'users_count' => 'Users count'
    ],
    'placeholders' => [
    ],


];
