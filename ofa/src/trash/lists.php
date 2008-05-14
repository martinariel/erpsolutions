<?php

	/*
	Abm tabla list_price
	
	@author Martin Fernandez
	
	*/
	
	require ('global.php');
	
	iniciarHtml($pagina);
	
	$list = new cls_list_price($db);
	
	$abm = new cls_abm ($list);
	cerrarHtml();
?> 