<<<<<<< HEAD
<?php

namespace EasySQL\Src\API;

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
     */
    public function __construct()
    {
        try {
            $config = $this->getDatabaseConfig(); 
            if(!isset($config[5])) {
                $config[5] = null;
            }
            // Connect to database or throw error message.
            $this->db = new \PDO($config[1].':host='.$config[2].';dbname='.$config[4], $config[3], $config[5]);
            $this->db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this;
        } catch (\PDOException $e) {
            print('Error!: '.$e->getMessage().'<br/>');
            return;
        }
    }

    /**
     * Reads the database configuration from the config.ini file
     *
     * @return array $config    Database Configuration.
     **/
    private function getDatabaseConfig()
    {
        $matches = [];
        $config = array();
        $dbIni = file_get_contents(dirname(__DIR__, 3).'/.env/database/config.ini');

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
=======
<?php

namespace EasySQL\Src\API;

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
     */
    public function __construct()
    {
        try {
            $config = $this->getDatabaseConfig(); 
            if(!isset($config[5]))
                $config[5] = null;
            // Connect to database or throw error message.
            $this->db = new \PDO($config[1].':host='.$config[2].';dbname='.$config[4], $config[3], $config[5]);
            $this->db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo 'Error!: '.$e->getMessage().'<br/>';
            die();
        }
    }

    /**
     * Reads the database configuration from the config.ini file
     *
     * @return array $config    Database Configuration.
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
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13
