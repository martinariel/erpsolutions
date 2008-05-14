<?php
	/*
	Clase de conexion sql.
	@Author: Martín Fernández. ERP Solutions 2007
	@Last Update: 2007/08/16
	
	Harcodeado el server, la db , el user , la pwd y el driver de conexion.
	Usa adodb php como abstracion de datos.
	
	*/
	require('adodb/adodb.inc.php');
	require('adodb/toexport.inc.php'); 
	
	class cls_sql{
		
		private $conn;
		const SERVER = "localhost";
		const DB	 = "ofar";
		const USER	 = "root";
		const PWD  	 = "";
		
		public function __construct(){
			$this->conn = NewADOConnection('mysql');
			$this->conn->Connect(self::SERVER,self::USER,self::PWD,self::DB);
		}
		
		private static function clean ( $cadena ) {
			return $cadena;
		}
		
		public static function cadenaSQL ($cadena){
			//$cadena = str_replace($cadena,"'","");
			//TODO: VALIDAR LA CADENA!!!
			$cadena = addslashes($cadena);
			$cadena = "'".$cadena."'";
			return $cadena;
		}
		
		
		public static function numeroSQL ($numero){
			//TODO : VALIDAR EL NUMERO!!!
			$numero = $numero + 0;
			return $numero;
		}
		
		public static function cadenaLike ($cadena) {
			$cadena = addslashes($cadena);
			$cadena = "'$cadena'";
			return $cadena;
		}
		
		//simplemente ejecuta la sentencia sql, si es un select devuelve el recordset
		// comprobar return == true
		public function ejecutar_sql($strsql){
			return $this->conn->execute($strsql);
		}
		
		//devuelve una 'pagina' de un recordset
		public function ejecutar_sql_pagina($strsql,$pagina,$resultados=10){
			return $this->conn->selectLimit($strsql, $resultados, ($pagina-1)*$resultados);
		}
		
		
		
		public static function pager ( $rows,$currentPage, $rows_pp){
			$y = $rows / $rows_pp;
			$x = floor($y);
			
			if ( $x != $y) { 
				$x++; 
			}
			else
			{
				//nothing to do
			}
			$total_pages = $x; 
			
			$i = $currentPage - 1; 
			
			if ($i>0) echo "<a href=javascript:goPage($i)> << </a>&nbsp;";
			
			for ( $i = ($currentPage-4) ; $i<=($currentPage+4); $i++) {
				if ( $i>0 && $i <= $total_pages) {
				
					if ($i == $currentPage){ 
						echo "<b>[$i]</b>&nbsp;"; }
					else { 
						echo "<a href=javascript:goPage($i)>[$i]</a>&nbsp;"; 
					}
					
				}
			}
			
			$i = $currentPage + 1; 
			
			if ($i <= $total_pages) echo "<a href=javascript:goPage($i)> >> </a>&nbsp;";
			echo "&nbsp;Registros: $rows";
		}
		
	}
?>