<?php

	ini_set('max_execution_time', 7000);
	require("global.php");
	
	$tran = new cls_transaction($db);
	
	$tran->setNumeroPedido($_GET['param']);
	
	echo ($tran->validarNumeroPedido())?'OK':'NOT OK';
	
?>