<?php

	/*
	Clase contenedora de productos
	
	@author Martin Fernandez
	*/
	
	class cls_product_container 
	{
		
		private $db;
		private $list_price;
		private $dummie;
		public  $arrProducts = array ();
		
		function __construct ( cls_sql &$db , cls_list_price &$list_price) 
		{
			$this->db         = $db;
			$this->list_price = $list_price;
		}
		
		//----------------------------------------------------------------------
		
		private function getDummie()
		{
			if ( !isset($this->dummie) )
			{
				$this->dummie = new cls_product($this->db);
			}

			return $this->dummie;
		}

		//----------------------------------------------------------------------
		
		/*
		Cargo los productos en el vector arrProducts
		*/
		public function load ( $stringId = '*' , $cargarMuestras = false ) 
		{
			
			$dummie = $this->getDummie();
			$sql    = $dummie->get_select_query();
				
			if ( $stringId != '*' ) 
			{
				$fld  = $dummie->get_id_field();
				$sql .= " where  $fld in ($stringId) and ";
			}
			else
			{
				$sql .= ' where ';
			}
			
			$sql .= $this->condiciones();
			$sql .= ' order by description';
			
			$productos = array();

			//AGREGO LOS PRODUCTOS
			$this->pushVector( $sql , $productos , 0 );

			if ( $cargarMuestras )
			{
				$sql  = $dummie->get_select_query();
				$sql .= ' where ';
				$sql .= $this->condicionesPromo();
				$sql .= ' order by description';
				
				$this->pushVector( $sql , $productos , 2 );
			}

			$_SESSION['PRODUCTOS'] = $productos;
		}

		//----------------------------------------------------------------------
		
		private function condiciones()
		{
			$sql = "segment1 < '899' ";
			$sql = $sql . ' and organization_id = 105';
			$sql = $sql . " and segment1 like '%-01-%'";
			$sql = $sql . " and segment1 < '899-01'";
			return $sql;
		}
		
		//----------------------------------------------------------------------

		private function condicionesPromo() 
		{
			$sql = "segment1 < '899' ";
			$sql = $sql . " and organization_id = 105";
			$sql = $sql . " and segment1 <= '800-80-6'";
			$sql = $sql . " and segment1 >= '800-80-5'";
			return $sql;
		}	
		
		//----------------------------------------------------------------------

		private function condicionFlete() 
		{
			return "segment1 = '999-99-99'";
		}

		//----------------------------------------------------------------------
		
		private function pushVector( $sql , &$productos , $modo  )
		{
			$ret = array();

			$rs = $this->db->ejecutar_sql($sql);

			if ( $rs && !$rs->EOF)
			{
				$arr = $rs->GetRows();
				
				foreach ($arr as $arr1) 
				{
					$obj = new cls_product ( $this->db, $arr1[0]);	

					$obj->set_modo ( $modo );
					$obj->set_details($arr1);

					array_push ( $this->arrProducts , $obj     );
					array_push ( $productos         , $arr1[0] );

					$ret = &$this->arrProducts[count($this->arrProducts)-1];
				}	
			}

			return $ret;
		}
		
		//----------------------------------------------------------------------

		private function addProduct ( $id , $modo = 0 ) 
		{
			$dummie = new cls_product($this->db);

			$sql         = $dummie->get_select_query();
			$id	         = cls_sql::numeroSQL($id);
			$fld         = $dummie->get_id_field();
			$condiciones = ( $modo > 0 )? $this->condicionesPromo() : $this->condiciones();
			$sql         = "$sql where  $fld = $id and $condiciones";
			
			$productos = array();

			return $this->pushVector( $sql , $productos , $modo);
		}
		
		//----------------------------------------------------------------------
	
		public function cargarDeFormulario() 
		{
			$totalProductos = intval($_POST ['iter']); //numero de productos
			
			for ( $i = 0 ; $i < $totalProductos ; $i++)
			{
				$id       = $_POST ["check_$i" ];
				$precio   = $_POST ["precio_$i"];
				$modo     = $_POST ["modo_$i"  ];

				$cantidad = intval( $_POST ["txt_$i"] );

				if ( $id != '')
				{
					if ( $cantidad > 0 )
					{
						$this->agregarExterno( $id , $modo , $precio , $cantidad );
					}
				}
				
			}
		}
		
		//----------------------------------------------------------------------
		
		public function agregarExterno ( $product_id , $modo , $precio , $cantidad )
		{	
			if ( $product_id != 0 ) 
			{
				$producto = $this->addProduct ( $product_id , $modo );
				
				$producto->set_modo         ( $modo     );
				$producto->set_precioUnidad ( $precio   );
				$producto->set_cantidad     ( $cantidad );
			}
			
		}

		//----------------------------------------------------------------------
		
		public function json () 
		{
			$ret   = "[";
			$first = true;

			foreach ( $this->arrProducts as $arr ) 
			{	
				$id          = $arr->get_detail ( inventory_item_id );
				$precio      = $this->list_price->get_product_price ( $id ) + 0;
				$description = $arr->get_detail ( description );
				$precioIva   = $arr->precio_con_iva($precio);
				$modo        = $arr->get_modo();
				$exento      = $arr->exento() ? 'true' : 'false';

				if ( !$first)
				{
					$ret .= ",\n";
				}

				$ret .= '{ "Id":'          . $id          . ',';
				$ret .= ' "Precio":'       . $precio      . ',';
				$ret .= ' "Descripcion":"' . $description . '",';
				$ret .= ' "Modo":'         . $modo        . ',';
				$ret .= ' "Exento":'       . $exento      . ',';
				$ret .= ' "PrecioIva":'    . $precioIva   . '}';

				$first = false;

			}

			$ret .= ']';

			return $ret;
		}

		//----------------------------------------------------------------------

		/*
		Tabla de seleccion de productos
		*/
		public function tablaProductos () 
		{		
			//Javascript !!!
			addJs('js/products_table.js');
			hidden ('iter',0); 
			
			echo '<table id="tabla_productos" bgColor=#333 cellspacing=1 cellpadding=2 width=700>';
			echo '<tr><th>Cantidad</th><th>Producto</th><th>Precio<br>sin Iva</th><th>Precio<br>con Iva</th><th>Total Neto</th><th>Total</th></tr>';

			echo '</table>';

			echo '<table bgColor=#333 cellspacing=1 cellpadding=2 width=700>';
			$this->tablaSumatoria();
			echo '</table>';			
		}

		//----------------------------------------------------------------------
		
		private function tablaSumatoria( $iva      = 0 , $neto = 0 , $total = 0 ,
			                             $cantidad = 0 , $cantidadMuestras  = 0 )
		{
		
		?>
			<tr>
				<td colspan="6">
					<table border="0" align="right">
						<tr>
							<td><b>Iva:</b></td><td>$<div id="iva" style="display:inline"><?php echo round($iva,2) ?></div></td>
						</tr>
						<tr>
							<td><b>Precio sin Iva:</b></td><td>$<div id="neto" style=display:inline><?php echo round($neto,2) ?></div></td>
						</tr>
						<tr>
							<td><b>Precio Total:</b></td><td>$<div id="precio" style=display:inline><?php echo round($total,2) ?></div></td>
						</tr>
						<tr>
							<td><b>Total Unidades:</b></td><td><div id="cantidad" style=display:inline><?php echo $cantidad ?></div></td>
						</tr>
						<tr>
							<td><b>Total Muestras:</b></td><td><div id="cantidad_muestras" style=display:inline><?php echo $cantidadMuestras ?></div></td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
		}

		//----------------------------------------------------------------------
		
		public function tablaProductosSeleccionados() 
		{
			echo '<table bgColor="#333" cellspacing="1" cellpadding="2" width="590">';
			echo '<tr><th>Cantidad</th><th>Producto</th><th>Precio<br>sin Iva</th><th>Precio<br>con Iva</th><th>Total</th></tr>';
		
			$sumatoriaTotalConIva = 0;
			$sumatoriaTotalSinIva = 0;
			$sumatoriaCantidad	  = 0;
			$sumatoriaMuestras    = 0;
			
			foreach ($this->arrProducts as $producto)
			{
				
				$cantidad 	 = $producto->get_cantidad();
				$precio   	 = $producto->get_precioUnidad();
				$precioIva	 = $producto->precio_con_iva($precio);
				$precioTotal = $producto->get_precioTotalIva();
				
				$sumatoriaTotalConIva +=  $precioTotal;
				$sumatoriaTotalSinIva += ($precio * $cantidad);
				
				if ($producto->get_modo() == 2 ) 
				{
					//Es una muestra
					$sumatoriaMuestras += $cantidad;
					
				}
				else
				{
					$sumatoriaCantidad += $cantidad;
				}
				
				echo '<tr>';
				echo "<td width=50>$cantidad</td>";
				
				if ( $producto->get_modo() == 1) 
					echo '<td><span style=color:green><b>'.$producto->get_detail(description).'</b></span></td>';
				else
					echo "<td><b>".$producto->get_detail(description).'</b></td>';
				
				echo "<td>$precio</td>";
				echo "<td>$precioIva</td>";
				echo "<td>$precioTotal</td>";
				
				echo '</tr>';
			}
			
			$sumatoriaIva = $sumatoriaTotalConIva - $sumatoriaTotalSinIva;
			$this->tablaSumatoria($sumatoriaIva,$sumatoriaTotalSinIva ,$sumatoriaTotalConIva,$sumatoriaCantidad,$sumatoriaMuestras);
			
			echo '</table>';
		}
	}
?>