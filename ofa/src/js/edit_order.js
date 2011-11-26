function validar () {
	//Reglas de validacion
	var msg = ""
	var pedidos = Number ( $('cantidad').innerHTML);
	var term_id = $('term_id').value;
	var pay_term_id = $('pay_term_id').value;

	if ( pedidos <= 0 ) 	msg += "- Por lo menos debe seleccionar un producto.\n";
	if (term_id == 0) 	msg += "- Debe seleccionar los términos de pago.\n"
	if (pay_term_id == 0) 	msg += "- Debe seleccionar la condicion de pago.\n"
	
	validarNumeroPedido(msg);
	
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
	

function validarNumeroPedido(mensaje) {
	
	var numero = $('txt_numero_pedido').value;

	mensaje += (!isNaN(Number(numero)) && numero != '')? '' : "- Ingrese el numero de pedido de formulario (borde superior derecho).\n";

	if (mensaje == '')
	{
		var obj_ajax = nuevoAjaxEdit();
		
		var url = "xml_numero_pedido.php?param=";
		url += numero;
		url += "&t=" + new Date().getTime();
	
		obj_ajax.open("GET", url, true);
		
		obj_ajax.onreadystatechange = function(){
			if (obj_ajax.readyState == 4) {
				if (obj_ajax.responseText == "OK") 
				{
					if (confirm ('Confirma el pedido?') ) 
					{
						$('frmEdit').submit();
					}
				}
				else
				{
					mensaje += "- El número de pedido de formulario ya existe.\n";
					alert(mensaje);
				}
			}
		};
		obj_ajax.send(null);
	}
	else 
	{
		alert(mensaje);	
	}
}

function enviarOrden () {
	validar();
}
