<?php


namespace Hobbyworld\Controller;


use Hobbyworld\App;

use Hobbyworld\Http\NotFoundException;
use Hobbyworld\Http\Request;
use Hobbyworld\Http\Response;

use Hobbyworld\Database\ISqlDatabase;
use Hobbyworld\Grabber\IGrabber;

use Hobbyworld\Model\ArticlesModel;


class AppController extends AbstractController {


    private $db = null;
    private $grabber = null;


    public static function factory (Request $request, ISqlDatabase $db, IGrabber $grabber) : self {

        $controller = new self ($request);

        $controller->db = $db;
        $controller->grabber = $grabber;

        return $controller;
    }


    public function index () {

        require App::$config->index_file;
        exit (0);
    }


    public function page () : Response {

        $id = intval (str_replace ('/page/', '', $this->request->getMatches (0)));
        $limit = App::$config->limit;
        $model = new ArticlesModel ($this->db);
        $last = ceil ($model->getItemsCount () / $limit);
        $items = $model->getItems ($id, $limit);

        $page = [
            'current' => $id,
            'last' => $last,
            'items' => $items
        ];

        return new Response (200, $page);
    }

    public function item () : Response {

        $id = intval (str_replace ('/item/', '', $this->request->getMatches (0)));
        $model = new ArticlesModel ($this->db);
        $item = $model->getItem ($id);

        if ( ! isset ($item ['id']))

            throw new NotFoundException ('Not found: article/' . $id);

        return new Response (200, $item);
    }

    public function update () : Response {

        $model = new ArticlesModel ($this->db);
        $timestamp = $model->getItem (0);
        $timestamp = isset ($timestamp ['timestamp']) ? $timestamp ['timestamp'] : 0;
        $limit = App::$config->limit;
        $articles = $this->grabber->grab ($timestamp, $limit);
        $insertedItemsCount = $model->inserItems ($articles);

        return new Response (200, [$insertedItemsCount]);
    }
}