<?php
	/*
	xml para el combo ajax en appmain.php
	@author Martin Fernandez
	*/

	ini_set('max_execution_time', 7000);
	require('global.php');

	header('Content-Type: text/xml');


	echo '<'.'?xml version="1.0" encoding="utf-8" ?'.'>';

	$user_id  = $_SESSION['uid']+0;
	$busqueda = $_GET['mask'];
	$pos      = $_GET['pos'];

	
	$customer = new cls_customer ($db);
	$customer->printXml ($busqueda,$pos,$user_id);
?>