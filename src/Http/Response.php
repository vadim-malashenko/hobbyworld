<?php


namespace Hobbyworld\Http;


class Response {


    private $status_code = 200;
    private $body = '';

    private $charset = 'utf-8';
    private $content_type = 'application/json';


    public function __construct (int $status_code, $data = null, string $content_type = null) {

        $this->status_code = $status_code;

        if ($content_type !== null) {

            $this->content_type = $content_type;
        }

        $this->body = ( ! is_string ($data)) ? json_encode ($data) : $data;

        $this->sendHeaders ();
    }

    public function getBody () : string {

        return $this->body;
    }

    public function sendHeaders (array $headers = []) {

        http_response_code ($this->status_code);

        $headers ['Status'] = $this->status_code;
        $headers ['Content-Type'] = $this->content_type . ';' . $this->charset;

        foreach ($headers as $headerName => $value) {

            header ("{$headerName}:{$value}");
        }
    }
}