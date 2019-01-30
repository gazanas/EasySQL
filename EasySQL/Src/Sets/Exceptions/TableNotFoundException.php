<?php

namespace EasySQL\Src\Sets\Exceptions;

class TableNotFoundException extends \InvalidArgumentException
{
    protected $message;
    
    public function __construct(string $table)
    {
        $this->message = 'Table '.$table.' was not found.';
    }
}
