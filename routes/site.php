<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

/*
|--------------------------------------------------------------------------
| Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register site routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => Config::get('lft.public_routes.auth_middleware')], function () {
    //Route::post('logout', 'App\Http\Controllers\Site\SiteLoginController@logout')->name('logout');
    // Authenticated routes
    /*Route::get('/example-authenticated-route', function () {
        return 'ok';
    });*/
});
/*Route::group(['namespace' => 'App\Http\Controllers\Site'], function () {
    Route::get('logout', 'SiteLoginController@showLogin');
    Route::get('password/reset', 'SiteResetPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/reset', 'SiteResetPasswordController@reset')->name('password.execute_reset');
    Route::get('password/reset/{token}', 'SiteResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/email', 'SiteResetPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('login', 'SiteLoginController@showLogin')->name('login');
    Route::post('login', 'SiteLoginController@login');
    Route::get('/', 'SiteController@index')->name('index');

    Route::get('oauth_login','SiteLoginController@loginAzure')->name('loginAzure');
    Route::get('oauth2/azure','SiteLoginController@azureRedirect')->name('azureRedirect');
});*/
// CONFIGURA VAS
Route::group(['namespace' => 'App\Http\Controllers\DBT','as' => 'dbt.'],function(){
    Route::group(['as' => 'configurazione-vas.', 'prefix' => 'configura.vas'], function () {
        Route::get('/', 'ConfigurationController@showFormVas')
            ->name('showForm');

        Route::get('/vendors/{vendor}/models', 'ConfigurationController@getModels')
            ->name('getModels');

        Route::post('/', 'ConfigurationController@showTechSheetVas')
            ->name('showTechSheet');

        Route::get('/inviaMail', 'ConfigurationController@ShowSendMailModalVas')
            ->name('sendMail');

        Route::post('/invia-mail', 'ConfigurationController@sendMailVas')
            ->name('send-mail');

        Route::get('/inviaOta', 'ConfigurationController@ShowSendOtaModalVas')
            ->name('sendOta');

        Route::post('/invia-ota', 'ConfigurationController@sendOta')
            ->name('send-ota');
    });

// CONFIGURA WINDTRE
    Route::group(['as' => 'configurazione-windtre.', 'prefix' => 'configura.windtre'], function () {
        Route::get('/', 'ConfigurationController@showFormWindtre')
            ->name('showForm');

        Route::get('/vendors/{vendor}/models', 'ConfigurationController@getModels')
            ->name('getModels');

        Route::post('/', 'ConfigurationController@showTechSheetWindtre')
            ->name('showTechSheet');

        Route::get('/mostra-caratteristiche-tecniche/{terminal_id}',
            'ConfigurationController@showTechSheetModalWindtre')
            ->name('show-tech-sheet');

        Route::get('/inviaOta', 'ConfigurationController@ShowSendOtaModalWindtre')
            ->name('sendOta');

        Route::post('/invia-ota', 'ConfigurationController@sendOta')
            ->name('send-ota');
    });
});






