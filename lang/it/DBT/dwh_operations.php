<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

return [
    'title' => 'Operazioni DWH',
    'menu_title' => 'Operazioni DWH',
    'create' => [
        'title' => 'Genera Vista',
        'success' => 'Vista DWH creata con successo.',
        'error' => 'Si è verificato un errore. Vista non creata.',
        'confirm_msg' => 'Confermi di voler creare la vista ' ,
        'error_42P01' => 'Tabella Trasposta non presente, impossibile generare la vista. '
    ],
    'types'=> [
        'dwh_marca' => 'DWH Marca',
        'dwh_tac' => 'DWH Tac',
        'dwh_terminal' => 'DWH Terminale',
        'dwh_attributi' => 'DWH Attributi',
        'dwh_trasposta' => 'DWH Trasposta',
    ],
    'attributes' => [
        'type' => 'Tipo',
        'is_present' => 'Vista presente',
    ],

    'generate_view' => 'Genera vista',

];