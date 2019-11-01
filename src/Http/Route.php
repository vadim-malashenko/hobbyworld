<?php


namespace Hobbyworld\Http;


class Route {


    protected $id;
    protected $method;
    protected $pattern;
    protected $controller;

    protected $matches = NULL;


    public function __construct (string $id, string $method, string $pattern, string $controller, string $action) {

        $this->id         = $id;
        $this->method     = $method;
        $this->pattern    = "#$pattern#";
        $this->controller = $controller;
        $this->action = $action;
    }


    public function match (Request $request) : bool {

        return

            $this->method == $request->getMethod()

               and

            preg_match ($this->pattern, $request->getPath(), $this->matches);
    }


    public function getID () {

        return $this->id;
    }

    public function getPattern () {

        return $this->pattern;
    }

    public function getController () {

        return $this->controller;
    }

    public function getAction () {

        return $this->action;
    }

    public function getMatches () {

        return $this->matches;
    }
}