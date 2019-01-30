<?php

namespace EasySQL\Src\Parameters\Exceptions;

class MissingRequiredFieldsException extends \InvalidArgumentException
{
    protected $message;
    
    public function __construct($missing)
    {
        $this->message = preg_replace('/\,$/', ')', 'Missing Required Fields ('.$missing);
    }
}
