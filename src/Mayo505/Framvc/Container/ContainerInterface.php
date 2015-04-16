<?php  namespace Mayo505\Framvc\Container;

interface ContainerInterface {
    public function make($toMake);
    public function bind($toBind, $value);
    public function instance($instanceName, $instance);
    public function shared($toShare, $value);
    public function boot();
}