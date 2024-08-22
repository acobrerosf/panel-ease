<?php

return [

    'navigation' => [
        'label' => 'Usuarios',
        'group' => 'Gestión'
    ],

    'model' => 'usuario',

    'table' => [
        'tabs' => [      
            'all' => 'Todos',        
            'active' => 'Activos',
            'archived' => 'Archivados',
        ],
        'columns' => [
            'name' => 'Nombre',
            'email' => 'Email',
            'type_id' => 'Tipo',
            'email_verified_at' => 'Registrado'
        ]
    ],

    'form' => [
        'fields' => [
            'name' => 'Nombre',
            'email' => 'Email',
            'type_id' => 'Tipo',
        ]
    ],

    'actions' => [
        'invite' => [
            'label' => 'Invitar',
            'notification_title_success' => 'Usuario invitado.',
            'notification_title_failed' => 'Ha ocurrido un error y no se ha podido invitar.',
        ],
        'archive' => [
            'label' => 'Archivar',
            'notification_title_success' => 'Usuario archivado.',
            'notification_title_failed' => 'Ha ocurrido un error y no se ha podido archivar.',
        ],
        'unarchive' => [
            'label' => 'Restaurar',
            'notification_title_success' => 'Usuario restaurado.',
            'notification_title_failed' => 'Ha ocurrido un error y no se ha podido restaurar.',
        ],
    ],
];
