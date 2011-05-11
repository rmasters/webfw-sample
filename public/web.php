<?php

use WebFW\Router\Route as Route;

try {
	require __DIR__ . "/../application/bootstrap.php";

    $routes = array(
        "index-name" => new Route("/:name", array(
            "controller" => "SampleApp\\Controller\\IndexController", 
            "action" => "indexAction"
        ), array(":name" => ".+")),
        "index" => new Route("/", array(
            "controller" => "SampleApp\\Controller\\IndexController", 
            "action" => "indexAction"
        ))
    );

    $router = new WebFW\Router($routes);
    $route = $router->match($_SERVER["REQUEST_URI"]);
    WebFW\Registry::set("router", $router);

    if ($route) {
        $controllerName = $route->getControllerName();
        $controller = new $controllerName;

        $controller->setRoute($route);
        if (WebFW\Registry::exists("layout")) {
            $controller->setLayout(WebFW\Registry::get("layout"));
        }

        echo $controller->dispatch($route->getActionName(), 
            $route->getVariables());
    } else {
        throw new Exception("File not found", 404);
    }
} catch (Exception $e) {
    switch ($e->getCode()) {
        case 404:
            WebFW\Registry::get("layout")->content = new WebFW\View("404.php");
            echo WebFW\Registry::get("layout");
            break;
        default:
            echo "<h1>Error</h1><p>{$e->getMessage()}</p>";
            echo "<pre>{$e->getTraceAsString()}</pre>";
            break;
    }
}
