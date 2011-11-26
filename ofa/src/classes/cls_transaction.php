<?php
	/*
	Clase de manejo de la tabla transactions
	
	Metodos para administrar la transaccion
	
	@author Martin Fernandez
	*/
	
	class cls_transaction extends cls_sql_table 
	{
		
		private $oInterface;
		private $numeroPedido;
		
		function __construct ( &$db , $id = 0) 
		{
			parent::__construct($db,'transactions', $id, 'transaction_id');
			$this->oInterface = new cls_interface($this->db);
			$this->setNumeroPedido(0);
		}

		//----------------------------------------------------------------------
		
		public function setNumeroPedido($numero)
		{
			$this->numeroPedido = $this->db->numeroSQL($numero);
		}

		//----------------------------------------------------------------------
		
		public function getNumeroPedido()
		{
			return $this->numeroPedido;
		}

		//----------------------------------------------------------------------
		
		public function validarNumeroPedido()
		{	
			if ($this->getNumeroPedido() == 0)
			{
				return false;
			}
			else 
			{
				$sql = "select transaction_id from transactions where numero_pedido = " .
						$this->db->numeroSQL($this->getNumeroPedido());
			
				$rs = $this->db->ejecutar_sql($sql);
				
				return ($rs)? $rs->EOF: false;	
			}
		}

		//----------------------------------------------------------------------
		
		private function tablaFiltros() 
		{
			$user_id = cls_sql::numeroSQL( $_GET [ 'user'  ] );
			$codigo  = cls_sql::numeroSQL( $_GET [ 'code'  ] );
			$estado  = cls_sql::numeroSQL( $_GET [ 'state' ] );
			
			if (isset($_GET["impresiones"]) && trim($_GET["impresiones"]) != "" )
				$impresiones = cls_sql::numeroSQL($_GET['impresiones']);
			else
				$impresiones = 900000;
			
			$ret  = "( user_id        = $user_id      or 0      = $user_id     ) and ";
			$ret .= "( state_id       = $estado       or 0      = $estado      ) and ";
			$ret .= "( print_count    = $impresiones  or 900000 = $impresiones ) and ";
			$ret .= "( transaction_id = $codigo       or 0      = $codigo      )";
			
			return $ret;
		}

		//----------------------------------------------------------------------
		
		private function buscador() 
		{
			?>
			<script language="javascript">
				function goPage(numb) 
				{
					$('page'   ).value = numb;
					$('frmPage').submit();
				}
			</script>
			<?php
			
			$codigo = cls_sql::numeroSQL($_GET['code']);
			$user   = $_GET['user'];
			$estado = $_GET['state'];
			
			if (isset($_GET["impresiones"]) && trim($_GET["impresiones"]) != "")
				$impresiones = cls_sql::numeroSQL($_GET['impresiones']);
			else
				$impresiones = "";
			
			
			iniciarForm ('frmPage',cls_page::get_fileName(), 'GET','');
			hidden ( 'page' , cls_sql::numeroSQL($_GET['page']));
			hidden ( 'code' , $codigo );
			hidden ( 'user' , $user   );
			hidden ( 'state', $estado );
			cerrarForm();
			
			addDiv('linea');
			iniciarForm ('frmBuscador',cls_page::get_fileName(),'GET','');
			
			echo 'Código:&nbsp;';
			textBox ('','code',$codigo,false,'','',6,5);
			echo '&nbsp;';
			
			echo 'Creado:&nbsp;';
			$sql = 'select user_id, username from users order by username';
			comboBox ($this->db,$sql,'','user',$user,'' ,false);
			echo '&nbsp;';
			
			echo 'Impresiones:&nbsp;';
			textBox ('','impresiones',$impresiones,false,'','',6,5);
			echo '&nbsp;';
			
			echo 'Estado:&nbsp;';
			$sql = 'select state_id, description from transaction_states';
			comboBox ($this->db,$sql,'','state',$estado,'' ,false);
			echo '&nbsp;';
			
			
			echo '<br><br>';
			buttonTable ('Buscar', "$('frmBuscador').submit()");
			buttonTable ('Imprimir B&uacute;squeda', "$('frmPrint').submit()");
			cerrarForm();
			addDiv('linea');
			echo '<br>';
			
		}

		//----------------------------------------------------------------------
		
		public function detalle( $mostrar_saldo = false ) 
		{	
			$list       = new cls_list_price ( $this->db );
			$term       = new cls_terms      ( $this->db );
			$pay_terms  = new cls_pay_terms  ( $this->db );
			$order_type = new cls_order_type ( $this->db );
			
			$arrDetail = $this->getTransactionDetails();

			foreach ( $arrDetail['header'] as $key) 
			{
				$valor = $key ['value'];

				switch (strtolower($key['field'])) 
				{
					case 'customer_id'       : $customer_id  = intval ( $valor ) ; break;
					case 'custom_address_id' : $address_id   =  $valor           ; break;

					case 'order_type_id'     : $order_type->set_id ( intval ( $valor ) ) ; break;
					case 'price_list_id'     :       $list->set_id ( intval ( $valor ) ) ; break;
					case 'payment_term_id'   :       $term->set_id ( intval ( $valor ) ) ; break;
					case 'custom_pay_term'   :  $pay_terms->set_id ( intval ( $valor ) ) ; break;
					
				}
			}
			
			$condiciones = ( $address_id != 0 ) ? array ("address_id = $address_id") : array();
			
			$customer = ($this->get_detail(type_id) == 1) ? 
							new cls_customer        ( $this->db,$customer_id,$condiciones) : 
							new cls_custom_customer ( $this->db,$customer_id);

		
			$products = new cls_product_container($this->db,$list);
			
			foreach ($arrDetail['lines'] as $line)
			{
				$cantidad = 0;
				foreach ($line as $key) 
				{
					switch (strtolower($key['field'])) 
					{
						case 'inventory_item_id' : $product_id = intval ( $key['value'] ); break;
						case 'custom_modo'       : $modo       = intval ( $key['value'] ); break;
						case 'ordered_quantity'  : $cantidad   = intval ( $key['value'] ); break;
					}
				}
				$products->agregarExterno($product_id,$modo,$cantidad);
			}
			
			addDiv('linea','','');

			$customer->html_detail ( $mostrar_saldo );
			addDiv('linea','','');

			echo '<br>';
			$products->tablaProductosSeleccionados();
			
			echo '<br><table bgColor=#000000 cellspacing=1 cellpadding=2 width=590>';
			echo '<tr><td><b>Número de Pedido</b></td><td>';
			echo $this->get_detail(numero_pedido);

			echo '</td></tr><tr><td><b>Tipo de Pedido</b></td><td>';
			$order_type->titulo();
			
			echo '</td></tr><tr><td><b>Lista de precios</b></td><td>';
			$list->titulo();
			
			echo '</td></tr><tr><td width=150><b>Condiciones de pago</b></td><td>';
			$pay_terms->titulo();
			
			echo '</td></tr><tr><td width=150><b>Términos de pago</b></td><td>';
			$term->titulo();
			
			echo '</td><tr><td valign=top><b>Observaciones</b></td><td>';
			echo $this->get_detail(obs);
			echo '</td></tr></table>';
		}

		//----------------------------------------------------------------------
		
		private function cambiaf_a_normal($fecha)
		{ 
			//echo $fecha;
    		ereg( "([0-9]{2,2})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{2,2}):([0-9]{2,2})", $fecha, $mifecha); 
    		$lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1]." ".$mifecha[4].":".$mifecha[5];
    		return $lafecha; 
		} 

		//----------------------------------------------------------------------
		
		public function tabla() 
		{
			addJs ('js/transaction.1.2.js');
			
			$sql = $this->get_select_query();
			$sql .= " where $this->id_field >0";
			$sql .= ' and ' . $this->tablaFiltros();
			$sql .= " order by created DESC";
			
			$rows_pp = 35 ; //registros por pagina
			$pagina = cls_sql::numeroSQL($_GET['page']);
			$pagina = ( $pagina == 0 )? 1: $pagina;
			
			$rs = $this->db->ejecutar_sql_pagina($sql,$pagina,$rows_pp);
			
			$this->buscador();
			
			$rs1 = $this->db->ejecutar_sql($sql);
			$total_rows =$rs1->RecordCount();
			$rs1->Close();
			
			if ($rs && !$rs->EOF)
			{
				
				echo '<table bgColor=#000000 cellspacing=1 cellpadding=4>';
				echo '<tr><th>Código</th><th>N°Pedido</th><th>Creado</th><th>Fecha Creaci&oacute;n</th><th>Modificado</th><th>Fecha Modificaci&oacute;n</th><th>Impresiones</th><th>Estado</th><th>Cambiar<br>Estado</th><th></th></tr>';
				
				while (!$rs->EOF)
				{
					
					$state = new cls_transaction_state ($this->db, $rs->fields['state_id']);

					$color      = $state->get_detail ( html_color  );
					$state_desc = $state->get_detail ( description );
					
					echo '<tr>';
					
					echo '<td><a href=javascript:openDetails('. $rs->fields[$this->id_field] . ')><u>'.$rs->fields[$this->id_field].'</u></a></td>';
					echo '<td>'.$rs->fields['numero_pedido'].'</td>';
					
					$user = new cls_user ($this->db, $rs->fields ('user_id'));
					echo '<td>' . $user->get_detail (username) . '</td>';
					echo '<td>' . $this->cambiaf_a_normal($rs->fields['created']) . '</td>';
					
					$user = new cls_user ($this->db, $rs->fields ('modified_by'));
					echo '<td>' . $user->get_detail (username) . '</td>';
					echo '<td>' . $this->cambiaf_a_normal($rs->fields['last_modified'])  . '</td>';
					
					echo '<td><b>';
					echo ( $rs->fields['print_count'] > 0 )?'<span style=color:red>'.$rs->fields['print_count'] : '<span style=color:green>0';
					echo '</span></b></td>';
					
					echo "<td align=center style=background-color:$color><b>$state_desc</b></td>";
					
					echo '<td>';
					
					if ($state->comboEstados($rs->fields[$this->id_field]) ) 
					{
						echo '&nbsp;<a href=javascript:actionTransaction('.$rs->fields[$this->id_field].')><img src=img/go.gif></a>';
					}
					
					echo '</td>';
					
					if ( cls_sql::numeroSQL($rs->fields['state_id']) != 4 && 
					     cls_sql::numeroSQL($rs->fields['state_id']) != 3 && 
					     cls_sql::numeroSQL($rs->fields['state_id']) != 5 )
					{
						echo '<td><a href=javascript:openDetailsUpdate('. $rs->fields[$this->id_field] . ')><img src=img/edit.gif></a></td>';
					}
					else
					{
						echo '<td></td>';
					}
					
					echo '</tr>';
					
					//agrego el id al vector para la impresion multiple
					echo "<script language=javascript>$('t_id_print').value = $('t_id_print').value + ',' + '" .$rs->fields[$this->id_field]."';</script>";
					
					$rs->MoveNext();
					
				}
				
				echo '</table>';
				
				cls_sql::pager($total_rows,$pagina,$rows_pp);
			}
			else 
			{
				echo 'No hubo resultados para su búsqueda.';
			}
		}

		//----------------------------------------------------------------------
		
		public function newTransaction (
										cls_user               &$user       ,
										cls_customer           &$customer   ,
										cls_product_container  &$products   ,
										cls_list_price         &$list       ,
										cls_terms              &$term       ,
										cls_salesrep           &$salesrep   ,
										cls_pay_terms          &$pay_term   ,
										cls_order_type         &$order_type ,
										$observaciones                      ,
										$estadoInicial = -1
										) 
		{
			
			//para que la interfaz funcione con la tabla cu_customers
			
			if ($this->validarNumeroPedido())
			{
				if ($estadoInicial != -1)
				{
					$customer->set_table_name('ra_customers');
				}
					
				$observaciones = substr($observaciones, 0, 100);
				$strFechaHora  = cls_utils::fechaHoraActual();
			
				//seteo los detalles de la transaccion
			
				$this->set_detail ( obs           , $observaciones);
				$this->set_detail ( type_id       , ($estadoInicial==-1)?1:2);
				$this->set_detail ( user_id       , $user->get_id() );
				$this->set_detail ( modified_by   , $user->get_id() );
				$this->set_detail ( created       , $strFechaHora);
				$this->set_detail ( last_modified , $strFechaHora);
				$this->set_detail ( state_id      , $estadoInicial);
				$this->set_detail ( numero_pedido , $this->getNumeroPedido());
	
				//genero el registro de cabezera
				$header_id = $this->oInterface->iface_header->InsertLocal (
					$customer , $list       ,$term ,$salesrep,
					$pay_term , $order_type ,$this );
				
				//seteo el id del header al numero de insert
				$this->oInterface->iface_header->set_detail($this->oInterface->iface_header->get_id_field(), $header_id);
			
				//genero los registros de lineas
				$this->oInterface->iface_lines->InsertLocal($this->oInterface->iface_header,
														$customer , $list     , $term       , $salesrep ,
														$products , $pay_term , $order_type , $this     );
			
				$strFechaHora = cls_utils::fechaHoraActual();
			
				//vinculo la transaccion con el registro de cabezera
				$this->set_detail(header_id,$header_id);
			
				if ($this->execute_insert('local')) 
				{
				
					$sql = "select max($this->id_field) from $this->table_name";
					$rs = $this->db->ejecutar_sql($sql);
				
					if (!$rs->EOF) 
					{
						header("Location: detail_transaction_full.php?id=".$rs->fields[0]."&modo=1" );
					}
					else
					{
						header("Location: detail_transaction_full.php" );
					}
				}
				else 
				{
					header("Location: detail_transaction_full.php" );
				}
			}
		}

		//----------------------------------------------------------------------
		
		public function transferTransaction()
		{
			$id  = cls_sql::numeroSQL($this->get_id());
			
			$header_id = $this->get_detail(header_id);
			
			$this->oInterface->iface_header->set_id($header_id);
			
			if ( $this->get_detail(state_id) != 1) 
			{
				 $this->set_detail ( state_id    , 1);
				 $this->set_detail ( modified_by ,$_SESSION['uid']);
				
				if ($this->execute_update()) 
				{
					$this->tituloTransaccion();
					echo 'Ha sido transferida con éxito';
				}
			}
		}

		//----------------------------------------------------------------------
		
		public function confirmTransaction() 
		{
			$this->set_detail(state_id, 2);
			$this->set_detail(modified_by,$_SESSION['uid']);

			if ($this->execute_update()) 
			{
				$this->tituloTransaccion();
				echo 'Ha sido confirmada con éxito';
			}
		}

		//----------------------------------------------------------------------
		
		public function retenerTransaction() 
		{
			$this->set_detail(state_id, 7);
			$this->set_detail(modified_by,$_SESSION['uid']);

			if ($this->execute_update()) 
			{
				$this->tituloTransaccion();
				echo 'Ha sido retenida con éxito';
			}
		}

		//----------------------------------------------------------------------
		
		public function cancelTransaction ()
		{
			$this->set_detail(modified_by,$_SESSION['uid']);
			$this->set_detail(state_id, 3);
			
			if ($this->execute_update())
			{
				$this->tituloTransaccion();
				echo 'Ha sido cancelada con éxito';
			}
		}

		//----------------------------------------------------------------------
		
		private function tituloTransaccion() 
		{
			$id = $this->get_id();
			echo "<b>Transacción: $id </b><br>";
		}

		//----------------------------------------------------------------------
		
		public function getTransactionDetails() 
		{
		
			$header_id = $this->get_detail(header_id);
			$this->oInterface->iface_header->set_id ($header_id);
		
			$header = $this->oInterface->iface_header->get_array_recordset();
			$lines  = array(); 
			
			$sql = $this->oInterface->iface_lines->get_select_query();
			$sql .= ' where '.$this->oInterface->iface_header->get_id_field().' = ' . cls_sql::numeroSQL( $header_id) ;
			
			$rs = $this->db->ejecutar_sql ( $sql );
			
			if ( $rs) 
			{
				while ( !$rs->EOF ) 
				{
					$this->oInterface->iface_lines->set_details( $rs->fields );
					array_push ( $lines, $this->oInterface->iface_lines->get_array_recordset() );
					$rs->MoveNext();
				}
			}
			return array ('header'=> $header, 'lines' => $lines);
		}
		
		//----------------------------------------------------------------------

		public function getTransactionHeader() 
		{
			$header_id = $this->get_detail(header_id);
			$this->oInterface->iface_header->set_id ($header_id);
		
			$header = $this->oInterface->iface_header->get_array_recordset();
			
			return $header;
		}

		//----------------------------------------------------------------------
	
		public function getTransactionLines() 
		{
		
			$header_id = $this->get_detail(header_id);
			$this->oInterface->iface_header->set_id ($header_id);
		
			$lines  = array(); 
			
			$sql = $this->oInterface->iface_lines->get_select_query();
			$sql .= ' where '.$this->oInterface->iface_header->get_id_field().' = ' . cls_sql::numeroSQL( $header_id) ;
			
			$rs = $this->db->ejecutar_sql ( $sql );
			
			if ( $rs) 
			{
				while ( !$rs->EOF ) 
				{
					$this->oInterface->iface_lines->set_details( $rs->fields );
					array_push ( $lines, $this->oInterface->iface_lines->get_array_recordset() );
					$rs->MoveNext();
				}
			}
			
			return $lines;
		}
		
		//----------------------------------------------------------------------
		
		public function getTransactionSql() 
		{
			$driver = 'oracle';
			
			$string_sql = '';
			
			$header_id = $this->get_detail(header_id);
			$this->oInterface->iface_header->set_id($header_id);
			
			$config = new cls_header_config($this->db,1);
			
			$config->setearCamposNoInsert($this->oInterface->iface_header);
			
			$ret  = array(); 
			
			if ($this->get_detail(state_id) == 1 || $this->get_detail(state_id) == 2) 
			{
			
				array_push ($ret,  $this->oInterface->iface_header->sqlInsert($driver) );
				
				$config = new cls_lines_config($this->db,1);
				$config->setearCamposNoInsert($this->oInterface->iface_lines);
				
				$sql = $this->oInterface->iface_lines->get_select_query();
				$sql .= ' where '.$this->oInterface->iface_header->get_id_field().' = ' . cls_sql::numeroSQL( $header_id) ;
				
				$rs = $this->db->ejecutar_sql ($sql);
				
				if ( $rs) 
				{
					while ( !$rs->EOF ) 
					{
						$this->oInterface->iface_lines->set_details( $rs->fields );
						array_push ( $ret, $this->oInterface->iface_lines->sqlInsert($driver) );
						$rs->MoveNext();
					}
				}	
			}
			
			return $ret;
			
		}
	}
	
?>