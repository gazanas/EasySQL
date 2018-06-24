<?php

	require_once('init.php');

	$api = new EasySQL\Src\API\API();

	$config = $api->config;

	$sql = new EasySQL\Src\Data\SQL($config);

	$tables = $api->dbinfo->getTables($config[4]);
	rsort($tables);

	$fields = array();

	/**
	* Set all values of the array to uppercase (for sensitive unique array values)
	* Remove every duplicate values of the array
	* Set the values of the array to a new array so indexing will be from 0-n (n the length of the array)
	*/

	$actions = array_values(array_unique(array_map('strtoupper', $sql->sets->actionSet)));

	$options = array_values(array_unique(array_map('strtoupper', $sql->sets->options)));

	$operators = array_values(array_unique(array_map('strtoupper', $sql->sets->operators)));

	$columns = array();

	foreach($tables as $table) {
		$fields[] = array('entity' => $table, 'values' => $api->dbinfo->getColumns($table));
		
		$auto_table[$table] = $api->dbinfo->getAutoCompleted($table);

	}
	$insert_values = $fields;

	foreach($fields as $key => $field) {
		foreach($field['values'] as $index => $value) {
			foreach($auto_table[$field['entity']] as $auto) {
				if($value == $auto['name']) {
					$insert_values[$key]['auto'][] = $auto['type'];
				} else {
					$insert_values[$key]['auto'][] = '';
				}
			}
		}
		$insert_values[$key]['values'] = array_values($insert_values[$key]['values']);
	}


	print($template->render('{{> head }} '.file_get_contents(__DIR__.'/api.mustache'), array('entities' => $tables, 'actions' => $actions, 'options' => $options, 'operators' => $operators, 'fields' => $fields, 'insert_values' => $insert_values)));
?>
