<?php


namespace Hobbyworld\Http;


class NotImplementedException extends \Exception {


    public function __construct (string $message = NULL) {

        parent::__construct ($message ?: 'Not implemented', 501);
    }
}