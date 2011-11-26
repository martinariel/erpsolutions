<?php

	session_start();
	
	require ('classes/package.php');
	
	function session_defaults() 
	{ 
		$_SESSION['logged'  ] = false; 
		$_SESSION['uid'     ] = 0; 
		$_SESSION['username'] = ''; 
		$_SESSION['cookie'  ] = 0; 
		$_SESSION['remember'] = false; 
	} 
	
	//Valido el ingreso, to set the defaults. Of course session_start must be called before that.
	if (!isset($_SESSION['uid']) ) 
	{ 
		session_defaults(); 
	}
	
	function checkLogin()
	{
		if ( !isset($_SESSION['uid']) || $_SESSION['uid']==0)
		{
			header( 'login.php' ) ;
		}
		else
		{
			echo 'here';
		}
	}
	
	$date = gmdate("'Y-m-d'"); 
	$db   = new cls_sql();
	$user = new User($db); 
?>