<?php

	include( $_SERVER['DOCUMENT_ROOT'] . "/ofar/titulo.php");
	include( $_SERVER['DOCUMENT_ROOT'] . "/ofar/include/error.php");

	if(!defined("_SQL_CLASS_")){
		define("_SQL_CLASS_",1);
		
		class sql {
		
			var $obj_conexion;
			var $resultado;
			var $campos;
			var $total;
			var $trans_activa;
			
			function conectar(){
				//$this->obj_conexion = mysql_connect ("200.32.5.50","under_zero","h2vgvj4dd9@");
				$this->obj_conexion = @mysql_connect ("localhost","ip011271_ofa3","cap084");
				if (!$this->obj_conexion){
					Error ("Error al Conectar con el Servidor");
					die();
				}
				else{
					
  				    if(!@mysql_select_db("ip011271_ofa3",$this->obj_conexion)){
					//if(!@mysql_select_db("under_zero",$this->obj_conexion)){
						Error ("Error al Conectar con la Base de Datos");
						die();
					}
				}
			}
			
			function desconectar(){
				@mysql_close($this->obj_conexion);
			}
			
			function consulta($texto){
				$this->conectar();
				
				$this->campos = "";
				$this->resultados = "";
				
				$this->resultados = mysql_query($texto,$this->obj_conexion);
				if ($texto == "BEGIN"){
					$this->trans_activa = 1;
				}
				if ($texto == "COMMIT"){
					$this->trans_activa = 0;
				}
				if(mysql_error()){
					if ($this->trans_activa == 1){
						mysql_query("ROLLBACK",$this->obj_conexion);
						$this->trans_activa = 0;
					}
					echo mysql_error() . "<br>"; 
					echo "<p>Consulta: " . $texto . "<br>";
					return false;
				}

				$this->desconectar();
				return true;
			}
			
			function siguiente(){
				return $this->campos = mysql_fetch_array($this->resultados);
			}
			
			function obtener($nombre_campo){
				return $this->campos[$nombre_campo];
			}
			
			function idoperacion(){
				$t = gettimeofday();
				return $t['sec'];
			}
			
		}
			
	}

?>
