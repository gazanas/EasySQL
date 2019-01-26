<?php

namespace EasySQL\Src\Query;

<<<<<<< HEAD
use EasySQL\Src\Parameters\Exceptions\FieldNotFoundException;

class GetQuery extends ClausableQuery
{
    
    /**
     * Initialize the query.
     */
    protected function __init__(...$args)
    {
        if(!empty($args))
            $this->query = 'SELECT * FROM '.$this->table.' JOIN '.$args[0].' ON '.$this->table.'.'.$args[1].'='.$args[0].'.'.$args[2];  
        else
            $this->query = 'SELECT * FROM '.$this->table;        
    }
    
    /**
     * Extracts the return values clause for the get query.
     *
     * @param array|string $returnFields    The fields to return passed by the user.
     *
     * @return ClausableQuery
     */
    public function return(...$returnFields)
    {   
        $fields = '';
        try {
            foreach($returnFields as $field) {
                if(preg_match('/[a-zA-Z0-9_]+\.[a-zA-Z0-9_]+/', $field))
                    $this->parameters->checkFieldExists(explode('.', $field)[1], explode('.', $field)[0]);
                else
                    $this->parameters->checkFieldExists($field, $this->table);
                
                $fields .= $field.', ';
            }
    
            $fields = preg_replace('/, $/', '', $fields);
            
            $this->query = preg_replace('/^SELECT \* FROM/', 'SELECT '.$fields.' FROM', $this->query);
            
            return $this;
        } catch (FieldNotFoundException $e) {
            print($e->getMessage());
            return;
        }
    }
}
=======
use EasySQL\Src\Sets as Sets;

class GetQuery extends GenericQuery {
	
	/**
    * Checks the type of the query and initializes the static start of the query
    * before the parameters of the query are required
    *
    * @param Sets\Sets $sets    The sets object.
    * @param string $table      The table name.
    * @param array $params      The parameters array passed by the user.
    *
    * @return string $query     The start of the query string.
    * 
    * @throws \Exeption         Requested action is invalid.
    */
    public function initializeQuery(Sets\Sets $sets, string $table, array $params) {
        $tableFields = $sets->getColumns($table);
        
        if(isset($params['return'])) {
            $returnFields = $this->returnFields($tableFields, $params['return']);
            $query = 'SELECT '.$returnFields.' FROM '.$table;
        } else {
            $query = 'SELECT * FROM '.$table;
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
                $fields .= $field.', ';
            }
        } else {
            $fields = $returnFields;
        }

        $fields = preg_replace('/, $/', '', $fields);

        return $fields;
    }
}
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13
