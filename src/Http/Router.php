<?php


namespace Hobbyworld\Http;


class Router {


    private $routes;


    public function __construct (array $routes = null) {

        $this->setRoutes($routes ?? []);
    }


    public function addRoute (Route $route) {

        $this->routes [] = $route;
    }

    public function addRoutes (array $routes) {

        foreach ($routes as $route) {

            $this->routes [] = new Route (
                $route [0],
                $route [1],
                $route [2],
                $route [3],
                $route [4]
            );
        }
    }

    public function findRoute (Request $request) : Route {

        foreach ($this->routes as $route) {

            if ($route->match($request)) {

                return $route;
            }
        }

        throw new NotFoundException ($request->getUrl ());
    }


    public function setRoutes (array $routes) {

        $this->routes = $routes;
    }

    public function getRoutes () : array {

        return $this->routes;
    }
}