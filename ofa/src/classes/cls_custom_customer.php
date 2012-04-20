<?php

	Class cls_custom_customer extends cls_customer{
		
		function __construct( cls_sql &$db,$id=0) {
			parent::__construct($db,$id,array(),'cu_customers','customer_id');
		}
		
		public function html_detail(){
			?>
			<table align="center" width=700>
				<tr>
					<td colspan=2><b>N° de Cliente:</b> <?php echo $this->get_detail(customer_id)?></td>
				</tr>
				<tr>
					<td><b>Raz&oacute;n social:</b> <?php echo $this->get_detail(customer_name)?></td>
					<td><b>Nombre Fantasia:</b> <?php echo $this->get_detail(customer_name_phonetic)?></td>
				</tr>
				<tr>
					<td><b>CUIT:</b> <?php echo $this->get_detail(cuit)?></td>
					<td><b>CP:</b> <?php echo $this->get_detail(postal_code)?></td>
				</tr>
				<tr>
					<td><b>Provincia:</b> <?php echo $this->get_detail(province)?></td>
					<td><b>Direcci&oacute;n:</b> <?php echo $this->get_detail(address1)?></td>
				</tr>
				<tr>
					<td><b>Localidad:</b> <?php echo $this->get_detail(city)?></td>
					<td><b>Tel&eacute;fono:</b> <?php echo $this->get_detail(telephone)?></td>
				</tr>
				
			</table>
			<?php
			echo '<br>';
		}
		
	}
?>