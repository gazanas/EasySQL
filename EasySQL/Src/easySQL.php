<?php

function easy_sql($set, $action, $params = null) {

	$action = strtolower($action);
	try{
		// Connect to the database
	    $database = new EasySQL\Src\API\Connection();
	    $db = $database->getDB();
	    
	    $sets = new EasySQL\Src\Sets\Sets($db);
	    
	    $sql = new EasySQL\Src\Data\SQL($db);
		
		$api = new EasySQL\Src\API\API($sets);
		
		return $api->_easy_sql($sql, $set, $action, $params);
	} catch(\Exception $e) {
        print('Error on file '.$e->getTrace()[count($e->getTrace())-1]['file'].' on line '.$e->getTrace()[count($e->getTrace())-1]['line'].'<br/>'.$e->getMessage());    
        return false;       
    }
}

?>