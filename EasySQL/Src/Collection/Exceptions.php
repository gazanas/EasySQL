<?php

namespace EasySQL\Src\Collection;

class CollectionException extends \Exception
{


    public function __construct($message)
    {
        parent::__construct($message."\n");
    }
}
