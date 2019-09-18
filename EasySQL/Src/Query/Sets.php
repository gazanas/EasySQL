<?php

namespace EasySQL\Query;

trait Sets
{
    /**
     * Returns the tables of the database
     *
     * @return array $tables    An array that contains the names of the tables.
     */
    public function getTables()
    {
        $query = $this->syntax->tables();
        /**
        * Fetch only a single column from the query, if the query fetches
        * multiple columns it fetches only the first.
        */
        $tables = $this->dao->executeQuery($query, []);

        foreach ($tables as $index => $table) {
            $tables[$index] = array_values($table)[0];
        }

        return $tables;
    }


    /**
     * Returns the columns of a table
     *
     * @param string $set Table name.
     *
     * @return array $columns   An array that contains the column names of the table.
     */
    public function getColumns(string $set)
    {
        //From the columns info save only the column name
        return array_map(function($column) {
            if(array_key_exists('name', $column)) {
                return $column['name'];
            } else if (array_key_exists('Field', $column)) {
                return $column['Field'];
            }
        }, $this->getColumnsInfo($set));
    }

    /**
     * Get info of the tables columns e.g.(type, default value, nullable etc)
     *
     * @param string $set Table name.
     *
     * @return array $columns    The array of the columns info.
     */
    public function getColumnsInfo(string $set)
    {
        $query = $this->syntax->columns($set);
        /**
        * Fetch the results of the query in an associative array
        * which keys are column names.
        */
        $columns = $this->dao->executeQuery($query, []);

        return $columns;
    }

    /**
     * Get the primary key of a table
     * 
     * @param string $set
     * 
     * @return string
     */
    public function getPrimaryKey(string $set)
    {
        foreach ($this->getColumnsInfo($set) as $data) {
            if (array_key_exists('Key', $data)) {
                if (strcmp($data['Key'], 'PRI') == 0) {
                    return $data['Field'];
                }
            } else if (array_key_exists('pk', $data)) {
                if ($data["pk"] == 1) {
                    return $data['name'];
                }
            }
        }
    }
}
