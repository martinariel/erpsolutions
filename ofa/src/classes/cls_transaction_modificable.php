<?php
	/*
	Clase de manejo de la tabla transactions
	
	Metodos para administrar la transaccion
	
	@author Martin Fernandez
	*/
	
	class cls_transaction_modificable extends cls_transaction {
		
		function __construct ( &$db , $id = 0) {
			parent::__construct($db,$id);
		}
		
		
		
		public function detalle() {

			
			$list = new cls_list_price($this->db);
			$term = new cls_terms($this->db);
			$pay_terms = new cls_pay_terms($this->db);
			
			$arrDetail = $this->getTransactionDetails();

			foreach ( $arrDetail['header'] as $key) {
				switch (strtolower($key['field'])) {
					case 'customer_id':     $customer_id  = intval ( $key['value'] ) ; break;
					case 'price_list_id':   $list->set_id ( intval ($key['value'] ) ); break;
					case 'payment_term_id': $term->set_id ( intval ($key['value'] )); break;
					case 'custom_address_id': $address_id = $key['value'];break;
					case 'custom_pay_term': $pay_terms->set_id( intval ($key['value'] ) ); break;
				}
			}
			
			if ( $address_id != 0 ) {
				$condiciones = array ("address_id = $address_id");
			}
			else
			{
				$condiciones = array();
			}
			
			$customer = ($this->get_detail(type_id) == 1) ? new cls_customer($this->db,$customer_id,$condiciones) : new cls_custom_customer($this->db,$customer_id);

			
			
			$products = new cls_product_container($this->db,$list);
			
			foreach ($arrDetail['lines'] as $line){
				$cantidad=0;
				foreach ($line as $key) {
					switch (strtolower($key['field'])) {
						case 'inventory_item_id': $product_id = intval ( $key['value'] ); break;
						case 'custom_modo': $modo = intval ( $key['value'] ); break;
						case 'ordered_quantity': $cantidad = intval ( $key['value'] ); break;
					}
				}
				$products->agregarExterno($product_id,$modo,$cantidad);
			}
			
			addDiv('linea','','');
			$customer->html_detail();
			addDiv('linea','','');

			echo '<br>';
			$products->tablaProductosSeleccionados();
			
			echo '<br><table bgColor=#000000 cellspacing=1 cellpadding=2 width=590>';
			echo '<tr><td><b>Número de Pedido</b></td><td>';
			echo $this->get_detail(numero_pedido);
			
			echo '</td></tr><tr><td><b>Lista de precios</b></td><td>';
			$list->titulo();
			
			echo '</td></tr><tr><td width=150><b>Condiciones de pago</b></td><td>';
			$pay_terms->combo($pay_terms->get_id(),false);
			
			echo '</td></tr><tr><td width=150><b>Términos de pago</b></td><td>';
			$term->combo($term->get_id(),false);
			
			echo '</td><tr><td valign=top><b>Observaciones</b></td><td>';
			echo $this->get_detail(obs);
			echo '</td></tr></table>';
		}
		
		public function cambiarPayTerms(cls_pay_terms $nuevoPayTerms){
			global $user;
		
			$header = new cls_interface_header($this->db, $this->get_detail('header_id'));
			$header->cambiarPayTerms($nuevoPayTerms);
			
			$this->set_detail(modified_by, $user->get_id() );
			$this->set_detail(last_modified,cls_utils::fechaHoraActual());
			
			$this->execute_update();
		}
		
		
		public function cambiarTerms(cls_terms $nuevoTerms){
			global $user;
			
			$header = new cls_interface_header($this->db, $this->get_detail('header_id'));
			$header->cambiarTerms($nuevoTerms);
			$this->set_detail(modified_by, $user->get_id() );
			$this->set_detail(last_modified,cls_utils::fechaHoraActual());
			
			$this->execute_update();
		
		}
		
		
	}
	
?>