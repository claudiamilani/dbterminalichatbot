<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Traduzioni per Transpose Requests
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'title' => 'Richieste Trasposta',
    'menu_title' => 'Richieste Trasposta',
    'create' => [
        'title' => 'Crea nuova Richiesta trasposta',
        'success' => 'Richiesta trasposta creata con successo.',
        'error' => 'Si è verificato un errore. Richiesta trasposta non creata.',
        'confirm_msg' => 'Confermi la creazione della Richiesta trasposta?',
    ],

    'show' => [
        'title' => 'Dettagli Richiesta trasposta',
    ],

    'delete' => [
        'title' => 'Elimina richiesta trasposta',
        'confirm_msg' => 'Confermi la cancellazione della richiesta trasposta?',
        'success' => 'Richiesta trasposta eliminata con successo.',
        'error' => 'Si è verificato un errore. Richiesta trasposta non eliminata.',
    ],

    'attributes' => [
        'id' => 'ID',
        'requested_by_id' => 'Richiesto da',
        'status' => 'Status',
        'created_at' => 'Richiesto il',
        'updated_at' => 'Ultima aggiornamento',
        'started_at' => 'Avviato il',
        'ended_at' => 'Terminato il',
        'elapsed_time' => 'Tempo impiegato',
        'message' => 'Output',
    ],

    'placeholders' => [
        'searchbar' => 'Ricerca Richieste Trasposta',
    ],

    'status' => [
        0 => 'Richiesto',
        1 => 'In coda',
        2 => 'In elaborazione',
        3 => 'Errore',
        4 => 'Elaborato'
    ],
];