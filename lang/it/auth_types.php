<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Users Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    'title' => 'Modalità di autenticazione',
    'menu_title' => 'Modalità di autenticazione',

    'edit' => [
        'title' => 'Modifica ',
        'success' => 'Modalità di autenticazione modificata con successo.',
        'error' => 'Si è verficato un errore. Modalità di autenticazione non modificata.',
        'no_auto_register' => "La modalità Local non può avere Autoregistrazione abilitata.",
        'enabled_needed'=>"La modalità di autenticazione deve essere abilitata per essere default.",
        'need_one_enabled'=>"Questa modalità di autenticazione non può essere disabilitata. E' necessario che almeno una modalità sia abilitata.",
        'default_needed' => "Default non può essere rimosso. Settare come default la modalità di autenticazione desiderata."
    ],

    'attributes' => [
        'name' => 'Nome',
        'enabled' => 'Abilitato',
        'default' => 'Default',
        'auto_register' => 'Auto registrazione'
    ],

];
