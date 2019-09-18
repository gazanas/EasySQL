<?php

namespace EasySQL\Entities;

class EntityFactory
{
    
    /**
     * Create a new Entity object
     * 
     * @param string $entity
     * @param array ...$parameters
     * 
     * @return Entity
     */
    public static function factory(string $entity, ...$parameters)
    {
        $class = ucfirst($entity);
        
        return (new \ReflectionClass($class))->newInstance();
    }
}
