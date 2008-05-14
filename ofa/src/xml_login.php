<?php
	/*
	Xml login ajax, login.php
	
	TODO: devolver un xml en vez de texto, ver js/login.js
	
	*/
	ini_set('max_execution_time', 7000);
	require("global.php");

	$user_id  = $_GET['user'];
	$password = $_GET['pwd'];
	
	$user->_logout();
	$user->_checkLogin($user_id,$password);

	if ( logged() ) 
		echo 'auth_ok';
	else 
		echo 'auth_failed';

?>