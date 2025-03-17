<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

/*
|--------------------------------------------------------------------------
| LFT Routes
|--------------------------------------------------------------------------
|
| LFT Base application routes
|
*/

Route::group(['middleware' => Config::get('lft.admin_routes.auth_middleware')], function () {

    // DBT routes
    include __DIR__.'/dbt.php';

    // Authenticated routes
    Route::post('logout', 'Auth\MyLoginController@logout')->name('logout');
    Route::get('/', 'HomeController@index')->name('dashboard');
    Route::post('toggleSidebar', 'HomeController@toggleSidebar')->name('toggleSidebar');
    Route::get('documentazione', 'HomeController@viewManual')->name('viewManual');
    //Route::get('/ricerca-globale', 'GlobalSearch\GlobalSearchController@search')->name('global_search');

    Route::group(['namespace' => 'Auth'], function () {
        //USER ROUTES
        Route::group(['prefix' => 'utenti', 'as' => 'users.'], function () {
            Route::get('/', 'UsersController@index')->name('index');
            Route::get('crea', 'UsersController@create')->name('create');
            Route::post('/', 'UsersController@store')->name('store');
            Route::get('{id}/modifica', 'UsersController@edit')->name('edit')->where('id', '[0-9]+');
            Route::patch('{id}', 'UsersController@update')->name('update')->where('id', '[0-9]+');
            Route::get('{id}/elimina', 'UsersController@delete')->name('delete')->where('id', '[0-9]+');
            Route::delete('{id}', 'UsersController@destroy')->name('destroy')->where('id', '[0-9]+');
            Route::get('/select2', 'UsersController@select2')->name('select2');
            Route::get('/list', 'UsersController@list')->name('list');
        });

        //AUTH TYPES ROUTES
        Route::group(['prefix' => 'tipi-di-autenticazione', 'as' => 'auth_types.'], function () {
            Route::get('/', 'AuthTypesController@index')->name('index');
            Route::get('{id}/modifica', 'AuthTypesController@edit')->name('edit')->where('id', '[0-9]+');
            Route::patch('{id}', 'AuthTypesController@update')->name('update')->where('id', '[0-9]+');
        });

        //USER SESSIONS ROUTES
        Route::group(['prefix' => 'sessioni-utente', 'as' => 'user_sessions.'], function () {
            Route::get('/', 'UserSessionsController@index')->name('index');
            Route::get('{id}/delete', 'UserSessionsController@delete')->name('delete');
            Route::get('{id}/purgeAuthenticated',
                'UserSessionsController@purgeAuthenticated')->name('purgeAuthenticated');
            Route::get('{id}/purgeUnauthenticated',
                'UserSessionsController@purgeUnauthenticated')->name('purgeUnauthenticated');
            Route::delete('{id}', 'UserSessionsController@destroy')->name('destroy');
            Route::delete('purge/{authenticated}', 'UserSessionsController@purge')->name('destroySessions');
        });

        //ROLES ROUTES
        Route::group(['prefix' => 'ruoli', 'as' => 'roles.'], function () {
            Route::get('/', 'RolesController@index')->name('index');
            Route::get('crea', 'RolesController@create')->name('create');
            Route::post('/', 'RolesController@store')->name('store');
            Route::get('{id}/modifica', 'RolesController@edit')->name('edit')->where('id', '[0-9]+');
            Route::patch('{id}', 'RolesController@update')->name('update')->where('id', '[0-9]+');
            Route::get('{id}/elimina', 'RolesController@delete')->name('delete')->where('id', '[0-9]+');
            Route::delete('{id}', 'RolesController@destroy')->name('destroy')->where('id', '[0-9]+');
            Route::get('/select2', 'RolesController@select2')->name('select2');
            Route::get('/reimposta-permessi-default',
                'RolesController@defaultPermissionsModal')->name('defaultPermissionsModal');
            Route::post('/reimposta-permessi-default',
                'RolesController@defaultPermissions')->name('defaultPermissions');
        });

        //PERMISSIONS ROUTES
        Route::group(['prefix' => 'permessi', 'as' => 'permissions.'], function () {
            Route::get('/', 'PermissionsController@index')->name('index');
            Route::post('modifica', 'PermissionsController@update')->name('update');
        });

        //PENDING PWD RESETS ROUTES
        Route::group(['prefix' => 'richieste-recupero-password', 'as' => 'pending_pwd_resets.'], function () {
            Route::get('/', 'PendingPwdResetsController@index')->name('index');
            Route::get('{id}', 'PendingPwdResetsController@show')->name('show')->where('id', '[0-9]+');
            Route::get('{id}/elimina', 'PendingPwdResetsController@delete')->name('delete')->where('id', '[0-9]+');
            Route::delete('{id}', 'PendingPwdResetsController@destroy')->name('destroy')->where('id', '[0-9]+');
        });

        //EXTERNAL ROLES ROUTES
        Route::group(['prefix' => 'ruoli-esterni', 'as' => 'external_roles.'], function () {
            Route::get('/', 'ExternalRolesController@index')->name('index');
            Route::get('crea', 'ExternalRolesController@create')->name('create');
            Route::post('/', 'ExternalRolesController@store')->name('store');
            Route::get('{id}/modifica', 'ExternalRolesController@edit')->name('edit')->where('id', '[0-9]+');
            Route::patch('{id}', 'ExternalRolesController@update')->name('update')->where('id', '[0-9]+');
            Route::get('{id}/elimina', 'ExternalRolesController@delete')->name('delete')->where('id', '[0-9]+');
            Route::delete('{id}', 'ExternalRolesController@destroy')->name('destroy')->where('id', '[0-9]+');

            Route::get('/select2', 'ExternalRolesController@select2')->name('select2');
        });
    });

    //APP CONFIGURATION ROUTES
    Route::group(['prefix' => 'configurazione', 'as' => 'app_configuration.'], function () {
        Route::get('/', 'AppConfigurationsController@show')->name('show');
        Route::get('/modifica', 'AppConfigurationsController@edit')->name('edit');
        Route::get('/manual', 'AppConfigurationsController@viewManual')->name('view_manual');
        Route::patch('/update', 'AppConfigurationsController@update')->name('update');
    });
});

Route::get('login', 'Auth\MyLoginController@showLogin')->name('login');
Route::post('login', 'Auth\MyLoginController@login');
Route::get('logout', 'Auth\MyLoginController@showLogin')->name('logoutUnauthenticated');
Route::get('password/reset', 'Auth\MyResetPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\MyResetPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\MyResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\MyResetPasswordController@reset')->name('password.execute_reset');

Route::get('oauth_login', 'Auth\MyLoginController@loginAzure')->name('loginAzure');
Route::get('oauth2/azure', 'Auth\MyLoginController@azureRedirect')->name('azureRedirect');

Route::get('samlvas/login', 'Auth\MyLoginController@loginSamlVas')->name('loginSamlVas');
Route::get('samlvas/landing', 'Auth\MyLoginController@samlVasRedirect')->name('samlVasRedirect');
