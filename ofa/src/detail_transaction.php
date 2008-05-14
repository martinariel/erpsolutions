<?php
	require_once ('global.php');
	
	$tran_id = $_GET['id'];
	$tran_id = cls_sql::numeroSQL($tran_id);
	$tran    = new cls_transaction ( $db, $tran_id);
	$vendor  = new cls_user ( $db, $tran->get_detail(user_id) );
	
	iniciarHTMLsimple($pagina);
	echo "<b>Código de transacción:</b>&nbsp;$tran_id &nbsp;";
	echo "<b>Vendedor:&nbsp;</b>";
	echo $vendor->get_detail('username');
	
	$tran->detalle();
	
	echo '<br>';
	button('Imprimir',"$('frmPrint').submit()");
	echo '&nbsp;';
	button('Cerrar','window.self.close()');
	
	iniciarForm ('frmPrint','print.php', 'POST');	
	hidden ('t_id_print',$tran_id);
	cerrarForm();
	
	if (isset($_GET[reload]) && $_GET[reload] = "1") {
		//recargo la pagina de busqueda
		?>
		<script language=javascript>
			if (window.opener) {
				window.opener.location = window.opener.location;
			}
		</script>
		<?php
	}
	
	cerrarHTMLsimple();
	
?>