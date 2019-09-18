<?php

namespace EasySQL\Entities;

use EasySQL\Query\DB;

trait Relationships
{
    /**
     * Create default key pairs for the Owner Of relationship
     * 
     * @param string $local_key
     * @param string $foreign_key
     * 
     * @return array
     */
    private function ownerRelationshipKeys(string $local_key = null, string $foreign_key = null)
    {

        /**
         * Make the assumption that the foreign key is of the 
         * format [foreign table name]_[id]
         * This assumption is only being used when $foreign_key is null
         */
        if ($foreign_key == null) {
            $foreign_key = substr($this->builder->table, 0, -1).'_id';
        }

        if ($local_key == null) {
            $local_key = $this->builder->key;
        }

        return array($local_key, $foreign_key);
    }

    /**
     * Create default key pairs for the Member Of relationship
     * 
     * @param string $table
     * @param string $local_key
     * @param string $foreign_key
     * 
     * @return array
     */
    private function memberRelationshipKeys(string $table, string $local_key = null, string $foreign_key = null)
    {

        if ($foreign_key == null) {
            $foreign_key = $this->builder->getPrimaryKey($table);
        }

         /**
         * Make the assumption that the local key is of the 
         * format [local table name]_[id]
         * This assumption is only being used when $local_key is null
         */
        if ($local_key == null) {
            $local_key = substr($table, 0, -1).'_id';
        }

        return array($local_key, $foreign_key);
    }

    /**
     * Has One relationship
     *
     * @param string $table
     * @param string $local_key
     * @param string $foreign_key
     *
     * @return object
     */
    public function ownerOfOne(string $table, string $local_key = null, string $foreign_key = null)
    {

        list($local_key, $foreign_key) = (isset($local_key, $foreign)) ? array($local_key, $foreign_key) : $this->ownerRelationshipKeys($local_key, $foreign_key);

        $foreign_key = str_replace("_", "", ucwords($foreign_key, "_"));

        $instance = EntityFactory::factory($this->getNamespace()."\\".$table);

        return $instance->{"filterBy{$foreign_key}"}($this->{$local_key})->first();
    }

    /**
     * Inverse of Has One relationship
     * Named matches because the local key should match the lock
     * which is the foreign key
     * 
     * @param string $table
     * @param string $local_key
     * @param string $foreign_key
     *
     * @return object
     */
    public function memberOfOne(string $table, string $local_key = null, string $foreign_key = null)
    {
 
        list($local_key, $foreign_key) = (isset($local_key, $foreign)) ? array($local_key, $foreign_key) : $this->memberRelationshipKeys($table, $local_key, $foreign_key);

        $instance = EntityFactory::factory($this->getNamespace()."\\".$table);

        return $instance->{"filterBy{$foreign_key}"}($this->{$local_key})->first();
    }

    /**
     * Has Many relationship
     *
     * @param string $table
     * @param string $local_key
     * @param string $foreign_key
     *
     * @return array object[]
     */
    public function ownerOfMany(string $table, string $local_key = null, string $foreign_key = null)
    {
 
        list($local_key, $foreign_key) = (isset($local_key, $foreign)) ? array($local_key, $foreign_key) : $this->ownerRelationshipKeys($local_key, $foreign_key);
 
        $instance = EntityFactory::factory($this->getNamespace()."\\".$table);

        return $instance->{"filterBy{$foreign_key}"}($this->{$local_key})->all();
    }

    /**
     * Inverse of Has Many relationship
     * 
     * @param string $table
     * @param string $local_key
     * @param string $foreign_key
     * 
     * @return array object[]
     */
    public function memberOfMany(string $table, string $local_key = null, string $foreign_key = null)
    {
        list($local_key, $foreign_key) = (isset($local_key, $foreign)) ? array($local_key, $foreign_key) : $this->memberRelationshipKeys($table, $local_key, $foreign_key);

        $instance = EntityFactory::factory($this->getNamespace()."\\".$table);

        return $instance->{"filterBy{$foreign_key}"}($this->{$local_key})->all();
    }

    /**
     * Many to Many relationship
     * 
     * @param string $table
     * @param string $local_key
     * @param string $foreign_key
     * @param string $pivot
     * 
     * @return array object[]
     */
    public function membersHaveMany(string $table, string $local_key = null, string $foreign_key = null, string $pivot = null)
    {
        $pivot = ($pivot == null) ? lcfirst($table).'_'.lcfirst($this->builder->table) : $pivot;

        list($local, $first_pivot_column) = $this->ownerRelationshipKeys($local_key, $foreign_key);
        list($second_pivot_column, $foreign) = $this->memberRelationshipKeys($table, $local_key, $foreign_key);

        $instance = EntityFactory::factory($this->getNamespace()."\\".$table);

        $subquery = DB::table($pivot)->select($second_pivot_column)->where($first_pivot_column, '=', $this->{$local});

        return $instance->in($foreign, $subquery)->all();
    }
}
