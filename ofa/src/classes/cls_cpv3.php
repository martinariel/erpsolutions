<?php
	class cls_cpv3 extends cls_sql_table 
	{
		function __construct ( cls_sql &$db , $id = 0 ) 
		{
			parent::__construct ($db,'CPV3', $id,'ID');	
		}

		public function combo ($seleccionado = -1,$mostrarSeleccione = true) 
		{

			$sql = "select $this->id_field, NOMBRE from $this->table_name";

			comboBox ($this->db,$sql,'','cpv3_id',$seleccionado,'',false ,0, $mostrarSeleccione);
		}

		public function titulo () 
		{
			echo $this->get_detail('nombre');
		}

		public function caption () 
		{
			return "Retenido";
		}
	}
?>