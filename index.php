<?php

	# instatracks
	# presentation layer
	# james@movement.co.uk / july 2017
	
	require_once('config.php');
	require_once('engine/database.php');
	require_once('engine/component.php');
	require_once('engine/template.php');
	require_once('engine/misc.php');

	$db = PDOManager::getInstance();
	
	$request = false;
	
	if(array_key_exists('request',$_REQUEST)) {
		$request = trim($_REQUEST['request'],'/');
	}
	
	if(!$request) {
		$request = 'Home';
	}
	
	$component = "{$request}_Page";
	
	$filename = COMPONENTS.strtolower($component).'.php';
	if(!file_exists($filename)) {
		die('Fatal: Component '.$filename.' missing');
	}

	require_once($filename);
	
	if(!class_exists($component)) {
		die('Fatal: Class '.$component.' missing');
	}
	
	$instance = new $component($db);
	
	if(method_exists($instance,'render')) {
		$body = $instance->render();
		$layout = $instance->tpl->set('body',$body);
		echo $instance->tpl->render('_layout');
	}
	
	exit;