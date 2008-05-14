<?php

	/*
	
	Interfaz de configuracion de la aplicacion:
		Configuracion de la interfaz.
		Configuracion de db oracle.
		
		TODO: clase de interfaz, archivo o tabla de configuracion
	@author Martin Fernandez
	
	*/
	
	require ('global.php');
	
	iniciarHtml($pagina);
	
	echo '<b>Configuración de oe_headers_iface_all:</b><br><br>';
	
	$header_conf = new cls_header_config ($db);
	$header_conf->configurar();
	
	cerrarHtml();
?> 