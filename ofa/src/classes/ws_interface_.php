<?php
	require_once("nusoap-php5/lib/nusoap.php");
	require_once("../global.php");
	
	
			
	$server = new soap_server();
	$server->configureWSDL('ofarWebInterfaceV1', 'http://ofar.com.ar/wInterface', false, 'rpc');
	
	function cambiarEstado ( $tran_id, $state_id, $user_id, $password) {
		$db   = new cls_sql();
		$user = new cls_user($db);
		
		if ( $user->_checkLogin($user_id,$password) ) {
			
			$tran = new cls_transaction($db, $tran_id);
			$tran->set_detail (state_id, $state_id);
			$tran->set_detail (last_modified, cls_utils::fechaHoraActual() );
			$tran->set_detail (modified_by, $user->get_id() );
			
			if ( $tran->execute_update() ) {
				$resultado = 'OK';
			}
			else {
				$resultado = 'ERROR: sql update';
			}
		}
		else {
			$resultado = 'ERROR: login failed';
		}
		
		return $resultado;
	}
		
		
	function getTransactionId($state_id) {
		$db   = new cls_sql();
		$tran = new cls_transaction($db);
		
		$sql = "select $tran->id_field from $tran->table_name where state_id = " . cls_sql::numeroSQL($state_id);
		
		$rs = $tran->db->ejecutar_sql($sql);
		
		if ($rs && !$rs->EOF) {
			return $rs->fields[0];
		}
		else
		{
			return 0;
		}
	}
	
	function downloadTransactionHeader ( $tran_id) {
		
		$db   = new cls_sql();
		$tran = new cls_transaction ( $db, $tran_id);
		
		return $tran->getTransactionHeader();
		
	}
	
	function downloadTransactionLines ( $tran_id) {
		
		$db   = new cls_sql();
		$tran = new cls_transaction ( $db, $tran_id);
		
		return $tran->getTransactionLines();
		
	}
	
	function downloadTransactionSql ($tran_id, $user_id, $password) {
	
		$db = new cls_sql();
		$user = new cls_user($db);
		
		if ( $user->_checkLogin($user_id,$password) ) {
			
			$db   = new cls_sql();
			$tran = new cls_transaction ( $db, $tran_id);
			
			return $tran->getTransactionSql();
		}
		else
		{
			return array();
		}
	}
	
	
	$server->wsdl->addComplexType(
		'tField',
		'complexType',
		'struct',
		'all',
		'',
		array(
			'field' => array('name' => 'field', 'type' => 'xsd:string'),
			'value' => array('name' => 'value', 'type' => 'xsd:string')
		)
	);	
	
	$server->wsdl->addComplexType(
	  'tFieldList',
	  'complexType', 
	  'array', 
	  '', 
	  'SOAP-ENC:Array', 
	  array(),
	  array(
	    array('ref' => 'SOAP-ENC:arrayType', 
	         'wsdl:arrayType' => 'tns:tField[]')
	  ),
	  'tns:tField'
	);
	
	$server->wsdl->addComplexType(
	  'tLines',
	  'complexType', 
	  'array', 
	  '', 
	  'SOAP-ENC:Array', 
	  array(),
	  array(
	    array('ref' => 'SOAP-ENC:arrayType', 
	         'wsdl:arrayType' => 'tns:tFieldList[]')
	  ),
	  'tns:tFieldList'
	);
	
	
	$server->wsdl->addComplexType(
	  'tSqlList',
	  'complexType', 
	  'array', 
	  '', 
	  'SOAP-ENC:Array', 
	  array(),
	  array(
	    array('ref' => 'SOAP-ENC:arrayType', 
	         'wsdl:arrayType' => 'xsd:string[]')
	  ),
	  'xsd:string'
	);
	
	
	
	
	$server->wsdl->addComplexType(
		'tTransaction',
		'complexType',
		'struct',
		'all',
		'',
		array(
			'header' => array('name' => 'header', 'type' => 'tns:tFieldList'),
			'lines'  => array('name' => 'lines' , 'type' => 'tns:tLines')
		)
	);
	
	
	$server->register('downloadTransactionHeader',                    // method name
		array('tran_id' => 'xsd:int'),          // input parameters
		array('return' => 'tns:tFieldList'),    // output parameters
		'http://ofar.com.ar/wInterface',        // namespace
		'http://ofar.com.ar/wInterface/#downloadTransactionHeader',       // soapaction
		'rpc',                           // style
		'encoded',                            // use
		'Download'  // documentation
	);
	
	$server->register('downloadTransactionLines',                    // method name
		array('tran_id' => 'xsd:int'),          // input parameters
		array('return' => 'tns:tLines'),    // output parameters
		'http://ofar.com.ar/wInterface',        // namespace
		'http://ofar.com.ar/wInterface/#downloadTransactionLines',       // soapaction
		'rpc',                           // style
		'encoded',                            // use
		'Download'  // documentation
	);
	
	$server->register('downloadTransactionSql',                    // method name
		array('tran_id' => 'xsd:int', 'user_id' => 'xsd:string','password' =>'xsd:string'),          // input parameters
		array('return' => 'tns:tSqlList'),    // output parameters
		'http://ofar.com.ar/wInterface',        // namespace
		'http://ofar.com.ar/wInterface/#downloadTransactionSql',       // soapaction
		'rpc',                           // style
		'encoded',                            // use
		'Download'  // documentation
	);
	
	$server->register('getTransactionId',
		array('state_id'=>'xsd:int') ,  
		array('return'=>'xsd:int'),
		'http://ofar.com.ar/wInterface',        // namespace
		'http://ofar.com.ar/wInterface/#getTransactionId',       // soapaction
		'rpc',                           // style
		'encoded',                            // use
		'Devuelve la primera transaccion en el estado solicitado'  // documentation
		);
	
	
	$server->register('cambiarEstado',
		array('tran_id'=>'xsd:int','state_id'=>'xsd:int','user_id'=>'xsd:string','password'=>'xsd:string' ) ,  
		array('return'=>'xsd:string'),
		'http://ofar.com.ar/wInterface',        // namespace
		'http://ofar.com.ar/wInterface/#cambiarEstado',       // soapaction
		'rpc',                           // style
		'encoded',                            // use
		'Cambia el estado de la transaccion'  // documentation
		);
	
		
	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	$server->service($HTTP_RAW_POST_DATA);

?>