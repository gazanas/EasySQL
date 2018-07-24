<?php

namespace Build;

/**
 * ConnectSingleton Class
 *
 * @version 0.1.0
 **/
class Connection
{

     /**
      * Database connection variable.
      *
      * @var $db
      */
    public $db;


    /**
     * Connect to the database.
     *
     * @param array $config Database Configuration Array.
     */
    public function __construct(array $config)
    {
        try {
            // Connect to database or throw error message.
            $this->db = new \PDO($config[1].':host='.$config[2].';dbname='.$config[4], $config[3], $config[5]);
        } catch (PDOException $e) {
            echo 'Error!: '.$e->getMessage().'<br/>';
            die();
        }
    }//end __construct()


    /**
     * Get the database connection
     *
     * @return PDO $db The Database connection object.
     */
    public function getConnection()
    {
        return $this->db;
    }//end getConnection()
}//end class
