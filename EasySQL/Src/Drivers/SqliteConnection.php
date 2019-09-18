<?php

namespace EasySQL\Drivers;

class SqliteConnection implements Connection
{
    /**
     * Connect to a database
     *
     * @param string $filename
     * 
     * @return Pdo | Mysqli
     */
    public function connect(string $dbms, string $file, string $username = null, string $password = null)
    {
        // Connect to database or throw error message.
        $db = new \PDO("{$dbms}:{$file}", $username, $password, [
            \PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ]);

        return $db;
    }
}
