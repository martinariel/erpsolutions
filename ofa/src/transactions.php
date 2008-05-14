<?php

	require ('global.php');
	
	$tran = new cls_transaction($db);
	
	iniciarHtml ($pagina);
	
	iniciarForm ('frmPrint','print.php', 'POST');	
	hidden ('t_id_print',0);
	cerrarForm();
	
	$tran->tabla();

	iniciarForm ('frmTransaction','action_transaction.php', 'POST');	
	hidden ('modo');
	hidden ('t_id');
	cerrarForm();
	
	
	cerrarHtml();
?>