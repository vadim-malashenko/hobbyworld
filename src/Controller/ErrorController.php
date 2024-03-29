<?php


namespace Hobbyworld\Controller;


use Hobbyworld\Http\Response;


class ErrorController extends AbstractController {

    public function not_found () {

        return new Response(404, ['error' => $this->request->ex->getMessage ()]);
    }

    public function internal_server_error () {

        return new Response(500, ['error' => $this->request->ex->getMessage ()]);
    }

    public function not_implemented () {

        return new Response(501, ['error' => $this->request->ex->getMessage ()]);
    }
}