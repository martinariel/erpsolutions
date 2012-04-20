<?php

	class cls_lines_config extends cls_iface_config 
	{
	
		function __construct (cls_sql &$db,$param=0) 
		{
			$lines = new cls_interface_lines($db);
			
			parent::__construct($db,$lines,'lines_config','ajax_iface_lines.php',$param);
			
			$this->agregarObjeto ( new cls_product          ( $db ) );
			$this->agregarObjeto ( new cls_interface_header ( $db ) );
		}
		
	}
?>