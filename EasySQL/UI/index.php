<?php

	require_once('init.php');

	$db = new EasySQL\Src\API\Connection();

	$sets = new EasySQL\Src\Data\Sets($db->getDB());

	$tables = $sets->getTables();
	rsort($tables);

	$fields = array();

	/**
	* Set all values of the array to uppercase (for sensitive unique array values)
	* Remove every duplicate values of the array
	* Set the values of the array to a new array so indexing will be from 0-n (n the length of the array)
	*/

	$actions = array_values(array_unique(array_map('strtoupper', $sets->getActionSet())));

	$options = array_values(array_unique(array_map('strtoupper', $sets->getOptionSet())));

	$operators = array_values(array_unique(array_map('strtoupper', $sets->getOperatorSet())));

	$columns = array();

	foreach($tables as $table)
		$fields[] = array('entity' => $table, 'values' => $sets->getColumns($table));
		
	$insert_values = $fields;

	foreach($fields as $key => $insert_value) {
		$i = 0;
		foreach($insert_value['values'] as $value) {
			$flag = false;
			foreach($sets->getAutoCompleted($insert_value['entity']) as $autos) {
				if($value == $autos['name']) {
					$flag = true;
					$type = $autos['type'];
					break;
				}
			}
			if($flag == true) {
				$insert_values[$key]['auto_values'][$i] = $type; 
			} else {
				$insert_values[$key]['auto_values'][$i] = ''; 
			}
			$i++;
		}
	}

	print($template->render('{{> head }} '.file_get_contents(__DIR__.'/api.mustache'), array('entities' => $tables, 'actions' => $actions, 'options' => $options, 'operators' => $operators, 'fields' => $fields, 'insert_values' => $insert_values)));
?>
