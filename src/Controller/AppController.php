<?php


namespace Hobbyworld\Controller;


use Hobbyworld\Http\Response;


class AppController extends AbstractController {


    public function index () : Response {

        return new Response(200, require HOBBYWORLD_APP_DIR . '/assets/index.html');
    }
}