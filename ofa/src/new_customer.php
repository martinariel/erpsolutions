<?php

	require ('global.php');
	
	iniciarHtml ($pagina);
	
	$customer = new cls_custom_customer($db);
	$modo     = $_POST['modo'];
	

	if ($modo == 'guardar') {
		
		$name      = $_POST['txtRazon'];
		$phonetic  = $_POST['txtFantasia'];
		$cuit      = $_POST['txtCuit'];
		$address   = $_POST['txtDir'];
		
		$localidad = $_POST['txtLoc'];
		$provincia = $_POST['txtProv'];
		$cp 	   = $_POST['txtCp'];
		$tel	   = $_POST['txtTel'];
		
		$salesrep  = $user->get_detail(salesrep_id);
		$customer->set_detail(customer_name, $name);
		$customer->set_detail(customer_name_phonetic,$phonetic);
		$customer->set_detail(salesrep_id,$salesrep);
		$customer->set_detail(cuit,$cuit);
		$customer->set_detail(address1, $address);
		$customer->set_detail(postal_code, $cp);
		$customer->set_detail(province, $provincia);
		$customer->set_detail(telephone, $tel);
		$customer->set_detail(city,$localidad);
	
		if ( $customer->execute_insert('local') ) {
			echo 'El cliente ha sido guardado con éxito.<br>';
			$customer_id = $customer->get_detail(customer_id);
			button ('<< Anterior',"window.location='appmain.php'");
			button ('Siguiente >>',"window.location='edit_order_customer.php?customer_id=$customer_id'");
		}
		
	}
	else
	{
		//Formulario
		addJs("js/new_customer.js");
		iniciarForm ('frmNew',cls_page::get_filename(), 'POST','',true);
		hidden('modo','guardar');
		textBox ('Razón Social','txtRazon','',true,'','');
		textBox ('Nombre Fantasia','txtFantasia','',true,'','');
		hidden ('txtCuit','');
		?>
		<tr>
			<td align=right>
				Cuit
			</td>
			<td>
			<input name=val1 id=val1 size=2 maxlength=2>-
			<input name=val2 id=val2 size=9 maxlength=8>-
			<input name=val3 id=val3 size=1 maxlength=1>
			</td>
		</tr>
		<?php
		textBox ('Dirección','txtDir','',true,'','');
		textBox ('Localidad','txtLoc','',true,'','');
		textBox ('Provincia','txtProv','',true,'','');
		textBox ('CP','txtCp','',true,'','');
		textBox ('Tel&eacute;fono', 'txtTel', '',true,'' , '' ) ;
		
		cerrarForm(true);
		
		button ('<< Cancelar',"window.location='appmain.php'");
		button ('Aceptar >>',"guardar()");
	}
	
	cerrarHtml();
?>