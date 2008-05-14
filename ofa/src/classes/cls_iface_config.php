<?php

	abstract class cls_iface_config {
	
		private $table;
		private $obj;
		
		const src_tb   = 'src_tb';
		const src_fld  = 'src_fld';
		const dest_fld = 'dest_fld';
		const bool_fld = 'tran_b';
		
		private $arrSource = array();
		private $arrFunctions = array();
		private $ajaxUrl;
		private $db;
		
		private $contadorInserts;
		
		public function setContador($cont){
			$this->contadorInserts = $cont;
		}
		
		public function getContador()
		{
			return $this->contadorInserts;
		}
		
		private $arrInterfaz; //matriz de la tabla de interfaz
		private $fechaActual;
		
		function __construct ( cls_sql &$db ,cls_sql_table &$obj, $table_name, $url, $param=0) {
			$this->db 		= $db;
			$this->table 	= $table_name;
			$this->obj		= $obj;
			$this->ajaxUrl 	= $url;
			
			if ($param==0) {
				$this->agregarObjeto ( new cls_customer($db) );
				$this->agregarObjeto ( new cls_salesrep($db) );
				$this->agregarObjeto ( new cls_list_price($db) );
				$this->agregarObjeto ( new cls_terms($db) );
				$this->agregarObjeto ( new cls_pay_terms($db) );
				$this->agregarObjeto ( new cls_transaction($db));
				
				$this->cargarFunciones ();
			}
			
			$this->setContador(0);
		}
		
		public function agregarObjeto ( cls_sql_table $obj ){
			array_push ( $this->arrSource, $obj );
		}
		
		public function get_interfaz() {
			if ( !isset ( $this->arrInterfaz) ) {
				$this->interfaz();
			}
			return $this->arrInterfaz;
		}

		private function interfaz () {
			$table 	  = $this->table;
			$src_tb   = self::src_tb;
			$src_fld  = self::src_fld;
			$dest_fld = self::dest_fld;
			$bool_fld = self::bool_fld;
			
			$sql = "select $src_tb, $src_fld, $dest_fld,$bool_fld from $table";
			$rs = $this->db->ejecutar_sql($sql);
			
			if ($rs && !$rs->EOF) {
				$this->arrInterfaz = $rs->GetRows();
			}
		}
		
		public function ejecutarFuncion ($indiceInterfaz) {
			$interfaz = $this->get_interfaz();
			$fila	  = $interfaz[$indiceInterfaz];
			$table 	  = $this->obj->table_name;
			$src_tb   = self::src_tb;
			$src_fld  = self::src_fld;
			$dest_fld = self::dest_fld;

			switch ($fila[$src_tb] ) {
				case 'fijo' : return $fila[$src_fld];break;
				case 'incremento': 
						$campo = $fila[$src_fld];
						$sql = "select max($campo) from $table";
						$rs = $this->db->ejecutar_sql($sql);
						if ($rs && !$rs->EOF){
							return ($rs->fields[0] + 1);
						}
						break;
				case 'fecha' :
						if (!isset ($this->fechaActual) ) {
							$this->fechaActual = cls_utils::fechaHoraActual();
						}
						
						return $this->fechaActual;
						break;
				case 'contador':
						return $this->getContador()+1;
						break;
						
			}
			
		}
		
		private function cargarFunciones() {
			$autoIncrement = array('Incremento','incremento','combo',$this->table);
			$fijo		   = array('Valor Fijo', 'fijo','text');
			$fecha		   = array('Fecha Actual', 'fecha','');
			$contador	   = array('Contador', 'contador','');
			
			array_push ( $this->arrFunctions, $autoIncrement);
			array_push ( $this->arrFunctions, $fijo );
			array_push ( $this->arrFunctions, $fecha );
			array_push ( $this->arrFunctions, $contador);
		}
		
		
		private function guardar() {
			$campos = $this->obj->get_vector_campos();
			
			$table 	  = $this->table;
			$src_tb   = self::src_tb;
			$src_fld  = self::src_fld;
			$dest_fld = self::dest_fld;
			$bool_fld = self::bool_fld;
			
			foreach($campos as $campo) {
				$name  = strtolower($campo->name);
				
				$tabla = $_POST["tabla_$name"];
				$fld   = $_POST["fld_$name"];
				$bool_t= $_POST["bool_$name"];
				
				$name  = cls_sql::cadenaSQL($name);
				$tabla = cls_sql::cadenaSQL($tabla);
				$fld   = cls_sql::cadenaSQL($fld);
				$bool_t= cls_sql::numeroSQL($bool_t);
				
				$sql = "update $table set $src_tb = $tabla, $src_fld = $fld,$bool_fld = $bool_t where $dest_fld = $name";
				
				$this->db->ejecutar_sql($sql);
			}
			echo '<b>La configuración ha sido guardada.</b>';
		}
		
		public function configurar () {
			$url = $this->ajaxUrl;
			if (intval($_POST['save']) == 1) {
				$this->guardar();
			}
			addJs ('js/cls_iface_config.js');
			addDiv ('loader','style=display:none;position:absolute;top:100px;left:300px;background-color:white;border:1px solid black','<img src=img/ajax-loader.gif><br>Cargando...');
			
			$campos = $this->obj->get_vector_campos();
			
			iniciarForm('frmConfig',cls_page::get_fileName() );
			echo '<table bgColor=#000000 cellspacing=1 cellpadding=2>';
			echo '<tr><th>Campo Destino</th><th>Tabla Origen</th><th>Campo Origen</th><th>Transferir Oracle</th></tr>';
			
			foreach($campos as $campo) {
				echo '<tr>';
				$vector = $this->actualValue($campo->name);
				
				echo '<td><b>'.$vector[dest_fld] .'</b></td>';
				echo '<td>';
				$this->comboTablas($vector[dest_fld],$vector[src_tb],$vector[src_fld]);
				echo '</td>';
				echo "<td id=fld_combo_$vector[dest_fld]>$vector[src_fld]</td>";
				
				if ($vector[tran_b] == 1){
					
					echo "<td><input checked type=checkbox name=bool_$vector[dest_fld] value=1></td>";
				}
				else{
					echo "<td><input type=checkbox name=bool_$vector[dest_fld] value=1></td>";
				}
				
				
				echo '</tr>';
				
				if ( trim($vector[src_tb]) == '') {
					$obj = $this->arrSource[0];
					$tabla = $obj->table_name;
				}
				else {
					$tabla = $vector[src_tb];
				}
				echo "<script language='javascript'>combos('$tabla','$vector[src_fld]','$vector[dest_fld]','$url')</script>";
			
			}
			echo '</table><br>';
			hidden ('save',1);
			button ('Guardar', "if (confirm('Modificar la configuración?'))$('frmConfig').submit()");
			cerrarForm();
		}
		
		private function comboTablas($nombre='',$selectTabla='',$selectField='') {
			$url = $this->ajaxUrl;
			echo "<select style=width:150px;font-family:verdana;font-size:11px name=tabla_$nombre onChange=combos(this.value,'','$nombre','$url')>";
			foreach ($this->arrSource as $tabla) {
				if (strtolower($selectTabla) == strtolower($tabla->table_name) )
					echo '<option selected value='. $tabla->table_name .'>' . $tabla->table_name .'</option>';
				else
					echo '<option value='. $tabla->table_name .'>' . $tabla->table_name .'</option>';
			}
			foreach ($this->arrFunctions as $funcion) {
				if (strtolower ( $funcion[1]) == strtolower ($selectTabla) ) 
					echo "<option selected value=$funcion[1]>$funcion[0]</option>";
				else
					echo "<option value=$funcion[1]>$funcion[0]</option>";
			}
			echo '</select>';
		}
		
		
		public function comboFld($tabla,$selected,$nombre) {
		
			$encontre = false;
			$i = 0;
			
			while (!$encontre && $i < count ($this->arrFunctions) ){
				$vector = $this->arrFunctions[$i];
				if (strtolower($vector[1]) == strtolower($tabla) ) {
					$encontre = true;
				}
				$i++;
			}
		
			if (!$encontre ) {
				
				$i = 0;
				$encontre = false;
				
				while (!$encontre && $i < count($this->arrSource) ) {
					$obj = $this->arrSource[$i];
					if ( strtolower($obj->table_name) == strtolower($tabla) )
						$encontre = true;
					$i++;
				}
				
				if ($encontre) {
					$obj = $this->arrSource[$i-1];
					$obj->comboCampos ("fld_$nombre",$selected);
				}
				
			}
			else {
				$i--;
				$vector = $this->arrFunctions[$i];
				$this->componenteFuncion ($vector,$nombre, $selected);
			}
		}
		
		private function componenteFuncion ( $vector , $nombre , $valor) {
			switch ( $vector[2] ) {
				case 'text': 
							textBox ('',"fld_$nombre",$valor,false);
							break;
				case 'combo': 
							$this->obj->comboCampos ("fld_$nombre",$valor);
							break;
			}
		}
	
		private function actualValue($valor) {
		
			$table 	  = $this->table;
			$src_tb   = self::src_tb;
			$src_fld  = self::src_fld;
			$dest_fld = self::dest_fld;
			$bool_fld = self::bool_fld;
			
			$valor = cls_sql::cadenaSQL(strtolower($valor));
			$sql   = "select $src_tb,$src_fld,$dest_fld,$bool_fld from $table where $dest_fld = $valor";
			$rs    = $this->db->ejecutar_sql($sql);
			
			if ($rs && !$rs->EOF){
				$arr = $rs->GetRows();
				return $arr[0];
			}
			else
			{
				$sql = "insert into $table ($dest_fld) values($valor)";
				$this->db->ejecutar_sql($sql);
			}
		}
		
		public function setearCamposNoInsert(cls_sql_table &$obj) {
			$vector = $this->get_interfaz();
			
			foreach ($vector as $fila) {
				if ($fila[tran_b] == 0) {
					$obj->agregarCampoNoInsert($fila[dest_fld]);
				}
			}
		}
		
		public function ejecutarMapeo (cls_sql_table &$obj, &$matrizObj, $modo = 'local') {
			$vector = $this->get_interfaz();
			$contador = 0;
			
			
			
			foreach ($vector as $fila) {
				
				$detail 	 = $fila[dest_fld];
				$tablaOrigen = $fila[src_tb];
				$detailSrc	 = $fila[src_fld];
				
				$encontre = false;
				$i = 0;
					
				while (!$encontre && $i < count($matrizObj) ) {
					if ( strtolower($matrizObj[$i]->table_name) == strtolower($tablaOrigen) ) {
						$encontre = true;
					}
					$i++;
				}
					
				if ($encontre) {
					$i--;
					$obj->set_detail($detail, $matrizObj[$i]->get_detail($detailSrc));
				}
				else {
					//tiene que ser una funcion 
					$obj->set_detail($detail,$this->ejecutarFuncion($contador));
				}
				
				$contador++;
			}
				
			$obj->execute_insert($modo);
			$this->setContador($this->getContador()+1);
			
		}
		
	}
?>