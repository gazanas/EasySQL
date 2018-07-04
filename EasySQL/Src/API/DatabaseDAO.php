<?php

namespace EasySQL\Src\API;

use EasySQL\Src\Data as Data;

class DatabaseDAO
{
    
    protected $sql;
    protected $db;

    public function __construct(array $config)
    {
        $this->db          = Data\DatabaseSingleton::getDB($config);
        $this->sql = new Data\SQL($config);
    }

    /**
     *   Returns the tables of the database
     *
     * @param string $database The name of the database.
     *
     * @return array $result The array with the names of the tables.
     */
    public function getTables(string $database)
    {
        $query      = 'SELECT table_name FROM information_schema.tables WHERE table_schema=?';
        $params[] = $database;
        $tables   = $this->sql->fetchDBData($query, $params);
        return $tables;
    }//end getTables()


    /**
     *   Returns the columns of a table
     *
     * @param string $table The name of the table.
     *
     * @return array $columns The columns of the table.
     */
    public function getColumns(string $table)
    {
        $query     = 'SHOW COLUMNS FROM '.$table;
        $columns = $this->sql->fetchDBData($query);
        return $columns;
    }//end getColumns()


    /**
    * Return an array containing all the columns from a table that can have a value of NULL
    * except those that can be NULL but have auto completed values
    *
    * @param string $database The database name.
    * @param string $table The table name.
    *
    * @return array|boolean The array of the columns.
    */
    public function getNullableColumns(string $database, string $table)
    {
        try {
            $columns = [];

            $query  = 'SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE '."TABLE_SCHEMA='".$database."' AND table_name='".$table."' AND IS_NULLABLE='YES'";
           
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results            = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
            if (empty($results)) {
                return false;
            }
            $columns = $this->subtractAutoCompleted($results, $table);

            return $columns;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return false;
        }//end try
    }


    /**
     * Get column names whose values can not be NULL and are not auto completed.
     *
     * @param string $database The database name.
     * @param string $table    The table name.
     *
     * @return array|boolean The array of the required column names.
     *
     * @throws PDOException Query execution error.
     */
    public function getRequiredColumns(string $database, string $table)
    {
        try {
            $query      = 'SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE '."TABLE_SCHEMA='".$database."' AND table_name='".$table."' AND IS_NULLABLE='NO'";
            $stmt     = $this->db->prepare($query);
            $stmt->execute();
            $results            = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
            if (empty($results)) {
                return false;
            }
            $required = $this->subtractAutoCompleted($results, $table);

            return $required;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return false;
        }//end try
    }//end getRequired()


    /**
    * Subtracts all auto completed columns of a table from a given array of columns
    * of the same table.
    *
    * @param array $columns The array containing the columns of the table.
    * @param string $table The name of the table.
    *
    * @return array|boolean The columns array after the subtraction of the auto completed columns.
    */
    private function subtractAutoCompleted(array $columns, string $table)
    {

        $autos              = $this->getAutoCompleted($table);
        if(empty($autos))
            return false;

        $subtracted = array();
        $autoCompleted = array();
           
        foreach ($autos as $auto) {
            $autoCompleted[] = $auto['name'];
        }

        foreach ($columns as $column) {
            if (in_array($column, $autoCompleted) === false) {
                $subtracted[$column] = $column;
            }
        }

        return $subtracted;
    }


    /**
     * Get table columns that have autocompleted values (auto_increment, current_timestamp)
     *
     * @param string $table The table name.
     *
     * @return array|boolean The array of the autocompleted columns.
     *
     * @throws PDOException Query execution error.
     */
    public function getAutoCompleted(string $table)
    {
        try {
            $auto = array();
            $required = array();

            $query = 'show columns from '.$table;
            $sth = $this->db->prepare($query);
            $sth->execute();
            $columns = $sth->fetchAll(\PDO::FETCH_ASSOC);
            $sql     = null;

            foreach ($columns as $column) {
                if ($column['Default'] === 'CURRENT_TIMESTAMP') {
                    $auto[] = [
                        'name' => $column['Field'],
                        'type' => 'current_timestamp'
                    ];
                }

                if ($column['Extra'] === 'auto_increment') {
                    $auto[] = [
                        'name' => $column['Field'],
                        'type' => 'auto_increment'
                    ];
                }
            }

            return $auto;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return false;
        }//end try
    }//end getAutoCompleted()
}
