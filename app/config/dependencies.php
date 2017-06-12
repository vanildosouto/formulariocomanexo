<?php
$container = $app->getContainer();

# Libraries
## Logger
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger   = new \Monolog\Logger($settings['name']);
    $filename = $settings['filename'];

    $stream = new \Monolog\Handler\StreamHandler(
        $settings['filename'],
        $settings['level'],
        true,
        0666
    );

    $logger->pushHandler($stream);
    return $logger;
};

## Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings')['view'];

    $cache_path     = $settings['cache'];
    $debug_mode     = $settings['debug_mode'];
    $templates_path = $settings['templates'];

    $view = new \Slim\Views\Twig($templates_path, [
        'cache' => $cache_path,
        'debug' => $debug_mode
    ]);

    $view->addExtension(new Twig_Extension_Debug());

    $view->addExtension(new \Slim\Views\TwigExtension(
        $c['router'],
        $c['request']->getUri()
    ));

    return $view;
};

##Email
$container['email'] = function ($c) {
    $mail_config = $c->get('settings')['email'];
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host       = $mail_config['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $mail_config['username'];
    $mail->Password   = $mail_config['password'];
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->CharSet    = "utf-8";
    $mail->isHTML();
    $mail->setFrom($mail_config['email'], $mail_config['name']);
    $mail->addAddress($mail_config['email'], $mail_config['name']);

    return $mail;
};

# Actions
$container[App\Action\Home::class] = function ($c) {
    return new App\Action\Home($c['logger'], $c['view']);
};

$container[App\Action\Enviar::class] = function ($c) {
    return new App\Action\Enviar($c['logger'], $c['view'], $c['email']);
};
