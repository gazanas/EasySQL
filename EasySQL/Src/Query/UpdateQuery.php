<?php

namespace EasySQL\Src\Query;

use EasySQL\Src\Parameters\Exceptions\FieldNotFoundException;

class UpdateQuery extends ClausableQuery
{
    
    /**
     * Initialize the query.
     */
    public function __init__()
    {
        
        $this->query = 'UPDATE '.$this->table.' ';

        return $this;
    }
    
    /**
     * Set the field to be updated and the updated value
     * 
     * @param string  $to_update
     * @param mixed  $updated
     * 
     * @return UpdateQuery
     */
    public function set(string $to_update, $updated)
    {
        try
        {
            $this->parameters->checkFieldExists($to_update, $this->table);
        
            $this->query .= 'SET '.$to_update.' = ?';
        
            $this->params['updated'] = $updated;
        
            return $this;
        } catch (FieldNotFoundException $e) {
            print($e->getMessage());
            return;
        }
    }
}