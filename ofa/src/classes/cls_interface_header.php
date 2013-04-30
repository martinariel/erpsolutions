<?php
	/*
	Clase de manejo de la tbla oe_headers_iface_all
	@author Martin Fernandez
	*/

	class cls_interface_header extends cls_sql_table
	{
		function __construct ( &$db, $id = 0 ) 
		{
			parent::__construct ( $db, 'oe_headers_iface_all',$id,'orig_sys_document_ref');
		}

		//----------------------------------------------------------------------

		public function InsertLocal ( 
									cls_customer 	&$customer	 ,
									cls_list_price 	&$list		 ,
									cls_terms 		&$terms		 ,
									cls_salesrep 	&$salesrep	 ,
									cls_pay_terms  	&$pay_term	 ,
									cls_order_type 	&$order_type ,
									cls_cpv2        &$cpv2       ,
									cls_cpv3        &$cpv3       ,
									cls_transaction &$tran       )
		{
			$obj_interfaz = new cls_header_config ( $this->db);

			$matrizObj    = array ( 
				$customer   , $list     , $terms,
				$salesrep   , $pay_term , $tran , 
				$order_type , $cpv2     , $cpv3 
			);	

			$obj_interfaz->ejecutarMapeo($this,$matrizObj);
			return $this->get_detail($this->get_id_field());
		}

		//----------------------------------------------------------------------

		public function cambiarPayTerms(cls_pay_terms $nuevoPayTerm)
		{
			$this->set_detail('attribute1'     , $nuevoPayTerm->get_id());
			$this->set_detail('custom_pay_term', $nuevoPayTerm->get_id());
			$this->execute_update();

		}

		//----------------------------------------------------------------------

		public function cambiarTerms(cls_terms $nuevoTerms)
		{

			$dummie = new cls_interface_lines($this->db);

			$strsql = 'select ' . $dummie->get_id_field() . ' from '. $dummie->get_table_name() .
					  ' where ' . $this->get_id_field() . '=' . $this->get_id();

					
			$rs = $this->db->ejecutar_sql($strsql);


			if ($rs)
			{
				while (!$rs->EOF)
				{
					$dummie = new cls_interface_lines($this->db, $rs->fields[0]);

					$dummie->set_detail('payment_term_id',$nuevoTerms->get_id());

					$dummie->set_detail('payment_term', $nuevoTerms->get_detail('name'));

					$dummie->execute_update();

					$rs->moveNext();

				}

				$this->set_detail('payment_term_id', $nuevoTerms->get_id());

				$this->set_detail('payment_term', $nuevoTerms->get_detail('name'));

				$this->execute_update();

			}
			else 
			{
				echo $strsql;
			}

		}

	}

?>