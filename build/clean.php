<?php

require_once 'Connection.php';

$dbIni = file_get_contents(dirname(__DIR__).'/.env/database/config.ini');

preg_match_all('/.+ =\> .+/', $dbIni, $matches);

foreach ($matches[0] as $index => $match) {
    $index++;
    $matchArray     = explode(' => ', $match);
    $config[$index] = $matchArray[1];
}

$connection = new Build\Connection($config);

$db = $connection->getConnection();

$db->query('SET foreign_key_checks = 0');
$result = $db->query('SHOW TABLES');

$tables = $result->fetchAll();

foreach ($tables as $table) {
    $db->query('DROP TABLE IF EXISTS '.$table[0]);
}

file_put_contents(dirname(__DIR__).'/.env/database/config.ini', '');

$db->query('SET foreign_key_checks = 1');

$result = null;
$db     = null;
