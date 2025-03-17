<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Roles Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    'title' => 'Ruoli',
    'menu_title' => 'Ruoli',
    'create' => [
        'title' => 'Crea nuovo Ruolo',
        'success' => 'Ruolo creato con successo.',
        'error' => 'Si è verificato un errore. Ruolo non creato.',
    ],
    'edit' => [
        'title' => 'Modifica Ruolo',
        'success' => 'Ruolo modificato con successo.',
        'error' => 'Si è verficato un errore. Ruolo non modificato.',
        'success_but_in_use' => 'Non è possibile aggiornare nome e descrizione del ruolo attualmente in uso. I permessi sono stati comunque aggiornati.',
        'in_use' => 'Non è possibile cambiare il nome o la descrizione quando il ruolo è in uso.'
    ],
    'delete' => [
        'title' => 'Elimina Ruolo',
        'confirm_msg' => 'Confermi la cancellazione del Ruolo?',
        'success' => 'Ruolo eliminato con successo.',
        'error' => 'Si è verificato un errore. Ruolo non eliminato.',
    ],
    'default_permissions' => [
        'title' => 'Reimposta permessi di default',
        'confirm_msg' => 'Confermi la reimpostazione dei permessi di default? Saranno reimpostati i permessi di default. Non saranno aggiunti o eliminati ruoli.',
        'success' => 'Permessi di default reimpostati con successo.',
        'error' => 'Si è verificato un errore. Permessi di default non reimpostati.',
    ],
    'attributes' => [
        'name' => 'Nome',
        'description' => 'Descrizione',
        'permissions' => 'Permessi',
        'users_count' => 'Conteggio utenti'
    ],
    'placeholders' => [
    ],


];
