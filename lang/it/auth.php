<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Credenziali non valide',
    'failed_with_attempts' => 'Sono rimasti :attempts_left tentativi',
    'throttle' => 'Troppi tentativi falliti. Prossimo tentativo consentito in :seconds secondi',
    'remember_me' => 'Ricordami',
    'login_msg' => 'Accedi per avviare la sessione',
    'login' => 'Accedi',
    'generic_error' => 'Si è verificato un errore. Non è possibile procedere',
    'invalid_user' => 'Nome utente non valido',
    'locked' => 'Account bloccato. Ti invitiamo a contattare lo staff di ' .config('app.name'),
    'disabled' => 'Account disabilitato',
    'pwd_complexity_error' => 'La password non rispetta i criteri di complessità richiesti',
    'pwd_reset_unlock' => 'Account bloccato. Ti invitiamo a reimpostare la password per sbloccare l\'account o a contattare lo staff di ' .config('app.name'),
    'pwd_history_error' => 'La tua nuova password non può essere uguale a nessuna delle tue password recenti. Scegli una nuova password',
    'insecure_pwd_should_be_changed' => "La password non rispetta i criteri di complessità previsti. È necessario cambiare la password utilizzandone una più sicura.",
    'account_not_configured' => 'Account non configurato',
    'wrong_configuration' => 'Account non configurato. Contattare un amministratore.', //Same key used by NagiosProvisioner app
    'ldap_unknown_user' => 'Credenziali non valide',
    'ldap_password_expired' => 'La password è scaduta.',
    'ldap_account_disabled' => 'Account disabilitato.',
    'ldap_account_expired' => 'Account scaduto.',
    'ldap_account_not_found' => 'Account non trovato su LDAP.',
    'expired' => 'Account scaduto.',
    'ldap_password_change_required' => 'E\' necessario cambiare la password per effettuare l\'accesso.',
    'ldap_unknown_error' => 'Errore sconosciuto ( :error_code ).',
    'local_login_message' => 'Inserire credenziali per accedere localmente',
    'ldap_login_message' => 'Inserire credenziali per accedere con LDAP',
    'azure_login_message' => 'Premere il bottone per essere reindirizzati verso Microsoft Azure',
    'saml_login_message' => 'Premere il bottone per essere reindirizzati verso SAML VAS',
    'select_auth_type' => 'Scegliere la modalità di accesso.',
    'no_auth_types' => 'Sistema di autenticazione non disponibile<br> Contattare l\'amministratore',
    'type_disabled' => 'Tipo di autenticazione disabilitata',
    'pwd_reset_unavailable' => 'Recupero password non disponibile per il metodo di autenticazione dello username fornito.',
];
