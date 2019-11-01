<?php


namespace Hobbyworld\Http;


class Request {


    private $method;
    private $url;
    private $get;
    private $post;

    private $scheme;
    private $user;
    private $pass;
    private $host;
    private $port;
    private $path;
    private $fragment;
    private $query;

    private $params;

    private $isAjax;

    private $matches;


    public function __construct () {

        $this->url = $_SERVER ['REQUEST_URI'];
        $this->method = strtoupper ($_SERVER ['REQUEST_METHOD']);
        $this->get = $_GET;
        $this->post = json_decode (file_get_contents ('php://input'), true);

        $url_parts = parse_url ($this->url);

        foreach ($url_parts as $key => $value) {

            $this->$key = $value;
        }

        $this->params = [];

        parse_str ($this->query, $this->params);

        $this->isAjax = ! empty ($_SERVER ['HTTP_X_REQUESTED_WITH']) and strtolower ($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }


    public function getMethod() : string
    {
        return $this->method;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function getGet(string $key = null)
    {
        return ($key === null)
            ? $this->get
            : isset ($this->get [$key])
                ? $this->get [$key]
                : null;
    }

    public function getPost(string $key = null)
    {
        return ($key === null)
            ? $this->post
            : isset ($this->post [$key])
                ? $this->post [$key]
                : null;
    }

    public function getPath() : string
    {
        return $this->path ?? '';
    }

    public function getQuery() : string
    {
        return $this->query ?? '';
    }

    public function isAjax() : bool
    {
        return $this->isAjax;
    }

    public function getMatches(int $key = null)
    {

        return ($key === null)
            ? $this->matches
            : isset ($this->matches [$key])
                ? $this->matches [$key]
                : null;
    }

    public function setMatches(array $matches)
    {
        $this->matches = $matches;
    }
}