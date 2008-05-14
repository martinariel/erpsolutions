<?php
	//TODO clase de manejo de la tabla ra_terms
	
	class cls_terms extends cls_sql_table {
	
		function __construct ( cls_sql &$db, $id = 0 ) {
			parent::__construct ( $db, 'ra_terms_tl',$id, 'term_id' );
		}
		
		public function comboTerms () {
			$sql = "select distinct $this->id_field, description from $this->table_name";
			comboBox ($this->db,$sql,'','term_id','','',false );
		}
		
		//html titulo de la lista de precios
		public function titulo () {
			echo $this->get_detail(description);
		}
	}
	
?>