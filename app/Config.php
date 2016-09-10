<?php
/**
 * This is a example of RBot Application configuration file
 */
return [

    'all' => [ // this is the default settings group, all others environments groups will overwrite/complete those settings

        'php' => [
            'display_errors'         => 0,
            'display_startup_errors' => 0,
            'date.timezone'          => "America/Toronto"
        ],

        'auth' => [
            'hash'          => 'sha512',
            // admin / admin
            'user_hash'     => 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec',
            'password_hash' => 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec',
            //'ip'            => '127.0.0.1',
        ],
    ],

    'dev' => [
        'php' => [
            'display_errors'         => 1,
            'display_startup_errors' => 1,
        ],
        'db' => [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'rbot_dev',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
    ],

    'prod' => [
        'db' => [
            'driver'    => 'mysql',
            'host'      => 'locahost',
            'database'  => '',
            'username'  => '',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
    ],

    'staging' => [],
    'test' => []

];