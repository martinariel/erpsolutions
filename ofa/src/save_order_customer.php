<?php
	require ('global.php');
	$tran = new cls_transaction($db);

	$customer_id	= $_POST [ 'customer_id'       ];
	$list_id		= $_POST [ 'list_id'           ];
	$term_id 		= $_POST [ 'term_id'           ];
	$pay_term_id    = $_POST [ 'pay_term_id'       ];
	$numero_pedido  = $_POST [ 'txt_numero_pedido' ];
	$order_type     = $_POST [ 'order_type_id'     ];
	$cpv2_id        = $_POST [ 'cpv2_id'           ];
	$cpv3_id        = $_POST [ 'cpv3_id'           ];

	$customer   = new cls_custom_customer   ( $db , $customer_id , $condiciones );	
	$list 	    = new cls_list_price        ( $db , $list_id       );
	$products   = new cls_product_container ( $db , $list          );
	$salesrep   = new cls_salesrep          ( $db , $user->get_detail ( salesrep_id ) );
	$term	    = new cls_terms             ( $db , $term_id       );
	$pay_term   = new cls_pay_terms         ( $db , $pay_term_id   );
	$order_type = new cls_order_type        ( $db , $order_type_id );
	$cpv2       = new cls_cpv2              ( $db , $cpv2_id       );
	$cpv3       = new cls_cpv3              ( $db , $cpv3_id       );

	$products->cargarDeFormulario();

	//confirmacion del pedido

	if ( $customer->validate ( $user ) ) 
	{
		$tran->setNumeroPedido ( $numero_pedido );
		$tran->newTransaction  ( $user     , $customer   , $products , 
			                     $list     , $term       , $salesrep ,
			                     $pay_term , $order_type , $cpv2 , $cpv3 ,
			                     $_POST['observaciones'], 5 );
	}
?>