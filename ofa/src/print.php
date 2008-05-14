<?php

	require ('global.php');

	$id_tran = explode(",", $_POST['t_id_print'] );
	
	iniciarHTMLsimple($pagina);
	
	$arr = array();
	
	
	foreach ($id_tran as $tranId) {
		$tranId+=0;
		
		if ($tranId != 0 && enArray($tranId, $arr) ) {
			$tran 	 = new cls_transaction ( $db, $tranId);
			$vendor  = new cls_user ( $db, $tran->get_detail(user_id) );
			
			echo "<b>Código de transacción:</b>&nbsp;$tranId&nbsp;&nbsp;";
			echo "<b>Vendedor:&nbsp;</b>";
			echo $vendor->get_detail('username');
	
			$tran->detalle();
			
			$tran->set_detail("print_count", $tran->get_detail("print_count") + 1);
			$tran->execute_update();
			echo '<br><hr><br>';
			
			array_push ( $arr, $tranId);
		}
		
	}
	
	echo '<script language="javascript">
	window.print();
	setTimeout("volver()", 5000);
	
	function volver() {
		window.self.location = "';
		echo (strpos($_SERVER[HTTP_REFERER],"?"))?$_SERVER[HTTP_REFERER]."&" :$_SERVER[HTTP_REFERER]."?";
		echo 'reload=1";
	}
	</script>';
	cerrarHTMLsimple();
	
	function enArray($clave, $array) {
		$ret = true;
		
		$i = 0;
		
		while( $ret && $i<count($array) ) {
			
			if ($clave == $array[$i]) $ret = false;
			$i++;
		}
		
		return $ret;
	}
?>