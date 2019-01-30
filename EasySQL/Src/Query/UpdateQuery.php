<?php

namespace EasySQL\Src\Query;

class UpdateQuery extends ClausableQuery
{
    
    /**
     * Initialize the query.
     */
    public function init()
    {
        
        $this->query = 'UPDATE '.$this->table.' ';

        return $this;
    }
    
    /**
     * Set the field to be updated and the updated value
     *
     * @param string $to_update
     * @param mixed  $updated
     *
     * @return UpdateQuery
     */
    public function set(string $toUpdate, $updated)
    {
        $this->parameters->checkFieldExists($toUpdate, $this->table);
        
        $this->query .= 'SET '.$toUpdate.' = ?';
        
        $this->params['updated'] = $updated;
        
        return $this;
    }
}
