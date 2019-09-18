<?php

namespace EasySQL\Query;

use EasySQL\Facades\Facade;
use EasySQL\Drivers\MysqlConnection;
use EasySQL\Drivers\SqliteConnection;
use EasySQL\Drivers\MysqlDAO;
use EasySQL\Drivers\SqliteDAO;

class DB extends Facade
{

    public static $connection = null;
    public static $config = null;
    public static $dependencies = [];

	/**
	 * Resolve the service name
	 * 
	 * @return string
	 */
    public static function resolveServiceName() {
    	return 'EasySQL\\Query\\Builder';
    }

    /**
     * Reads the database configuration
     *
     * @return array
     **/
    private static function getDatabaseConfig()
    {
        self::$config = (isset(self::$config)) ? self::$config : 'config.ini';
        $dir = (isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__FILE__, 4);

        return parse_ini_file($dir.'/.env/database/'.self::$config);
    }

    /**
     * Resolve service dependencies
     * 
     * @return array
     */
    protected static function resolveServiceDependencies() {
        $config = self::getDatabaseConfig();

        if (empty(self::$dependencies)) {
            /**
             * If the connection object is not passed explicitly
             * then read the configuration, create the appropriate
             * connection objet and use it to instanciate the dependencies.
             */
            if (!isset(self::$connection)) {
                if ($config["dbms"] == "mysql") {
                    self::$connection = (new MysqlConnection)->connect($config['dbms'], $config['host'], $config['username'], $config['database'], $config['password']);
                    $dao = new MysqlDAO(self::$connection);
                } else if ($config["dbms"] == "sqlite") {
                    self::$connection = (new SqliteConnection)->connect($config['dbms'], $config['file'], $config['username'], $config['password']);
                    $dao = new SqliteDAO(self::$connection);
                }
                $syntax = __NAMESPACE__."\\".ucfirst($config["dbms"])."Syntax";
            } else {
                /**
                 * If the connection object is passed then retrieve the driver user
                 * and create the dependencies.
                 */
                $driver = self::$connection->getAttribute(\PDO::ATTR_DRIVER_NAME);
                $daoName = "EasySQL\\Drivers\\".ucfirst($driver)."DAO";
                $dao = new $daoName(self::$connection);
                $syntax = __NAMESPACE__."\\".ucfirst($driver)."Syntax";
            }
            self::$dependencies = [$dao, new $syntax];
        }
    
        return self::$dependencies;
    }
}
