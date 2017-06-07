<?php

# config values

$cfg = array(
	'database' => array(
		'driver' => 'mysql',
		'host' =>'localhost',
		'port' => '3306',
		'user' => 'root',
		'password' => 'm0bilem3',
		'schema' => 'instratracks',
	),
);

date_default_timezone_set('Europe/London') ;
putenv('GOOGLE_APPLICATION_CREDENTIALS=creds/google.json');

