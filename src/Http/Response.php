<?php


namespace Hobbyworld\Http;


class Response {


    private $status_code;
    private $data;

    private $charset = 'utf-8';
    private $content_type = 'text/html';

    private $headers = [];


    public function __construct (int $status_code, $data = null, array $headers = null) {

        $this->status_code = $status_code;
        $this->data        = $data ?? '';

        if ($headers !== null) {

            $this->headers = $headers + $this->headers;
        }
    }


    public function setCharset (string $set) {

        $this->charset = $set;

        return $this;
    }

    public function setContentType (string $type) {

        $this->content_type = $type;

        return $this;
    }

    public function send () {

        header_remove ();

        http_response_code ($this->status_code);

        $this->headers ['Status'] = $this->status_code;
        $this->headers ['Content-Type'] = $this->content_type . ';' . $this->charset;

        foreach ($this->headers as $headerName => $value) {

            header("{$headerName}:{$value}");
        }

        if ($this->content_type == 'application/json') {

            $this->data = json_encode($this->data);
        }

        echo $this->data;

        exit (0);
    }
}