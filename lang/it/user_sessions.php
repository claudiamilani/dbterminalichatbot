<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Users Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    'title' => 'Sessioni utente',
    'menu_title' => 'Sessioni utente',

    'delete' => [
        'title' => 'Termina sessione utente',
        'confirm_msg' => 'Confermi di voler terminare la sessione utente?',
        'success' => 'Sessione terminata con successo',
        'error' => 'Si è verificato un errore. La sessione non è stata terminata',
    ],
    'purge' => [
        'title_auth' => 'Termina tutte le sessioni autenticate',
        'confirm_msg_auth' => 'Confermi di voler terminare la sessione utente autenticate?',
        'title' => 'Termina tutte le sessioni non autenticate',
        'confirm_msg' => 'Confermi di voler terminare la sessione utente non autenticate?',
        'success' => 'Sessioni terminate con successo',
        'error' => 'Si è verificato un errore. Le sessioni non sono state terminate',
    ],
    'attributes' => [
        'id' => 'ID',
        'user' => 'Utente',
        'nameWithMail' => 'Nominativo',
        'ip_address' => 'Indirizzo IP',
        'user_agent' => 'Client',
        'last_activity' => 'Data Ultima attività',
        'robot' => 'Robot',
        'expiring_in' => 'Scadenza',
        'browser' => 'Browser',
        'os' => 'Sistema Operativo',
        'last_page' => 'Ultima pagina',
    ],
    'placeholders' => [

    ],
    'profile' => 'Profilo',
    'sections' => [
        'u_sessions' => 'Sessioni non autenticate',
        'a_sessions' => 'Sessioni autenticate',
    ],
    'session_driver_error' => "Per gestire le sessioni è necessario utilizzare il driver custom-database",
];
