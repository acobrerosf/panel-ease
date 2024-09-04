<?php

return [

    'navigation' => [
        'label' => 'Users',
        'group' => 'Management',
    ],

    'model' => 'user',

    'table' => [
        'tabs' => [
            'all' => 'All',
            'active' => 'Active',
            'archived' => 'Archived',
        ],
        'columns' => [
            'name' => 'Name',
            'email' => 'Email',
            'type_id' => 'Type',
            'email_verified_at' => 'Registered',
        ],
    ],

    'form' => [
        'fields' => [
            'name' => 'Name',
            'email' => 'Email',
            'type_id' => 'Type',
        ],
    ],

    'actions' => [
        'invite' => [
            'label' => 'Invite',
            'notification_title_success' => 'User invited successfully.',
            'notification_title_failed' => 'An error occurred and the user could not be invited.',
        ],
        'archive' => [
            'label' => 'Archive',
            'notification_title_success' => 'User archived successfully.',
            'notification_title_failed' => 'An error occurred and the user could not be archived.',
        ],
        'unarchive' => [
            'label' => 'Restore',
            'notification_title_success' => 'User restored successfully.',
            'notification_title_failed' => 'An error occurred and the user could not be restored.',
        ],
    ],
];
