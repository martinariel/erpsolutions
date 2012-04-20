<?php
	require_once ('global.php');
	
	$tran_id = $_GET['id']+0;
	$tran_id = cls_sql::numeroSQL($tran_id);
	$tran    = new cls_transaction ( $db, $tran_id);
	
	iniciarHTML($pagina);
	if ($_GET[modo] == '1') {
		echo "<span style=color:green><b>Ha sido enviado con éxito<br></b></span>";
		echo "<b>Código de transacción:</b>&nbsp;$tran_id<br>";
		$tran->detalle();
		button('Imprimir', "print()" );
		
			iniciarForm ('frmPrint','print.php', 'POST');	
			hidden ('t_id_print',$tran_id);
			cerrarForm();
	
	}else
	{
		echo "<span style=color:red><b>Ha ocurrido un error<br></b></span>";
	}

	cerrarHTML();
	
?>