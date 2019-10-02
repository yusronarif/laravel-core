<?php

return [
    'version'     => '1.2',
    'codename'    => 'arkid',
    'name'        => 'Arek Kampung Istiqomah Development',
    'medium-name' => 'Arek Kampung ID',
    'short-name'  => 'ArKID',
    'website'     => 'http://www.ark.id',

    'allowed-domains' => [],
    'avatar_path'     => 'img/avatar',

    'status' => [
        'active'   => 'Aktif',
        'inactive' => 'Tidak Aktif',
    ],

    'guarded-setting' => ['version', 'codename', 'name', 'address', 'postcode'],

];
