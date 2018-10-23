<?php

namespace EasySQL\Src\Query;

use EasySQL\Src\Sets as Sets;
use EasySQL\Src\Clause as Clause;

class InsertQuery implements Query {
	
	/**
    * Checks the type of the query and initializes the static start of the query
    * before the parameters of the query are required
    *
    * @param Sets\Sets $sets    The sets object.
    * @param string $action     The action to be performed by the query.
    * @param string $table      The table name.
    * @param array $params      The parameters array passed by the user.
    *
    * @return string $query     The start of the query string.
    * 
    * @throws \Exeption         Requested action is invalid.
    */
    public function initializeQuery(Sets\Sets $sets, string $table, array $params) {

        $query = 'INSERT INTO '.$table.'('.$this->prepareFieldsOfQuery($sets, $table, $params).') VALUES(';
        return $query;
    }

    /**
    * Sets the parameters (column names) that were not passed by the user.
    *
    * @param Sets\Sets $sets    The sets object.
    * @param string $table      The table name.
    * @param array $params      The parameters array passed by the user.
    *
    * @return array $params     The parameters array containing all the column names as keys.
    */
    private function setNotPassedParameters($sets, $table, $params) {
        foreach($sets->getColumns($table) as $index => $field) {
            /**
            * If field was passed in the parameters or field was not passed and has default value
            * then don't include it in the parameters for the insert fields clause.
            */
            if(array_key_exists($field, $params) || $this->checkDefaultValue($sets, $table, $field))
                continue;

            $params = array_slice($params, 0, $index, true) + array($field => NULL) + array_slice($params, $index, count($params), true);
        }

        return $params;
    }

    /**
    * If a column has a default value return true
    *
    * @param Sets\Sets $sets    The sets object.
    * @param string $table      The table name.
    * @param string $field      The column name.
    *
    * @return boolean
    */
    private function checkDefaultValue($sets, $table, $field) {
        if(in_array($field, $sets->getDefaultNames($table), true))
            return true;

        return false;
    }

    /**
    * Prepare the fields clause of the insert query e.g. INSERT INTO table(column_name1, column_name2, etc)
    * the fields are the column_name1, column_name2 etc.
    *
    * @param Sets\Sets $sets    The sets object.
    * @param string $table      The table name.
    * @param array $params      The parameters array passed by the user.
    *
    * @return string $fields    The fields clause of the query.
    */
    private function prepareFieldsOfQuery($sets, $table, $params) {
        $fields = '';

        $params = $this->setNotPassedParameters($sets, $table, $params);

        foreach($params as $key => $value)
            $fields .= $key.', ';

        $fields = preg_replace('/\, $/', '', $fields);

        return $fields;
    }
    
    /**
     * Set up the SQL query
     *
     * @param Sets\Sets $sets    The sets object.
     * @param string $action     The action to be performed by the query.
     * @param string $table      The table name.
     * @param array $params      The parameters array passed by the user.
     *
     * @return string $query    The finished query to be executed including the where clause.
     */
    public function setUpQuery(Sets\Sets $sets, string $table, array $params) {
        
        $query = $this->initializeQuery($sets, $table, $params);

        $query = $this->insertQuery($query, $sets, $table, $this->setNotPassedParameters($sets, $table, $params));
        
        return $query;
    }

    /**
    * Setup an insert sql query, this differs from generic queries
    * because it can not contain a where clause or an options clause.
    *
    * @param string $query      The initialized query for the action.
    * @param Sets\Sets $sets    The sets object.
    * @param string $table      The table name.
    * @param array $params      The parameters array passed by the user.
    *
    * @return string            The complete query to be executed.
    */
    private function insertQuery($query, $sets, $table, $params) {
        $params = $this->setNotPassedParameters($sets, $table, $params);
        
        $insertClauseObject = new Clause\InsertClause($sets->getAutoCompleted($table));
        $query .= $insertClauseObject->prepareClause($params);
    
        return $query;
    }
}