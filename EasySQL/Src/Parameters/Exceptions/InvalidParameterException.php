<?php

namespace EasySQL\Src\Parameters\Exceptions;

class InvalidParameterException extends \InvalidArgumentException
{
    protected $message;
    
    public function __construct($parameter)
    {
        $this->message = 'Parameter: '.preg_replace('/\,\s*\)$/', ')', $parameter).' is not valid.';
    }
}
