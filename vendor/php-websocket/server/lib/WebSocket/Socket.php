<?php

namespace WebSocket;

/**
 * Socket class
 *
 * @author Moritz Wutz <moritzwutz@gmail.com>
 * @author Nico Kaiser <nico@kaiser.me>
 *
 * @author Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * @version 0.2
 */

/**
 * This is the main socket class
 */
class Socket
{
    /**
     * @var Socket Holds the master socket
     */
    protected $master;

    /**
     * @var array Holds all connected sockets
     */
    protected $allsockets = array();

    public function __construct($host = 'localhost', $port = 8000, $max = 100)
    {
        ob_implicit_flush(true);
        $this->createSocket($host, $port);
    }

    /**
     * Create a socket on given host/port
     * 
     * @param string $host The host/bind address to use
     * @param int $port The actual port to bind on
     */
    private function createSocket($host, $port)
    {
        self::console('Start listening on Socket.');

        $this->master = stream_socket_server("tcp://{$host}:{$port}", $errno, $errstr);
        if (!$this->master) {
          die("$errstr ($errno)<br />");
        }

        $this->allsockets[] = $this->master;
    }

    /**
     * Log a message
     *
     * @param string $msg The message
     * @param string $type The type of the message
     */
    protected function console($msg, $type='System')
    {
        /* $msg = explode("\n", $msg);
        foreach ($msg as $line)
            echo date('Y-m-d H:i:s') . " {$type}: {$line}\n"; */
    }

    /**
     * Sends a message over the socket
     * @param socket $client The destination socket
     * @param string $msg The message
     */
    protected function send($client, $msg)
    {
        fwrite($client, $msg, strlen($msg));
    }

}
