<?php
	require_once("nusoap-0.9.5/lib/nusoap.php");
	require_once("../global.php");

			
	$server = new soap_server();
	$server->configureWSDL('ofarWebInterfaceV1', 'http://ofar.com.ar/wInterface', false, 'rpc');


	//-------------------------------------------------------------

	function iniciar ( $entitie )
	{
	
		global $db;
		$tablaTemp = $entitie . "_temp";

		// Vacio la tabla de proceso
		$db->ejecutar_sql ( "truncate table $tablaTemp");

		// TODO: Logueo el fin
	}

	//-------------------------------------------------------------
	
	function agregarRegistro ( $entitie , $registro )
	{
		global $db;

		$array   = explode ( "||||" , $registro );
		$campos  = array();
		$valores = array();

		$temp = $entitie . "_temp";

		for ( $i = 0 ; $i < count($array) ; $i++)
		{
			if ( trim($array[$i]) == '')
				continue;

			if ( $i == 0 )
			{
				$campos = explode ( "|;-|" , $array[$i]);
				continue;
			}

			$valores = explode ( "|;-|" , $array[$i] );

			$sql = "insert into $temp (";
			for ( $j = 0 ; $j < count($campos) ; $j++)
			{
				if ( $j > 0 )
				{
					$sql .= ",";
				}

				$sql .= $campos[$j];
			}

			$sql .= ") VALUES (";

			for ( $j = 0 ; $j < count($campos) ; $j++)
			{
				if ( $j > 0 )
				{
					$sql .= ",";
				}
				$sql .= $valores[$j];
			}

			$sql .= ")";

			$db->ejecutar_sql ( $sql );
		}
	}

	//-------------------------------------------------------------

	function finalizar ( $entitie )
	{
		global $db;

		//Vacio la tabla verdadera
		$db->ejecutar_sql ( "truncate table $entitie");
		
		$temp = $entitie."_temp";

		// Muevo los datos desde la temporal a la verdadera
		$db->ejecutar_sql ( "inserto into $entitie select * from $temp")

		// Vacio la temporal
		$db->ejecutar_sql ( "truncate table $temp");

		// TODO: Logueo el fin
			
	}

	//-------------------------------------------------------------
	//-------------------------------------------------------------
	//-------------------------------------------------------------
	
	$server->register('iniciar',                    // method name
		array('entitie' => 'xsd:string'),          // input parameters
		array('return' => 'xsd:boolean'),    // output parameters
		'http://ofar.com.ar/wInterface',        // namespace
		'http://ofar.com.ar/wInterface/#iniciar',       // soapaction
		'rpc',                           // style
		'encoded',                            // use
		'Download'  // documentation
	);
	
	$server->register('agregarRegistro',                    // method name
		array('entitie' => 'xsd:string' , 'registro' => 'xsd:string'),          // input parameters
		array('return' => 'xsd:boolean'),    // output parameters
		'http://ofar.com.ar/wInterface',        // namespace
		'http://ofar.com.ar/wInterface/#agregarRegistro',       // soapaction
		'rpc',                           // style
		'encoded',                            // use
		'Download'  // documentation
	);

	$server->register('finalizar',                    // method name
		array('entitie' => 'xsd:string'),          // input parameters
		array('return' => 'xsd:boolean'),    // output parameters
		'http://ofar.com.ar/wInterface',        // namespace
		'http://ofar.com.ar/wInterface/#iniciar',       // soapaction
		'rpc',                           // style
		'encoded',                            // use
		'Download'  // documentation
	);
	
		
	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	$server->service($HTTP_RAW_POST_DATA);

?>