<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace EasySQL\Query;

use EasySQL\Drivers\DAO;
use EasySQL\Query\Syntax\SqlSyntax;

/**
 * Builder class sets up all the components of the query
 * in a clean streamline way and. The two components needed
 * the query string and the parameters of the query are formatted
 * to avoid sql injection attacks and executed.
 *
 * @author gatas
 */
class Builder
{

    use Sets;

    private $dao;
    private $sets;
    private $syntax;

    public $table;
    public $key;
    
    public $select = [];
    
    public $where = [];
    
    public $logicQueue;
    
    public $options = [
        'limit' => null,
        'order' => null
    ];
    
    public $group = [];
    public $having = [];
    
    public $join = [];

    public $union = [];
    
    private $parameters = [];
    public $update;
    
    private $not = [];
    
    public function __construct(DAO $dao, SqlSyntax $syntax)
    {
        $this->dao = $dao;
        $this->syntax = $syntax;
    }

    public function raw(string $query) 
    {
        $this->execute($query);
    }

    /**
     * Set the table to execute the query
     *
     * @param string $table
     *
     * @return $this
     */
    public function table(string $table)
    {
        $this->table = $table;
        $this->key = $this->getPrimaryKey($this->table);

        return $this;
    }

    /**
     * Create a select query.
     *
     * @param string[] ...$columns
     *
     * @return $this
     */
    public function select(string ...$columns)
    {
        $this->select = $columns;

        return $this;
    }

    /**
     * Add columns to the columns already selected
     *
     * @param string[] ...$columns
     *
     * @return $this
     */
    public function addSelect(string ...$columns)
    {
        $this->select = array_merge($this->select, $columns);

        return $this;
    }

    /**
     * Select aggregates of records
     *
     * @param string $column
     * @param string $aggregate
     *
     * @return $this
     */
    public function aggregate(string $column, string $aggregate)
    {
        $this->select = ['aggregate' => $aggregate, 'column' => $column];

        return $this;
    }

    /**
     * Add the syntax of the between condition query to the where array
     *
     * @param string $column
     * @param bool   $not
     * @param string $operator
     * @param type   $value
     * @param string $type
     * @param string $logic
     */
    public function whereBetween(string $column, $not, string $operator, $value, string $type, string $logic = null)
    {
        array_push($this->where, ['type' => $type, 'condition' => ['conjunction' => $logic, 'column' => $column, 'not' => $not, 'operator' => $operator, 'values' => $value]]);
    }

    /**
     * Add the syntax of the in condition query to the where array
     *
     * @param string $column
     * @param bool   $not
     * @param string $operator
     * @param type   $value
     * @param string $type
     * @param string $logic
     */
    public function whereIn(string $column, $not, string $operator, $value, string $type, string $logic = null)
    {
        array_push($this->where, ['type' => $type, 'condition' => ['conjunction' => $logic, 'column' => $column, 'not' => $not, 'operator' => $operator, 'list' => $value]]);
    }

    /**
     * Add the syntax of a basic condition query to the where array
     *
     * @param string $column
     * @param bool   $not
     * @param string $operator
     * @param type   $value
     * @param string $type
     * @param string $logic
     */
    public function whereBasic(string $column, $not, string $operator, $value, string $type, string $logic = null)
    {
        array_push($this->where, ['type' => $type, 'condition' => ['conjunction' => $logic, 'column' => $column, 'not' => $not, 'operator' => $operator, 'value' => $value]]);
    }

    /**
     * Add the syntax of the exists condition query to the where array
     *
     * @param string $column
     * @param bool   $not
     * @param string $operator
     * @param type   $value
     * @param string $type
     * @param string $logic
     */
    public function whereExists(string $column, $not, string $operator, $value, string $type, string $logic = null)
    {
        array_push($this->where, ['type' => $type, 'condition' => ['conjunction' => $logic, 'column' => $column, 'not' => $not, 'operator' => $operator, 'value' => $value]]);
    }

