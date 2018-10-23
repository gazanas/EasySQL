<?php

namespace EasySQL\Src\Query;

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