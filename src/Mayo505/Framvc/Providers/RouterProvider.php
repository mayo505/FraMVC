<?php  namespace Mayo505\Framvc\Providers;

class RouterProvider extends Provider {

    public function register() {
        $this->container->shared("Mayo505\Framvc\Router\RouterInterface", "Mayo505\Framvc\Router\Router");
    }
}