<?php

require_once('init.php');

$command = trim($_POST['command']);

/**
* If a user tries to execute a different function than the api, kill the execution
*/
if(!preg_match('/^easy_sql\(\'.+\', \'.+\', array\((.+|)\)\);$/', $command)) {
	die($command.' not a valid api call.');
}

print('<pre>'.print_r(eval('return '.htmlspecialchars_decode($command)), true).'</pre>');

?>