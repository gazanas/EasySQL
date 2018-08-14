<?php

namespace EasySQL\Src\Query;

class WhereClause implements ClauseInterface {

	/**
     * Simple Where Clause formats an array that consists of parameters
     * in a where clause where all conditions are equalities and are
     * connected with an AND expression.
     *
     * @param array $params     The parameters array passed by the user.
     * 
     * @return string|null      The where clause of the SQL query.
     */
    public function prepareClause($params)
    {
        unset($params['options']);
        
        if (empty($params) === true)
            return null;

        $sql = ' WHERE ';

        // Traverse the parameter array and set up the where clause for each one.
        $i = 0;
        if (isset($params['condition']) === true) {
            $size = (count($params) - 2);
        } else {
            $size = (count($params) - 1);
        }
        
        foreach ($params as $key => $param) {
            $sql = $this->setUpWhereClause($sql, $i, $size, $params, $key);
            $i++;
        }

        return $sql;
    }

    /**
     * Set up the where clause of the query  for each parameter passed concatenate it with the query string
     * and return the finished query to be executed
     *
     * @param string  $query    The SQL query to be executed.
     * @param integer $i        A counter of the iterations of the parameters array.
     * @param int $size         The size of the parameters array passed by the user, excluding the conditions array.
     * @param array  $params    The parameters array passed by the user.
     * @param string $key       The key of the parameters associative array iterated value.
     *
     * @return $string $query   The SQL query containing the where clause.
     */
    private function setUpWhereClause(string $query, int $i, int $size, $params, string $key)
    {
        $condition = $this->setUpCondition($params, $i);

        if (is_array($params[$key]) === true && is_numeric($key) === true) {
            foreach ($params[$key] as $field => $param) {
                if ($field === 'operator') {
                    $op = $param;
                    continue;
                } else if (!isset($op)) {
                    $op = '=';
                }

                $query .= $this->whereQuery($i, $size, $field, $op, $condition);
            }
        } else {
            if ($params[$key] && $key !== 'condition') {
                $query .= $this->whereQuery($i, $size, $key, '=', $condition);
            }
        }

        return $query;
    }

    /**
    * Set up the where condition as a string to concatenate with the query
    *
    * @param integer $i         A counter of the iterations of the parameters array.
    * @param integer $size      The size of the parameters array passed by the user, excluding the conditions array.
    * @param string  $field     The field (column name) to be used in the where operation.
    * @param string $op         The operator for the where operation. e.g.(=, <, >)
    * @param string $condition  The condition that connects two where operations. e.g.(AND, OR)
    *
    * @return $string $query     The SQL query containing the where clause.
    */
    private function whereQuery($i, $size, $field, $op, $condition) {
        if ($i < $size) {
            $query = $field.' '.$op.' ? '.$condition.' ';
        } else {
            $query = $field.' '.$op.' ?';
        }

        return $query;
    }


    /**
     * If the condition array is passed, setup the condition between
     * each statement of the where clause.
     *
     * @param array   $params       The parameters array passed by the user.
     * @param integer $i            A counter of the iterations of the parameters array.
     *
     * @return string $condition    The condition that connects two statements e.g. (AND, OR).
     */
    private function setUpCondition($params, int $i)
    {
        if (empty($params['condition'][$i]) === false) {
              $condition = $params['condition'][$i];
        } else {
            $condition = 'AND';
        }

        return $condition;
    }
}