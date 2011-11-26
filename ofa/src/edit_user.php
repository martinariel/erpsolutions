<?php

	/*
	
	Abm tabla users
	
	@author Martin Fernandez
	*/

	require ('global.php');
	
	iniciarHtml($pagina);
	
	$user_id = $_REQUEST['user_id']+0;
	$modo = $_GET['modo'];	
	
	$user = new cls_user($db, $user_id);
	
	if ($_GET[modo] == 'save') {
		$user->set_detail(email, $_POST[txtemail]);
		$user->set_detail(username, $_POST[txtusername]);
		$user->set_detail(user_level_id,$_POST[nivel]);
		$user->set_detail(salesrep_id, $_POST['salesrep_id']);
		
		if ($user_id == 0) {
			$user->set_detail(password, md5(trim($_POST[txtpass] )) );
			if ($user->execute_insert('local'))
				echo '<span style=color:green;font-weight:bold>Se ha creado el nuevo usuario.</span>';
			else
				echo '<span style=color:red;font-weight:bold>Error: No se ha creado el nuevo usuario.</span>';
		}
		else {
			
			if ($_POST[txtpass] != '') {
				$user->set_detail(password, md5(trim($_POST[txtpass])));
			}
			else {
				$user->set_detail(password,$user->get_detail(password)  );
			}
			
			if ($user->execute_update())
				echo '<span style=color:green;font-weight:bold>El usuario ha sido modificado.</span>';
			else
				echo '<span style=color:red;font-weight:bold>Error: El usuario no ha sido modificado.</span>';
			
		}
	}
	else {
	
	
	iniciarForm ('frmuser','edit_user.php?modo=save','post','', true);
		hidden('user_id',$user_id);

	
	
	if ($user_id != 0) {
	
	?>
	<script language="javascript">
		function guardarDatos() {
			var msg= "";
			if ($('txtusername').value == '') msg += "- Debe completar el nombre de usuario ";
			//if ($('txtpass').value == '') msg += "\n- Debe completar la contraseña ";
			if ($('txtemail').value == '') msg += "\n- Debe completar el email ";
			if ($('nivel').value == '0') msg += "\n- Debe Seleccionar el nivel de usuario ";
			
			
			
			if(msg=='') {
				$('frmuser').submit();
			}
			else
			{
				alert(msg);
			}
		}
	</script>
	<?php
	textBox('Nombre de usuario: ','txtusername',$user->get_detail(username) );
	textBox('E-Mail: ','txtemail',$user->get_detail(email) );
	textBoxPassword ('Contraseña <br>(en blanco para no <br>cambiarla)','txtpass');
	comboBox($db,'select user_level_id,description from users_levels','Nivel:','nivel',$user->get_detail(user_level_id),'');
	comboBox($db,'select salesrep_id,name from salesreps where salesrep_id > 0','Salesrep:','salesrep_id',$user->get_detail(salesrep_id),'');
	}
	else
	{
	?>
	<script language="javascript">
		function guardarDatos() {
			var msg= "";
			if ($('txtusername').value == '' ) msg += "- Debe completar el nombre de usuario ";
			if ($('txtpass'    ).value == '' ) msg += "\n- Debe completar la contraseña ";
			if ($('txtemail'   ).value == '' ) msg += "\n- Debe completar el email ";
			if ($('nivel'      ).value == '0') msg += "\n- Debe Seleccionar el nivel de usuario ";
			
			if(msg=='') {
				$('frmuser').submit();
			}
			else
			{
				alert(msg);
			}
		}
	</script>
	<?php
	
	textBox('Nombre de usuario: ','txtusername' ,'' );
	textBox('E-Mail: ','txtemail','');
	textBoxPassword ('Contraseña <br>','txtpass');
	comboBox($db,'select user_level_id,description from users_levels','Nivel:','nivel',0,'');
	comboBox($db,'select salesrep_id,name from salesreps where salesrep_id > 0','Salesrep:','salesrep_id',0,'');
	
	}
	
	echo '<tr><td align=center colspan=2><br>';
	button("Guardar","guardarDatos()");
	echo '</td></tr>';
	cerrarForm(true);
	
	if ($user_id == 0 ) {
		echo "<script language=javascript>$('frmuser').reset();</script>";
	}
	
	}
	
	cerrarHtml();
?> 