<?php

return [
    'model' => [
        'users' => config('auth.providers.users.model'),
        'user_key_type' => 'int'    // int, uuid
    ],
];
