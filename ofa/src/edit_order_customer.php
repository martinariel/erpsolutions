<?php

	/*
	
	Formulario de seleccion de productos para el cliente seleccionado.
	
	Primero valida que el cliente pertenezca al salesrep (usuario)
	
	@author Martin Fernandez
	
	*/

	require ('global.php');
	
	iniciarHtml ($pagina);
	addJs('js/edit_order.js');
	
	$customer_id   = $_GET [ 'customer_id'   ] + 0;
	$list_id 	   = $_GET [ 'list_id'       ] + 0;
	$term_id	   = $_GET [ 'term_id'       ] + 0;
	$pay_term_id   = $_GET [ 'pay_term_id'   ] + 0;
	$order_type_id = $_GET [ 'order_type_id' ] + 0;
	
	$customer   = new cls_custom_customer ( $db , $customer_id   );	
	$list 	    = new cls_list_price      ( $db , $list_id       );
	$terms	    = new cls_terms           ( $db , $term_id       );
	$pay_terms  = new cls_pay_terms       ( $db , $pay_term_id   );
	$order_type = new cls_order_type      ( $db , $order_type_id );
	
	//el salesrep_id del usuario es igual al salesrep_id del cliente
	if ( $customer->validate($user) ) 
	{
		
		//detalle html del cliente, razon social, direccion, etc
		iniciarForm ('frmAdd',cls_page::get_filename(), 'GET');
		addDiv('linea','','');
		$customer->html_detail();
		$customer->comboSelectorDireccion($address_id);

		hidden ( 'list_id'       , $list->get_detail(list_header_id) );
		hidden ( 'customer_id'   , $customer->get_id()   );

		addDiv('linea','','');
		cerrarForm();
		//-----------------------------------------------------
		
		
		// ******************* Formulario de lista de precios *********************
		if ($customer->get_detail (price_list_id) == 0 ) 
		{
			iniciarForm ('frmList',cls_page::get_filename() , 'GET');

			hidden ( 'customer_id'   , $customer->get_id() );
			hidden ( 'address_id'    , $customer->get_detail(address_id) );

			$list->comboSelector('frmList'); //Combo selector de listas de precios
			cerrarForm();
		}
		else 
		{
			echo '<b>Lista de precios:&nbsp;</b> ' ; $list->titulo();
		}
		//********************************************************************

		$products = new cls_product_container($db,$list);
		$products->load($list->productos_existentes() , false ); //cargo los productos de la lista de precios

		iniciarForm ('frmProducto',cls_page::get_filename() , 'GET' , '' , true);
		ajaxComboBoxNoCache ('xml_products.php','combo_producto','id_producto',"<b>Producto:</b>", 0 , 300 , 'Agregar' , 'agregar_producto()');
		cerrarForm(true);
		
		// ******************* Formulario de edicion de la orden  *********************
		iniciarForm ('frmEdit','save_order_customer.php');

		hidden ( 'list_id'       , $list->get_detail(list_header_id) );
		hidden ( 'address_id'    , $customer->get_detail(address_id) );
		hidden ( 'customer_id'   , $customer->get_id()   );
		
		
		?>
		<script language="javascript">

		var productos = <?php echo $products->json(); ?>;
		var idxProductos = [];

		for ( var i = 0 ; i < productos.length ; i++ )
		{
			idxProductos[productos[i].Id] = i;
		}
		</script>
		<?php

		$products->tablaProductos(); //Muestro la tabla de edicion de la orden

		echo '<br><table bgColor=#000000 cellspacing=1 cellpadding=2 width=700>';

		echo '<tr><td width=150><b>Tipo de Pedido</b></td><td>';
		$order_type->combo();	
		echo '</td></tr>';
		
		///Numero de pedido

		echo '<tr><td><b>Número de Pedido</b></td><td>';
		comboBoxFromArray ( $user->getNumerosPedido() , "txt_numero_pedido" , "txt_numero_pedido" );
		echo '</td></tr>';

		
		if ($customer->get_detail (price_list_id) != 0 ) 
		{
			echo '<tr><td><b>Lista de precios</b></td><td>';
			$list->titulo();
			echo '</td></tr>';
		}
		
		echo '<tr><td width=150><b>Términos de pago</b></td><td>';
		if ($customer->get_detail (payment_term_id) == 0 ) 
		{
			$terms->comboTerms();	
		}
		else
		{
			$terms->titulo();
		}
		
		echo '</td></tr>';
		
		echo '<tr><td width=150><b>Condicion de pago</b></td><td>';
		
		$pay_terms->combo();	
		
		echo '</td></tr>';
		
		echo '<tr><td valign=top><b>Observaciones</b></td><td>';
		textArea('','observaciones','',false, 'width:100%;height:100px;border:0px');
		echo '</td></tr></table>';
		
		cerrarForm();
		//********************************************************************
		
		button ('<< Cancelar',"window.location='appmain.php'");
		button ('Aceptar >>','enviarOrden()');
		
	}
	else 
	{
		echo '<br>El cliente seleccionado no corresponde al vendedor<br>';
	}

	
	cerrarHtml ();
?>