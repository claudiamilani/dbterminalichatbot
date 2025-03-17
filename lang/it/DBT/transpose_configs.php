<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Traduzioni per Configurazione Trasposta
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'title' => 'Configurazione Trasposta',
    'menu_title' => 'Configurazione Trasposta',
    'create' => [
        'title' => 'Crea nuova Configurazione Trasposta',
        'success' => 'Configurazione Trasposta creato con successo.',
        'error' => 'Si è verificato un errore. Configurazione Trasposta non creato.',
    ],

    'edit' => [
        'title' => 'Modifica Configurazione Trasposta',
        'success' => 'Configurazione Trasposta modificata con successo.',
        'error' => 'Si è verificato un errore. Configurazione Trasposta non modificata.',
    ],

    'show' => [
        'title' => 'Dettagli Configurazione Trasposta',
    ],

    'import' => [
        'title' => 'Importa Configurazione Trasposta',
        'success' => 'Configurazione Trasposta importata con successo.',
        'error' => 'Si è verificato un errore. Configurazione Trasposta non importata.',
        'confirm_msg' => 'La Configurazione Trasposta verrà importata dai file di configurazione predefiniti presenti sul server e sulla base degli attributi presenti a sistema.',
    ],

    'delete' => [
        'title' => 'Elimina Configurazione Trasposta',
        'confirm_msg' => 'Confermi la cancellazione del Configurazione Trasposta?',
        'success' => 'Configurazione Trasposta eliminata con successo.',
        'error' => 'Si è verificato un errore. Configurazione Trasposta non eliminata.',
    ],

    'attributes' => [
        'id' => 'ID',
        'dbt_attribute_id' => 'Attributo',
        'ingestion_source_id' => 'Tipologia Ingestion',
        'label' => 'Nome colonna',
        'type' => 'Tipologia',
        'display_order' => 'Ordinamento',
        'created_at' => 'Creato il',
        'updated_at' => 'Aggiornato il'
    ],


    'validation' => [
        'hint' => 'Il nome della colonna non può contenere spazi, caratteri speciali ad eccezione di "_" e non può iniziare con un numero.'
    ]

];