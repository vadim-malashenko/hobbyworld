<?php


namespace Hobbyworld\Http;


class Route {


    private $method = '';
    private $pattern = '';

    private $action = '';
    private $matches = null;


    public function __construct (string $method, string $pattern) {

        $this->method = $method;

        /*
        if ( ! (@preg_match ("#$pattern#", '') !== false)) {

            $pattern = '#\.*#';
        }
        */

        $this->pattern = "#$pattern#";

        $match = [];
        preg_match ('#[a-z_]+#i', $pattern, $match);

        $this->action = isset ($match [0]) ? $match [0] : 'index';
    }


    public function match (Request $request) : bool {

        return

            $this->method == $request->getMethod()

               and

            @preg_match ($this->pattern, $request->getPath(), $this->matches);
    }


    public function getMethod () {

        return $this->method;
    }

    public function getPattern () {

        return $this->pattern;
    }

    public function getAction () {

        return $this->action;
    }

    public function getMatches () {

        return $this->matches;
    }
}