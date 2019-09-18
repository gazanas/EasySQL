<?php

namespace EasySQL\Entities;

trait Map
{
	/**
     * If the query does not contain a join then just map
     * the values of the resulted array as properties to
     * the corresponding object. If there is a join call the
     * special mapping method for joins.
     *
     * @param array $elements
     *
     * @return array $hydrated
     */
    private function map(array $elements)
    {
        $hydrated = [];

        // Gives info about the entity that called the join method
        $reflector = new \ReflectionClass(get_called_class());

        if (empty($this->builder->join)) {
            /**
             * Traverse each result and create the appropriate object
             */
            foreach ($elements as $element) {
                $model = EntityFactory::factory($reflector->getName());
                /**
                 * Create each object property
                 */
                foreach ($element as $property => $value) {
                    $model->{$property} = $value;
                }
                // Push the object to the results
                array_push($hydrated, $model);
            }
        } else {
            // Special mapping for joined tables
            $hydrated = $this->joinMap($elements, $reflector);
        }

        return $hydrated;
    }

    /**
     * Check if an object has the same primary key as another object
     *
     * @param array $result
     * @param array $hydrated
     * @param string $key
     *
     * @return int
     */
    private function objectExists(array $result, array $hydrated, string $key)
    {

        /**
         * Traverse the results array and check if
         * any existing object shares the same primary key
         * with the object passed
         */
        foreach ($hydrated as $index => $object) {
            if ($object->{$key} == $result[$key]) {
                return $index;
            }
        }

        return -1;
    }

    /**
     * Traverse all the resulted arrays. Check each primary key if the primary key has not
     * yet appeared create a new object containing the properties of this array.
     * For each array traversed if it is found before the joined values merged with
     * the joined values with the appropriate object. If it's the first occurence then just
     * create the joined array.
     *
     * @param array $data
     * @param \Reflector $reflector
     *
     * @return array $hydrated
     */
    private function joinMap(array $data, \Reflector $reflector)
    {
        $hydrated = [];
        
        foreach ($data as $result) {
            // Assume we have to create a new object
            $index = $this->objectExists($result, $hydrated, $this->builder->key);
            
            /**
             * If a new object should be created then create the entity
             * traverse the resulted array and set it's key values pairs
             * as its properties
             */ 
            if ($index == -1) {
                $object = EntityFactory::factory($reflector->getName());                
                
                foreach ($result as $key => $value) {
                    if (in_array($key, $this->builder->getColumns($this->builder->table))) {
                        $object->{$key} = $value;
                    }
                }
                array_push($hydrated, $object);
                $index = count($hydrated) - 1;
            }
            
            /**
             * Traverse the joined tables
             */
            foreach ($this->builder->getJoinedTables() as $table) {

                /**
                 * If the table is a pivot on a many to many relationship
                 * then ignore it
                 */
                if ($this->builder->join[$table]['is_pivot'])
                    continue;

                // If the joined objects array isn't created already create it 
                $hydrated[$index]->{$table} = (!isset($hydrated[$index]->{$table})) ? array() : $hydrated[$index]->{$table};
                
                /**
                 * Create new joined entity
                 */
                $joinedObject = EntityFactory::factory($reflector->getNamespaceName().'\\'.$table);

                /**
                 * Traverse the resulted array and set its key value pairs
                 * as properties of the joined entity
                 */
                foreach ($this->builder->getColumns($table) as $column) {
                    $joinedObject->{$column} = $result['joined_'.$table.'_'.$column];
                }

                // Push the object to the results
                array_push($hydrated[$index]->{$table}, $joinedObject);
            }
        }
               
        return $hydrated;
    }
}