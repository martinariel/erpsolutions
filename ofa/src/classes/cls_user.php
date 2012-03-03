<?php
/*
	Clase de usuario
	maneja la tabla users y efectua el login
	
	@author Martin Fernandez
	
*/

class cls_user extends cls_sql_table 
{ 
	
	function __construct(&$db,$id = 0) 
	{ 		
		parent::__construct($db,'users', $id);						
		$this->id_field = 'user_id';
	}

	//----------------------------------------------------------------------
	
	public function _checkLogin($user, $pass) 
	{ 

		$ret = false;
		
		$strsql = 'select user_id,username,user_level_id from users u';
		$strsql .= ' where UPPER(u.username) = ' . cls_sql::cadenaSQL ( strtoupper ( $user ) ) ;
		$strsql .= ' and   u.password        = ' . cls_sql::cadenaSQL ( md5        ( $pass ) ) ;
		
		$rs = $this->db->ejecutar_sql ( $strsql ); 
		
		if ($rs)
		{
			if ( !$rs->EOF ) 
			{
				$this->_setSession($rs); 
				$ret = true;
			} 
			else 
			{ 
				$this->failed = true; 
				$this->_logout(); 
			}
		}
		
		return $ret;
	}

	//----------------------------------------------------------------------

	public function getNumerosPedido ()
	{
		$ret = array();

		for ( $i = $this->get_detail ( pedido_desde) ; $i <= $this->get_detail(pedido_hasta); $i++)
		{
			array_push ( $ret , $i );
		}

		$sql = "select numero_pedido from transactions where numero_pedido >= " . $this->get_detail ("pedido_desde") .
			   " and numero_pedido <= " . $this->get_detail ( "pedido_hasta" );

		$rs = $this->db->ejecutar_sql ( $sql );

		$existen = array();

		if ( $rs )
		{
			while ( !$rs->EOF )
			{
				array_push( $existen, $rs->fields[0] );
				$rs->MoveNext();	
			}
		}

		$ret = array_diff ( $ret , $existen );

		asort ( $ret );

		return $ret;
	}

	//----------------------------------------------------------------------

	public function validarRango ( $desde , $hasta ) 
	{
		$sql = "select user_id,username,pedido_desde,pedido_hasta from users where " .
		"(pedido_desde >= $desde       and pedido_desde <= $hasta      ) or " .
		"(pedido_hasta >= $desde       and pedido_hasta <= $hasta      ) or " .
		"($desde       >= pedido_desde and $desde       <= pedido_hasta) or " .
		"($hasta       >= pedido_desde and $hasta       <= pedido_hasta)";

		$rs = $this->db->ejecutar_sql ( $sql );

		$ret = "";

		if ( $rs )
		{
			while ( !$rs->EOF )
			{
				$id = $rs->fields[0];

				if ( $id + 0  != $this->id + 0 )
				{
					if ( $ret == "" )
						$ret .= "Error de solapamiento de rangos: \n\n";

					$ret .= "    - " . $rs->fields[1] . " ( ".$rs->fields[2]." - ".$rs->fields[3]." )\n";
				}

				$rs->MoveNext();
			}
		}

		if ( $ret == "")
			$ret = "OK";

		return $ret;
	}

	//----------------------------------------------------------------------
	
	private function _setSession ( &$rs ) 
	{
		$this->id = $rs->fields['user_id'];
	
		$_SESSION [ 'uid'        ] = $this->id + 0 ;
		$_SESSION [ 'username'   ] = htmlspecialchars($rs->fields['username']);
		$_SESSION [ 'user_level' ] = $rs->fields['user_level_id'] + 0;
		$_SESSION [ 'logged'     ] = true;	
	}
		
	//----------------------------------------------------------------------

	public function _logout()
	{
		session_defaults();
	}

	

}
?>