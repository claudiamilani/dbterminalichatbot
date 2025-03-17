<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Traduzioni per Legacy Import Items
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'title' => 'Import Legacy Items',
    'menu_title' => 'Import Legacy Items',

    'show' => [
        'title' => 'Dettagli Import Legacy Item',
    ],

    'attributes' => [
        'id' => 'ID',
        'legacy_id' => 'ID Record Legacy',
        'status' => 'Status',
        'result' => 'Esito',
        'message' => 'Output',
        'created_at' => 'Richiesto il',
        'updated_at' => 'Elaborato il',
    ],

    'placeholders' => [
        'searchbar' => 'Ricerca Import Legacy Items'
    ],

    'status' => [
        0 => 'Richiesto',
        1 => 'In coda',
        2 => 'In elaborazione',
        3 => 'Errore',
        4 => 'Elaborato'
    ],

    'result' => [
        'UPDATED' => 'Aggiornato',
        'CREATED' => 'Creato',
        'FAILED' => 'Scartato',
        'SKIPPED' => 'Saltato'
    ]

];