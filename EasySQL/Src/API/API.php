<?php

namespace EasySQL\Src\API;

use EasySQL\Src\Data as Data;

<<<<<<< HEAD
class API
{
    protected $db;
    protected $sets;
    
=======
class API extends APICall
{

    protected $db;

    public $dbinfo;

    protected $sets;

>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
    /**
     * Constructs the API Object and initializes the essential objects
     * for the API.
     *
     * @param PDO $db   The database object (it is only passed for unit testing)
     */
<<<<<<< HEAD
    public function __construct(\PDO $db)
    {
        $this->db = $db;
        $this->sets = new Data\Sets($this->db);
=======
    public function __construct($db = NULL)
    {
        if($db == NULL) {
            $database = new Data\Connection();
            $this->db = $database->getDB();
        } else {
            $this->db = $db;
        }
        $this->dbinfo = new DatabaseDAO($this->db);
        $this->sets = new Data\Sets($this->dbinfo->getTables());
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
    }


    /**
     * Gets the api call input, constructs a call
     * to the associated data access object (DAO),
     * and returns the result of the call.
     *
     * @param string $set      Table name.
     * @param string $action   Action to perform.
     * @param array  $params   The parameters array passed by the user.
     *
<<<<<<< HEAD
     * @return mixed $data      Result Of The Performed Action.
     **/
    public function _easy_sql(string $set, string $action, array $params = null)
    {
        $this->checkDataSet($set);

        $this->checkActionSet($action);

        $object          = new Data\DAO($this->sets, $set, strtolower($action), $params, $this->db);
        $data            = $object->$action();
        return $data;
    }

        /**
     *   Checks if the set provided is one of the database tables
     *
     * @param string $set   The name of the Data Set.
     *
     * @return boolean
     *
     * @throws SetException Set Could Not Be Found in the Data Sets.
     */
    private function checkDataSet(string $set) {
        
        if (in_array($set, $this->sets->getTables()) === true) {
            return true;
        } else {
            throw new \Exception('The requested '.$set.' data set can not be found.');
        }
    }


    /**
     *   Checks if the action provided is a valid action
     *
     * @param string $action    Action to be executed by the api.
     *
     * @return boolean
     *
     * @throws ActionException Action Could Not Be Found in the Action Set.
     */
    private function checkActionSet(string $action)
    {
        if (in_array($action, $this->sets->getActionSet()) === true) {
            return true;
        } else {
            throw new \Exception('The requested action '.$action.' could not be performed.');
        }
    }
}
=======
     * @return mixed $data Result Of The Performed Action.
     **/
    public function _easy_sql(string $set, string $action, array $params = null)
    {
        try {
            $this->sets->checkDataSet($set);
        } catch (Data\SetException $e) {
            echo $e->getMessage();
            return false;
        }

        try {
            $this->sets->checkActionSet($action);
        } catch (Data\ActionException $e) {
            echo $e->getMessage();
            return false;
        }

        $class           = preg_replace_callback(
            '/_([a-z]?)/',
            function ($match) {
                return '_'.strtoupper($match[1]);
            },
            ucfirst($set)
        );
        $dir             = $this->_searchClassDir($class);
        $namespacedClass = 'EasySQL\\Src\\API\\DAOs\\'.$class;
        $function        = $action;
        $object          = new $namespacedClass($set, $this->db);
        $data            = $object->$function($params);
        return $data;
    }


    /**
     * Find the directory which contains the file with the desired class.
     *
     * @param string $class     Class Name.
     *
     * @return string $directory Directory of Class File.
     **/
    private function _searchClassDir(string $class)
    {
        $it      = new \RecursiveDirectoryIterator(__DIR__);
        $display = [ 'php' ];
        $rit     = new \RecursiveIteratorIterator($it);
        foreach ($rit as $file) {
            $tmp = explode('.', $file);
            $ext = end($tmp);
            if (in_array($ext, $display) === true) {
                if (preg_match('/[A-Za-z0-9_]*'.$class.'.php/', basename($file)) === true) {
                    $dirArray  = explode(DIRECTORY_SEPARATOR, $file);
                    $directory = $dirArray[(count($dirArray) - 2)];
                    return $directory;
                }
            }
        }
    }
}
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
