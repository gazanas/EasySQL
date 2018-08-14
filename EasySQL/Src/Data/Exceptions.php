<?php

namespace EasySQL\Src\Data;

class OptionsException extends \Exception
{


    public function __construct($message)
    {
        parent::__construct($message);
    }
}

class SetException extends \Exception
{


    public function __construct($message)
    {
        parent::__construct($message."\n");
    }
}

class ActionException extends \Exception
{


    public function __construct($message)
    {
        parent::__construct($message);
    }
}
