<?php

	require ( "global.php" );

	$sql      = $_REQUEST ['sql'];
	$checksum = $_REQUEST ['checksum'];

	// TODO: Validar el checksum

	$db->ejecutar_sql ( $sql );

?>