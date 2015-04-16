<?php

use Mayo505\Framvc\Facade\Facade;

class Lang  extends Facade {
    public static function getAccessor() {
        return "Mayo505\Framvc\Lang\LangInterface";
    }
}