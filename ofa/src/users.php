<?php

	/*
	
	Abm tabla users
	
	@author Martin Fernandez
	*/

	require ('global.php');
	
	iniciarHtml($pagina);
	$abm = new cls_abm ($user);
	$abm->hideColumn ("Password");
	$abm->modeSelector();
	cerrarHtml();
?> 