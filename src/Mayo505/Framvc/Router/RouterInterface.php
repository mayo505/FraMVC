<?php namespace Mayo505\Framvc\Router;

interface RouterInterface {
	public function route($method, $url, $controller);
	public function get($url, $controller);
	public function post($url, $controller);
    public function handle($method, $url);
}