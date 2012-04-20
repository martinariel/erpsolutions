<?php
	require ('global.php');
	
	$tabla  = $_GET['tabla'];
	$dest	= $_GET['dest'];
	$valor  = $_GET['valor'];

	$config = new cls_lines_config ($db);
	$config->comboFld($tabla,$valor,$dest);
?>