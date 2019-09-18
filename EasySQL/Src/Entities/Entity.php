<?php

namespace EasySQL\Entities;

use EasySQL\Query\DB;

class Entity
{

	use Map, Relationships;

	protected $builder;

    public function __construct()
    {
        $class = get_called_class();
        $table = lcfirst((new \ReflectionClass($class))->getShortName());
        $this->builder = DB::table($table)->select("{$table}.*");
    }

    /**
     * Returns an array of object mapped data from the database.
     *
     * @return array object[]
     */
    public function all()
    {
        return $this->map($this->builder->get());
    }

    /**
     * Returns an object containing the data from the database
     * primary key
     *
     * @param type $key
     *
     * @return object
     */
    public function find($key)
    {
        return $this->map($this->builder->where($this->builder->key, '=', $key)->get());
    }

    /**
     * Returns the first object
     *
     * @return object
     */
    public function first()
    {
        $result = $this->map($this->builder->first());
        return (empty($result)) ? [] : $result[0];
    }

    /**
     * Returns the last object
     *
     * @return object
     */
    public function last()
    {
        return $this->map($this->builder->last())[0];
    }
    
    /**
     * Delete a model by its primary key.
     */
    public function delete()
    {
        $this->builder->where($this->builder->key, '=', $this->{$this->builder->key})->delete();
    }

    /**
     * Get all the columns from the joined tables.
     *
     * @param array $tables
     *
     * @return array
     */
    public function joinedColumns(string $table)
    {
        $joinedColumns = array_map(
            function ($column) use ($table) {
                return $table.'.'.$column.' AS joined_'.$table.'_'.$column;
            },
            $this->builder->getColumns($table)
        );
        
        return $joinedColumns;
    }

    public function join(string $table, string $local_column, string $operator, string $joined_column, bool $is_pivot = false)
    {
        /**
         * If method is join to replace the selected columns
         * but add more to them
         */
        if(!$is_pivot) {
            $columns = $this->joinedColumns($table);
            $this->builder->addSelect(...$columns);
        }

        $this->builder->join($table, $local_column, $operator, $joined_column, $is_pivot);
        return $this;
    }
	
	/**
     * Insert/update a model.
     *
     * @return null
     */
    public function save()
    {
        $properties = (new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC);

        if (isset($this->{$this->builder->key})) {
            if (!empty(DB::table($this->builder->table)->select('*')->where($this->builder->key, '=', $this->{$this->builder->key})->first())) {
                foreach ($properties as $property) {
                    DB::table($this->builder->table)->where($this->builder->key, '=', $this->{$this->builder->key})
                        ->update($property->name, $this->{$property->name});
                }
                return;
            }
        }
        
        $values = [];
        foreach ($properties as $property) {
            $values[$property->name] = $this->{$property->name};
        }
        
        $this->builder->insert($values);
    }

    public function __call(string $method, array $arguments)
    {
        /**
         * If an argument is an Entity then get the query builder
         * of this entity
         */
        foreach ($arguments as $index => $argument) {
            if (is_subclass_of($argument, __CLASS__, false)) {
                $arguments[$index] = $argument->builder;
            }
        }

        /**
         * If the method is a filter then apply a where to the builder
         */
        if (preg_match("/^filterBy/", $method)) {
            /**
             * Column names come after "filterBy" part of the method and each one starts with a capital letter
             * if column name is a word then just lowercase it
             * if column name is more than one words concatenated by capital letters then split the words by capital
             * letter, implode the array by "_" and lowercase the result
             */
            $column = strtolower(implode("_", preg_split('/(?=[A-Z])/', substr($method, 8), -1, PREG_SPLIT_NO_EMPTY)));

            /**
             * If an operator is not defined then use equals
             */
            $arguments = (sizeof($arguments) == 2) ? array_merge([$column], $arguments) : array_merge([$column, "="], $arguments);

            $this->builder->where(...$arguments);

            return $this;
        }
        
        $return = $this->builder->{$method}(...$arguments);

        return ($return instanceof \EasySQL\Query\Builder) ? $this : $return;
    }

    public function getNamespace()
    {
        return (new \ReflectionClass(get_called_class()))->getNamespaceName();
    }
}
