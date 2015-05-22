<?php

return [
    'dev' => [
        'db' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'rbot_dev',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
        'console' => [
            'datetime_format' => 'D j F Y H:i:s O',
            'show_line_date'  => false,
        ],
    ],

    'prod' => [
        'db' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'rbot_dev',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ]
];