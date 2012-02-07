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
    protected function __construct($args) { } // HACK LAIGU

    final private function __clone() { }
    
    final public static function getInstance($args = null) // HACK LAIGU
    {
        $calledClassName = get_called_class();
        if (!isset(self::$instances[$calledClassName])) {
            self::$instances[$calledClassName] = new $calledClassName($args); // HACK LAIGU
        }

        return self::$instances[$calledClassName];
    }

    public function onConnect($connection) { }

    public function onDisconnect($connection) { }
    
    public function onTick() { }

    public function onData($data, $client) { }
}