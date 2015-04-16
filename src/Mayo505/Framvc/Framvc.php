<?php  namespace Mayo505\Framvc;

use Mayo505\Framvc\Container\ContainerInterface;
use Mayo505\Framvc\Facade\Facade;
use Mayo505\Framvc\View\View;
use Mayo505\Framvc\View\ViewInterface;

class Framvc {

    private $url;
    private $request;
    private $container;

    /**
     * @param $url Url from root of the application
     */
    public function __construct($request, $url, ContainerInterface $container) {
        $this->url = $url;
        $this->request = $request;
        $this->container = $container;
    }

    public function handle() {
        $router = $this->container->make("Mayo505\Framvc\Router\RouterInterface");
        $response = $router->handle($this->request, $this->url);

        if ($response instanceof ViewInterface) {
            $response->render();
            return;
        }

        if (is_string($response)) {
            echo $response;
            return;
        }

        return $response;
    }

    public function boot() {
        $this->container->boot();
        $this->registerProviders([
            "Mayo505\Framvc\Providers\RouterProvider",
            "Mayo505\Framvc\Providers\LangProvider"
        ]);

        Facade::setContainer($this->container);
        View::setViewBasePath(appRootPath() . "App/Views/");
    }

    public function registerProviders(array $providers) {
        foreach ($providers as $provider) {
            $providerInstance = $this->container->make($provider);
            $providerInstance->register();
        }
    }

    public function addRoutesProvider($provider) {
        $routesProvider = $this->container->make($provider);
        $routesProvider->register();
    }

}