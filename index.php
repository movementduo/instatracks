<?php

	# instatracks
	# presentation layer
	# james@movement.co.uk / july 2017
	
	session_start();
	
	require_once('config.php');
	require_once('engine/database.php');
	require_once('engine/component.php');
	require_once('engine/template.php');
	require_once('engine/misc.php');

	$db = PDOManager::getInstance();
	
	$request = false;
	
	if(array_key_exists('SCRIPT_NAME',$_SERVER)) {
		$request = explode('/',trim($_SERVER['SCRIPT_NAME'],'/'));
	}
	
	if(!$request || empty($request[0])) {
		$request = ['Home'];
	}

die('<pre>'.var_export($request,true));	
	$component = "{$request[0]}_Page";
	
	$filename = COMPONENTS.strtolower($component).'.php';
	if(!file_exists($filename)) {
		die('Fatal: Component '.$filename.' missing');
	}

	require_once($filename);
	
	if(!class_exists($component)) {
		die('Fatal: Class '.$component.' missing');
	}
	
	$instance = new $component($db);
	$instance->args = $request;
	
	if(method_exists($instance,'render')) {
		$body = $instance->render();
		$layout = $instance->tpl->set('body',$body);
		echo $instance->tpl->render('_layout');
	}

	exit;
