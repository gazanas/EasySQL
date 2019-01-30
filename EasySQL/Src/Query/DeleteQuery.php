<?php

namespace EasySQL\Src\Query;

class DeleteQuery extends ClausableQuery
{
    
    /**
     * Initialize the query.
     *
     * @return DeleteQuery
     */
    public function init()
    {
        $this->query = 'DELETE FROM '.$this->table;

        return $this;
    }
}
