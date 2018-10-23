<?php

namespace EasySQL\Src\Sets;

class DatabaseSets {

    /**
    * Create the database sets object, with the required PDO dependency.
    *
    * @param \PDO $db   The PDO object.
    */
    public function __construct($db) {
        
        $this->db = $db;
    }
    
    /**
     *   Returns the tables of the database
     *
     * @return array $tables    An array that contains the names of the tables.
     */
    public function getTables() {
        $query      = 'SHOW TABLES';
        // Prepare statement and execute it.
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        /**
        * Fetch only a single column from the query, if the query fetches
        * multiple columns it fetches only the first.
        */
        $tables = $stmt->fetchAll($this->db::FETCH_COLUMN, 0);
       
        return $tables;
    }


    /**
     *   Returns the columns of a table
     *
     * @param string $set       Table name.
     *
     * @return array $columns   An array that contains the column names of the table.
     */
    public function getColumns(string $set) {
        $column_info = $this->getColumnsInfo($set);
        $columns = array();

        //From the columns info save only the column name
        foreach($column_info as $info) {
            $columns[] = $info['Field'];
        }
        
        return $columns;
    }

    /**
    *   Get info of the tables columns e.g.(type, default value, nullable etc)
    *
    * @param string $set        Table name.
    *
    * @return array $columns    The array of the columns info.
    */
    public function getColumnsInfo(string $set) {
        // If the request table is not found stop the execution.
        if(!in_array($set, $this->getTables()))
            throw new \Exception('Table does not exist.');
        
        $query = 'SHOW COLUMNS FROM '.$set;
        // Prepare statement and execute it.
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        /**
        * Fetch the results of the query in an associative array
        * which keys are column names.
        */
        $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $columns;
    }


    /**
     * Get column names whose values can not be NULL and are not auto completed.
     *
     * @param string $set       Table name.
     *
     * @return array $required  The array of the required column names.
     */
    public function getRequiredColumns(string $set) {
        $autos = $this->getAutoCompletedNames($set);

        // Retrieve the database name.
        $database = $this->db->query('select database()')->fetchColumn();
        
        // This query selects all the columns from the table that have not nullable values.
        $query      = 'SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=\''.$database.'\' AND table_name=\''.$set.'\' AND IS_NULLABLE=\'NO\'';
        // Prepare statement and execute it.
        $stmt     = $this->db->prepare($query);
        $stmt->execute();
        /**
        * Fetch only a single column from the query, if the query fetches
        * multiple columns it fetches only the first.
        */
        $results            = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);

        // If no results were returned, return empty array.
        if (empty($results))
            return array();

        /**
        * Subtract columns that have auto completed values,
        * since they are not needed to be passed as parameters on the api call.
        */
        return array_diff($results, $autos);
    }


    /**
     * Get table columns that have autocompleted values (auto_increment, current_timestamp)
     *
     * @param string $set   Table name.
     *
     * @return array $auto  An array that contains the column names that have autocompleted values.
     */
    public function getAutoCompleted(string $set) {
            $columns = $this->getColumnsInfo($set);

            /**
            * Create an array containing the name of the column
            * and the type of the auto completed value.
            */
            $auto = $this->getColumnsWithDefaultValues($set);

            /**
            * Append to the array the columns that have
            * auto incremented values.
            */
            foreach ($columns as $column) {
                if ($column['Extra'] === 'auto_increment') {
                    $auto[] = [
                        'name' => $column['Field'],
                        'type' => 'auto_increment'
                    ];
                }
            }

            return $auto;
    }

    /**
    * Get an array containing only the names of the columns
    * that have auto completed values
    *
    * @param string $set            The table name.
    *
    * @return array $autoCompleted  The array containing the auto completed columns.
    */
    public function getAutoCompletedNames(string $set) {
        $autos = $this->getAutoCompleted($set);
        $autoCompleted = array();
        
        /**
        * From the auto completed columns array save only the 
        * names of the columns into an array.
        */
        foreach ($autos as $auto) {
            $autoCompleted[] = $auto['name'];
        }

        return $autoCompleted;
    }

    /**
     * Get table columns that have default values.
     *
     * @param string $set       Table name.
     *
     * @return array $defaults  An array that contains the column names that have default values.
     */
    public function getColumnsWithDefaultValues(string $set) {
        $defaults = array();
        $columns = $this->getColumnsInfo($set);

        /**
        * Append to the array the columns that have
        * default values.
        */
        foreach ($columns as $column) {
            if ($column['Default'] != NULL) {
                $defaults[] = [
                    'name' => $column['Field'],
                    'type' => $column['Default']
                ];
            }
        }

        return $defaults;
    }

    /**
    * Get an array containing only the names of the columns
    * that have default values
    *
    * @param string $set            The table name.
    *
    * @return array $defaultNames   The array containing the columns that have default values.
    */
    public function getDefaultNames(string $set) {
        $defaults = $this->getColumnsWithDefaultValues($set);
        $defaultNames = array();
        
        /**
        * From the auto completed columns array save only the 
        * names of the columns into an array.
        */
        foreach ($defaults as $default) {
            $defaultNames[] = $default['name'];
        }

        return $defaultNames;
    }
}
