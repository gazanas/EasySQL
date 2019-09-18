<?php

namespace EasySQL\Facades;

abstract class Facade
{

    protected static $dependencies;

    public static function __callStatic(string $method, array $arguments)
    {
        /**
         * Instantiate the facade object only once cause
         * in each chain call return the builder object
         * not the facade class
         */
        $service = static::resolveServiceName();
        $instance = new $service(...static::resolveServiceDependencies());

        return $instance->{$method}(...$arguments);
    }
}
