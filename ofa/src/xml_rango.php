<?php

	ini_set ( 'max_execution_time' , 7000 );
	require ( "global.php" );
	

	$user_id = $_GET ['user' ] + 0;
	$desde   = $_GET ['desde'] + 0;
	$hasta   = $_GET ['hasta'] + 0;
	
	$user = new cls_user ( $db , $user_id );
	
	echo $user->validarRango ( $desde , $hasta );
	
?>