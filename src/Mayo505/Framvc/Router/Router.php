<?php namespace Mayo505\Framvc\Router;

use Mayo505\Framvc\Container\Container;
use Mayo505\Framvc\Router\Exceptions\InvalidControllerException;
use Mayo505\Framvc\Router\Exceptions\RouteNotFoundException;

class Router implements RouterInterface {

	protected $routes = [];
    protected $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

	public function route($method, $url, $controller) {
		if (is_array($method)) {
			foreach($method as $onemethod) {
				$this->route($onemethod, $url, $controller);
			}
		}

		if (!is_string($method) || !is_string($url) || !is_string($controller)) {
			throw new \InvalidArgumentException("Values for route method have to be strings");
		}

		$route = new Route($method, $url, $controller);
		if (!$this->routeExists($route)) {
			$this->routes[] = $route;
		}
	}

	public function get($url, $controller) {
		$this->route("GET", $url, $controller);
	}

	public function post($url, $controller) {
		$this->route("POST", $url, $controller);
	}

	public function handle($method, $url) {
        $route = $this->findRoute($method, $url);

        if ($route === false) {
            throw new RouteNotFoundException;
        }

        return $this->fire($route,$url);
	}

	protected function routeExists(RouteInterface $route) {
		foreach ($this->routes as $savedroute) {
			if ($savedroute->equalsRequest($route)) {
				return true;
			}
		}
		return false;
	}

	protected function findRoute($method, $url) {
		foreach ($this->routes as $route) {
			if ($route->equalsMethod($method) && $route->matchesRealUrl($url)) {
				return $route;
			}
		}
		return false;
	}

    protected function fire(RouteInterface $route, $url) {
        list($controllerName, $methodName) = $this->explodedController($route);

        $controller = $this->container->make($controllerName);
        if (!method_exists($controller, $methodName) && !is_callable([$controller, $methodName])) {
            throw new InvalidControllerException("Given method does not exist in a controller");
        }

        $arguments = $route->getArgumentsFromUrl($url);

        $method = new \ReflectionMethod($controllerName, $methodName);
        if ($method->getNumberOfRequiredParameters() > count($arguments)) {
            throw new InvalidControllerException("Method expects more attributes");
        }

        return call_user_func_array([$controller, $methodName], $arguments);
    }

    protected function explodedController(RouteInterface $route) {
        $parts = explode("@", $route->getController());
        $controllerName = $parts[0];
        $method = "index";
        if (count($parts) != 1) {
            $method = $parts[1];
        }
        return array($controllerName, $method);
    }
}