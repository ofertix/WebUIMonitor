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
    protected $application_sockets = array();

    public function run()
    {
        while(true)
        {
            $changed_sockets = $this->allsockets;
            @stream_select($changed_sockets, $write = NULL, $except = NULL, 0, 0); // HACK, timeout to 0

            // HACK
            foreach ($this->applications as $application)
            {
                $application->onTick($changed_sockets);
            }

            foreach($changed_sockets as $socket)
            {
                // do nothing here if it is a socket coming from application
                if(in_array($socket, $this->application_sockets)) continue;

                if($socket == $this->master)
                {
                    if(($ressource = stream_socket_accept($this->master)) === false)
                    {
                        $this->log('Socket error: ' . socket_strerror(socket_last_error($ressource)));
                        continue;
                    }
                    else
                    {
                        $client = new Connection($this, $ressource);
                        $this->clients[(int)$ressource] = $client;
                        $this->allsockets[] = $ressource;

                        if(count($this->clients) > $this->_maxClients)
                        {
                            $client->onDisconnect();
                            if($this->getApplication('status') !== false)
                            {
                                $this->getApplication('status')->statusMsg('Attention: Client Limit Reached!', 'warning');
                            }
                            continue;
                        }

                        $this->_addIpToStorage($client->getClientIp());
                        if($this->_checkMaxConnectionsPerIp($client->getClientIp()) === false)
                        {
                            $client->onDisconnect();
                            if($this->getApplication('status') !== false)
                            {
                                $this->getApplication('status')->statusMsg('Connection/Ip limit for ip ' . $client->getClientIp() . ' was reached!', 'warning');
                            }
                            continue;
                        }
                    }
                }
                else
                {
                    $client = $this->clients[(int)$socket];
                    if(!is_object($client))
                    {
                        unset($this->clients[(int)$socket]);
                        continue;
                    }
                    $data = $this->readBuffer($socket);
                    $bytes = strlen($data);

                    if($bytes === 0)
                    {
                        $client->onDisconnect();
                        continue;
                    }
                    elseif($data === false)
                    {
                        $this->removeClientOnError($client);
                        continue;
                    }
                    elseif($client->waitingForData === false && $this->_checkRequestLimit($client->getClientId()) === false)
                    {
                        $client->onDisconnect();
                    }
                    else
                    {
                        $client->onData($data);
                    }
                }
            }
        }
    }
//    public function run()
//    {
//        while (true)
//        {
//            $changed_sockets = $this->allsockets;
//            @stream_select($changed_sockets, $write = NULL, $except = NULL, 0); // HACK, timeout to 0
//            foreach ($this->applications as $application)
//            {
//                $application->onTick($changed_sockets);
//            }
//
//            foreach ($changed_sockets as $socket)
//            {
//                // no do anything here if it is a socket coming from application
//                if(in_array($socket, $this->application_sockets)) continue;
//
//                if ($socket == $this->master) {
//                    if (($resource = stream_socket_accept($this->master)) < 0) {
//                        $this->log('Socket error');
//                        continue;
//                    } else
//                    {
//                        $client = new Connection($this, $resource);
//                        $this->clients[$resource] = $client;
//                        $this->allsockets[] = $resource;
//                    }
//                } else
//                {
//                    $client = $this->clients[$socket];
//                    if(!feof($socket) && (false !== ($data = fread($socket, 4096))))
//                    {
//                        $client->onData($data);
//                    }
//                    else {
//                        $client->onDisconnect();
//                        unset($this->clients[$socket]);
//                        $index = array_search($socket, $this->allsockets);
//                        unset($this->allsockets[$index]);
//                        unset($client);
//                    }
//                }
//            }
//        }
//    }

    public function registerApplication($key, $application)
    {
        parent::registerApplication($key, $application);

        // add socket to socket_select
        $sock = $application->getSocket();
        if(!empty($sock))
        {
            $this->allsockets[] = $sock;
            $this->application_sockets[] = $sock;
        }
    }

}