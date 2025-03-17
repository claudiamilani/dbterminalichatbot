<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Terminal Config Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    'title' => 'Configurazione Terminale',
    'menu_title' => 'Configurazione Terminale',

    'create' => [
        'title' => 'Carica nuova Configurazione Terminale',
        'success' => 'Configurazione Terminale caricata con successo.',
        'error' => 'Si è verificato un errore. Configurazione Terminale non caricata.',

        'creation_errors' => [
            'terminal_not_found' => 'Terminale non trovato.'
        ]
    ],

    'edit' => [
        'title' => 'Modifica Configurazione Terminale',
        'success' => 'Configurazione Terminale modificata con successo.',
        'error' => 'Si è verificato un errore. Configurazione Terminale non modificato.'
    ],

    'show' => [
        'title' => 'Dettagli Configurazione Terminale',
    ],

    'delete' => [
        'title' => 'Elimina Configurazione Terminale',
        'confirm_msg' => 'Confermi la cancellazione della Configurazione Terminale?',
        'success' => 'Configurazione Terminale eliminata con successo.',
        'error' => 'Si è verificato un errore. Configurazione Terminale non eliminata.',
        'button_title_info' => 'Impossibile eliminare la Configurazione Terminale.'
    ],

    'attributes' => [
        'id' => 'ID',
        'ota' => 'Ota',
        'document' => 'Documento',
        'published' => 'Pubblicato',
        'not_published' => 'Non pubblicato',
        'terminal_name' => 'Nome terminale',
        'config_id' => 'ID configurazione',
        'created_at'  => 'Creato il',
        'updated_at' => 'Aggiornato il'
    ],

    'placeholder_hints' => [
    ],

    'select2' => [
        'already_registered' => 'Configurazione Terminale già presente: '
    ]
];