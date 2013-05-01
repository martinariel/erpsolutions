<?php

class cls_order_type extends cls_sql_table
{
	//----------------------------------------------------------------------

	private $mapper_provincias;

	function __construct ( cls_sql &$db , $id = 0 ) 
	{
		parent::__construct ($db,'order_type', $id,'order_type_id');	

		$this->mapper_provincias = array();

		$this->mapper_provincias [ 'TIERRA DEL FUEGO'] = array ( 60 );
	}

	//----------------------------------------------------------------------

	public function combo ( $seleccionado = -1, $mostrarSeleccione = true ) 
	{
		$sql = "select distinct $this->id_field, order_type from $this->table_name";

		comboBox ( $this->db , $sql ,'' ,'order_type_id' , $seleccionado , '' , false ,0, false);
	}

	//----------------------------------------------------------------------

	public function titulo () 
	{
		echo $this->get_detail(order_type);

	}

	//----------------------------------------------------------------------

	public function combo_reestringido ( $provincia )
	{
		$key = trim ( strtoupper ( $provincia ) );

		$sql = "select distinct $this->id_field, order_type from $this->table_name";

		comboBox ( $this->db , $sql ,'' ,'order_type_id' , -1 , '' , false ,0, false );

	}	

}
?>