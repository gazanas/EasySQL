<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once(dirname(__DIR__, 2).'/vendor/autoload.php');

$template = new \Mustache_Engine(
	array(
		'partials' => array(
				'head' => file_get_contents(__DIR__.'/Partials/head.mustache')
			)
	));

?>
