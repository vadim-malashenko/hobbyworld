<?php


namespace Hobbyworld\Http;


class InternalServerErrorException extends \Exception {


    public function __construct (string $message = NULL) {

        parent::__construct ($message ?: 'Internal server error', 500);
    }
}