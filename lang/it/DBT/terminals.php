<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Terminals Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    'title' => 'Terminali',
    'menu_title' => 'Terminali',
    'create' => [
        'title' => 'Crea nuovo Terminale',
        'success' => 'Terminale creato con successo.',
        'error' => 'Si è verificato un errore. Terminale non creato.',
    ],
    'show' => [
        'title' => 'Dettagli Terminale',
    ],
    'edit' => [
        'title' => 'Modifica terminale',
        'success' => 'Terminale modificato con successo.',
        'error' => 'Si è verficato un errore. Terminale non modificato.',
    ],
    'delete' => [
        'title' => 'Elimina terminale',
        'confirm_msg' => 'Confermi la cancellazione dell\'terminale?',
        'success' => 'Terminale eliminato con successo.',
        'error' => 'Si è verificato un errore. Terminale non eliminato.',
    ],
    'attributes' => [
        'id' => 'ID',
        'name' => 'Nome',
        'vendor_id' => 'Vendor',
        'description' => 'Descrizione',
        'created_by_id'  => 'Creato da',
        'updated_by_id' => 'Aggiornato da',
        'ingestion_id' => 'Ingestion',
        'ingestion_source_id' => 'Origine Ingestion',
        'ota_vendor' => 'Vendor OTA',
        'ota_model' => 'Modello OTA',
        'certified' => 'Certificato',
        'published' => 'Pubblicato',
        'updated_at' => 'Aggiornato alle',
        'created_at' => 'Creato alle',
        'dbt_attribute_id' => 'Attributo',
        'attr_category_id' => 'Categoria',
    ],

    'dbt_attributes' => [

        'edit' => [
            'title' => 'Aggiorna valore attributo',
            'confirm_msg' => 'Si è sicuri di voler aggiornare il valore dell\' attributo?',
            'success' => 'Attributo aggiornato con successo',
            'error' => 'Errore, attributo non aggiornato',
        ],
        'delete' => [
            'title' => 'Resetta valore attributo',
            'confirm_msg' => 'Si è sicuri di voler resettare il valore dell\' attributo?',
            'success' => 'Attributo aggiornato con successo',
            'error' => 'Errore, attributo non aggiornato',
        ]
    ]


];