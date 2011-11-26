<?php

	ini_set ( 'max_execution_time', 7000);
	require ( "global.php" );

	$user_id  = $_GET['user'];
	$password = $_GET['pwd'];
	
	$user->_logout();
	$user->_checkLogin( $user_id , $password );

	echo ( logged() ) ? 'auth_ok' : 'auth_failed'; 

?>