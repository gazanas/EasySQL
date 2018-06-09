<?php

namespace Build;

/**
 * ConnectSingleton Class
 *
 * @version 0.1.0
 **/
class connectSingleton
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
    public function __construct(array $config)
    {
        try {
            // Connect to database or throw error message.
            self::$db = new \PDO($config[1].':host='.$config[2].';dbname='.$config[4], $config[3], $config[5]);
        } catch (PDOException $e) {
            echo 'Error!: '.$e->getMessage().'<br/>';
            die();
        }
    }//end __construct()


    /**
     * Get the database connection
     *
     * @param array $config Database Configuration Array.
     *
     * @return DatabaseSingleon $db The Database connection object.
     */
    public static function getConnection(array $config)
    {
        if (isset(self::$db) === false) {
            $db = new connectSingleton($config);
        }

        return self::$db;
    }//end getConnection()
}//end class
