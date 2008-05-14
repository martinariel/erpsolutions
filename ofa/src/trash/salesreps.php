<?php
	/*
	
	Abm tabla salesreps
	
	@author Martin Fernandez
	
	*/

	require ('global.php');
	
	iniciarHtml($pagina);
	
	$sale = new cls_salesrep($db);
	
	$abm = new cls_abm ($sale);
	cerrarHtml();
?> 