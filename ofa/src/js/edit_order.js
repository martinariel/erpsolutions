function validar () {
	//Reglas de validacion
	var msg = ""
	var pedidos = Number ( $('cantidad').innerHTML);
	var term_id = $('term_id').value;
	var pay_term_id = $('pay_term_id').value;

	if ( pedidos <= 0 ) 	msg += "- Por lo menos debe seleccionar un producto.\n";
	if (term_id == 0) 		msg += "- Debe seleccionar los términos de pago.\n"
	if (pay_term_id == 0) 	msg += "- Debe seleccionar la condicion de pago.\n"
	
	msg+=validarNumeroPedido();
	
	if ( msg == "") {
		return true;
	}
	else
	{
		alert(msg);
		return false;
	}
}

function nuevoAjaxEdit(){
		var xmlhttp=false;
		try {
			xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e){
			try{
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			} 
			catch(E){
				xmlhttp=false;
			}
		}
		if (!xmlhttp && typeof XMLHttpRequest!='undefined'){xmlhttp=new XMLHttpRequest();} 
		return xmlhttp; 
	}
	

function validarNumeroPedido(){

	var retorno = "- Ingrese el numero de pedido de formulario (borde superior derecho).\n";
	
	var numero = $('txt_numero_pedido').value;
	
	if (!isNaN(Number(numero)) && numero != ''){
		var ajax = nuevoAjaxEdit();
		
		var url = "xml_numero_pedido.php?param=";
		url += numero;
		url += "&t=" + new Date().getTime();
	
		ajax.open("GET",url, false);
		
		ajax.onreadystatechange = function(){
			if (ajax.readyState == 4)
				retorno = (ajax.responseText == 'OK')? '' : "- El número de pedido de formulario ya existe.\n";
		};
		ajax.send(null);
	}
	
	return retorno;

}

function enviarOrden () {
	if (!validar()) return;
	
	if (confirm ('Confirma el pedido?') ) {
		$('frmEdit').submit();
	}

}
