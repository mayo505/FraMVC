<?php

use Mayo505\Framvc\Facade\Facade;

class Container extends Facade {
    public static function getAccessor() {
        return "container";
    }
}