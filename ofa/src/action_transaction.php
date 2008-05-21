<?php
	require ('global.php');
	
	$t_id = $_POST['t_id']+0;
	$modo = $_POST['modo']+0;
	
	$tran = new cls_transaction($db,$t_id);
	
	iniciarHtml ($pagina);
	
	switch ($modo){
		case 1: $tran->transferTransaction(); break;
		case 2: $tran->confirmTransaction();break;
		case 3: $tran->cancelTransaction(); break;
		case 7: $tran->retenerTransaction();break;
	}
	
	echo '<br><a href=transactions.php>[Volver]</a>';
	
	cerrarHtml();
	
?>