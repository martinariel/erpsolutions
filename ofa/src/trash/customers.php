<?php

	/*
	
	Abm tabla ra_customers
	
	@author Martin Fernandez
	
	*/

	require ('global.php');
	
	iniciarHtml($pagina);
	
	$customer= new cls_customer($db);
	
	$abm = new cls_abm ($customer);
	cerrarHtml();
?> 