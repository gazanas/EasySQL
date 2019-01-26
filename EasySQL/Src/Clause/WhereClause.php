<?php

namespace EasySQL\Src\Clause;

use EasySQL\Src\Clause\Exceptions\InvalidConditionException;
use EasySQL\Src\Clause\Exceptions\InvalidOperatorException;
use EasySQL\Src\Sets\Sets;

class WhereClause implements ClauseInterface
{

    private $sets;
    private $columns;
    private $table;

    public function __construct(string $table, Sets $sets)
    {
        $this->table = $table;
        $this->sets = $sets;
    }

    private function boot()
    {
        $this->columns = $this->sets->getColumns($this->table);
    }
    
    /**
     * Simple Where Clause formats an array that consists of parameters
     * in a where clause where all conditions are equalities and are
     * connected with an AND expression.
     *
     * @param array $params The parameters array passed by the user.
     * 
     * @return string|null      The where clause of the SQL query.
     */
    public function prepareClause(array $params)
    {
        
        $this->boot();
        
        unset($params['options']);
        
        if (empty($params) === true) {
            return null;
        }

        $query = ' WHERE ';

        // Traverse the parameter array and set up the where clause for each one.
        $i = 0;
        if (isset($params['condition']) === true) {
            $size = (count($params) - 2);
        } else {
            $size = (count($params) - 1);
        }
        
        foreach (array_keys($params) as $key) {
            $query = $this->setUpWhereClause($query, $i, $size, $params, $key);
            $i++;
        }

        return $query;
    }

    /**
     * Set up the where clause of the query  for each parameter passed concatenate it with the query string
     * and return the finished query to be executed
     *
     * @param string  $query  The SQL query to be executed.
     * @param integer $i      A counter of the iterations of the parameters array.
     * @param int     $size   The size of the parameters array passed by the user, excluding the conditions array.
     * @param array   $params The parameters array passed by the user.
     * @param string  $key    The key of the parameters associative array iterated value.
     *
     * @return $string $query   The SQL query containing the where clause.
     */
    private function setUpWhereClause(string $query, int $i, int $size, $params, string $key)
    {
        $condition = $this->setUpCondition($params, $i);

        if (is_array($params[$key]) === true && is_numeric($key) === true) {
            foreach($params[$key] as $field => $param) {
                if ($field === 'operator') {
                    if(!in_array($param, $this->sets->getOperatorSet(), true)) {
                        throw new InvalidOperatorException($param);
                    }
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
     * @param string  $op        The operator for the where operation. e.g.(=, <, >)
     * @param string  $condition The condition that connects two where operations. e.g.(AND, OR)
     *
     * @return $string $query     The SQL query containing the where clause.
     */
    private function whereQuery(int $i, int $size, string $field, string $op, string $condition)
    {
        return ($i < $size) ? $field.' '.$op.' ? '.$condition.' ' : $field.' '.$op.' ?';
    }


    /**
     * If the condition array is passed, setup the condition between
     * each statement of the where clause.
     *
     * @param array   $params The parameters array passed by the user.
     * @param integer $i      A counter of the iterations of the parameters array.
     *
     * @return string $condition    The condition that connects two statements e.g. (AND, OR).
     */
    private function setUpCondition($params, int $i)
    {
        if (empty($params['condition'][$i]) === false) {
            $this->checkCondition($params['condition'][$i]);
            $condition = $params['condition'][$i];
        } else {
            $condition = 'AND';
        }

        return $condition;
    }

    private function checkCondition($condition)
    {
        if(!is_string($condition)) {
            throw new InvalidConditionException(gettype($condition));
        } else if(!in_array($condition, $this->sets->getConditionSet(), true)) {
            throw new InvalidConditionException($condition);
        }
    }
}
