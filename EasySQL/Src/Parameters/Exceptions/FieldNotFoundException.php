<?php

namespace EasySQL\Src\Parameters\Exceptions;

class FieldNotFoundException extends \InvalidArgumentException
{
    protected $message;
    
    public function __construct(string $field)
    {
        $this->message = 'Field '.$field.' was not found.';
    }
}
