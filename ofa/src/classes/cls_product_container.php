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
			$this->db = $db;
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
		public function load ( $stringId = '*') 
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
			
			//AGREGO LOS PRODUCTOS
			$this->pushVector($sql);
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
		
		public function comboProductos ($nombre,$indice=0) 
		{
		
			$idFld = $this->getDummie()->get_id_field();
			$nameFld = 'description';
			$tabla = $this->getDummie()->get_table_name();

			$sql = "select $idFld,$nameFld from $tabla";
			$sql.= ' where ' . $this->condicionesPromo();
			$sql.= ' order by segment1';
			
			comboBox ( $this->db, $sql,"", $nombre,0,"setCheck(this.value, $indice)",false,1);
			//javascript para setear el check del combo por default
			?>
			<script language=javascript>
				var combo = $('<?php echo $nombre ?>');
				
				if (combo) {
					setCheck(combo.value, <?php echo $indice ?>);
				}
			</script>
			<?php
		}

		//----------------------------------------------------------------------
		
		private function pushVector($sql)
		{
			$ret = array();
			$rs = $this->db->ejecutar_sql($sql);
			if ( $rs && !$rs->EOF)
			{
				$arr = $rs->GetRows();
				
				foreach ($arr as $arr1) 
				{
					$obj = new cls_product ($this->db,$arr1[0]);
					$obj->set_details($arr1);
					array_push ($this->arrProducts, $obj);
					$ret = &$this->arrProducts[count($this->arrProducts)-1];
				}	
			}
			return $ret;
		}
		
		//----------------------------------------------------------------------

		private function addProduct ($id,$modo=0) 
		{
			$dummie = new cls_product($this->db);

			$sql         = $dummie->get_select_query();
			$id	         = cls_sql::numeroSQL($id);
			$fld         = $dummie->get_id_field();
			$condiciones = ($modo == 2)? $this->condicionesPromo() : $this->condiciones();
			$sql         = "$sql where  $fld = $id and $condiciones";
			
			return $this->pushVector($sql);
		}
		
		//----------------------------------------------------------------------
	
		public function cargarDeFormulario() 
		{
			$totalProductos = intval($_POST ['iter']); //numero de productos
			
			for ( $i = 0 ; $i < $totalProductos; $i++)
			{
				$valor = $_POST ["check_$i"];
				
				if ( $valor != '')
				{
					$cantidad = intval($_POST ["txt_$i"]);
					if ($cantidad > 0)
					{
						$modo = intval($_POST ["modo_$i"]);
						$this->agregarExterno($valor,$modo,$cantidad);
					}
				}
				
			}
		}
		
		//----------------------------------------------------------------------
		
		public function agregarExterno ( $product_id , $modo , $cantidad )
		{
			$precio	= $this->list_price->get_product_price ( $product_id);
			
			if ($product_id != 0 ) 
			{
				$producto = $this->addProduct ( $product_id , $modo);
				
				$producto->set_modo         ( $modo     );
				$producto->set_precioUnidad ( $precio   );
				$producto->set_cantidad     ( $cantidad );
			}
			
		}

		//----------------------------------------------------------------------
		
		/*
		Tabla de seleccion de productos
		*/
		public function tablaProductos () 
		{
			$combos = 2;
		
			//Javascript !!!
			addJs('js/products_table.js');
			hidden ('iter',(count ($this->arrProducts) * 2)+$combos); 
			
			echo '<table bgColor=#000000 cellspacing=1 cellpadding=2 width=590>';
			echo '<tr><th>Lleva</th><th>Cantidad</th><th>Producto</th><th>Precio<br>sin Iva</th><th>Precio<br>con Iva</th><th>Total</th></tr>';
			
			$i=0;
			
			foreach ( $this->arrProducts as $arr ) 
			{	
				$id     = $arr->get_detail(inventory_item_id);
				$precio = $this->list_price->get_product_price ( $id );
				
				echo '<tr>';
				echo "<td>";
				check ("check_$i",$id,"updatePrice($i)",'');
				echo '</td>';
				
				echo '<td width=50>';
				textBox ('',"txt_$i",'0',false,'border:0;font-size:12px;width:100%',"onblur=updatePrice($i)",6);
				echo '</td>';
				
				echo "<td><b>"              . $arr->get_detail(description) . '</b></td>';
				echo "<td id=unit_neto_$i>" . $precio                       . '</td>';
				echo "<td id=unit_$i>"      . $arr->precio_con_iva($precio) . "</td>";
				echo "<div id=neto_$i style=display:none>0</div>";
				echo "<td id=total_$i>0</td>";
				echo '</tr>';
				
				hidden ("modo_$i", 0);
				
				$i++;
				
				//valor 0
				echo '<tr>';
				echo "<td id=celda_check_$i>";
				check ("check_$i",$id,"updateNoPrice($i)",'');
				echo '</td>';
				
				echo '<td width=50>';
				textBox ('',"txt_$i",'0',false,'border:0;font-size:12px;width:100%',"onblur=updateNoPrice($i)",6);
				echo '</td>';
				
				echo '<td><span style=color:green><b>'.$arr->get_detail(description).'</b></span></td>';
				echo '<td>0</td>';
				echo '<td>0</td>';
				echo "<div id=neto_$i style=display:none>0</div>";
				echo "<td id=total_$i>0</td>";
				echo '</tr>';
				
				hidden ("modo_$i", 1);
				$i++;
			}
			
			for ($j = $i ; $j < $i+$combos ; $j++) 
			{
			
				echo '<tr>';
				echo "<td id=celda_check_$j>";
				check ("check_$j",0,"updateNoPriceMuestras($j)",'');
				echo '</td>';
				
				echo '<td width=50>';
				textBox ('',"txt_$j",'0',false,'border:0;font-size:12px;width:100%',"onblur=updateNoPriceMuestras($j) tipo=3",6);
				echo '</td>';
				
				echo '<td>';
				$this->comboProductos("combo_$j",$j);
				echo '</td>';
				
				echo '<td>0</td>';
				echo '<td>0</td>';
				echo "<div id=neto_$j style=display:none>0</div>";
				echo "<td id=total_$j>0</td>";
				echo '</tr>';
				
				hidden ("modo_$j", 2);
				
			}
			
			echo '<tr>';
			echo "<td id=celda_check_$j>";
			check ("check_$j",0,"updateNoPriceMuestras($j)",'');
			echo '</td>';
			
			echo '<td width=50>';
			textBox ('',"txt_$j",'0',false,'border:0;font-size:12px;width:100%',"onblur=updateNoPriceMuestras($j) tipo=3",6);
			echo '</td>';
			
			echo '<td>';
			$this->comboProductos("combo_$j",$j);
			echo '</td>';
			
			
			echo '<td>0</td>';
			echo '<td>0</td>';
			echo "<div id=neto_$j style=display:none>0</div>";
			echo "<td id=total_$j>0</td>";
			echo '</tr>';
			
			hidden ("modo_$j", 2);
			

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
			echo '<table bgColor="#000000" cellspacing="1" cellpadding="2" width="590">';
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