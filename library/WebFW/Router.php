<?php

namespace WebFW;
use Exception;
use WebFW\Router\Route;

class Router
{
    protected $routes = array();

    public function __construct(array $routes = null) {
        if (is_array($routes)) {
            $this->addRoutes($routes);
        }
    }

    public function addRoute($name, $route) {
        if ($route instanceof Route) {
            $this->routes[$name] = $route;
        } /*else if (is_array($route)) {
            if (isset($route["pattern"], $route["defaults"])) {
                if (is_array($route["defaults"]) && isset($route["defaults"]["controller"], $route["defaults"]["action"])) {
                    $route = new Route($route["pattern"], $route["defaults"]);
                } else {
                    throw new Exception("Invalid route defaults - missing required controller or action");
            } else {
                throw new Exception("Invalid route - needs a pattern and default parameters");
        }*/ else {
            throw new Exception("Invalid route");
        }
    }

    public function addRoutes(array $routes) {
        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function getRoute($name) {
        return $this->routes[$name];
    }

    public function routeExists($name) {
        return isset($this->routes[$name]);
    }

    public function removeRoute($name) {
        unset($this->routes[$name]);
    }

    public function match($input) {
        $matchedRoute = null;
        foreach ($this->routes as $route) {
            $matchedRoute = $route->match($input);
            if ($matchedRoute) {
                break;
            }
        }

        if (!is_null($matchedRoute)) {
            return $matchedRoute;
        } else {
            return false;
        }
    }
}
