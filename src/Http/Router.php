<?php


namespace Hobbyworld\Http;


use Hobbyworld\Http;


class Router {


    private $routes;


    public function addRoutes (array $routes) {

        foreach ($routes as $route) {

            list ($method, $pattern) = explode (' ', $route);

            $this->routes [] = new Route ($method, $pattern);
        }
    }

    public function getRoute (Request $request) : Route {

        if ( ! isset (Http::METHODS [$request->getMethod ()])) {

            throw new NotImplementedException ();
        }

        foreach ($this->routes as $route) {

            if ($route->match ($request)) {

                $request->setMatches ($route->getMatches ());
                return $route;
            }
        }

        throw new NotFoundException ();
    }


    public function setRoutes (array $routes) {

        $this->routes = $routes;
    }

    public function getRoutes () : array {

        return $this->routes;
    }
}