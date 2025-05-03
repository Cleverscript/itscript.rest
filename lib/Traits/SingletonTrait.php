<?php

namespace Itscript\Rest\Traits;

trait SingletonTrait
{
    private static $instance = null;

    /**
     * prohibiting direct instantiation
     */
    private function __construct(){}

    /**
     * prohibition of clone
     */
    private function __clone(){}

    /**
     * prohibition of deserialization
     */
    private function __wakeup(){}

    /**
     * @return SingletonTrait|null
     */
    public static function getInstance()
    {
        return static::$instance ?? (static::$instance = new static());
    }
}
