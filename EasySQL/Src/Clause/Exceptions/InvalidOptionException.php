<?php

namespace EasySQL\Src\Clause\Exceptions;

class InvalidOptionException extends \InvalidArgumentException
{
    
    protected $message;
    
    public function __construct($message)
    {
        $this->message = $message;
    }
}