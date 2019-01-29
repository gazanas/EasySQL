<?php

namespace EasySQL\Src\Clause;

use EasySQL\Src\Parameters\Exceptions\FieldNotFoundException;
use EasySQL\Src\Sets\Sets;

class GroupClause implements ClauseInterface 
{
    private $sets;
    private $table;
    
    public function __construct(string $table, Sets $sets)
    {
        $this->sets = $sets;    
        $this->table = $table;
    }
    
    public function prepareClause(array $params)
    {
            $this->checkFieldExists($params[0]);
            return ' GROUP BY '.$params[0];
    }
    
    /**
     * Checks if the field exists.
     *
     * @param mixed  $field The field name.
     * @param string $table The table name.
     */
    public function checkFieldExists(string $field)
    {
        if(!in_array($field, $this->sets->getColumns($this->table), true)) {
            throw new FieldNotFoundException($field);
        }
    }
}