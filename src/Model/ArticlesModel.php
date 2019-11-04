<?php


namespace Hobbyworld\Model;


use Hobbyworld\Database\ISqlDatabase;


class ArticlesModel {


    private $db;


    const TABLE = 'articles';

    const SCHEME = [
        'id' => 'INT',
        'timestamp' => 'INT',
        'url' => 'VARCHAR',
        'title' => 'VARCHAR',
        'brief' => 'TEXT',
        'content' => 'TEXT'
    ];


    public function __construct (ISqlDatabase $db) {

        $this->db = $db;
        $this->db->create (self::TABLE, self::SCHEME);
    }


    public function inserItems (array $articles) : int {

        return $this->db
            
            ->insert (self::TABLE, $articles);
    }


    public function getItemsCount () : int {

        $item = $this->db
            
            ->select (self::TABLE, ['COUNT(*)'])
            ->orderBy ('timestamp', 'DESC')
            ->one ();

        return $item ['COUNT(*)'];
    }


    public function getItems (int $page, int $limit) : array {

        return $this->db
            
            ->select (self::TABLE, ['id', 'title', 'url', 'brief'])
            ->orderBy ('timestamp')
            ->limit (($page - 1) * $limit . ',' . $limit)
            ->all ();
    }

    public function getItem (int $id) : array {

        return ($id > 0)

            ? $this->db

                ->select (self::TABLE, ['id', 'title', 'content'])
                ->where ('id', '=', $id)
                ->one ()

            : $this->db
                
                ->select (self::TABLE, ['*'])
                ->orderBy ('timestamp', 'DESC')
                ->limit (1)
                ->one ();
    }
}