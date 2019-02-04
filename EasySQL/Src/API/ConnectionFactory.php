<?php
/**
 * Created by PhpStorm.
 * User: gatas
 * Date: 3/2/2019
 * Time: 10:03 μμ
 */

namespace EasySQL\Src\API;

class ConnectionFactory
{
    public function createPdo()
    {
        return (new PdoConnection())->connect();
    }

    public function createMysqli()
    {
        return (new MysqliConnection())->connect();
    }
}
