<?php namespace Mayo505\Framvc\Router;

interface RouteInterface {
	public function getMethod();
	public function getUrl();
	public function getController();
	public function equalsRequest(RouteInterface $route);
	public function matchesRealUrl($url);
	public function equalsMethod($route);
    public function getArgumentsFromUrl($url);
}