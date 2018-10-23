<?php

namespace EasySQL\Src\Query;

use EasySQL\Src\Sets as Sets;
use EasySQL\Src\Clause as Clause;

abstract class GenericQuery implements Query {
    
    /**
     * Set up the SQL query
     *
     * @param Sets\Sets $sets    The sets object.
     * @param string $action     The action to be performed by the query.
     * @param string $table      The table name.
     * @param array $params      The parameters array passed by the user.
     *
     * @return string $query    The finished query to be executed including the where clause.
     */
    public function setUpQuery(Sets\Sets $sets, string $table, array $params) {
        
        $query = $this->initializeQuery($sets, $table, $params);

        $query = $this->genericQuery($table, $query, $sets, $params);
      
        return $query;
    }

    /**
    * Setup a generic sql query, generic queries include select,
    * update, delete queries. That means queries that can contain a where
    * clause and options.
    *
    * @param string $query      The initialized query for the action.
    * @param Sets\Sets $sets    The sets object.
    * @param array $params      The parameters array passed by the user.
    *
    * @return string            The complete query to be executed.
    */
    private function genericQuery($table, $query, $sets, $params) {
        $params = array_diff_key($params, array_flip($sets->getActionParameters()));

        $whereClause = new Clause\WhereClause($table, $sets);

        $query .= $whereClause->prepareClause($params);        

        $queryOptions = new Clause\QueryOptions($sets);
        
        $query = $queryOptions->queryOptions($query, $params);

        return $query;
    }

}
