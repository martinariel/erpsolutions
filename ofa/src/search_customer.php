<?php

	/*
	Busqueda avanzada en la tabla ra_customers
	
	@author Martin Fernandez
	
	*/

	require ('global.php');
	iniciarHtml ($pagina) ;
	addJs ('js/search_customer.js');
	
	$salesrep_id = $user->get_detail (salesrep_id);
	$arrCamposClick = array(8,6);
	$arrCamposNoView= $arrCamposClick;
	
	$customer = new cls_customer ( $db, 0);
	$search   = new cls_search ($customer, 0, 'goCustomer', $arrCamposClick, $arrCamposNoView);
	
	/*Configuracion de busqueda*/
	$arrCampos		  = array ('customer_number','cuit' , 'customer_name','customer_name_phonetic','province','city','address_id','address1','customer_id');
	$arrAlias		  = array ('N° de Cliente','CUIT','Raz&oacute;n Social','Nombre Fantasia','Provincia','Localidad','C&oacute;digo Domicilio','Direcci&oacute;n','C&oacute;digo');
	
	$arrSearch		  = array ('customer_number','cuit','customer_name','customer_name_phonetic','address1');
	$arrSearchCaption = array ('N° de Cliente','Cuit','Raz&oacute;n Social','Nombre Fantasia','Direcci&oacute;n');
	
	$arrCondiciones	  = array ( "salesrep_id = $salesrep_id " );
	/*Fin configuracion busqueda*/
	
	$search->iniciar ( $arrCampos, $arrAlias, $arrSearch , $arrCondiciones ,$arrSearchCaption); 
	
	cerrarHtml ();
?>