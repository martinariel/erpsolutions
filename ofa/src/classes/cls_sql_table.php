<?php

	/*
	Clase base para clases dependientes de una tabla.
	@Author: Martín Fernández. ERP Solutions 2007
	@E-Mail: martin.fernandez@erp-solutions.com
	@Last Update: 2007/08/14
	
	Metodos de accion sobre la tabla:
		execute_update: actualiza el registro con los valores cargados en el vector details
		execute_insert: inserta un registro con los valores cargados en el vector details
		execute_delete:  elimina el registro
		CleanTable : Borra todos los registros de la tabla
	*/

	abstract class cls_sql_table {
		
		public $db;
		public $table_name;
		public $details;
		public $id;
		public $sql_select;
		public $id_field;
		
		
		private $campos;
		private $arrConditions;
		private $camposNoInsertOracle;
		
		function __construct (&$db,$table_name,$id=0,$id_field='id',$condiciones=array() )
		{
			$this->db = $db;
			$this->table_name = $table_name;
			$this->id = $id;
			$this->id_field = $id_field;
			$this->arrConditions = $condiciones;			
		}
		
		public function setCamposNoInserOracle( $vector) 
		{
			$this->camposNoInsertOracle = $vector;
		}
		
		
		public function agregarCampoNoInsert($campo) 
		{
			if (!isset($this->camposNoInsertOracle ))
			{
				$this->camposNoInsertOracle =array();
			}
			array_push ($this->camposNoInsertOracle ,$campo);
		}
		
		private function analizarCampoInOracle ( $campo ) 
		{
			if ( isset ( $this->camposNoInsertOracle ) ) 
			{
				foreach ($this->camposNoInsertOracle as $fld) 
				{
					if ( strtolower($campo) == strtolower($fld) ) 
					{
						return false;
					}
				}
				return true;
			}
			else
			{
				return true;
			}
		}
		
		private function insertDummie() 
		{
			$sql = "insert into $this->table_name ($this->id_field) values (-1)";
			return $this->db->ejecutar_sql($sql);
		}
		
		/*obtiene la estructura de la tabla y se almacena en el array campos*/
		private function obtener_estructura($times=0)
		{
			$sql = "select * from $this->table_name";
			$rs = $this->db->ejecutar_sql_pagina($sql,1,1);
			
			if ($rs && !$rs->EOF)
			{
				$this->campos = array();
				for ( $i = 0 ; $i < $rs->FieldCount(); $i++)
				{
					$fld = $rs->FetchField($i);
					array_push($this->campos,$fld);
				}
			}
			else 
			{
				if ($times == 0) 
				{
					if ( $this->insertDummie() )
					{
						$this->obtener_estructura(1);
					}
				}
			}
		}
		
		/*
			devuelve una cadena con los campos de la tabla separdos por coma
		*/
		private function sql_campos($driver='') 
		{
			if (!isset($this->campos) ) 
				$this->obtener_estructura();
			
			$i=0;
			
			foreach ($this->campos as $arr)
			{
				
				if (	strtolower($arr->name) != strtolower($this->id_field) 
						|| $driver !='local' ) 
				{
					
					if ( ($driver=='oracle' && $this->analizarCampoInOracle($arr->name)) 
						 || ($driver != 'oracle' )
					)
					{
						if ( $i > 0 )
						{
							$ret .= ',' . $arr->name;
						}
						else 
						{
							$ret = $arr->name;
						}
						$i++;
					}
				}
				
				
			}

			return strtolower($ret);
		}
		
		//Carga el vector details con los datos del registro
		private function db_details (){
			$id  = cls_sql::numeroSQL ( $this->id);
			
			$campos = $this->sql_campos();
			$sql	= $this->get_select_query();
			
			if ($id != 0){
				$sql = $sql . " where $this->id_field = $id";
				foreach ($this->arrConditions as $condicion){
					$sql = "$sql and $condicion";
				}
			}
			else
			{
				//si el id no esta seteado, voy a seleccionar el primer registro de la base.
				// solo para obtener la estructura, existe un registro dummie para ello
				//TIENE QUE EXISTIR POR LO MENOS UN REGISTRO EN LA TABLA:
				//PARA ELLO CREAR UN REGISTRO "TONTO" LLENO DE CEROS
			}
			
			$rs = $this->db->ejecutar_sql_pagina($sql,1,1);
			
			if ( $rs && !$rs->EOF ) {
				$arr = $rs->GetRows();
				$this->details = $arr[0];
			}
			
		}
		
		/*
		segun el el tipo de campo date o datetime se aplica la funcion para el objetivo de base
		*/
		private function formato_fecha ($valor,$driver,$tipo) {
			switch ( $driver ) {
				case 'mysql':
					if ($tipo == 'd') return "STR_TO_DATE('$valor' , '%m/%d/%Y' )" ;
					if ($tipo == 't') return "STR_TO_DATE('$valor' , '%m/%d/%Y %H:%i:%s')";
				break;
				case 'oracle':
					if ($tipo == 'd') return "TO_DATE('$valor' , 'yyyy-mm-dd' )" ;
					if ($tipo == 't') return "TO_DATE('$valor' , 'yyyy-mm-dd HH24:MI:SS')";
				break;
				case 'local':
					if ($tipo == 'd') return "STR_TO_DATE('$valor' , '%Y-%m-%d' )" ;
					if ($tipo == 't') return "STR_TO_DATE('$valor' , '%Y-%m-%d %H:%i:%s')";
				break;
			}
		}
		
		/*
		devuelve el valor del campo del registro actual en formato sql
		ver manual de adodb
		*/
		private function valor_campo ($obj,$driver){
			$idx = strtolower($obj->name);
			$valor = $this->get_detail($idx);
			
			switch ( strtolower ($this->metaType($obj->type) ) ) {
				case 'c': $valor = cls_sql::cadenaSql($valor);break;
				case 'b': $valor = cls_sql::cadenaSql($valor);break;
				case 'x': $valor = cls_sql::cadenaSql($valor);break;
				case 'd': $valor = $this->formato_fecha ($valor,$driver,'d');break;
				case 't': $valor = $this->formato_fecha ($valor,$driver,'t');break;
				default : $valor = cls_sql::numeroSQL($valor);
			}
			return $valor;
		}
		
		/*
		devuelve los valores actuales de los campos del registro separados por coma y en formato sql
		*/
		Private function datos_campos_insert ($driver) {
			$i = 0;
			foreach ($this->campos as $obj){
				
				if ( strtolower($obj->name) != strtolower($this->id_field) || $driver!='local' ) {
					
					if ( ($driver =='oracle' && $this->analizarCampoInOracle($obj->name)) 
						 || ($driver != 'oracle' )
					)
					{
						if ($i == 0)
							$ret = '(' . $this->valor_campo($obj,$driver);
						else 
							$ret = $ret . ',' . $this->valor_campo($obj,$driver);
						$i++;
					}
					
					
				}
			}
			
			$ret = $ret . ')';
			return $ret;
		}
		
		private function armar_campos ($driver='' ){
			$ret = '('. $this->sql_campos($driver) . ')';
			return $ret;
		}
		
		private function campos_update($driver){
			$i = 0;
			foreach ($this->campos as $obj){
				if ($i == 0)
					$ret = $obj->name . '='. $this->valor_campo($obj,$driver);
				else 
					$ret = $ret . ',' . $obj->name . '=' . $this->valor_campo($obj,$driver);
				$i++;
			}
			return $ret;	
		}
		
		/*
			usando un recordset tonto para usar su metodo MetaType devuelve un char 'standarizado'
			del tipo de dato en la columna
		*/
		public function metaType($fldType){
			$dummie = $this->db->ejecutar_sql ('select 1 as dummie');
			if ($dummie)
				return $dummie->MetaType($fldType);
		}
		
		/*
		Imprime un combo con los campos de la tabla
		*/
		public function comboCampos ($nombre, $valor ) {
			$campos = $this->get_vector_campos();
			echo "<select style=width:150px;font-family:verdana;font-size:11px name=$nombre>";
			foreach ($campos as $campo) {
				$option = strtolower($campo->name);
				if ($option == strtolower($valor) )
					echo '<option selected value='.$option.'>'.$option.'</option>';
				else
					echo '<option value='.$option.'>'.$option.'</option>';
			}
			echo '</select>';
		}
		
		//*************************************************************************************************
		
		/*
			Trunquea la tabla
		*/
		public function CleanTable (){
			$sql = "TRUNCATE TABLE $this->table_name";
			$resultado = $this->db->ejecutar_sql ($sql);
			
			if ($resultado)
				return true;
			else
				return false;
		}
		
		public function sqlInsert($driver = 'local') {
		
			if (!isset($this->campos) ){
				$this->obtener_estructura();
			}

			$campos  = $this->armar_campos($driver);
			$valores = $this->datos_campos_insert($driver);
			
			$sql = "INSERT INTO $this->table_name $campos VALUES $valores";
			
			if (strtolower($driver)== 'oracle'){
				$sql = str_replace("'sysdate'","sysdate",$sql);
				$sql = str_replace("'SYSDATE'","SYSDATE",$sql);
			}
			return $sql;
		}
		
		/*
			inserta en la tabla los datos del vector details, loader desde oracle
		*/
		public function execute_insert($driver = 'local') {
			//TODO: ejecutar la sentencia sql en vez de devolverla
			
			$sql_max = "select max($this->id_field) from $this->table_name";
			
			$rs = $this->db->ejecutar_sql($sql_max);
			if ($rs && !$rs->EOF){
				if ($rs->fields[0]==-1){
					$this->set_detail($this->id_field,1);
				}
				else
				{
					$this->set_detail($this->id_field,$rs->fields[0] +1);
				}
			}
			
			$sql = $this->sqlInsert($driver);
			return $this->db->ejecutar_sql($sql);
		}
		
		/*
			updetea los el registro de la tabla a los valores en el vector details
		*/
		public function execute_update($driver = 'local') {
			//TODO: ejecutar la sentencia sql en vez de devolverla
			if (!isset($this->campos) ) {
				$this->obtener_estructura();
			}
			
			$sql = $this->campos_update($driver);
			$sql = "UPDATE $this->table_name SET $sql where $this->id_field = $this->id";
			
			return $this->db->ejecutar_sql ( $sql);
			

			
			//return $sql;
		}
		
		/*
			borra el registro
		*/
		public function execute_delete ($id) {
			$id  = cls_sql::numeroSQL($id);
			$sql = "delete from $this->table_name where $this->id_field = $id";
			$result = $this->db->ejecutar_sql ($sql);
			
			if  ($result) 
				return true; 
			else 
				return false; 
		}
		
		
		public function exportarCsv ($id = 0, $campo = '') {
			
			$sql = $this->get_select_query();
			
			if ( trim( $campo) != '') {
				$sql .= " where $campo = $id";
			}
			else {
				$id = $this->get_id();
				$sql .= " where $this->id_field = $id ";
			}
			
			$rs = $this->db->ejecutar_sql($sql);
			return rs2csv($rs,true);
		}
		//*************************************************************************************************
		
		/*Getters */
		public function get_select_query () {
			$campos = $this->sql_campos();	
			$sql = "select $campos from $this->table_name";
			return $sql;
		}
		
		public function get_id () {
			return $this->id;
		}
		
		public function get_id_field () {
			return $this->id_field;
		}
		
		public function get_detail ($detail ) {
			if ( !isset ( $this->details) ) {
				//Cargo el vector
				$this->db_details();
			}
			
			return $this->details[$detail];
		}
		
		public function get_details () {
			if ( !isset ( $this->details) ) {
				//Cargo el vector
				$this->db_details();
			}
			return $this->details;
		}
		
		/*
			total de registros en la tabla
		*/
		public function get_total_rows(){
			$sql = "select count(*) from $this->table_name";
			$rs = $this->db->ejecutar_sql($sql);
			
			if ($rs && !$rs->EOF){ return $rs->fields[0]; }
			else { return 0; }
			
			$rs->Close();
		}
		
		public function get_vector_campos() {
			if (!isset($this->campos) ) 
				$this->obtener_estructura();
			return $this->campos;
		}
		
		/*Setters*/
		public function set_details ($details) { 
			$this->details = $details;
		}
		
		public function set_detail ( $detail, $valor ) {
			if ( !isset ( $this->details) ) {
				//Cargo el vector
				$this->db_details();
			}
			$this->details[$detail] = $valor;
		}
		
		public function set_id ( $id){
			$this->id = $id;
		}
		
		public function set_table_name($name) {
			$this->table_name = $name;
		}
		
		public function get_table_name(){
			return $this->table_name;
		}
		
		public function get_array_recordset() {
			$vectorDatos  = $this->get_details();
			$vectorCampos = $this->get_vector_campos();
			
			$ret = array();

			foreach ( $vectorCampos as $campo ) {
				$campoLinea = array ( 'field'=> $campo->name, 'value'=> $vectorDatos[$campo->name] );
				array_push ( $ret, $campoLinea);
			}
			
			return $ret;
			
		}
		
	}
?>