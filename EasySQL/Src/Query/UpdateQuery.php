<?php

namespace EasySQL\Src\Query;

<<<<<<< HEAD
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
=======
use EasySQL\Src\Sets as Sets;

class UpdateQuery extends GenericQuery {
	
	/**
    * Checks the type of the query and initializes the static start of the query
    * before the parameters of the query are required
    *
    * @param Data\Sets $sets    The sets object.
    * @param string $table      The table name.
    * @param array $params      The parameters array passed by the user.
    *
    * @return string $query     The start of the query string.
    * 
    * @throws \Exeption         Requested action is invalid.
    */
    public function initializeQuery(Sets\Sets $sets, string $table, array $params) {
        
        $query = 'UPDATE '.$table.' SET '.$params['to_update'].' = ?';

        return $query;
    }
}
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13
