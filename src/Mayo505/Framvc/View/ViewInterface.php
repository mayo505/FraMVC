<?php  namespace Mayo505\Framvc\View;

interface ViewInterface {
    public function render();
    public function with($attributes, $value);
}