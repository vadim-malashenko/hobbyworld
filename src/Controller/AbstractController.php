<?php


namespace Hobbyworld\Controller;


use Hobbyworld\Http\Request;
use Hobbyworld\Http\Response;


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
	    return $this->$action ();
    }
}