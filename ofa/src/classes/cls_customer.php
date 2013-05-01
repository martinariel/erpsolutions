<?php

	/*
	
	Clase de manejo de customers , tabla ra_customers
	
	@author Martin Fernandez
	
	*/

	class cls_customer extends cls_sql_table 
	{
		
		function __construct (	cls_sql &$db,
								$id          = 0          , 
								$condiciones = array()    ,
								$tabla       = 'optional' ,
								$campo_id    = 'optional' )
		{			
								
			if ($campo_id == 'optional') $campo_id 	= 'customer_id';
			if ($tabla 	  == 'optional') $tabla 	= 'ra_customers';
			
			parent::__construct($db,$tabla, $id,$campo_id, $condiciones);
		}

		//----------------------------------------------------------------------
		
		public function html_detail ( $mostrar_saldo = false )
		{
			$mostrar_saldo = false;

			?>
			<table align="center" width="700">
				<tr>
					<td><b>N° de Cliente:</b> <?php echo $this->get_detail ( customer_number )?></td>
					<?php
					if ( $mostrar_saldo )
					{
					?>
					<td style="background-color:red;color:white;padding-left:4px;"><b>Saldo Adeudado:</b> <?php echo "$".$this->get_detail ( saldo )?></td>
					<?php
					}	
					else
					{
						echo "<td></td>";
					}	
					?>
				</tr>
				<tr>
					<td><b>Raz&oacute;n social:</b> <?php echo $this->get_detail ( customer_name          )?></td>
					<td><b>Nombre Fantasia:</b>     <?php echo $this->get_detail ( customer_name_phonetic )?></td>
				</tr>
				<tr>
					<td><b>CUIT:</b> <?php echo $this->get_detail ( cuit        )?></td>
					<td><b>CP:</b>   <?php echo $this->get_detail ( postal_code )?></td>
				</tr>
				<tr>
					<td><b>Provincia:</b>        <?php echo $this->get_detail(province)?></td>
					<td><b>Direcci&oacute;n:</b> <?php echo $this->get_detail(address1)?></td>
				</tr>
				<tr>
					<td><b>Localidad:</b>       <?php echo $this->get_detail ( city  )?></td>
					<td><b>Tel&eacute;fono:</b> <?php echo $this->get_detail ( phone )?></td>
				</tr>
				
			</table>
			<?php
			echo '<br>';
		}
		
		//----------------------------------------------------------------------

		public function comboSelectorDireccion($address_id, $salesrep_id = 0) 
		{
			$id = $this->get_id();
			
			$sql = "select count(*) from $this->table_name where $this->id_field = $id and ($salesrep_id = 0 or salesrep_id = $salesrep_id)";
			$rs = $this->db->ejecutar_sql($sql);
			
			if ( $rs && !$rs->EOF ) 
			{
				$total = $rs->fields[0] + 0;
				
				if ($total > 1) 
				{
				
					?>
					<table align="center" width="700">
						<tr>
							<td width="350"></td>
							<td align="left"><b>Cambiar Direcci&oacute;n de env&iacute;o:</b> 
							<?php 
							$sql = "select address_id, address1 from $this->table_name where $this->id_field = $id and ($salesrep_id = 0 or salesrep_id = $salesrep_id)";
							comboBox ($this->db,$sql,'','address_id',$this->get_detail(address_id),"$('frmAdd').submit()",false);
							?></td>
						</tr>
					</table>
					<?php
				}
			}
		}
		
		//----------------------------------------------------------------------
		
		//validate user->salesrep_id against this->get_detail(salesrep_id)
		public function validate(&$user)
		{

			if ( $user->get_detail ( clientes_todos )  == 1 )
				return true;

			return $user->get_detail(salesrep_id) == $this->get_detail(salesrep_id);
		}
		
		//----------------------------------------------------------------------
			
		public function printXml ($busqueda,$pos,$user_id)
		{
			$user = new cls_user ( $this->db , $user_id );

			$pos = cls_sql::numeroSQL($pos);
			$sql = "SELECT customer_id, customer_name_phonetic from ra_customers where ".
					" salesrep_id = (select salesrep_id from users where user_id = $user_id ) ".
					" and customer_name_phonetic like '$busqueda%' group by customer_id,customer_name_phonetic order by customer_name_phonetic limit $pos,100";

			if ( $user->get_detail ( clientes_todos ) == 1 )
			{
				$sql = "SELECT customer_id, customer_name_phonetic from ra_customers where ".
					"customer_name_phonetic like '$busqueda%' group by customer_id,customer_name_phonetic order by customer_name_phonetic limit $pos,100";	
			}
						
			$rs = $this->db->ejecutar_sql($sql);
			
			if ($pos == 0)
				echo '<complete>';
			else
				echo "<complete add='true'>";
			
			if ($rs)
			{
				while (!$rs->EOF)
				{
					$value = $rs->fields[0];
					$text  = $rs->fields[1];
					echo "<option value=\"$value\">$text</option>";
					$rs->MoveNext();
				}
			}
			echo '</complete>';
		}
		
	}
?>