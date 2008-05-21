<?php

	class cls_transaction_state extends cls_sql_table {
		
		function __construct (cls_sql &$db ,$id = 0) {
			parent::__construct($db,'transaction_states', $id,'state_id');			
		}
		
		public function comboEstados($id = 0) {
			
			$ret = false;
			if ( trim( $this->get_detail (next_avail) ) != '') {
				$proximos = explode ( ',' , $this->get_detail (next_avail) ) ;
		
		
				if ( count($proximos) > 0) {
				
					
					foreach ($proximos as $opcion) {
						$estado = new cls_transaction_state($this->db, $opcion);
						
						if (cls_sql::numeroSQL($estado->get_detail(min_user_level_id)) <= cls_sql::numeroSQL($_SESSION['user_level'])){
							
							
							if (!$ret) echo "<select id=cmb_est_$id name=cmb_est_$id>";
							
							echo "<option value=$opcion >". $estado->get_detail(description) . '</option>';
							
							
							
							$ret = true;
						}
					}
					
					if ($ret) echo '</select>';
					
				}
			}
			return $ret;
		}
		
	}
?>