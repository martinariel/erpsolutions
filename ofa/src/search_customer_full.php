<?php

	/*
	Busqueda avanzada en la tabla ra_customers
	
	@author Martin Fernandez
	
	*/

	require ('global.php');
	iniciarHtml ($pagina) ;
	addJs ('js/search_customer.js');
	
	$arrCamposClick = array();
	$arrCamposNoView= $arrCamposClick;
	
	$customer = new cls_customer ( $db, 0);
	$search   = new cls_search_customer_view ($customer, -1, '', $arrCamposClick, $arrCamposNoView);
	
	/*Configuracion de busqueda*/
	$arrCampos		  = array ('s.customer_number','s.cuit' , 's.customer_name','s.customer_name_phonetic','s.province','s.city','s.address1','v.name','s.salesrep_id');
	$arrAlias		  = array ('N° de Cliente','CUIT','Raz&oacute;n Social','Nombre Fantasia','Provincia','Localidad','Direcci&oacute;n','Vendedor','Codigo Vendedor');
	
	$arrSearch		  = array ('customer_number','cuit','customer_name','customer_name_phonetic','address1');
	$arrSearchCaption = array ('N° de Cliente','Cuit','Raz&oacute;n Social','Nombre Fantasia','Direcci&oacute;n');
	
	$arrCondiciones	  = array ( ' 1 = 1 ');
	/*Fin configuracion busqueda*/
	
	$search->iniciar ( $arrCampos, $arrAlias, $arrSearch , $arrCondiciones ,$arrSearchCaption); 
	
	cerrarHtml ();
?>