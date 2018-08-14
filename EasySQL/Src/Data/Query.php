<?php

namespace EasySQL\Src\Data;

class Query
{

    protected $sets;

    public function __construct()
    {
        $this->sets = new Sets();
    }


    /**
     * Set up the where clause and the options clause of an SQL query
     *
     * @param string $query     The query to be executed without the where clause.
     * @param array  $params    The parameters array passed by the user.
     *
     * @return string $query    The finished query to be executed including the where clause.
     */
    public function setUpQuery(string $query, array $params)
    {
        $query .= $this->simpleWhereClause($params);

        try {
            $query  = $this->queryOptions($query, $params);
        } catch (OptionsException $e) {
            echo $e->getMessage();
        }

        return $query;
    }


    /**
     * Simple Where Clause formats an array that consists of parameters
     * in a where clause where all conditions are equalities and are
     * connected with an AND expression.
     *
     * @param array $params     The parameters array passed by the user.
     * 
     * @return string|null      The where clause of the SQL query.
     */
    public function simpleWhereClause(array $params)
    {
        foreach ($this->sets->boundParams as $bound) {
            unset($params[$bound]);
        }

        if (is_array($params) === false || empty($params) === true) {
            return null;
        }

        $sql = ' WHERE ';

        // Traverse the parameter array and set up the where clause for each one.
        $i = 0;
        foreach ($params as $key => $param) {
            $sql = $this->setUpWhereClause($sql, $i, $params, $key);
            $i++;
        }

        return $sql;
    }

    /**
     * Set up the where clause of the query for each parameter passed
     *
     * @param string  $query    The SQL query to be executed.
     * @param integer $i        A counter of the iterations of the parameters array.
     * @param array   $params   The parameters array passed by the user.
     * @param string  $key      The key of the traversed value from the parameters array. If the value is not an array
     *                          then the key is the parameter name.
     *
     * @return $string $sql     The SQL query containing the where clause.
     */
    private function setUpWhereClause(string $query, int $i, array $params, string $key)
    {
        $condition = $this->setUpCondition($params, $i);

        if (isset($params['condition']) === true) {
            $size = (count($params) - 2);
        } else {
            $size = (count($params) - 1);
        }

        if (is_array($params[$key]) === true && is_numeric($key) === true) {
            $op = '';
            foreach ($params[$key] as $field => $param) {
                if ($field === 'operator') {
                    $op = $param;
                    continue;
                } else if ($op === '') {
                    $op = '=';
                }

                if ($i < $size) {
                    $query .= $field.' '.$op.' ? '.$condition.' ';
                } else {
                    $query .= $field.' '.$op.' ?';
                }
            }
        } else {
            if ($params[$key] && $key !== 'condition') {
                if ($i < $size) {
                    $query .= $key.' = ? '.$condition.' ';
                } else {
                    $query .= $key.' = ?';
                }
            }
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
    private function setUpCondition(array $params, int $i)
    {
        if (empty($params['condition'][$i]) === false) {
              $condition = $params['condition'][$i];
        } else {
            $condition = 'AND';
        }

        return $condition;
    }


    /**
     * Setup the options of the query
     *
     * @param string $query     The query string to be parsed for available options.
     * @param array  $params    The parameters array passed by the user.
     *
     * @return string $query    The query including the options e.g. (LIMIT, ORDER BY).
     *
     * @throws OptionsException Option does not exist in options set.
     */
    public function queryOptions(string $query, array $params)
    {
        if (isset($params['options']) === true && is_array($params['options']) === true) {
            if ($this->sets->checkOptions($params['options']) === true) {
                foreach ($params['options'] as $option => $value) {
                    $option = preg_replace('/order/', 'order by', $option);
                    $query .= ' '.$option.' '.$value;
                }
            } else {
                throw new OptionsException('Option is not correct');
            }
        }

        return $query;
    }
}