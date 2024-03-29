<?php


namespace Hobbyworld\Database;


class Sqlite3Database implements ISqlDatabase {


    const TYPE = [
        'INT' => SQLITE3_INTEGER,
        'VARCHAR' => SQLITE3_TEXT,
        'TEXT' => SQLITE3_TEXT
    ];


    private $db = null;
    private $scheme = [];
    private $sql = '';


    public function __construct (string $file) {

        //throw new \Exception ('Database error: Something went wrong', 500);

        $this->db = new \SQLite3 ($file);
    }

    public function __destruct () {

        $this->db->close ();
    }


    public function create (string $table, array $scheme) {

        $columns = [];

        foreach ($scheme as $name => $type) {

            $columns [] = "$name $type";
        }

        $columns = implode (',', $columns);

        if ($this->db->exec ("CREATE TABLE IF NOT EXISTS $table ($columns)") !== false) {

            $this->scheme [$table] = $scheme;
        }
    }

    public function insert (string $table, array $items) : int {

        if (count ($items) == 0) {

            return 0;
        }

        $scheme = $this->scheme [$table];

        $rows = 0;
        $columns = array_keys ($scheme);

        $values = array_reduce ($columns, function ($values, $column) {

            $values [] = ":$column";

            return $values;
        }, []);

        $values = implode (',', $values);

        $st = $this->db->prepare ("INSERT INTO $table VALUES ($values)");

        foreach ($scheme as $column => $type) {

            $$column = null;

            $st->bindParam (":$column", $$column, self::TYPE [$type]);
        }

        foreach ($items as $item) {

            foreach ($columns as $column) {

                $$column = $item [$column];
            }

            $rows += ($st->execute () !== false);
        }

        return $rows;
    }


    public function select ($table, array $columns) : self {

        $columns = (count ($columns) > 0) ? implode (',', $columns) : '*';

        $this->sql = "SELECT $columns FROM $table";

        return $this;
    }

    public function where (string $column, string $operator, $value) : self {

        if (is_string ($value)) {

            $value = "'$value'";
        }

        $this->sql .= " WHERE $column $operator $value";

        return $this;
    }

    public function orderBy (string $column, string $order = null) : self {

        $order = $order ?? 'DESC';

        $this->sql .= " ORDER BY $column $order";

        return $this;
    }

    public function limit (string $limit) : self {

        $this->sql .= " LIMIT $limit";

        return $this;
    }

    public function all () : array {

        $results =  $this->db->query ($this->sql);
        $items = [];

        while ($item = $results->fetchArray (SQLITE3_ASSOC)) {

            $items [] = $item;
        }

        return $items;
    }

    public function one () : array {

        $result = $this->db->query ($this->sql)->fetchArray (SQLITE3_ASSOC);

        return ($result !== false) ? $result : [];
    }

    public function exec ($sql) {

        $this->db->exec ($sql);
    }
}