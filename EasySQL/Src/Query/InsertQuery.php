<?php

namespace EasySQL\Src\Query;


use EasySQL\Src\Data\DAO;
use EasySQL\Src\Parameters\Parameters;
use EasySQL\Src\Sets\Sets;
use EasySQL\Src\Clause\InsertClause;

class InsertQuery
{
    
    private $dao;
    
    private $parameters;
    
    private $sets;
    
    private $table;
    
    private $query;
    
    private $params;
    
    public function __construct(DAO $dao, Parameters $parameters, Sets $sets, string $table)
    {
        $this->dao = $dao;
        
        $this->parameters = $parameters;
    
        $this->sets = $sets;
        
        $this->table = $table;
    
        $this->params = [];
        
        $this->__init__();
    }
    
    public function __init__()
    {
        return $this;
    }
    
    /**
     * Sets the parameters (column names) that were not passed by the user.
     *
     * @param string    $table  The table name.
     * @param array     $params The parameters array passed by the user.
     *
     * @return array $params     The parameters array containing all the column names as keys.
     */
    private function setNotPassedParameters(string $table, array $params)
    {
        foreach($this->sets->getColumns($table) as $index => $field) {
            /**
            * If field was passed in the parameters or field was not passed and has default value
            * then don't include it in the parameters for the insert fields clause.
            */
            if(array_key_exists($field, $params) || $this->checkDefaultValue($table, $field)) {
                continue;
            }

            $params = array_slice($params, 0, $index, true) + array($field => null) + array_slice($params, $index, count($params), true);
        }

        return $params;
    }

    /**
     * If a column has a default value return true
     *
     * @param string    $table The table name.
     * @param string    $field The column name.
     *
     * @return boolean
     */
    private function checkDefaultValue(string $table, string $field)
    {
        if(in_array($field, $this->sets->getDefaultNames($table), true)) {
            return true;
        }

        return false;
    }

    /**
     * Prepare the fields clause of the insert query e.g. INSERT INTO table(column_name1, column_name2, etc)
     * the fields are the column_name1, column_name2 etc.
     *
     * @param string    $table  The table name.
     * @param array     $params The parameters array passed by the user.
     *
     * @return string $fields    The fields clause of the query.
     */
    private function prepareFieldsOfQuery(string $table, array $params)
    {
        $fields = '';

        $params = $this->setNotPassedParameters($table, $params);

        foreach(array_keys($params) as $key) {
            $fields .= $key.', ';
        }

        $fields = preg_replace('/\, $/', '', $fields);

        return $fields;
    }
    
    /**
     * Set up the SQL query and prepare the parameters
     *
     * @param array     $params The parameters array passed by the user.
     *
     * @return string $query    The finished query to be executed including the where clause.
     */
    public function values(array $params)
    {
        try {
        
            $this->query = 'INSERT INTO '.$this->table.'('.$this->prepareFieldsOfQuery($this->table, $params).') VALUES(';
            $this->query = $this->insertQuery($this->query, $this->table, $this->setNotPassedParameters($this->table, $params));
                  
            $this->params = $this->parameters->prepareParameters($this->table, $params);
            
            return $this;
        } catch(\Exception $e) {
            print($e->getMessage());
            return;
        }
    }

    /**
     * Setup an insert sql query, this differs from clausable queries
     * because it can not contain a where clause or an options clause.
     *
     * @param string    $query  The initialized query for the action.
     * @param string    $table  The table name.
     * @param array     $params The parameters array passed by the user.
     *
     * @return string            The complete query to be executed.
     */
    private function insertQuery(string $query, string $table, array $params)
    {
        $params = $this->setNotPassedParameters($table, $params);
        
        $insertClauseObject = new InsertClause($this->sets->getAutoCompleted($table));
        $query .= $insertClauseObject->prepareClause($params);
    
        return $query;
    }
    
    public function execute()
    {
        try {
            return $this->dao->executeQuery($this->query, array_values($this->params));
        } catch(\Exception $e) {
            print($e->getMessage());
            return;
        }
    }
}
