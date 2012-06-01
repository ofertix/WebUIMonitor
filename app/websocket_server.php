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

require_once __DIR__ . '/../src/bootstrapWebsocket.php';

// config
$app['config'] = Symfony\Component\Yaml\Yaml::parse(__DIR__ . '/config/test.yml');

$server = new \WebSocketMonitor\Server($app['config']['websocket']['host'], $app['config']['websocket']['port']);
$server->setCheckOrigin(false);
//$server->setCheckOrigin(true);
//$server->setAllowedOrigin('localhost');
//$server->setAllowedOrigin('example.com');
$server->setMaxClients(100);
$args = array(
    'logger' => $app['monolog'],
    'config' => $app['config']['subscriber']
);
$server->registerApplication(
    'monitorUpstreamApplication', $app['config']['class']::getInstance($args));
$app['monolog']->addDebug('Worker monior upstream error started...');
$server->run();
