<?php

	class cls_header_config extends cls_iface_config {
		function __construct (cls_sql &$db, $param=0) {
			$header = new cls_interface_header($db);
			parent::__construct($db,$header,'header_config','ajax_iface_header.php',$param);
		}


		
	}
?>