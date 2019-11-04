<?php


namespace Hobbyworld\Controller;


use Hobbyworld\Http\Request;
use Hobbyworld\Http\Response;
use Hobbyworld\Http\NotFoundException;


abstract class AbstractController {


    protected $request = null;
    protected $action = '';


    public function __construct (Request $request) {

        $this->request = $request;
    }

    public function setAction (string $action) {

	$this->action = $action;
    }

    public function createResponse () : Response {

        $action = $this->action;

        if ($action != 'index' and  ! $this->request->isAjax ())

            return new Response (404, '');

        try {

            return $this->$action ();
        }
        catch (NotFoundException  $ex) {

            $this->request->ex = $ex;
            $controller = new ErrorController ($this->request);
            return $controller->not_found ();
        }
        catch (\Exception  $ex) {

            $this->request->ex = $ex;
            $controller = new ErrorController ($this->request);
            return $controller->internal_server_error ();
        }
    }
}
