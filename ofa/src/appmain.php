<?php

	/*
	
	Formulario de seleccion de cliente, combo componente  dhtmlXCombo
	
	@author Martin Fernandez
	
	*/

	require('global.php');
	
	iniciarHtml($pagina);
	
	echo '<div style=height:300px>';
	
	iniciarForm('frmClient','edit_order.php','get','',true);
	ajaxComboBox('xml_customers.php','combo_customer','customer_id',"Cliente: ", 
					0, 300 , 'Aceptar >>','document.frmClient.submit()');
	
	
	echo '<tr><td align=center colspan=2><br><a href=search_customer.php>B&uacute;squeda Avanzada</a> | <a href=new_customer.php>Nuevo Cliente</a></td></tr>';

	cerrarForm(true);
	
	echo '</div>';
	cerrarHtml();
	
?>