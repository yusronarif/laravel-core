<?php

return [
    'model' => [
        'users' => config('auth.providers.users.model'),
        'user_key_type' => 'int',    // int, uuid
    ],

    'plugins' => [
        'config_path' => 'yusronarif.plugins',
        'public_path' => 'plugins', // without /public
    ],
];
