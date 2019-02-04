<?php

namespace EasySQL\Src\API;

abstract class Connection
{

    /**
     * Database connection variable.
     *
     * @var $db
     */
    protected $db;

    abstract public function connect();

    /**
     * Reads the database configuration from the config.ini file
     *
     * @return array $config    Database Configuration.
     **/
    protected function getDatabaseConfig()
    {
        $matches = [];
        $config = array();
        $dbIni = file_get_contents(dirname($_SERVER['DOCUMENT_ROOT']).'/.env/database/config.ini');

        preg_match_all('/.+ =\> .+/', $dbIni, $matches);

        foreach ($matches[0] as $index => $match) {
            $index++;
            $matchArray     = explode(' => ', $match);
            $config[$index] = $matchArray[1];
        }

        return $config;
    }

    /**
     * Get the database connection
     *
     * @return \PDO $db     The Database connection object.
     */
    public function getDB()
    {
        return $this->db;
    }
}
