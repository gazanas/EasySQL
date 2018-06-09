<?php

namespace Src\API;

class RequiredException extends \Exception
{


    public function __construct($message)
    {
        parent::__construct($message);
    }
}
