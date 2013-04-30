<?php

	/*

	Clase de manejo de la tbla oe_lines_iface_all
	@author Martin Fernandez
	*/

	class cls_interface_lines extends cls_sql_table 
	{
		function __construct ( &$db, $id = 0 ) 
		{
			parent::__construct ( $db, 'oe_lines_iface_all',$id,'line_id');
		}

		//----------------------------------------------------------------------

		public function InsertLocal (
								cls_interface_header 	&$header		,
								cls_customer 			&$customer		, 
								cls_list_price 			&$list			,
								cls_terms 				&$terms			, 
								cls_salesrep 			&$salesrep		,
								cls_product_container 	&$products		,
								cls_pay_terms 			&$pay_term		,
								cls_order_type 			&$order_type	,
								cls_cpv2                &$cpv2          ,
								cls_cpv3                &$cpv3          ,
								cls_transaction 		&$tran          )
		{
		
			$obj_interfaz = new cls_lines_config($this->db);

			foreach ($products->arrProducts as $product) 
			{

				$matrizObj = array ( 
					$header   , $customer , $list       ,
					$terms    , $salesrep , $product    , 
					$pay_term , $tran     , $order_type ,
					$cpv2     , $cpv3 
				);

				$obj_interfaz->ejecutarMapeo ($this, $matrizObj);

			}
		}
	}

?>