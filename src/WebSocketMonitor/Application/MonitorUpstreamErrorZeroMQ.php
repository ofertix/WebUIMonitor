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

namespace WebSocketMonitor\Application;

use \WebSocket\Application\Application;

class MonitorUpstreamErrorZeroMQ extends Application
{

    private $clients = array();

    protected $subscriber;
    protected $logger;

    protected function __construct($args)
    {
        // config
        $config = $args['config'];

        // log
        $this->logger = $args['logger'];

        $this->logger->addDebug('Initializing ZeroMQ...');

        $context = new \ZMQContext();
        $this->subscriber = new \ZMQSocket($context, \ZMQ::SOCKET_SUB);
        $this->subscriber->setSockOpt(\ZMQ::SOCKOPT_SUBSCRIBE, "");
        foreach ($config['urls'] as $url) $this->subscriber->connect($url);

        $this->logger->addDebug('ZeroMQ OK');
    }

    public function onConnect($client)
    {
        $this->clients[] = $client;
    }

    public function onDisconnect($client)
    {
        $key = array_search($client, $this->clients);
        if ($key) {
            unset($this->clients[$key]);
        }
    }

    public function onData($data, $client)
    {
        foreach ($this->clients as $sendto)
        {
            $sendto->send($data);
        }
    }

    public function onTick()
    {
        //sudo execcap 'cap_net_raw=ep' /usr/bin/php src/workers/monitorUpstreamError.php


        $data = $this->subscriber->recv();
        //    $data = json_decode($data, true);
        //    $data = print_r($data, true);

        $this->logger->addDebug('Data recv: ' . $data);

        foreach ($this->clients as $k => $sendto)
        {
            $this->logger->addDebug('Send to: (' . $sendto->getClientId() . ') ' . $sendto->getClientIp() . ':' . $sendto->getClientPort());
            if (!$sendto->send($data)) {
                // unsubscribe client
                unset($this->clients[$k]);
            }
        }
    }

}
