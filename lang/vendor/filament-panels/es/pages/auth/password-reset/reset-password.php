<?php

return [

    'title' => 'Restablecer su contraseña',

    'heading' => 'Restablecer su contraseña',

    'form' => [

        'email' => [
            'label' => 'Correo electrónico',
        ],

        'password' => [
            'label' => 'Contraseña',
            'validation_attribute' => 'contraseña',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar contraseña',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Restablecer contraseña',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Demasiados intentos',
            'body' => 'Por favor, inténtelo de nuevo en :seconds segundos.',
        ],

    ],

];
