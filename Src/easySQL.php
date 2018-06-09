<?php

function easy_sql($set, $action, array $params = null){
	$action = strtolower($action);
	$api = new Src\API\API();
	return $api->_easy_sql($set, $action, $params);
}

?>