<?php

namespace EasySQL\Drivers;

class MysqlConnection implements Connection
{

    /**
     * Connect to the server without using a database
     * 
     * @param string $filename
     * 
     * @return Pdo | Mysqli
     */
    public function connectNoDatabase(string $dbms, string $host, string $username, string $password = null)
    {
        // Connect to database or throw error message.
        $db = new \PDO("{$dbms}:host={$host};charset=utf8", $username, $password,  [
          \PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
          \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
          \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ]);

        return $db;
    }
    
    /**
     * Connect to a database
     *
     * @param string $filename
     * 
     * @return Pdo | Mysqli
     */
    public function connect(string $dbms, string $host, string $username, string $database, string $password = null)
    {
        // Connect to database or throw error message.
        $db = new \PDO("{$dbms}:host={$host};dbname={$database};charset=utf8", $username, $password,  [
          \PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
          \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
          \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ]);

        return $db;
    }
}
