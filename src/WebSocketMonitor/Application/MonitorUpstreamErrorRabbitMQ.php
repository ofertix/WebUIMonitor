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

use WebSocket\Application\Application;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MonitorUpstreamErrorRabbitMQ extends Application
{

    protected $clients = array();
    protected $logger;
    protected $conn;
    protected $ch;

    protected function __construct($args)
    {
        // config
        $config = $args['config'];

        // log
        $this->logger = $args['logger'];

        $this->logger->addDebug('Initializing RabbitMQ...');

        if (isset($config['debug']) && $config['debug'] && !defined('AMQP_DEBUG')) define('AMQP_DEBUG', true);

        // subscriber
        $this->conn = new AMQPConnection($config['host'], $config['port'], $config['user'], $config['pass'], $config['vhost']);
        $this->ch = $this->conn->channel();
        list($queue_name, ,) = $this->ch->queue_declare("", false, false, true, false);
        foreach ($config['exchanges'] as $exchange)
        {
            $this->ch->exchange_declare($exchange, 'fanout', false, false, false);
            $this->ch->queue_bind($queue_name, $exchange);
        }
        $this->ch->basic_consume($queue_name, 'consumer', false, true, false, false, array($this, 'processMessage'));

        $this->logger->addDebug('RabbitMQ OK');
    }

    public function onConnect($client)
    {
        echo 'onconnect ' . "\n";
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

    public function processMessage($msg)
    {
        $data = $msg->body;
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

    public function onTick()
    {
        $this->ch->wait();
    }

}
