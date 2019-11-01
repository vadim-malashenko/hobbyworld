<?php


namespace Hobbyworld\Controller;


use Hobbyworld\Http\Request;
use Hobbyworld\Http\Response;
use Hobbyworld\App;
use Hobbyworld\Model\ArticlesModel;
use Hobbyworld\Database\Sqlite3Database;
use Hobbyworld\Grabber\HabrGrabber;


class ArticlesController extends AbstractController {


    private $model;


    public function __construct(Request $request) {

        parent::__construct($request);

        $this->model = new ArticlesModel (new Sqlite3Database (App::sqlite3_db_file (), ArticlesModel::SCHEME));
    }


    public function articles () : Response {

        $id = intval (str_replace ('/articles/', '', $this->request->getMatches(0)));
        $limit = App::articles_per_page ();

        $data = [
            'pageNumber' => $id,
            'pagesCount' => ceil ($this->model->getArticlesCount () / $limit),
            'articles' => $this->model->getArticles ($id, $limit)
        ];

        return (new Response (200, $data))
            ->setContentType ('application/json');
    }

    public function article () : Response {

        $id = intval (str_replace ('/article/', '', $this->request->getMatches(0)));

        return (new Response (200, $this->model->getArticle ($id)))
            ->setContentType ('application/json');
    }

    public function update () : Response {

        return (new Response (200, $this->model->insertArticles ((new HabrGrabber ())->grab ($this->model->getLastArticleTimestamp (), App::articles_per_page ()))))
           ->setContentType ('application/json');
    }
}