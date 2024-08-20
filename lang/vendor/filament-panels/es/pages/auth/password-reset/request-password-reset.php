<?php

return [

    'title' => 'Recuperar contraseña',

    'heading' => 'Recuperar contraseña',

    'actions' => [

        'login' => [
            'label' => 'iniciar sesión',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Correo electrónico',
        ],

        'actions' => [

            'request' => [
                'label' => 'Enviar correo',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Demasiados intentos',
            'body' => 'Por favor intente de nuevo en :seconds segundos.',
        ],

    ],

];
