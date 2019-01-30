<?php

namespace EasySQL\Src\Parameters;

use EasySQL\Src\Sets\Sets;
use EasySQL\Src\Parameters\Exceptions\FieldNotFoundException;

abstract class Parameters
{
    
    protected $sets;
    
    /**
     * Intializes the parameters object.
     *
     * @param Sets $sets The sets object.
     */
    public function __construct(Sets $sets)
    {
        $this->sets = $sets;
    }
    
    /**
     * Checks if the field exists.
     *
     * @param mixed  $field The field name.
     * @param string $table The table name.
     */
    public function checkFieldExists(string $field, string $table)
    {
        if (!in_array($field, $this->sets->getColumns($table), true)) {
            throw new FieldNotFoundException($field);
        }
    }
}
