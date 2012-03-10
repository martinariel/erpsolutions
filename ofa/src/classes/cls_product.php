<?php



	/*

	Clase de manejo de la tabla mtl_system_items_b

	

	

	@author Martin Fernandez

	*/

	

	class cls_product extends cls_sql_table 

	{

		private $precioUnidad = 0;

		private $cantidad     = 0;

		private $modo		  = 0;

		

		const iva = 0.21;

		

		function __construct (cls_sql &$db, $id = 0 ) 

		{

			parent::__construct($db,'mtl_system_items_b', $id,'inventory_item_id');

		}



		//---------------------------------------------------------------------



		public function exento()

		{

			return strtolower($this->get_detail(global_attribute2)) != 't-bienes';

		}



		//----------------------------------------------------------------------

		

		//Devuelve el precio con iva si es que el producto no esta exento

		public function precio_con_iva ( $precio ) 

		{

			if ( !$this->exento() ) 

			{

				return round($precio  + ($precio*self::iva) , 2);

			}

			else 

			{

				return $precio;

			}

		}



		//----------------------------------------------------------------------

		

		public function set_modo ($modo) 

		{

			$this->modo = $modo;

			$this->set_detail(custom_modo, $this->modo);

		}



		//----------------------------------------------------------------------

		

		public function get_modo ()

		{

			return $this->modo;

		}



		//----------------------------------------------------------------------

		

		public function set_precioUnidad ( $precio )

		{

			$this->precioUnidad = $precio;

			$this->set_detail ( custom_price , $precio );

		}



		//----------------------------------------------------------------------

		public function get_precioUnidad()
		{
			return $this->precioUnidad;
		}



		//----------------------------------------------------------------------

		public function set_cantidad ( $cantidad ) 
		{

			$this->cantidad = $cantidad;
			$this->set_detail ( custom_quantity , $this->cantidad);
		}



		//----------------------------------------------------------------------

		

		public function get_cantidad ()

		{

			return $this->cantidad;

		}



		//----------------------------------------------------------------------

		

		public function get_precioTotalIva ()

		{

			return $this->cantidad * $this->precio_con_iva ( $this->precioUnidad );

		}



		//----------------------------------------------------------------------

		public function get_precioTotalSinIva ()
		{			

			return $this->cantidad * $this->precioUnidad;

		}	

	}

?>