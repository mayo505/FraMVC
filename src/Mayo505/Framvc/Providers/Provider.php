<?php  namespace Mayo505\Framvc\Providers;

use Mayo505\Framvc\Container\ContainerInterface;

abstract class Provider implements ProviderInterface {

    /**
     * @var Container
     */
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }


    abstract public function register();
}