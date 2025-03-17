<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Images Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    'title' => 'Immagini Terminale',
    'menu_title' => 'Immagini Terminale',

    'create' => [
        'title' => 'Carica nuova immagine terminale',
        'image_title' => 'Titolo immagine',
        'success' => 'Immagine terminale caricata con successo.',
        'error' => 'Si è verificato un errore. Immagine terminale non caricata.',
        'display_order' => 'Ordinamento',

        'creation_errors' => [
            'no_requested_file' => 'File immagine non presente nella request.',
            'terminal_not_found' => 'Terminale non trovato.',
            'file_not_saved' => 'Impossibile salvare il file sul server.'
        ]
    ],

    'edit' => [
        'title' => 'Modifica immagine terminale',
        'success' => 'Immagine terminale modificata con successo.',
        'error' => 'Si è verificato un errore. Immagine terminale non modificato.'
    ],

    'show' => [
        'title' => 'Dettagli Immagine terminale'
    ],

    'delete' => [
        'title' => 'Elimina Immagine terminale',
        'confirm_msg' => 'Confermi la cancellazione dell\' immagine terminale?',
        'success' => 'Immagine terminale eliminata con successo.',
        'error' => 'Si è verificato un errore. Immagine terminale non eliminata.',
        'button_title_info' => 'Impossibile eliminare l\' immagine terminale.'
    ],

    'attributes' => [
        'id' => 'ID',
        'title' => 'Titolo',
        'file_path' => 'File',
        'created_by_id'  => 'Creato il',
        'updated_by_id' => 'Aggiornato il',
        'display_order' => 'Ordinamento',
        'preview' => 'Anteprima'
    ],

    'placeholder_hints' => [
        'file_types' => 'Il file deve essere un\'immagine in formato .jpg, .gif, .bmp, .png'
    ],

    'select2' => [
        'already_registered' => 'Immagine già presente: '
    ]
];