<?php

	class cls_transaction_state extends cls_sql_table {
		function __construct ( cls_sql &$db , $id = 0 ) {
			parent::__construct($db,'transaction_states', $id,'state_id');			
		}
		
		public function comboEstados($id = 0) {
			
			$ret = false;
			if ( trim( $this->get_detail (next_avail) ) != '') {
				$proximos = explode ( ',' , $this->get_detail (next_avail) ) ;
				
				if ( count($proximos) > 0) {
					echo "<select id=cmb_est_$id name=cmb_est_$id>";
					foreach ($proximos as $opcion) {
						echo "<option value=$opcion >". $this->nombreEstado($opcion) . '</option>';
					}
					echo '</select>';
					$ret = true;
				}
			}
			return $ret;
		}
		
		private function nombreEstado ($id) {
			$id = cls_sql::numeroSQL($id);
			$sql = $this->get_select_query() . " where $this->id_field = $id ";
			$rs = $this->db->ejecutar_sql($sql);
			
			if ($rs && !$rs->EOF){
				return $rs->fields['description'];
			}
		}
	
	}
?>