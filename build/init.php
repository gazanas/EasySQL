<?php

require_once 'connectSingleton.php';
require_once 'SQLBuilder.php';

$it = new DirectoryIterator('Schemata/');

$db = Build\connectSingleton::getConnection($argv);

$xmlTosql = new Build\SQLBuilder($db);
foreach ($it as $file) {
    if ($file->isDot() === false) {
        $xmlTosql->populateFromXML($file->getPathname());
    }
}
