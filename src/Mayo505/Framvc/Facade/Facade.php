<?php  namespace Mayo505\Framvc\Facade;

use Mayo505\Framvc\Container\ContainerInterface;
use Mayo505\Framvc\Facade\FacadeException;

abstract class Facade {

    protected static $container;

    public static function getAccessor() {
        throw new FacadeException("Accessor has not been defined");
    }

    public static function setContainer(ContainerInterface $container) {
        static::$container = $container;
    }

    public static function __callStatic($name, $arugments) {
        $object = static::$container->make(static::getAccessor());
        return call_user_func_array([$object, $name], $arugments);
    }
}