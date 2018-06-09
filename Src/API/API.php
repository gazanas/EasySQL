<?php

namespace Src\API;

use Src\Data as Data;
use Src\Collection\Collection as Collection;

class API extends APICall
{


    /**
     * Database Configuration Table.
     *
     * @var $config
     **/
    public $config;

    public $dbinfo;

    protected $sets;

    /**
     * Constructs the API Object and initializes the essential objects
     * for the API.
     *
     * @param array $config The Database configuration array.
     */
    public function __construct($config = null)
    {
        if ($config == null) {
            $configuration = new Data\Configuration();
            $this->config     = $configuration->getDatabaseConfig();
        } else {
            $this->config = $config;
        }
        $this->dbinfo = new DatabaseDAO($this->config);
        $this->sets = new Data\Sets($this->dbinfo->getTables($this->config[4]));
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
        $namespacedClass = 'Src\\API\\DAOs\\'.$class;
        $function        = $action;
        $object          = new $namespacedClass($this->config, $set);
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
