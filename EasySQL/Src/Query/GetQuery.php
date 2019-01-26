<?php

namespace EasySQL\Src\Query;

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
