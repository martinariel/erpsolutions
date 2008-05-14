<?php

	/*
	Clase de manejo de la tbla oe_headers_iface_all
	
	@author Martin Fernandez
	*/
	Class cls_interface_header extends cls_sql_table{
	
		function __construct ( &$db, $id = 0 ) {
			parent::__construct ( $db, 'oe_headers_iface_all',$id,'orig_sys_document_ref');
		}
		
		public function InsertLocal(cls_customer &$customer,
									cls_list_price &$list,
									cls_terms &$terms,
									cls_salesrep &$salesrep,
									cls_pay_terms &$pay_term,
									cls_transaction &$tran
									){
						
				$obj_interfaz = new cls_header_config ( $this->db);
				$matrizObj = array ( $customer,$list,$terms,$salesrep, $pay_term, $tran);		
				$obj_interfaz->ejecutarMapeo($this,$matrizObj);
				return $this->get_detail($this->get_id_field());
		}
		
	}
?>