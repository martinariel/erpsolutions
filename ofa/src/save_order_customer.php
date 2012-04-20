<?php

	require ('global.php');
	
	$tran = new cls_transaction($db);
	
	$customer_id	= $_POST [ 'customer_id'       ];
	$list_id		= $_POST [ 'list_id'           ];
	$term_id 		= $_POST [ 'term_id'           ];
	$pay_term_id    = $_POST [ 'pay_term_id'       ];
	$numero_pedido  = $_POST [ 'txt_numero_pedido' ];
	$order_type     = $_POST [ 'order_type_id'     ];
	
	$customer   = new cls_custom_customer   ( $db , $customer_id, $condiciones);	
	$list 	    = new cls_list_price        ( $db , $list_id);
	$products   = new cls_product_container ( $db , $list);
	$salesrep   = new cls_salesrep          ( $db , $user->get_detail(salesrep_id) );
	$term	    = new cls_terms             ( $db , $term_id);
	$pay_term   = new cls_pay_terms         ( $db , $pay_term_id);
	$order_type = new cls_order_type        ( $db , $order_type_id );

	$products->cargarDeFormulario();
	
	//confirmacion del pedido
	if ( $customer->validate($user) ) 
	{
		$tran->setNumeroPedido ( $numero_pedido );

		$tran->newTransaction  ( $user     , $customer   , $products , 
			                     $list     , $term       , $salesrep ,
			                     $pay_term , $order_type , $_POST['observaciones'], 5 );
	}

?>