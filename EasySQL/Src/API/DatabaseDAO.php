<?php

namespace EasySQL\Src\API;

use EasySQL\Src\Data as Data;

class DatabaseDAO
{
    
    protected $sql;
    protected $db;

    public function __construct(\PDO $db)
    {
        $this->db          = $db;
        $this->sql = new Data\SQL($this->db);
    }

    /**
     *   Returns the tables of the database
     *
     * @return array $tables    An array that contains the names of the tables.
     */
    public function getTables()
    {
        $query      = 'SHOW TABLES';
        $tables   = $this->sql->fetchDBData($query, array());
        return $tables;
    }


    /**
     *   Returns the columns of a table
     *
     * @param string $set       Table name.
     *
     * @return array $columns   An array that contains the column names of the table.
     */
    public function getColumns(string $set)
    {
        $query     = 'SHOW COLUMNS FROM '.$set;
        $columns = $this->sql->fetchDBData($query);
        return $columns;
    }


    /**
    * Return an array containing all the columns from a table that can have a value of NULL
    * except those that can be NULL but have auto completed values
    *
    * @param string $set        Table name.
    *
    * @return array|boolean     The array of the columns.
    */
    public function getNullableColumns(string $set)
    {
        $database = $this->db->query('select database()')->fetchColumn();
        try {
            $columns = [];

            $query  = 'SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE '."TABLE_SCHEMA='".$database."' AND table_name='".$set."' AND IS_NULLABLE='YES'";
           
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results            = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
            if (empty($results)) {
                return false;
            }
            $columns = $this->subtractAutoCompleted($results, $set);

            return $columns;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }


    /**
     * Get column names whose values can not be NULL and are not auto completed.
     *
     * @param string $set       Table name.
     *
     * @return array|boolean    The array of the required column names.
     *
     * @throws PDOException     Query execution error.
     */
    public function getRequiredColumns(string $set)
    {
        $database = $this->db->query('select database()')->fetchColumn();
        try {
            $query      = 'SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE '."TABLE_SCHEMA='".$database."' AND table_name='".$set."' AND IS_NULLABLE='NO'";
            $stmt     = $this->db->prepare($query);
            $stmt->execute();
            $results            = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
            if (empty($results)) {
                return false;
            }
            $required = $this->subtractAutoCompleted($results, $set);

            return $required;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }


    /**
    * Subtracts all auto completed columns of a table from a given array of columns
    * of the same table.
    *
    * @param array $columns     An array containing the column names of the table.
    * @param string $set        Table name.
    *
    * @return array|boolean     The columns array after the subtraction of the auto completed columns.
    */
    private function subtractAutoCompleted(array $columns, string $set)
    {

        $autos              = $this->getAutoCompleted($set);
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
     * @param string $set       Table name.
     *
     * @return array|boolean    An array that contains the column names that have autocompleted values.
     *
     * @throws PDOException     Query execution error.
     */
    public function getAutoCompleted(string $set)
    {
        try {
            $auto = array();
            $required = array();

            $query = 'show columns from '.$set;
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
        }
    }
}
