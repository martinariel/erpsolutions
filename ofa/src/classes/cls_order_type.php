<?php


class cls_order_type extends cls_sql_table
{

	//----------------------------------------------------------------------

	function __construct ( cls_sql &$db , $id = 0 ) {
		
		parent::__construct ($db,'order_type', $id,'order_type_id');	
		
	}

	//----------------------------------------------------------------------

	public function combo ($seleccionado = -1, $mostrarSeleccione = true) 
	{
		$sql = "select distinct $this->id_field, order_type from $this->table_name";
		comboBox ( $this->db , $sql ,'' ,'order_type_id',$seleccionado,'',false ,0, $mostrarSeleccione);
	}

	//----------------------------------------------------------------------
	
	public function titulo () 
	{
		echo $this->get_detail(order_type);
	}
}
?>