<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Documents Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    'title' => 'Documenti',
    'menu_title' => 'Documenti',
    'create' => [
        'title' => 'Crea nuovo documento',
        'success' => 'Documento creato con successo.',
        'error' => 'Si è verificato un errore. Documento non creato.',
    ],
    'edit' => [
        'title' => 'Modifica documento',
        'success' => 'Documento modificato con successo.',
        'error' => 'Si è verficato un errore. Documento non modificato.',
    ],
    'show' => [
        'title' => 'Dettagli documento',
    ],
    'delete' => [
        'title' => 'Elimina documento',
        'confirm_msg' => 'Confermi la cancellazione dell\'documento?',
        'success' => 'Documento eliminato con successo.',
        'error' => 'Si è verificato un errore. Documento non eliminato.',
    ],
    'attributes' => [
        'id' => 'ID',
        'document_type_id' => 'Tipo documento',
        'file_path' => 'File',
        'file_path_uploaded' => 'File caricato',
        'file_mime_type' => 'Tipo file',
        'created_by_id'  => 'Creato il',
        'updated_by_id' => 'Aggiornato il',
        'title' => 'Titolo'
    ],
    'placeholder_hints' => [
        'file_types' => 'File deve essere .pdf, .doc, .docx, .txt, .pdf, .html',
    ]
];
