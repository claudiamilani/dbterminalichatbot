<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | App Configuration Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'title' => 'Configurazione',
    'menu_title' => 'Configurazione Applicativo',
    'edit' => [
        'title' => 'Modifica Configurazione',
        'success' => 'Configurazione Applicativo modificata con successo',
        'error' => 'Si è verficato un errore. Configurazione Applicativo non modificata',
        'file_overwrite_warning' => 'Caricando un file sarà eliminato il precedente al salvataggio',
        'passwords_reset_warning' => 'La modifica di questo valore modificherà il numero di password storiche salvate per tutti gli utenti',
    ],
    'attributes' => [
        'wl_object' => 'Titolo della WL',
        'wl_body' => 'Testo della WL',
        'pwdr_mail_obj_c' => 'Titolo  mail Cliente',
        'pwdr_mail_body_c' => 'Testo  mail Cliente',
        'pwdr_mail_obj_u' => 'Titolo della mail Utente',
        'pwdr_mail_body_u' => 'Testo della mail Utente',
        'object' => 'Titolo',
        'body' => 'Testo',
        'manual_file_name' => 'Nome file manuale',
        'manual_file_path' => 'File manuale',
        'file_exists' => 'File caricato',
        'file_404' => 'File non presente',
        'max_failed_login_attempts' => 'N° massimo accessi falliti consecutivi',
        'failed_login_reset_interval' => 'Timer Reset accessi falliti (giorni)',
        'pwd_reset_unlocks_account' => 'Reset password sblocca account automaticamente',
        'pwd_min_length' => 'Lunghezza minima password',
        'pwd_regexp' => 'RegExp complessità password',
        'pwd_complexity_err_msg' => 'Messaggio di errore complessità password',
        'pwd_history' => 'N° di password storiche memorizzate',
        'pwd_expires_in' => 'Password scade in (giorni)',
        'pwd_never_expires' => 'Password non scade mai',
        'allow_pwd_reset' => 'Consenti reimpostazione password',
        'allow_ldap_auth' => 'Autenticazione LDAP',
        'autoreg_ldap_users' => 'Auto-registrazione utenti LDAP'
    ],
    'fieldset' => [
        'manual' => 'Manuale',
        'wl' => 'Welcome Letter',
        'password_recovery_customer' => 'Mail recupero password Clienti',
        'password_recovery_user' => 'Mail recupero password Utenti',
        'security' => 'Account e Sicurezza'
    ],
    'placeholders_hint' => [
        'wl' => '',
        'pwdr_c' => 'E\' possibile utilizzare il placeholder %RESET_URL% nel corpo della mail per generare l\'URL con il token per il recupero password',
        'pwdr_u' => 'E\' possibile utilizzare il placeholder %RESET_URL% nel corpo della mail per generare l\'URL con il token per il recupero password'
    ]

];
