<?php

namespace Src\Data;

use Src\Data\Sets as Sets;

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
     * @param string $query  The sql query
     * @param array  $params The parameters passed by the users
     *
     * @return string $query The finished query to be executed
     */
    public function setUpQuery(string $query, array $params)
    {
        $query .= $this->simpleWhereClause($params);
        $query  = $this->queryOptions($query, $params);

        return $query;
    }


    /**
     * Simple Where Clause formats an array that consists of parameters
     * in a where clause where all conditions are equalities and are
     * connected with an AND expression.
     *
     * @param array $params The parameters passed by the user call.
     *
     * @return string|void The where clause of the SQL query to call.
     */
    public function simpleWhereClause(array $params)
    {
        foreach ($this->sets->boundParams as $bound) {
            unset($params[$bound]);
        }

        if (is_array($params) === false || isset($params) === false || empty($params) === true) {
            return;
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
     * @param string  $query  The SQL query.
     * @param integer $i      The index of the param element in the parameters array.
     * @param array   $params The parameters array.
     * @param string  $key    The key of the traversed value from the params array.
     *
     * @return $string $sql The SQL query containing the where clause.
     */
    private function setUpWhereClause(string $query, int $i, array $params, string $key)
    {
        $condition = $this->setUpCondition($params, $i);

        if (is_array($params[$key]) === true && is_numeric($key) === true) {
            $op = '';
            foreach ($params[$key] as $field => $param) {
                if ($field === 'operator') {
                    $op = $param;
                    continue;
                } elseif ($op === '') {
                    $op = '=';
                }

                if (isset($params['condition']) === true) {
                    $size = (count($params) - 2);
                } else {
                    $size = (count($params) - 1);
                }

                if ($i < $size) {
                    $query .= $field.' '.$op.' ? '.$condition.' ';
                } else {
                    $query .= $field.' '.$op.' ?';
                }
            }
        } else {
            if ($params[$key] && $key !== 'condition') {
                if ($i < (count($params) - 1)) {
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
     * @param array   $params The parameters array passed by the user.
     * @param integer $i      The index of the traversed value of params array.
     *
     * @return string $condition The condition that connects two statements e.g. (AND, OR).
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
     * @param string $query  The query to be parsed for available options.
     * @param array  $params The params passed by the user.
     *
     * @return string $query The query containing the options.
     *
     * @throws OptionsException Option does not exist in options set.
     */
    public function queryOptions(string $query, array $params)
    {
        if (isset($params['options']) === true && is_array($params['options']) === true) {
            try {
                if ($this->sets->checkOptions($params['options']) === true) {
                    foreach ($params['options'] as $option => $value) {
                        $option = preg_replace('/order/', 'order by', $option);
                        $query .= ' '.$option.' '.$value;
                    }
                } else {
                    throw new OptionsException('Option is not correct');
                }
            } catch (OptionsException $e) {
                echo $e->getMessage();
                return;
            }
        }

        return $query;
    }


    /**
     * Gets the parameters array for the insert query and sets up the
     * values clause from the values of this array.
     *
     * @param array $allColumns  All the columns of the table.
     * @param array $restColumns The columns that are not auto completed.
     *
     * @return string $query The insert query.
     */
    public function setUpInsertQuery(array $allColumns, $restColumns, array $autoColumns, $database, $table, $dbinfo)
    {
        $preparedParameter = $this->getRequiredParametersToInsert($allColumns, $restColumns);
        $preparedParameter = $this->setNotPassedParameters($preparedParameter, $database, $table, $dbinfo);

        $i     = 0;
        $query = '';
        foreach ($preparedParameter as $key => $parameter) {
            if ($i == (count($preparedParameter) - 1)) {
                if ($parameter === 'NULL') {
                    if ($this->checkCurrentTimestamp($key, $autoColumns)) {
                        $query .= 'NOW())';
                    } else {
                        $query .= 'NULL)';
                    }
                } else {
                    $query .= '?)';
                }
            } else {
                if ($parameter === 'NULL') {
                    if ($this->checkCurrentTimestamp($key, $autoColumns)) {
                        $query .= 'NOW(),';
                    } else {
                        $query .= 'NULL,';
                    }
                } else {
                    $query .= '?,';
                }
            }

            $i++;
        }

        return $query;
    }

    /**
    * If a parameter is required but is not passed set it to NULL
    *
    * @param array $params The parameters array.
    * @param string $table The name of the table.
    *
    * @return array The complete parameters array.
    */
    public function setNotPassedParameters(array $params, string $database, string $table, $dbinfo)
    {
        $new = array();
        $allColumns = $dbinfo->getColumns($table);
        $columns = $dbinfo->getNullableColumns($database, $table);
        foreach ($columns as $column) {
            if (isset($params[$column]) === false || empty($params[$column])) {
                $params[$column] = 'NULL';
            }
        }

        foreach($allColumns as $key => $column) {
            $new[$column] = $params[$column];

        }

        return $new;
    }

    /**
    * Check if a parameter is of current_timestamp type.
    *
    * @param string $field The column name.
    * @param array $autoColumns The array of the auto completed columns.
    *
    * @return boolean
    */
    public function checkCurrentTimestamp(string $field, array $autoColumns)
    {
        foreach ($autoColumns as $column) {
            if ($column['name'] == $field && $column['type'] == 'current_timestamp') {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets all the columns passed by the user for the insert value clause, checks if those that are missing
     * are auto_completed or nullable and gives them a value of NULL (string). Finally sets up the parameters array
     * for the insert value clause e.g. array('NULL', '?', '?', '?') say the first column is auto incremented.
     *
     * @param array $allColumns  All the columns from a table.
     * @param array $restColumns The columns that are not auto completed.
     *
     * @return array $preparedParameter An array that contains the prepared values of the insert values() clause.
     */
    private function getRequiredParametersToInsert(array $allColumns, array $restColumns)
    {
        foreach ($allColumns as $key => $column) {
            if (array_key_exists($column, $restColumns) === false) {
                if (empty(array_slice($restColumns, 0, $key)) === true) {
                    $restColumns = ([$column => 'NULL'] + $restColumns);
                } else {
                    $preparedParameter = (array_slice($restColumns, 0, $key) + [$column => 'NULL'] + array_slice($restColumns, $key, (count($restColumns) - 1)));
                }
            } else {
                $preparedParameter[$column] = $restColumns[$column]; 
            }
        }

        return $preparedParameter;
    }
}
