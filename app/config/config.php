<?php

return [
    'settings' => [
        'displayErrorDetails' => true,
        'logger'   => [
            'name'     => 'dev',
            'filename' => __DIR__  . '/../../logs/app-dev.log',
            'level'    => \Monolog\Logger::DEBUG
        ],
        'view' => [
            'cache'      => false,
            'debug_mode' => true,
            'templates'  => __DIR__ . '/../templates'
        ],
        'email' => [
            'host'     => $email['host'],
            'username' => $email['username'],
            'password' => $email['password'],
            'email'    => $email['email'],
            'name'     => $email['name'],
        ],
    ]
];