    /**
     * Filter the rows of the query.
     *
     * @param string $column
     * @param string $operator
     * @param string $value | Builder $value | string[] $value | Builder[] $value
     * @param string $type
     *
     * @return $this
     */
    public function where(string $column, string $operator, $value, string $type = null)
    {
        $type = (!isset($type)) ? "basic" : $type;

        /**
         * If the last method call is the "not" method
         * then toggle the not variable to true and remove
         * the call from the call stack.
         */
        $not = (!empty($this->not)) ? array_pop($this->not) : null;

        /**
         * If it is not the first where statement and no and(), or() method
         * was called right before this call then the logic operator will be AND
         * Else pop the first item from the queue of the logic operators.
         */
        if (!empty($this->where) && $this->logicQueue == null) {
            $this->logicQueue = 'and';
        }

        $logic = (empty($this->where)) ? null : $this->logicQueue;

        /**
         * If the value passed is an array traverse each element 
         * Then pass the contents of the array to the parameters and
         * call the syntax method to build the query of the operation.
         */
        if (is_array($value)) {
            /**
             * check if any element of the array is a Builder instance
             *  and unpack its contents.
             */
            $prepared_values = array_map(
                function ($value) {
                    
                    if ($value instanceof Builder) {
                        $this->parameters = array_merge($this->parameters, $value->getParameters());
                        return "(".$this->syntax->selectSyntax(get_object_vars($value)).")";
                    }
                    
                    array_push($this->parameters, $value);
                    return "?";
                },
                $value
            );
            $this->{"where".ucfirst($type)}($column, $not, $operator, $prepared_values, $type, $logic);
           return $this;
        }

        /**
         * If the value is an instance of Builder class
         * unpack its contents and call the syntax method to build
         * the query of the operation and then pass the parameters property
         * of the Builder object to the parameters array.
         */
        if ($value instanceof Builder) {
            $this->{"where".ucfirst($type)}($column, $not, $operator, "(".$this->syntax->selectSyntax(get_object_vars($value)).")", $type, $logic);
            $this->parameters = array_merge($this->parameters, $value->getParameters());
            return $this;
        }

        /**
         * If the value is a scalar variable then call the syntax method
         * to build the query of the operation and pass the value to the parameters.
         */
        $this->whereBasic($column, $not, $operator, "?", $type, $logic);
        array_push($this->parameters, $value);

        $this->logicQueue = null;

        return $this;
    }

    /**
     * Enforce a between condition.
     *
     * @param string $column
     * @param string $lower  | Builder $lower
     * @param string $higher | Builder $higher
     *
     * @return $this
     */
    public function between(string $column, $lower, $higher)
    {
        $condition = 'between';

        $this->where($column, $condition, [$lower, $higher], $condition);
        
        return $this;
    }

    /**
     * Adding an OR condition filter in the where clause.
     *
     * @return $this
     */
    public function or()
    {
        $this->logicQueue = 'or';

        return $this;
    }
    
    /**
     * Adding an AND condition filter in the where clause.
     *
     * @return $this
     */
    public function and()
    {
        $this->logicQueue = 'and';

        return $this;
    }
    
    /**
     * Order the rows of the query.
     *
     * @param string $column
     * @param string $order
     *
     * @return $this
     */
    public function order(string $column, string $order)
    {
        $this->options['order']['column'] =  $column;
        $this->options['order']['type'] = $order;
        
        return $this;
    }

    /**
     * Limit the rows of the query.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->options['limit'] = $limit;

        return $this;
    }

    /**
     * Arrange identical rows into groups.
     *
     * @param string[] ...$columns
     *
     * @return $this
     */
    public function group(string ...$columns)
    {
        $this->group = $columns;
        
        return $this;
    }
    
    /**
     * Filter which group of data (from the group clause) will be returned.
     *
     * @param string $column
     * @param string $operator
     * @param string $value    | Builder $value
     *
     * @return $this
     */
    public function having(string $column, string $operator, $value)
    {
        /**
         * If the value is a Builder object then
         * add its parameters to the parameters array
         * and create the subquery from the syntax class
         */
        if ($value instanceof Builder) {
            $this->parameters = array_merge($this->parameters, $value->getParameters());
            $value = "(".$this->syntax->selectSyntax(get_object_vars($value)).")";
        }
        $this->having = ["column" => $column, "operator" => $operator, "value" => $value];
        
        return $this;
    }
    
    /**
     * Check if a subquery record exists in the table
     *
     * @param Builder $subquery
     *
     * @return $this
     */
    public function exists(Builder $subquery)
    {
        $condition = "exists";

        $this->where('', $condition, $subquery, $condition);
        
        return $this;
    }

    /**
     * Check if record exists in a list of values
     *
     * @param string $column
     * @param array  $list   | Builder $list
     *
     * @return $this
     */
    public function in(string $column, $list)
    {
        $condition = "in";

        $this->where($column, $condition, $list, $condition);

        return $this;
    }
    
    /**
     * Inverse of a condition
     * 
     * @return $this
     */
    public function not()
    {
        array_push($this->not, __FUNCTION__);
        
        return $this;
    }

    /**
     * Perform a union query.
     *
     * @param Builder $query
     *
     * @return $this
     */
    public function union(Builder $subquery)
    {
        array_push($this->union, $this->syntax->selectSyntax($subquery));
        $this->parameters = array_merge($this->parameters, $subquery->getParameters());
        
        return $this;
    }

    /**
     * Perform an inner join query.
     *
     * @param string $table
     * @param string $local_column
     * @param string $operator
     * @param string $joined_column
     *
     * @return $this
     */
    public function join(string $table, string $local_column, string $operator, string $joined_column, bool $is_pivot = false, string $type = 'join')
    {
        $this->join[$table] = ['local_column' => $local_column, 'operator' => $operator, 'joined_column' => $joined_column, 'type' => $type, 'is_pivot' => $is_pivot];
        
        return $this;
    }
    
