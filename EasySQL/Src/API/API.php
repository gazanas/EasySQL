<?php

namespace EasySQL\Src\API;

use EasySQL\Src\Data as Data;

class API extends APICall
{

    protected $db;

    public $dbinfo;

    protected $sets;

    /**
     * Constructs the API Object and initializes the essential objects
     * for the API.
     *
     * @param array $config The Database configuration array.
     */
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
    }


    /**
     * Gets the api call input, constructs a call
     * to the associated data access object (DAO),
     * and returns the result of the call.
     *
     * @param string $set      Table Name.
     * @param string $action   Action To Perform.
     * @param array  $params   Parameters.
     *
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
     * @param string $class Class Name.
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
