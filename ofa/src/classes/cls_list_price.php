<?php

	/*
	Clase de manejo de la tbla list_price
	@author Martin Fernandez
	*/

	class cls_list_price extends cls_sql_table
	{

		private $arrProduct_id;
		const OFAR_LIST_ID = 6010;

		function __construct ( &$db , $id = 0) 
		{
			parent::__construct ( $db , 'list_price', $id, 'list_header_id');
			if ($this->id == 0) 
			{
				$this->getDefault();
			}
		}

		//----------------------------------------------------------------------

		private function getDefault()
		{

			$sqlCombo='select l.list_header_id,l.name from list_price l'.
					' inner join mtl_system_items_b p on (l.product_id = p.inventory_item_id)'.
					" where flexfield1 = 'y' " .
					' group by l.list_header_id';

			
			$result = $this->db->ejecutar_sql($sqlCombo);

			if ($result && !$result->EOF)
			{
				$this->set_id( $result->fields[0] );
			}
		}

		//----------------------------------------------------------------------
		
		//html titulo de la lista de precios

		public function titulo () 
		{
			echo $this->get_detail(name);
		}

		

		//devuelve los product id que esten en la lista de precios

		public function productos_existentes () {

			if (!isset($this->arrProduct_id) ) 
			{
				$this->arrProduct_id = array();

				$sql = "select distinct product_id from $this->table_name where $this->id_field = $this->id";
				$rs = $this->db->ejecutar_sql ($sql);

				if ($rs && !$rs->EOF) 
				{
					while (!$rs->EOF) 
					{
						array_push ( $this->arrProduct_id , $rs->fields[0] );
						$rs->MoveNext();
					}
				}
			}

			$ret = '';

			for ( $i = 0; $i < count ($this->arrProduct_id); $i++) 
			{
				if ( $ret == '') 
				{
					$ret = $this->arrProduct_id [$i];
				}
				else
				{
					$ret = $ret . ',' .$this->arrProduct_id [$i];
				}
			}
			return $ret;
		}

		//----------------------------------------------------------------------

		/*
		Devuelve el precio del producto para la lista de precio seleccionada
		*/

		public function  get_product_price($product_id) 
		{

			$product_id = cls_sql::numeroSQL($product_id);

			$sql = "select operand,attribute1,attribute3,attribute2 from $this->table_name ".

					" where $this->id_field = $this->id and product_id = $product_id ";

			$rs = $this->db->ejecutar_sql ($sql) ;

		
			if ($rs && !$rs->EOF) 
			{
				return $rs->fields[0];
			}
			else 
			{
				//echo $sql;
			}
		}

		//----------------------------------------------------------------------

		/*

		Crea un combo selector de listas de precio que tengan al menos un producto en la tabla mtl_system_items_b

		*/

		public function comboSelector () 
		{
			//Javascript!! metodo comboSelector

			addJs ( 'js/list_price.js');

			$sqlCombo = 'select l.list_header_id,l.name from list_price l'.
					' inner join mtl_system_items_b p on (l.product_id = p.inventory_item_id)'.
					" where flexfield1 = 'y' " .
					' group by l.list_header_id';


			echo '<b>Lista de precios:</b> ';

			comboBox ($this->db,$sqlCombo,'','list_id',$this->get_id(),"changeList(this.value)",false);
		}
	}
?>