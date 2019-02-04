<?php
/**
 * Created by PhpStorm.
 * User: gatas
 * Date: 3/2/2019
 * Time: 10:15 μμ
 */

namespace EasySQL\Src\API;

class MysqliConnection extends Connection
{

    public function connect()
    {
        $config = $this->getDatabaseConfig();
        if (!isset($config[5])) {
            $config[5] = null;
        }

        $this->db = new \mysqli($config[2], $config[3], $config[5], $config[4]);

        return $this->db;
    }
}
