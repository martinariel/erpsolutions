<?php
	require_once("nusoap-php5/lib/nusoap.php");
	require_once("../global.php");

	$server = new soap_server();
	$server->configureWSDL('wInterface', 'http://ofar.com.ar/wInterface');
	
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
	
	$server->register('cambiarEstado',
		array('tran_id'=>'xsd:integer','state_id'=>'xsd:integer','user_id'=>'xsd:string','password'=>'xsd:string' ) ,  
		array('return'=>'xsd:string'),
		'http://ofar.com.ar/wInterface',        // namespace
		'http://ofar.com.ar/wInterface#cambiarEstado',       // soapaction
		'rpc',                           // style
		'literal',                            // use
		'Cambia el estado de la transaccion'  // documentation
		);
		
	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	$server->service($HTTP_RAW_POST_DATA);

?>