<?php

namespace WebSocket\Application;

/**
 * WebSocket Server Application
 * 
 * @author Nico Kaiser <nico@kaiser.me>
 */
abstract class Application
{
    protected static $instances = array();
    
    /**
     * Singleton 
     */
    protected function __construct($args) { } // HACK

    final private function __clone() { }
    
    final public static function getInstance($args = null) // HACK
    {
        $calledClassName = get_called_class();
        if (!isset(self::$instances[$calledClassName])) {
            self::$instances[$calledClassName] = new $calledClassName($args); // HACK
        }

        return self::$instances[$calledClassName];
    }

    public function onConnect($connection) { }

    public function onDisconnect($connection) { }
    
    public function onTick($changed_sockets) { } // HACK

    public function onData($data, $client) { }

    public function getSocket() { } // HACK
}