<?php
	/*
	Login de usuario
	@author Martin Fernandez
	*/
	
	require ('global.php');
	
	$user->_logout();
	
	iniciarHtml( $pagina );
	
	addJs('js/login.js');
	
	echo '<div style=height:300px>';
	iniciarForm('frmLogin','login.php','post','',true);

	textBox         ('Usuario:'     , 'txtUser' , '' );
	textBoxPassword ('Contrase&ntilde;a:' , 'txtPwd'  ,'return keyPressLogin(event)');
	
	buttonTable ('Entrar','doLogin()');
	
	echo '<tr><td align=center colspan=2>';
	addDiv ( 'resultado','style=color:red','');
	echo '</td></tr>';
	
	cerrarForm(true);
	
	echo '</div>';
	cerrarHtml();
?>