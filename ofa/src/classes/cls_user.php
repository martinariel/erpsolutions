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