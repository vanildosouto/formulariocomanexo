<?php

require __DIR__ . '/../vendor/autoload.php';

$email = parse_ini_file(__DIR__ . '/../email.ini');
$config = require __DIR__ . '/../app/config/config.php';
$app = new \Slim\App($config);

require __DIR__ . '/../app/config/dependencies.php';
require __DIR__ . '/../app/config/routes.php';

$app->run();
