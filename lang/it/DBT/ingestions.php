<?php

use App\DBT\Models\Ingestion;
use App\DBT\Models\IngestionSource;

return [

    /*
    |--------------------------------------------------------------------------
    | Ingestion Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    'title' => 'Ingestion',
    'menu_title' => 'Ingestion',
    'create' => [
        'title' => 'Crea nuova Ingestion',
        'success' => 'Ingestion creata con successo.',
        'error' => 'Si è verificato un errore. Ingestion non creata.',
    ],
    'edit' => [
        'title' => 'Modifica Ingestion',
        'success' => 'Ingestion modificata con successo.',
        'error' => 'Si è verficato un errore. Ingestion non modificata.',
    ],
    'delete' => [
        'title' => 'Elimina Ingestion',
        'confirm_msg' => 'Confermi la cancellazione della Ingestion?',
        'success' => 'Ingestion eliminata con successo.',
        'error' => 'Si è verificato un errore. Ingestion non eliminata.',
    ],
    'show' => [
        'title' => 'Dettagli Ingestion',
    ],
    'attributes' => [
        'ingestion_source_id' => 'Tipologia',
        'file_path' => 'File',
        'id' => 'ID',
        'status' => 'Status',
        'started_at' => 'Iniziato il',
        'ended_at' => 'Terminato il',
        'notify_mails' => 'Notifica e-mail',
        'options' => 'Opzioni',
        'created_by_id' => 'Creato da',
        'created_at' => 'Creato il',
        'updated_by_id' => 'Aggiornato da',
        'updated_at' => 'Aggiornato il',
        'message' => 'Message',
    ],
    'statuses' => [
        Ingestion::STATUS_DRAFT => 'Bozza',
        Ingestion::STATUS_QUEUED => 'In coda',
        Ingestion::STATUS_REQUESTED => 'Richiesto',
        Ingestion::STATUS_PROCESSING => 'In processamento',
        Ingestion::STATUS_ERROR => 'Errore',
        Ingestion::STATUS_COMPLETED => 'Completato',
    ],
    'sources' => [
        IngestionSource::SRC_ADMIN => 'Admin',
        IngestionSource::SRC_MOBILETHINK => 'MobileThink',
        IngestionSource::SRC_GSMA => 'GSMA'
    ],
    'validation' => [
        'notify_mails' => 'Verificare le email inserite'
    ],
    'notify_mails_placeholder' => 'Inserire uno o più indirizzi Email da notificare',
    'placeholder_hints' => [
        'file_types' => 'Il file caricato deve essere un CSV valido.'
    ]

];
