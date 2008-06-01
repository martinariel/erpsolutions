<?php

	require("global.php");
	
	$term_id    	= $_REQUEST['term_id'];
	$payterm_id 	= $_REQUEST['pay_term_id'];
	$transaction_id = $_REQUEST['transaction_id'];
	
	$tran = new cls_transaction_modificable($db,$transaction_id);
	
	$header        = new cls_interface_header($db, $tran->get_detail('header_id'));
	$nuevoPayTerms = new cls_pay_terms($db,$payterm_id);
	$nuevoTerms    = new cls_terms($db,$term_id);
	

	$tran->cambiarPayTerms($nuevoPayTerms);
	$tran->cambiarTerms($nuevoTerms);
	

?>
<html>
<body>
Se ha modificado el pedido con exito<br>
<a href=detail_transaction.php?reload=1&id=<?php echo $transaction_id;?>> Volver </a>
</body>
</html>
