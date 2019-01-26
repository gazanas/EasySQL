<?php

namespace EasySQL\Src\Query;

<<<<<<< HEAD
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
=======
use EasySQL\Src\Sets as Sets;

class DeleteQuery extends GenericQuery {
	
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
        $query = 'DELETE FROM '.$table; 

        return $query;
    }
}
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13
