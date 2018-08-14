<?php

namespace EasySQL\Src\Query;

class Query {

    /**
    * Checks the type of the query and initializes the static start of the query
    * before the parameters of the query are required
    *
    * @param Data\Sets $sets    The sets object.
    * @param string $action     The action to be performed by the query.
    * @param string $table      The table name.
    * @param array $params      The parameters array passed by the user.
    *
    * @return string $query     The start of the query string.
    * 
    * @throws \Exeption         Requested action is invalid.
    */
    private function initializeQuery($sets, $action, $table, $params) {
        $tableFields = $sets->getColumns($table);
        switch($action) {
            case 'get':
                $query = 'SELECT * FROM '.$table;
                break;
            case 'value':
                $returnFields = $this->returnFields($tableFields, $params['return']);
                $query = 'SELECT '.$returnFields.' FROM '.$table;
                break;
            case 'update':
                $this->checkFieldExists($tableFields, $params['to_update']);
                $query = 'UPDATE '.$table.' SET '.$params['to_update'].' = ?';
                break;
            case 'delete':
                $query = 'DELETE FROM '.$table; 
                break;
            case 'insert':
                $query = 'INSERT INTO '.$table.'('.$this->prepareFieldsOfQuery($sets, $table, $params).') VALUES(';
                break;
            default:
                throw new \Exception('Request action is invalid.');
        }

        return $query;
    }

    /**
    * Extracts the return values clause for the value action query.
    *
    * @param array $tableFields             An array of all the columns of the table.
    * @param array|string $returnFields     The fields to return passed by the user.
    *
    * @return string $fields                The return clause of the query.
    */
    private function returnFields($tableFields, $returnFields) {
        if(is_array($returnFields)) {
            $fields = '';
            foreach($returnFields as $field) {
                $this->checkFieldExists($tableFields, $field);
                $fields .= $field.', ';
            }
        } else {
            $this->checkFieldExists($tableFields, $returnFields);
            $fields = $returnFields;
        }

        $fields = preg_replace('/, $/', '', $fields);

        return $fields;
    }
    
    /**
    * Checks if an action parameter passed by the user corresponds to a table
    * column. If not throw an exception.
    *
    * @param array $tableFields     An array of all the columns of the table.
    * @param string $field          The field to check.
    *
    * @throws \Exception             Field does not exist on the table.
    */
    private function checkFieldExists($tableFields, $field) {
        if(!in_array($field, $tableFields))
            throw new \Exception('Field '.$field.' does not exist.');
    }

    /**
    * Sets the parameters (column names) that were not passed by the user.
    *
    * @param Data\Sets $sets    The sets object.
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
    * @param Data\Sets $sets    The sets object.
    * @param string $table      The table name.
    * @param string $field      The column name.
    *
    * @return boolean
    */
    private function checkDefaultValue($sets, $table, $field) {
        if(in_array($field, $sets->getDefaultNames($table)))
            return true;

        return false;
    }

    /**
    * Prepare the fields clause of the insert query e.g. INSERT INTO table(column_name1, column_name2, etc)
    * the fields are the column_name1, column_name2 etc.
    *
    * @param Data\Sets $sets    The sets object.
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
     * @param Data\Sets $sets    The sets object.
     * @param string $action     The action to be performed by the query.
     * @param string $table      The table name.
     * @param array $params      The parameters array passed by the user.
     *
     * @return string $query    The finished query to be executed including the where clause.
     */
    public function setUpQuery($sets, $action, $table, $params) {
        $query = $this->initializeQuery($sets, $action, $table, $params);

        if($action == 'insert') {
            $query = $this->insertQuery($query, $sets, $table, $this->setNotPassedParameters($sets, $table, $params));
        } else {
            $query = $this->genericQuery($query, $sets, $params);
        } 
        return $query;
    }

    /**
    * Setup a generic sql query, generic queries include select,
    * update, delete queries. That means queries that can contain a where
    * clause and options.
    *
    * @param string $query      The initialized query for the action.
    * @param Data\Sets $sets    The sets object.
    * @param array $params      The parameters array passed by the user.
    *
    * @return string            The complete query to be executed.
    */
    private function genericQuery($query, $sets, $params) {
        $params = array_diff_key($params, array_flip($sets->getActionParameters()));
            
        $whereClause = new WhereClause();
        $queryOptions = new QueryOptions($sets);

        $query .= $whereClause->prepareClause($params);        
        return $queryOptions->queryOptions($query, $params);
    }

    /**
    * Setup an insert sql query, this differs from generic queries
    * because it can not contain a where clause or an options clause.
    *
    * @param string $query      The initialized query for the action.
    * @param Data\Sets $sets    The sets object.
    * @param string $table      The table name.
    * @param array $params      The parameters array passed by the user.
    *
    * @return string            The complete query to be executed.
    */
    private function insertQuery($query, $sets, $table, $params) {
        $params = $this->setNotPassedParameters($sets, $table, $params);
        
        $insertClauseObject = new InsertClause($sets->getAutoCompleted($table));
        $query .= $insertClauseObject->prepareClause($params);
    
        return $query;
    }
}
