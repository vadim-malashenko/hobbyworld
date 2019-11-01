<?php


namespace Hobbyworld\Model;


use Hobbyworld\Database\ISqlDatabase;


class ArticlesModel {


    const SCHEME = [
        'id' => 'INT',
        'timestamp' => 'INT',
        'url' => 'VARCHAR',
        'title' => 'VARCHAR',
        'brief' => 'TEXT',
        'content' => 'TEXT'
    ];


    private $db;


    public function __construct (ISqlDatabase $db) {

        $this->db = $db;
        $this->db->exec ('CREATE TABLE IF NOT EXISTS articles (id INT, timestamp INT, url VARCHAR, title VARCHAR, brief VARCHAR, content TEXT)');
    }


    public function insertArticles (array $articles) : int {

        return $this->db->insert ($articles);
    }


    public function getArticlesCount () : int {

        return intval ($this->db
            ->select (['COUNT(*)'])
            ->orderBy ('timestamp', 'DESC')
            ->value ());
    }


    public function getArticles (int $page, int $limit) : array {

        return $this->db
            ->select (['id', 'title', 'url', 'brief'])
            ->orderBy ('timestamp')
            ->limit (($page - 1) * $limit . ',' . $limit)
            ->all ();
    }

    public function getArticle (int $id) : array {

        return $this->db
            ->select (['title', 'content'])
            ->where ('id', '=', $id)
            ->one ();
    }


    public function getLastArticleTimestamp () : int {

        return intval ($this->db
            ->select (['timestamp'])
            ->orderBy ('timestamp', 'DESC')
            ->value ());
    }
}