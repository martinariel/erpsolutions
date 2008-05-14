<?php

	/*
	Clase de manejo de la tabla salesreps
	
	@author Martin Fernandez
	*/
	Class cls_salesrep extends cls_sql_table {
		
		function __construct(&$db = null,$id = 0){			
			parent::__construct($db,'salesreps', $id,'salesrep_id');				
		}
		
	}

?>