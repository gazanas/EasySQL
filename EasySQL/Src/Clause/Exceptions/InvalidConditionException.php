<?php

namespace EasySQL\Src\Clause\Exceptions;

class InvalidConditionException extends \InvalidArgumentException
{
    protected $message;
    
    public function __construct(string $condition)
    {
        $this->message = 'Condition '.$condition.' is not valid.';
    }
}
