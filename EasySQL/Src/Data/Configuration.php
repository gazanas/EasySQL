<?php

namespace EasySQL\Src\Data;

class Configuration
{
    
     /**
     * Reads the database configuration from the config.ini file
     *
     * @return array $config Database Configuration.
     **/
    public function getDatabaseConfig()
    {
        $config = array();
        $dbIni = file_get_contents(dirname(__DIR__, 3).'/.env/database/config.ini');

        preg_match_all('/.+ =\> .+/', $dbIni, $matches);

        foreach ($matches[0] as $index => $match) {
            $index++;
            $matchArray     = explode(' => ', $match);
            $config[$index] = $matchArray[1];
        }

        return $config;
    }//end getDatabaseConfig()
}
