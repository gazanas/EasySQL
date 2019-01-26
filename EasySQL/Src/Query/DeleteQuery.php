<?php

namespace EasySQL\Src\Query;

class DeleteQuery extends ClausableQuery
{
    
    /**
     * Initialize the query.
     * 
     * @return DeleteQuery
     */
    public function __init__()
    {
        $this->query = 'DELETE FROM '.$this->table; 

        return $this;
    }
}
