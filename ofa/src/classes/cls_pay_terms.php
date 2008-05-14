<?php

	class cls_pay_terms extends cls_sql_table {
		
		function __construct ( cls_sql &$db , $id = 0 ) {
		
			parent::__construct ($db,'flexfield_pay_terms', $id,'pay_term_id');	
		
		}
		
		public function combo () {
			$sql = "select distinct $this->id_field, pay_term_description from $this->table_name";
			comboBox ($this->db,$sql,'','pay_term_id','','',false );
		}
		
		public function titulo () {
			echo $this->get_detail(pay_term_description);
		}
	
	}

?>