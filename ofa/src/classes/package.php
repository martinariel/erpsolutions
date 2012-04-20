<?php
	/*
	
	Funcion autoload , carga las clases dinamicamente
	
	*/

	function __autoload($class_name) {
    	require_once $class_name . '.php';
	}
?>