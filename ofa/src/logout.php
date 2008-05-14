<?php
	
	/*
	Logout del sistema, restablece las sesiones y redireciona al login
	
	@author Martin Fernandez
	*/

	require ('global.php');
	session_defaults();
	header('location:login.php');
?>