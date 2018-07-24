<?php

require_once 'Connection.php';
require_once 'SQLBuilder.php';

$it = new DirectoryIterator('Schemata/');

$connection = new Build\Connection($argv);

$db = $connection->getConnection();

$xmlTosql = new Build\SQLBuilder($db);
foreach ($it as $file) {
    if ($file->isDot() === false) {
        $xmlTosql->populateFromXML($file->getPathname());
    }
}
