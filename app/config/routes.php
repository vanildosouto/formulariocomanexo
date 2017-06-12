<?php

$app->get('/', App\Action\Home::class);
$app->post('/enviar', App\Action\Enviar::class);
