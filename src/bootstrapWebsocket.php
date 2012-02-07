<?php

/*
 * This file is part of the WebUIMonitor package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/../vendor/silex/silex.phar';

$app = new Silex\Application();
$app['debug'] = true;

$app['autoloader']->registerNamespace('Monolog', __DIR__ . '/../vendor/monolog/src');
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/../app/log/development.log',
    'monolog.class_path' => __DIR__ . '/../vendor/monolog/src',
    'monolog.name' => 'websocketUpstreamError',
    'monolog.level' => Monolog\Logger::DEBUG
));

$app['autoloader']->registerNamespace('WebSocket', __DIR__ . '/../vendor/php-websocket/server/lib');
$app['autoloader']->registerNamespace('WebSocketMonitor', __DIR__);
$app['autoloader']->registerNamespace('Symfony', __DIR__ . '/../vendor');
$app['autoloader']->registerNamespace('PhpAmqpLib', __DIR__ . '/../vendor/php-amqplib');
