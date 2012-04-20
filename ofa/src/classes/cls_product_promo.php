<?php

	/*
	Clase de manejo de la tabla mtl_system_items_b
	
	
	@author Martin Fernandez
	*/
	
	class cls_product_promo extends cls_product 
	{
		function __construct (cls_sql &$db, $id = 0 ) 
		{
			parent::__construct($db,$id);
			$this->set_modo(1); //producto sin precio
		}
		
	}
?>