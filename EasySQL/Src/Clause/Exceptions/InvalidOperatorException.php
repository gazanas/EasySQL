<?php

namespace EasySQL\Src\Clause\Exceptions;

class InvalidOperatorException extends \InvalidArgumentException
{
    protected $message;
    
    public function __construct(string $operator)
    {
        $this->message = 'Operator '.$operator.' is not valid.';
    }
}