    /**
     * Perform a left join query.
     *
     * @param string $table
     * @param string $local_column
     * @param string $operator
     * @param string $joined_column
     *
     * @return $this
     */
    public function leftJoin(string $table, string $local_column, string $operator, string $joined_column, bool $is_pivot = false)
    {
        $this->join($table, $local_column, $operator, $joined_column, $is_pivot, 'left join');
        
        return $this;
    }
    
    /**
     * Perform a right join query.
     *
     * @param string $table
     * @param string $local_column
     * @param string $operator
     * @param string $joined_column
     *
     * @return $this
     */
    public function rightJoin(string $table, string $local_column, string $operator, string $joined_column, bool $is_pivot = false)
    {
        $this->join($table, $local_column, $operator, $joined_column, $is_pivot, 'right join');
        
        return $this;
    }
    
    /**
     * Get all the records of the query
     *
     * @return array
     */
    public function get()
    {
        return $this->execute(
            $this->syntax->selectSyntax(get_object_vars($this))
        );
    }

    /**
     * Get only the first record of the query
     *
     * @return array
     */
    public function first()
    {
        return $this->execute(
            $this->syntax->selectSyntax(get_object_vars($this->order($this->key, 'ASC')->limit(1)))
        );
    }

    /**
     * Get only the last record of the query
     *
     * @return array
     */
    public function last()
    {
        return $this->execute(
            $this->syntax->selectSyntax(get_object_vars($this->order($this->key, 'DESC')->limit(1)))
        );
    }

    /**
     * Get single or multiple columns from a row.
     *
     * @param string[] ...$columns
     *
     * @return array
     */
    public function find(string ...$columns)
    {
        return $this->execute(
            $this->syntax->selectSyntax(get_object_vars($this->select(...$columns)))
        );
    }
    
    /**
     * Get the maximum value of a column(s) rows that the query returns.
     *
     * @param string $column
     *
     * @return string
     */
    public function max(string $column)
    {
        return $this->execute(
            $this->syntax->selectSyntax(get_object_vars($this->aggregate($column, __FUNCTION__)))
        )[0][__FUNCTION__];
    }
    
    /**
     * Get the minimum value of a column(s) rows that the query returns.
     *
     * @param string[] ...$columns
     *
     * @return string
     */
    public function min(string $column)
    {
        return $this->execute(
            $this->syntax->selectSyntax(get_object_vars($this->aggregate($column, __FUNCTION__)))
        )[0][__FUNCTION__];
    }
    
    /**
     * Get the average value of the column rows that the query returns.
     *
     * @param string $column
     *
     * @return string
     */
    public function avg(string $column)
    {
        return $this->execute(
            $this->syntax->selectSyntax(get_object_vars($this->aggregate($column, __FUNCTION__)))
        )[0][__FUNCTION__];
    }

    /**
     * Get the count of the records.
     *
     * @param string $column
     *
     * @return string
     */
    public function count(string $column)
    {
        return $this->execute(
            $this->syntax->selectSyntax(get_object_vars($this->aggregate($column, __FUNCTION__)))
        )[0][__FUNCTION__];
    }
    
    /**
     * Get the summation of the column rows that the query returns.
     *
     * @param string $column
     *
     * @return string
     */
    public function sum(string $column)
    {
        return $this->execute(
            $this->syntax->selectSyntax(get_object_vars($this->aggregate($column, __FUNCTION__)))
        )[0][__FUNCTION__];
    }
    
    
    /**
     * Perform an insert query.
     *
     * @param array $values
     */
    public function insert(array $values)
    {
        $this->parameters = $values;

        $this->execute(
            $this->syntax->insertSyntax($this->table, $this->parameters)
        );
    }
    
    /**
     * Perform an update query.
     *
     * @param string $column
     * @param type   $value
     */
    public function update(string $column, $value)
    {
        array_unshift($this->parameters, $value);
        $this->update = $column;
        
        $this->execute(
            $this->syntax->updateSyntax(get_object_vars($this))
        );
    }
    
    /**
     * Perform a delete query.
     */
    public function delete()
    {
        $this->execute(
            $this->syntax->deleteSyntax(get_object_vars($this))
        );
    }
    
    /**
     * Get all the tables that are joined.
     *
     * @return array
     */
    public function getJoinedTables()
    {
        return array_keys($this->join);
    }
    
    /**
     * Get the parameters of the query.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
   
    /**
     * Execute the SQL query.
     *
     * @param string $query
     *
     * @return array
     */
    public function execute(String $query)
    {
        return $this->dao->executeQuery($query, array_values($this->parameters));
    }
}
