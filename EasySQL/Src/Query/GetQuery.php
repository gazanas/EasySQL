<?php

namespace EasySQL\Src\Query;

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