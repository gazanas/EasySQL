<?php

function easy_sql($set, $action, array $params = null){
<<<<<<< HEAD
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
=======
	$action = strtolower($action);
	$api = new EasySQL\Src\API\API();
	return $api->_easy_sql($set, $action, $params);
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
}

?>