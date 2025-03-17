<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

/*
|--------------------------------------------------------------------------
| DBTerminali Routes
|--------------------------------------------------------------------------
|
|
*/

Route::group(['as' => 'dbt.', 'namespace' => 'DBT'], function () {
    Route::group(['prefix' => 'categorie-attributi', 'as' => 'attr_categories.'], function () {
        Route::get('/', 'AttrCategoryController@index')->name('index');
        Route::get('crea', 'AttrCategoryController@create')->name('create');
        Route::post('/', 'AttrCategoryController@store')->name('store');
        Route::get('{id}/modifica', 'AttrCategoryController@edit')->name('edit')->where('id', '[0-9]+');
        Route::patch('{id}', 'AttrCategoryController@update')->name('update')->where('id', '[0-9]+');
        Route::get('{id}', 'AttrCategoryController@show')->name('show')->where('id', '[0-9]+');
        Route::get('{id}/elimina', 'AttrCategoryController@delete')->name('delete')->where('id', '[0-9]+');
        Route::delete('{id}', 'AttrCategoryController@destroy')->name('destroy')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'attributi', 'as' => 'attributes.'], function () {
        Route::get('/', 'AttributeController@index')->name('index');
        Route::get('crea', 'AttributeController@create')->name('create');
        Route::post('/', 'AttributeController@store')->name('store');
        Route::get('{id}/modifica', 'AttributeController@edit')->name('edit')->where('id', '[0-9]+');
        Route::patch('{id}', 'AttributeController@update')->name('update')->where('id', '[0-9]+');
        Route::get('{id}', 'AttributeController@show')->name('show')->where('id', '[0-9]+');
        Route::get('{id}/elimina', 'AttributeController@delete')->name('delete')->where('id', '[0-9]+');
        Route::delete('{id}', 'AttributeController@destroy')->name('destroy')->where('id', '[0-9]+');

        Route::get('select2category', 'AttributeController@select2Category')->name('select2Category');
        Route::get('select2ingestion', 'AttributeController@select2Ingestion')->name('select2Ingestion');
        Route::get('select2ingestionSource', 'AttributeController@select2ingestionSource')->name('select2ingestionSource');
        Route::get('select2TypeOptions', 'AttributeController@select2TypeOptions')->name('select2TypeOptions');

        Route::get('select2SearchableAttribute', 'AttributeController@select2SearchableAttributes')->name('select2SearchableAttributes');

        Route::post('loadOptions', 'AttributeController@loadOptions')->name('loadOptions');
        Route::post('loadInputTypeOptions', 'AttributeController@loadInputTypeOptions')->name('loadInputTypeOptions');
        Route::get('select2', 'AttributeController@select2')->name('select2');
    });

    Route::group(['as' => 'channels.', 'prefix' => 'canali'], function () {
        Route::get('/', 'ChannelController@index')->name('index');
        Route::get('/{id}', 'ChannelController@show')->name('show')->where(['id' => '[0-9]+']);
        Route::get('/crea', 'ChannelController@create')->name('create');
        Route::post('/', 'ChannelController@store')->name('store');
        Route::get('/{id}/modifica', 'ChannelController@edit')->name('edit')->where(['id' => '[0-9]+']);
        Route::patch('{id}', 'ChannelController@update')->name('update')->where(['id' => '[0-9]+']);
        Route::get('{id}/elimina', 'ChannelController@delete')->name('delete')->where(['id' => '[0-9]+']);
        Route::delete('{id}', 'ChannelController@destroy')->name('destroy')->where(['id' => '[0-9]+']);
        Route::get('select2', 'ChannelController@select2')->name('select2');
    });

    Route::group(['as' => 'document_types.', 'prefix' => 'tipi-documento'], function () {
        Route::get('/', 'DocumentTypeController@index')->name('index');
        Route::get('/{id}', 'DocumentTypeController@show')->name('show')->where(['id' => '[0-9]+']);
        Route::get('/crea', 'DocumentTypeController@create')->name('create');
        Route::post('/', 'DocumentTypeController@store')->name('store');
        Route::get('/{id}/modifica', 'DocumentTypeController@edit')->name('edit')->where(['id' => '[0-9]+']);
        Route::patch('{id}', 'DocumentTypeController@update')->name('update')->where(['id' => '[0-9]+']);
        Route::get('{id}/elimina', 'DocumentTypeController@delete')->name('delete')->where(['id' => '[0-9]+']);
        Route::delete('{id}', 'DocumentTypeController@destroy')->name('destroy')->where(['id' => '[0-9]+']);
        Route::get('select2', 'DocumentTypeController@select2')->name('select2');
    });

    Route::group(['as' => 'documents.', 'prefix' => 'documenti'], function () {
        Route::get('/', 'DocumentController@index')->name('index');
        Route::get('/{id}', 'DocumentController@show')->name('show')->where(['id' => '[0-9]+']);
        Route::get('/crea', 'DocumentController@create')->name('create');
        Route::post('/', 'DocumentController@store')->name('store');
        Route::get('/{id}/modifica', 'DocumentController@edit')->name('edit')->where(['id' => '[0-9]+']);
        Route::patch('{id}', 'DocumentController@update')->name('update')->where(['id' => '[0-9]+']);
        Route::get('{id}/elimina', 'DocumentController@delete')->name('delete')->where(['id' => '[0-9]+']);
        Route::delete('{id}', 'DocumentController@destroy')->name('destroy')->where(['id' => '[0-9]+']);
    });

    Route::group(['as' => 'vendors.', 'prefix' => 'vendors'], function () {
        Route::get('/', 'VendorController@index')->name('index');
        Route::get('create', 'VendorController@create')->name('create');
        Route::post('/', 'VendorController@store')->name('store');
        Route::get('{id}', 'VendorController@show')->where('id', '[0-9]+')->name('show');
        Route::get('{id}/modifica', 'VendorController@edit')->where('id', '[0-9]+')->name('edit');
        Route::patch('{id}', 'VendorController@update')->where('id', '[0-9]+')->name('update');
        Route::get('{id}/elimina', 'VendorController@delete')->where('id', '[0-9]+')->name('delete');
        Route::delete('{id}', 'VendorController@destroy')->where('id', '[0-9]+')->name('destroy');
    });

    Route::group(['as' => 'terminals.', 'prefix' => 'terminali'], function () {
        Route::get('/', 'TerminalController@index')->name('index');
        Route::get('/{id}', 'TerminalController@show')->name('show')->where(['id' => '[0-9]+']);
        Route::get('/crea', 'TerminalController@create')->name('create');
        Route::post('/', 'TerminalController@store')->name('store');
        Route::get('/{id}/modifica', 'TerminalController@edit')->name('edit')->where(['id' => '[0-9]+']);
        Route::patch('{id}', 'TerminalController@update')->name('update')->where(['id' => '[0-9]+']);
        Route::get('{id}/elimina', 'TerminalController@delete')->name('delete')->where(['id' => '[0-9]+']);
        Route::delete('{id}', 'TerminalController@destroy')->name('destroy')->where(['id' => '[0-9]+']);
        Route::get('/export', 'TerminalController@export')->name('export')->where(['id' => '[0-9]+']);
        Route::get('/exportTranspose', 'TerminalController@exportTranspose')->name('exportTranspose')->where(['id' => '[0-9]+']);

        Route::get('select2Attributes', 'TerminalController@select2attributes')->name('select2attributes');

        // Associazione OTA
        Route::get('/{id}/associa-OTA', 'TerminalController@associateOta')->name('associate')->where('id', '[0-9]+');
        Route::patch('/{id}/associa', 'TerminalController@linkOta')->name('link')->where('id', '[0-9]+');
        Route::post('/rtmp/vendors', 'TerminalController@getVendors')->name('getVendors');
        Route::get('/rtmp/vendors/{vendor}/models', 'TerminalController@getModels')->name('getModels');
        Route::get('/{id}/disassocia-OTA', 'TerminalController@disassociateOta')->name('disassociate')->where('id', '[0-9]+');
        Route::delete('/{id}/disassocia', 'TerminalController@unlinkOta')->name('unlink')->where('id', '[0-9]+');
        Route::get('select2vendors', 'VendorController@select2')->name('select2Vendors');
        Route::get('select2terminalvendors', 'TerminalController@select2')->name('select2TerminalVendors');
        Route::get('select2terminals', 'TerminalController@select2Terminals')->name('select2Terminals');

        Route::get('{terminal_id}/update-attribute-modal/{attribute_id}', 'TerminalController@updateAttributeModal')->name('updateAttributeModal');
        Route::post('{terminal_id}/update-attribute/{attribute_id}', 'TerminalController@updateAttribute')->name('updateAttribute');

        Route::get('{terminal_id}/delete-attribute-modal/{attribute_id}', 'TerminalController@deleteAttributeModal')->name('forceDeleteAttributeModal');
        Route::post('{terminal_id}/delete-attribute/{attribute_id}', 'TerminalController@forceDeleteAttribute')->name('forceDeleteAttribute');

        Route::group(['as' => 'pictures.', 'prefix' => '{terminal_id}/immagini'], function () {
            Route::get('/crea', 'TerminalPictureController@create')->name('create')->where('id', '[0-9]+');
            Route::post('/', 'TerminalPictureController@store')->name('store')->where('id', '[0-9]+');
            Route::get('/{picture_id}/modifica', 'TerminalPictureController@edit')->name('edit')->where('id', '[0-9]+');
            Route::patch('/{picture_id}', 'TerminalPictureController@update')->name('update')->where('id', '[0-9]+');
            Route::get('/{picture_id}/elimina', 'TerminalPictureController@delete')->name('delete')->where('id', '[0-9]+');
            Route::delete('/{picture_id}', 'TerminalPictureController@destroy')->name('destroy')->where('id', '[0-9]+');
        });

        Route::group(['as' => 'configs.', 'prefix' => '{terminal_id}/configs'], function () {
            Route::get('/crea', 'TerminalConfigController@create')->name('create')->where('id', '[0-9]+');
            Route::post('/', 'TerminalConfigController@store')->name('store')->where('id', '[0-9]+');
            Route::get('/{config_id}/visualizza', 'TerminalConfigController@show')->name('show')->where('id', '[0-9]+');
            Route::get('/{config_id}/modifica', 'TerminalConfigController@edit')->name('edit')->where('id', '[0-9]+');
            Route::patch('/{config_id}', 'TerminalConfigController@update')->name('update')->where('id', '[0-9]+');
            Route::get('/{config_id}/elimina', 'TerminalConfigController@delete')->name('delete')->where('id', '[0-9]+');
            Route::delete('/{config_id}', 'TerminalConfigController@destroy')->name('destroy')->where('id', '[0-9]+');
            Route::get('select2otas', 'TerminalConfigController@select2ota')->name('select2Otas');
            Route::get('select2documents', 'TerminalConfigController@select2document')->name('select2Documents');
        });
    });

    Route::group(['as' => 'tacs.', 'prefix' => 'tacs'], function () {
        Route::get('/', 'TacController@index')->name('index');
        Route::get('create', 'TacController@create')->name('create');
        Route::post('/', 'TacController@store')->name('store');
        Route::get('{id}', 'TacController@show')->where('id', '[0-9]+')->name('show');
        Route::get('{id}/modifica', 'TacController@edit')->where('id', '[0-9]+')->name('edit');
        Route::patch('{id}', 'TacController@update')->where('id', '[0-9]+')->name('update');
        Route::get('{id}/elimina', 'TacController@delete')->where('id', '[0-9]+')->name('delete');
        Route::delete('{id}', 'TacController@destroy')->where('id', '[0-9]+')->name('destroy');

        Route::get('select2terminal', ['as' => 'select2terminal', 'uses' => 'TacController@select2Terminal']);
    });

    Route::group(['prefix' => 'tipologie-ingestion', 'as' => 'ingestion_sources.'], function () {
        Route::get('/', 'IngestionSourceController@index')->name('index');
        Route::get('crea', 'IngestionSourceController@create')->name('create');
        Route::post('/', 'IngestionSourceController@store')->name('store');
        Route::get('{id}/modifica', 'IngestionSourceController@edit')->name('edit')->where('id', '[0-9]+');
        Route::patch('{id}', 'IngestionSourceController@update')->name('update')->where('id', '[0-9]+');
        Route::get('{id}', 'IngestionSourceController@show')->name('show')->where('id', '[0-9]+');
        Route::get('{id}/elimina', 'IngestionSourceController@delete')->name('delete')->where('id', '[0-9]+');
        Route::delete('{id}', 'IngestionSourceController@destroy')->name('destroy')->where('id', '[0-9]+');

        Route::group(['prefix' => 'header-mappings','as' => 'attribute_header_mappings.'], function () {
            Route::get('{id}/crea', 'AttributeHeaderMappingController@create')->name('create')->where('id', '[0-9]+');
            Route::post('{id}', 'AttributeHeaderMappingController@store')->name('store')->where('id', '[0-9]+');
            Route::get('{id}/modifica', 'AttributeHeaderMappingController@edit')->name('edit')->where('id', '[0-9]+');
            Route::patch('{id}', 'AttributeHeaderMappingController@update')->name('update')->where('id', '[0-9]+');
            Route::get('{id}','AttributeHeaderMappingController@show')->name('show')->where('id', '[0-9]+');
            Route::get('{id}/elimina','AttributeHeaderMappingController@delete')->name('delete')->where('id', '[0-9]+');
            Route::delete('{id}','AttributeHeaderMappingController@destroy')->name('destroy')->where('id', '[0-9]+');
            Route::get('select2','AttributeHeaderMappingController@select2')->name('select2')->where('id', '[0-9]+');
            Route::get('{id}/import', 'AttributeHeaderMappingController@importRequest')->name('import_request');
            Route::post('{id}/import', 'AttributeHeaderMappingController@import')->name('import');
        });
    });

    Route::group(['prefix' => 'ingestions', 'as' => 'ingestions.'], function () {
        Route::get('/', 'IngestionController@index')->name('index');
        Route::get('crea', 'IngestionController@create')->name('create');
        Route::post('/', 'IngestionController@store')->name('store');
        Route::get('{id}/modifica', 'IngestionController@edit')->name('edit')->where('id', '[0-9]+');
        Route::patch('{id}', 'IngestionController@update')->name('update')->where('id', '[0-9]+');
        Route::get('{id}', 'IngestionController@show')->name('show')->where('id', '[0-9]+');
        Route::get('{id}/elimina', 'IngestionController@delete')->name('delete')->where('id', '[0-9]+');
        Route::delete('{id}', 'IngestionController@destroy')->name('destroy')->where('id', '[0-9]+');

        Route::get('loadOptions', 'IngestionController@loadOptions')->name('loadOptions');
        Route::get('list-user-mails', 'IngestionController@listUserMails')->name('listUserMails');
    });

    Route::group(['as' => 'otas.', 'prefix' => 'otas'], function () {
        Route::get('/', 'OtaController@index')->name('index');
        Route::get('create', 'OtaController@create')->name('create');
        Route::post('/', 'OtaController@store')->name('store');
        Route::get('{id}', 'OtaController@show')->where('id', '[0-9]+')->name('show');
        Route::get('{id}/modifica', 'OtaController@edit')->where('id', '[0-9]+')->name('edit');
        Route::patch('{id}', 'OtaController@update')->where('id', '[0-9]+')->name('update');
        Route::get('{id}/elimina', 'OtaController@delete')->where('id', '[0-9]+')->name('delete');
        Route::delete('{id}', 'OtaController@destroy')->where('id', '[0-9]+')->name('destroy');
    });

    Route::group(['as' => 'transpose_configs.', 'prefix' => 'configurazione-trasposta'], function () {
        Route::get('/', 'TransposeConfigController@index')->name('index');
        Route::get('crea', 'TransposeConfigController@create')->name('create');
        Route::get('import', 'TransposeConfigController@importRequest')->name('import_request');
        Route::post('import', 'TransposeConfigController@import')->name('import');
        Route::post('/', 'TransposeConfigController@store')->name('store');
        Route::get('{id}', 'TransposeConfigController@show')->where('id', '[0-9]+')->name('show');
        Route::get('{id}/modifica', 'TransposeConfigController@edit')->where('id', '[0-9]+')->name('edit');
        Route::patch('{id}', 'TransposeConfigController@update')->where('id', '[0-9]+')->name('update');
        Route::get('{id}/elimina', 'TransposeConfigController@delete')->where('id', '[0-9]+')->name('delete');
        Route::delete('{id}', 'TransposeConfigController@destroy')->where('id', '[0-9]+')->name('destroy');

        Route::get('select2DbtAttribute', ['as' => 'select2DbtAttribute', 'uses' => 'TransposeConfigController@select2DbtAttribute']);

    });

    Route::group(['as' => 'dwh_operations.', 'prefix' => 'dwh-operations'], function () {
        Route::get('/', 'DwhOperationController@index')->name('index');
        Route::get('crea', 'DwhOperationController@create')->name('create');
        Route::post('esegui_creazione', 'DwhOperationController@executeCreate')->name('executeCreate');

    });

    Route::group(['as' => 'legacy_imports.','prefix' => 'import-legacy'],function () {
        Route::get('/', 'LegacyImportController@index')->name('index');
        Route::get('crea', 'LegacyImportController@create')->name('create');
        Route::post('/', 'LegacyImportController@store')->name('store');
        Route::get('{id}', 'LegacyImportController@show')->where('id', '[0-9]+')->name('show');
        Route::get('{id}/elimina', 'LegacyImportController@delete')->where('id', '[0-9]+')->name('delete');
        Route::delete('{id}', 'LegacyImportController@destroy')->where('id', '[0-9]+')->name('destroy');

        Route::group(['prefix' => 'items','as' => 'items.'], function () {
            Route::get('{id}','LegacyImportController@showItem')->name('show');
            Route::get('legacy/{id}','LegacyImportController@showLegacyItem')->name('show_legacy');
            Route::get('{id}/elimina','LegacyImportController@deleteItem')->name('delete');
            Route::delete('{id}','LegacyImportController@destroyItem')->name('destroy');
        });
    });

    Route::group(['as' => 'transpose_requests.','prefix' => 'richieste-trasposta'],function () {
        Route::get('/', 'TransposeRequestController@index')->name('index');
        Route::get('crea', 'TransposeRequestController@create')->name('create');
        Route::post('/', 'TransposeRequestController@store')->name('store');
        Route::get('{id}', 'TransposeRequestController@show')->where('id', '[0-9]+')->name('show');
        Route::get('{id}/elimina', 'TransposeRequestController@delete')->where('id', '[0-9]+')->name('delete');
        Route::delete('{id}', 'TransposeRequestController@destroy')->where('id', '[0-9]+')->name('destroy');
        Route::get('{id}/download', 'TransposeRequestController@download')->where('id', '[0-9]+')->name('download');

    });
});
