<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Traduzioni per Legacy Imports
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'title' => 'Import Legacy',
    'menu_title' => 'Import Legacy',
    'create' => [
        'title' => 'Crea nuovo Import Legacy',
        'success' => 'Import Legacy creato con successo.',
        'error' => 'Si è verificato un errore. Import Legacy non creato.',
    ],

    'show' => [
        'title' => 'Dettagli Import Legacy',
    ],

    'delete' => [
        'title' => 'Elimina Import Legacy',
        'confirm_msg' => 'Confermi la cancellazione dell\'Import Legacy?',
        'success' => 'Import Legacy eliminato con successo.',
        'error' => 'Si è verificato un errore. Import Legacy non eliminato.',
    ],

    'attributes' => [
        'id' => 'ID',
        'type' => 'Tipo',
        'requested_by_id' => 'Richiesto da',
        'status' => 'Status',
        'update_existing' => 'Aggiorna esistenti',
        'created_at' => 'Richiesto il',
        'updated_at' => 'Ultima aggiornamento',
        'started_at' => 'Avviato il',
        'ended_at' => 'Terminato il',
        'elapsed_time' => 'Tempo impiegato',
        'message' => 'Output',
        'created_cnt' => 'Creati',
        'updated_cnt' => 'Aggiornati',
        'skipped_cnt' => 'Saltati',
        'error_cnt' => 'Errori',

    ],

    'placeholders' => [
        'searchbar' => 'Ricerca Import Legacy'
    ],

    'status' => [
        0 => 'Richiesto',
        1 => 'In coda',
        2 => 'In elaborazione',
        3 => 'Errore',
        4 => 'Elaborato'
    ],
    'items' => [
        'result' => [
            'UPDATED' => 'Aggiornato',
            'CREATED' => 'Creato',
            'FAILED' => 'Scartato',
            'SKIPPED' => 'Saltato'
        ]
    ],
    'types' => [
        'VENDORS' => 'Vendor',
        'ATTR_CATEGORIES' => 'Categorie di Attributi',
        'ATTRIBUTES' => 'Attributi',
        'DOCUMENT_TYPES' => 'Tipo di Documento',
        'DOCUMENTS' => 'Documenti',
        'OTAS' => 'OTA',
        'TERMINALS' => 'Terminali',
        'TACS' => 'TAC',
        'PICTURES' => 'Immagini Terminali',
        'TERMINAL_CONFIGS' => 'Configurazioni Terminali',
        'ATTRIBUTE_VALUES' => 'Valori Attributi',
        'TERMINALS GSMA' => 'Terminali GSMA',
    ]

];