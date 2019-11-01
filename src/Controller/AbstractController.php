<?php


namespace Hobbyworld\Controller;


use Hobbyworld\Http\Request;


abstract class AbstractController {


    protected $request;


	public function __construct (Request $request) {

	    $this->request = $request;
	}
}