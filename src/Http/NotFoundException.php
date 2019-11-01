<?php


namespace Hobbyworld\Http;


class NotFoundException extends \Exception {


    public function __construct (string $message = NULL) {

        parent::__construct ($message ?: 'Not found', 404);
    }
}