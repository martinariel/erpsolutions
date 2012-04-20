<?php

	/*
	
	Clase interfaz, inserta los registros en las tablas oe_headers_iface_all y oe_lines_iface_all
	

	
	@author Martin Fernandez
	
	*/

	class cls_interface {
		
		private $db;
		private $products;
		private $customer;
		private $ora_db;
		
		public $iface_header;
		public $iface_lines;
		
		function __construct ( &$db ) {
			$this->db = $db;
			
			$this->iface_header = new cls_interface_header($db);
			$this->iface_lines  = new cls_interface_lines($db);
			
		}
		
		//TODO!!!
		public function transfer (cls_sql_table &$obj) {
			
			$sql = $obj->get_select_query_ora(); // TODO
			$rs  = $this->ora_db->ejecutar_sql ($sql) ;
			
			if ( $rs && !$rs->EOF ) {
				if ($obj->CleanTable()) {
					
					$arr = $rs->GetRows();
					
					for ( $i = 0; $i < count ($arr) ; $i++){
						$arr1 = $arr[$i];
						$obj->set_details($arr1);
						echo $obj->execute_insert("oracle");
					}
					
				}
			}
		}
		
	}
?>