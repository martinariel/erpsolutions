<?php

	/*
	Abm tabla mtl_systems_item_b
	
	@author Martin Fernandez
	*/
	
	require ('global.php');
	
	iniciarHtml($pagina);
	
	$producto = new cls_product($db);
	
	$abm = new cls_abm ($producto);
	cerrarHtml();
?> 