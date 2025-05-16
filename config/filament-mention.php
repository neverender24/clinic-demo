<?php

return [
    'mentionable' => [
        'model' => \App\Models\Patient::class,
        'column' => [
            'id' => 'id', // Unique identifier for the user
            'display_name' => 'full_name', // Display name for the mention
            'username' => 'first_name', // Username for the mention
            'avatar' => 'profile', // Avatar field (e.g. profile picture URL)
            
        ],
        'url' => 'admin/users/{id}', // this will be used to generate the url for the mention item
        'lookup_key' => 'display_name', // this will be used on static search
        'search_key' => 'display_name', // this will be used on dynamic search
    ],
    'default' => [
        'trigger_with' => '@',
        'menu_show_min_length' => 1,
        'menu_item_limit' => 10,
    ],
];
