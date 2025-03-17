<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

return [
    'title' => 'Attributi',
    'menu_title' => 'Attributi',
    'create' => [
        'title' => 'Crea nuovo attributo',
        'success' => 'Attributo creato con successo.',
        'error' => 'Si è verificato un errore. Attributo non creato.',
    ],
    'edit' => [
        'title' => 'Modifica attributo',
        'success' => 'Attributo modificato con successo.',
        'error' => 'Si è verficato un errore. Attributo non modificato.',
        'partial' => [
            'success' => 'Attributo già collegato a dei valori. Solamente nome, descrizione, categoria e opzioni possono essere aggiornati. ',
        ]
    ],
    'show' => [
        'title' => 'Dettagli attributo',

    ],
    'delete' => [
        'title' => 'Elimina attributo',
        'confirm_msg' => 'Confermi la cancellazione dell\'attributo?',
        'success' => 'Attributo eliminato con successo.',
        'error' => 'Si è verificato un errore. Attributo non eliminato.',
    ],

    'attributes' => [
        'id' => 'ID',
        'name' => 'Nome',
        'description' => 'Descrizione',
        'default_value' => 'Valori default',
        'attr_category_id' => 'Categoria',
        'type' => 'Tipologia',
        'type_options' => 'Opzioni Tipologia',
        'input_type' => 'Tipologia input',
        'options' => 'Opzioni',
        'published' => 'Pubblicato',
        'display_order' => 'Ordinamento',
        'created_by_id' => 'Creato da',
        'created_at' => 'Creato il',
        'updated_by_id' => 'Aggiornato da',
        'updated_at' => 'Aggiornato il',
        'ingestion_source_id' => 'Tipologia Ingestion',
        'ingestion_id' => 'Ingestion ID',
        'value' => 'Valore',

    ],
    'types' => [
        'BOOLEAN' => 'Booleano',
        'VARCHAR' => 'Stringa',
        'TEXT' => 'Testo',
        'INT' => 'Integer',
        'DECIMAL' => 'Decimale',
    ],

    'type_options' => [
        'input_types' => [
            'TEXT' => 'Testo',
            'TEXTAREA' => 'Area di Testo',
            'SELECT' => 'Select',
            'NUMBER' => 'Numero',
            'CHECKBOX' => 'Checkbox',
        ],
        'searchable' => 'Ricercabile',
        'required' => 'Richiesto',
        'multiple' => 'Multiplo',
        'options' => 'Opzioni',
    ],

    'input_too_short' => "E' richiesto almeno un carattere per aggiungere l'opzione",
    'empty_default_value' => "Un valore di default non presente nelle opzioni verrà aggiunto automaticamente alle opzioni disponibili",
    'validation' => [
        'multiple_default' => 'Sono stati selezionati valori di default multipli per un campo non multiplo.',
        'not_searchable' => 'La tipologia di input non puo essere ricercabile.',
        'not_multiple' => 'La tipologia di input non puo essere multipla.',
        'options_not_found' =>'Opzione di default :options non trovata tra le opzioni disponibili',
        'multiple_no_options' =>'Attributo  settato come multiplo ma senza opzioni',
        'not_numeric' => 'Opzione :option non numerica per un attributo di tipo Decimal o Integer'
    ],

    'placeholder_hints' => [
        'available_options' => 'N.B. "Ricercabile" è disponibile per la sola tipologia di input Select. "Multiplo" per la tipologia Select e Checkbox'
    ]

];