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

namespace WebSocketMonitor;

use \WebSocket\Connection;

class Server extends \WebSocket\Server
{

    public function run()
    {
        while (true)
        {
            $changed_sockets = $this->allsockets;
            @socket_select($changed_sockets, $write = NULL, $except = NULL, 0); // HACK LAIGU, timeout to 0
            foreach ($this->applications as $application)
            {
                $application->onTick();
            }

            foreach ($changed_sockets as $socket)
            {
                if ($socket == $this->master) {
                    if (($ressource = socket_accept($this->master)) < 0) {
                        $this->log('Socket error: ' . socket_strerror(socket_last_error($ressource)));
                        continue;
                    } else
                    {
                        $client = new Connection($this, $ressource);
                        $this->clients[$ressource] = $client;
                        $this->allsockets[] = $ressource;
                    }
                } else
                {
                    $client = $this->clients[$socket];
                    $bytes = @socket_recv($socket, $data, 4096, 0);
                    if ($bytes === 0) {
                        $client->onDisconnect();
                        unset($this->clients[$socket]);
                        $index = array_search($socket, $this->allsockets);
                        unset($this->allsockets[$index]);
                        unset($client);
                    } else
                    {
                        $client->onData($data);
                    }
                }
            }
        }
    }

}