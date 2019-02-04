<?php

namespace EasySQL\Src\Data;

abstract class DAO
{

    /**
     * The database connection object
     *
     * @var \PDO $_db
     */
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    abstract public function executeQuery(string $query, array $params);
}
