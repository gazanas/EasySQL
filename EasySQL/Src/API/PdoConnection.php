<?php

namespace EasySQL\Src\API;

class PdoConnection extends Connection
{

    public function connect()
    {
        $config = $this->getDatabaseConfig();
        if (!isset($config[5])) {
            $config[5] = null;
        }
        // Connect to database or throw error message.
        $this->db = new \PDO($config[1].':host='.$config[2].';dbname='.$config[4], $config[3], $config[5]);
        $this->db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $this->db;
    }
}
