<?php

	/*
	
	Script global de la aplicacion.
	Includes generales, session de login, objeto de usuario y pagina
	conexion con la db.
	
	@author Martin Fernandez
	
	*/
	
	session_start();
	
	Header('Cache-Control: no-cache');
	Header('Pragma: no-cache');
	
	require ('classes/package.php');
	require ('includes/forms.php');
	require ('includes/html.php');
	
	$retorno="\r\n";
	
	function session_defaults() 
	{ 
		$_SESSION [ 'logged'     ] = false; 
		$_SESSION [ 'uid'        ] = 0;
		$_SESSION [ 'user_level' ] = 0;
		$_SESSION [ 'username'   ] = ''; 
	} 
	
	
	function logged()
	{
		return $_SESSION['logged'];
	}
	
	if (!isset($_SESSION['uid'])) 
	{ 
		session_defaults(); 
	}
	
	$db     = new cls_sql();
	$user   = new cls_user($db,$_SESSION['uid']); 
	$pagina = new cls_page($db);
	
?>