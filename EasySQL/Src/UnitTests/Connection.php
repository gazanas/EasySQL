<?php

namespace EasySQL\Src\UnitTests;

class Connection
{

    /**
     * Database connection variable.
     *
     * @var $db
     */
    protected $db;


    /**
     * Connect to the database.
     *
     * @param array $config Database Configuration Array.
     */
    public function __construct()
    {
    }

    public function createDatabase() {
        try {
            $config = $this->getDatabaseConfig(); 
            // Connect to database or throw error message.
            $this->db = new \PDO($config[1].':host='.$config[2], $config[3], $config[5]);
            $this->db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo 'Error!: '.$e->getMessage().'<br/>';
            die();
        }
        $this->db->query('CREATE DATABASE test');
        $this->db = null;
    }

    public function dropDatabase() {
        try {
            $config = $this->getDatabaseConfig(); 
            // Connect to database or throw error message.
            $this->db = new \PDO($config[1].':host='.$config[2], $config[3], $config[5]);
            $this->db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo 'Error!: '.$e->getMessage().'<br/>';
            die();
        }
        $this->db->query('DROP DATABASE test');
        $this->db = null;
    }

    /**
     * Reads the database configuration from the config.ini file
     *
     * @return array $config Database Configuration.
     **/
    private function getDatabaseConfig()
    {
        $config = array();
        $dbIni = file_get_contents(dirname(__DIR__, 3).'/.env/database/config.ini');

        preg_match_all('/.+ =\> .+/', $dbIni, $matches);

        foreach ($matches[0] as $index => $match) {
            $index++;
            $matchArray     = explode(' => ', $match);
            $config[$index] = $matchArray[1];
        }

        return $config;
    }//end getDatabaseConfig()


    /**
     * Get the database connection
     *
     * @param array $config Database Configuration Array.
     *
     * @return \PDO $db The Database connection object.
     */
    public function getDB()
    {
        $config = $this->getDatabaseConfig(); 

        $this->db = new \PDO(
            $config[1].':host='.$config[2].';dbname=test',
            $config[3],
            $config[5]
        );
        $this->db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $this->db;
    }
}
