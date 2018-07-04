<?php

namespace EasySQL\Src\Data;

class DatabaseSingleton
{

    /**
     * Database connection variable.
     *
     * @var $db
     */
    public static $db;


    /**
     * Connect to the database.
     *
     * @param array $config Database Configuration Array.
     */
    private function __construct(array $config)
    {
        try {
            // Connect to database or throw error message.
            self::$db = new \PDO($config[1].':host='.$config[2].';dbname='.$config[4], $config[3], $config[5]);
            self::$db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo 'Error!: '.$e->getMessage().'<br/>';
            die();
        }
    }


    /**
     * Get the database connection
     *
     * @param array $config Database Configuration Array.
     *
     * @return \PDO $db The Database connection object.
     */
    public static function getDB(array $config)
    {
        /*
            * If the connection doesn't exist or the purpose of the connection
            * is unit testing then the connnection should reset.
        */

        if (isset(self::$db) === false || $config[4] === 'test') {
            $db = new DatabaseSingleton($config);
        }

        return self::$db;
    }
}
