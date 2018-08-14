<?php

	require_once('init.php');

<<<<<<< HEAD
	$db = new EasySQL\Src\API\Connection();

	$sets = new EasySQL\Src\Data\Sets($db->getDB());

	$tables = $sets->getTables();
=======
	$api = new EasySQL\Src\API\API();

	$sets = new EasySQL\Src\Data\Sets();

	$tables = $api->dbinfo->getTables();
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
	rsort($tables);

	$fields = array();

	/**
	* Set all values of the array to uppercase (for sensitive unique array values)
	* Remove every duplicate values of the array
	* Set the values of the array to a new array so indexing will be from 0-n (n the length of the array)
	*/

<<<<<<< HEAD
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

=======
	$actions = array_values(array_unique(array_map('strtoupper', $sets->actionSet)));

	$options = array_values(array_unique(array_map('strtoupper', $sets->options)));

	$operators = array_values(array_unique(array_map('strtoupper', $sets->operators)));

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


>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
	print($template->render('{{> head }} '.file_get_contents(__DIR__.'/api.mustache'), array('entities' => $tables, 'actions' => $actions, 'options' => $options, 'operators' => $operators, 'fields' => $fields, 'insert_values' => $insert_values)));
?>
