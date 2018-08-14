<?php

function easy_sql($set, $action, array $params = null){
    $database = new EasySQL\Src\API\Connection();
    $db = $database->getDB();

	$action = strtolower($action);
	$api = new EasySQL\Src\API\API($db);
	try {
		return $api->_easy_sql($set, $action, $params);
	} catch(Exception $e) {
		print($e->getMessage());
	} catch (PDOException $e) {
        print('Prepare failed: '.$e->getMessage());
        return false;
    }
}

?>