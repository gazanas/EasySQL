<?php

require_once(dirname(__DIR__, 2).'/vendor/autoload.php');

$template = new \Mustache_Engine(
	array(
		'partials' => array(
				'head' => file_get_contents(__DIR__.'/Partials/head.mustache')
			)
	));

?>
