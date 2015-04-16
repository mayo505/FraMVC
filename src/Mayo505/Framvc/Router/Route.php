<?php namespace Mayo505\Framvc\Router;

use \Mayo505\Framvc\Router\Exceptions\InvalidUrlException;
use \Mayo505\Framvc\Router\Exceptions\InvalidArgumentException;


class Route implements RouteInterface {
	protected $method;
	protected $url;
	protected $controller;

	public function __construct($method, $url, $controller) {
		if (! $this->areCorrectBrackets($url)) {
			throw new InvalidUrlException("Wrong url: " . $url);
		}
		$this->method = strtoupper($method);

		$this->url = $this->urlWithoutAttributes((trim($url, "/")));
		$this->controller = $controller;
	} 

	public function getMethod() {
		return $this->method;
	}

	public function getUrl() {
		return $this->url;
	}

	public function getController() {
		return $this->controller;
	}

	/*
	 * či sa zhoduje method aj url
	 */
	public function equalsRequest(RouteInterface $route) {
		return $this->equalsMethod($route) && $this->equalsUrl($route);
	}

	public function equalsMethod($route) {
		if (is_string($route)) {
			return $this->method == strtoupper($route);
		}
		if ($route instanceof RouteInterface) {
			return $this->method == $route->getMethod();
		}
		throw new \InvalidArgumentException("Invalid argument type for equalsMethod");
	}

	/*
	 * Či sa zhodujú url inak ako v pomenovaní premmených
	 */
	public function equalsUrl($route) {
		if (is_string($route)) {
			$url = trim($route, "/");
		} 
		elseif ($route instanceof RouteInterface) {
			$url = trim($route->getUrl());
		}
		else {
			return false;
		}

		try {
			$urlWithoutAttributes = $this->urlWithoutAttributes($url);
		}
		catch (InvalidUrlException $ex) {
			return false;
		}
		return $this->url == $urlWithoutAttributes;
	}

	public function matchesRealUrl($url) {
		$routeUrlParts = explode("/", $this->url);
		$givenUrlParts = explode("/", trim($url, "/"));

        if (count($routeUrlParts) != count($givenUrlParts)) {
			return false;
		}

		for ($i = 0; $i < count($routeUrlParts); $i++) {
			if ($routeUrlParts[$i] == "{}") {
				continue;
			}
			if ($routeUrlParts[$i] != $givenUrlParts[$i]) {
				return false;
			}
		}
		return true;
	}

    public function getArgumentsFromUrl($url) {
        if (!$this->matchesRealUrl($url)) {
            throw new \InvalidArgumentException("Url doesn't match route");
        }

        $routeUrlParts = explode("/", $this->url);
        $givenUrlParts = explode("/", trim($url, "/"));

        $attributes = [];

        for ($i = 0; $i < count($routeUrlParts); $i++) {
            if ($routeUrlParts[$i] == "{}") {
                $attributes[] = $givenUrlParts[$i];
            }
        }

        return $attributes;
    }

	private function urlWithoutAttributes($url) {
		if (! $this->areCorrectBrackets($url)) {
			throw new InvalidUrlException("Invalid url " . $url);
		}
		return preg_replace("/{[^{}]*}/", "{}", $url);
	}

	private function areCorrectBrackets($url) {
		return preg_match("/^([^{}]*|({[^{}]*}))*$/", $url) ? true : false;
	}
}