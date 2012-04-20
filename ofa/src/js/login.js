/*
	Funciones de login
	ver login.php, xml_login.php
	@author Martin Fernandez
*/

	//----------------------------------------------------------------------
	//----------------------------------------------------------------------
	//----------------------------------------------------------------------

	function doLogin ()
	{	
		var user_id = $F('txtUser');
		var pwd		= $F('txtPwd');
		
		var url  = 'xml_login.php';
		var pars = 'user='+user_id+'&pwd='+pwd+"&ms="+new Date().getTime();;
		
		$('resultado').innerHTML = 'Cargando';
		
		//Usamos Prototype
		var ajax  = new Ajax.Request( url, {
                                parameters : pars         ,
                                method     : "get"        ,
                                onComplete : processLogin }
		);
	}
	
	//----------------------------------------------------------------------
	
	function processLogin(obj)
	{
		resp = obj.responseText;
		if ( resp == 'auth_ok')
		{
			window.self.location = 'appmain.php';
		}
		else
		{
			$('resultado').innerHTML = 'Usuario/Contrase&ntilde;a incorrecto';
		}
	}

	//----------------------------------------------------------------------
	
	//Evento keyPress del campo password, forzo el enter a la funcion doLogin();
	function keyPressLogin(e)
	{
		var keynum;
		var enter = 13;
		
		if (window.event)
		{
			keynum = e.keyCode
		}
		else if (e.which){
			keynum = e.which
		}
		
		if (enter == keynum)
		{
			doLogin();
		}
	}