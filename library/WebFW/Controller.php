<?php

namespace WebFW;
use Exception;

class Controller
{
    protected $layout;
    protected $route;
    protected $forwarded = false;

    public function setLayout(View $layout) {
        $this->layout = $layout;
    }

    public function setRoute(Router\Route $route) {
        $this->route = $route;
    }

    protected function init() {
    }
    protected function preDispatch($actionName) {
    }
    protected function postDispatch() {
    }

    protected function forward($routeName, $params = array()) {
        $router = Registry::get("router");
        if (!$router->routeExists($routeName)) {
            throw new Exception("Invalid route $routeName");
        }
        $route = $router->getRoute($routeName);
        $cName = $route->getControllerName();

        $this->forwarded = true;

        $controller = new $cName;
        $controller->setLayout($this->layout);
        $controller->setRoute($route);
        return $controller->dispatch($route->getActionName(), $params);
    }

    public function redirect($uri) {
        header("Location: $uri");
        return new View("redirect.php", array("uri" => $uri));
    }

    public function dispatch($action, array $params = array()) {
        $this->init();

        $preDispatchResult = $this->preDispatch($action);
        if ($preDispatchResult) {
            return $preDispatchResult;
        }

        $result = call_user_func_array(array($this, $action), $params);

        if (!$this->forwarded) {
            $this->postDispatch();

            if (isset($this->layout)) {
                $this->layout->content = $result;
                return $this->layout;
            } else {
                return $result;
            }
        } else {
            return $result;
        }
    }
}
