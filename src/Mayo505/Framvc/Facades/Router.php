<?php

use Mayo505\Framvc\Facade\Facade;

class Router extends Facade {
    public static function getAccessor() {
        return "Mayo505\Framvc\Router\RouterInterface";
    }
}