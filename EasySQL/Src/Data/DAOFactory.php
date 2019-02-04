<?php
/**
 * Created by PhpStorm.
 * User: gatas
 * Date: 3/2/2019
 * Time: 10:12 μμ
 */

namespace EasySQL\Src\Data;

class DAOFactory
{

    public function createPdoDAO(\PDO $db)
    {
        return new PdoDAO($db);
    }

    public function createMysqliDAO(\mysqli $db)
    {
        return new MysqliDAO($db);
    }
}
