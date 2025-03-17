<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'vas' => [
        'title' => 'Telefonino - Libero',
        'menu_title' => 'Configura VAS',

        'form' => [
            'title' => 'Configura il tuo Cellulare'
        ],

        'techsheet' => [
            'title' => 'Configurazione Wind',
            'tech_features' => ' Caratteristiche tecniche',
            'config_features' => 'Documenti di configurazione e OTA'
        ],
    ],

    'windtre' => [
        'title' => 'Configura Windtre',
        'menu_title' => 'Configura Windtre',

        'form' => [
            'title' => 'Wind',
            'button' => 'CONFERMA',
            'vendor' => 'Marca',
            'model' => 'Modello'
        ],

        'techsheet' => [
            'title' => 'Configurazione Wind',
            'tech_features' => ' Caratteristiche tecniche',
            'config_features' => 'Configurazione'
        ],
    ],

    'terminal_wind' => [
        'success' => [
            'result_type' => 'OK'
        ],

        'error' => [
            'result_type' => 'KO',
            'not_found' => 'NOT FOUND'
        ],

        'errors' => [
            'vendor_not_found' => 'Vendor non trovato per il TAC: ',
            'vendor_empty' => 'Il campo Vendor è vuoto per il TAC: ',
            'terminal_not_found' => 'Terminale non trovato per il TAC: '
        ]
    ],

    'attributes' => [
        'name' => 'Nome',
        'vendor_select' => 'Seleziona Marca',
        'model_select' => 'Seleziona Modello',
        'vendor' => 'Marca',
        'model' => 'Modello',
        'go' => 'Vai',
        'close' => 'Chiudi',

        'no_img' => 'Immagine non disponibile',

        'config_type' => 'Tipo di configurazione',
        'show' => 'Visualizza',
        'send_mail' => 'Invia mail',
        'send_ota' => 'Invia OTA',
        'send' => 'Invia',
        'show_new_tab' => 'Visualizza il documento in una nuova scheda'
    ],

    'send' => [
        'mail' => [
            'title' => 'Invio manuale di configurazione per posta elettronica',
            'description' => 'Inserisci l\'indirizzo di posta elettronica a cui inviare il manuale di configurazione',
            'label' => 'E-mail',
            'emailContent' => 'Gentile Cliente,<br>Le inviamo in allegato le configurazioni richieste.<br><br>La informiamo che non è possibile rispondere a questa mail e le ricordiamo che, per ogni altra informazione, può consultare il sito <a href="https://www.windtre.it" target="_blank">www.windtre.it</a> o contattare il Servizio Clienti Wind al numero 155.<br><br>Cordiali Saluti,<br><h3>Servizio Clienti Wind</h3>',

            'mail_sent' => 'Invio Configurazione',
            'mail_error' => 'Errore durante l\'invio della mail: '
        ],

        'ota' => [
            'title' => 'Invia la configurazione al tuo Smartphone o Tablet',
            'description' => 'Inserisci il numero Wind a cui inviare il messaggio di configurazione automatica',
            'hint' => 'Per completare la configurazione potrebbe essere richiesto l\'inserimento di un codice numerico (PIN). In tal caso inserire: 1234',
            'ota_hint' => 'Inserisci il numero di cellulare a cui inviare la configurazione tramite SMS:',
            'label' => 'Numero',
            'placeholder' => 'es: 3201234567'
        ],

        'mail_title_success' => 'Invio manuale di configurazione',
        'mail_message_success' => 'Il manuale di configurazione è stato correttamente inoltrato alla casella di posta indicata',
        'mail_title_error' => 'Errore nell\'invio manuale di configurazione',
        'mail_message_error' => 'Errore durante l\'invio dell\'email',

        'ota_title_success' => 'Invio configurazione',
        'ota_message_success' => 'La configurazione è stata correttamente inviata al numero di telefono indicato',
        'ota_title_error' => 'Errore nell\'invio configurazione',
        'ota_message_error' => 'Errore durante l\'invio della configurazione'
    ],

    'errors' => [
        'request_error' => 'Errore nella richiesta',
        'fetch_error' => 'Errore durante il caricamento del contenuto della modale',
        'form_error' => 'Errore nell\'invio del form'
    ]
];
