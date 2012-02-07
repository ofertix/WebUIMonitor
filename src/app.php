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

require_once __DIR__ . '/bootstrap.php';

$app->before(function () use ($app)
{
    $app['twig']->addGlobal('layout', $app['twig']->loadTemplate('layout.twig'));
});

$app->match('/monitor/upstream/error', function () use ($app)
{
    $app['monolog']->addDebug('Monitor upstream error.');
    return 'ok';
});

$app->match('/', function () use ($app)
{
    return $app['twig']->render('index.twig');
});
