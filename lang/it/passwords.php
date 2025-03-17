<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'password' => 'Le password devono essere composte di almeno 6 caratteri e devono corrispondere',
    'reset' => 'La tua password è stata reimpostata!',
    'sent' => 'Ti è stata inviata una mail con un link per reimpostare la password!',
    'token' => 'Il token per reimpostare la password non è valido',
    'user' => "Non è stato trovato un account con l'indirizzo mail fornito.",
    'reset_msg' => "Fornisci il tuo username e scegli una nuova password.",
    'new_password' => "Nuova password",
    'confirm_new_password' => "Conferma la nuova password",
    'reset_password' => "Password dimenticata",
    'reset_password_msg' => "Fornisci il tuo username. Sarà inviata una mail al tuo indirizzo con le istruzioni per reimpostare la password.",
    'recover_page_title' => "Recupera la password",
    'reset_page_title' => "Reimposta la password",
    'must_reset_password' => "È necessario reimpostare la password per effettuare l'accesso.",
    'change' => [
        'success' => 'Password modificata correttamente',
        'error' => 'Password modificata correttamente',
        'generic_error' => 'Si è verificato un errore. Password non modificata',
        'wrong_password' => 'La password corrente inserita non è valida',
        'mail_sent' => 'Email per il recupero password inviata',
        'same' => 'Errore, la nuova password non può essere uguale alla password attuale.'
    ],
    'resets' => [
        'generic_error' => 'Servizio temporaneamente non disponibile',
        'invalid_token' => 'Il token è scaduto o non è valido',
        'invalid_user' => 'L\'utente non corrisponde al token richiesto',
    ]
];
