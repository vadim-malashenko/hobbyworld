<?php


namespace Hobbyworld\Grabber;


interface IGrabber {

    public function grab (int $timestamp, int $limit) : array;
}