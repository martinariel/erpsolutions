function validar () 
{
	//Reglas de validacion
	var msg = ""

	var pedidos     = Number ( $('cantidad'         ).innerHTML);
	var muestras    = Number ( $('cantidad_muestras').innerHTML);

	var term_id       = $('term_id'       ).value;
	var pay_term_id   = $('pay_term_id'   ).value;
	var order_type_id = $('order_type_id' ).value; 

	if ( pedidos       <= 0 && muestras <= 0 )	msg += "- Por lo menos debe seleccionar un producto.\n";
	
	if ( order_type_id == 0 )	msg += "- Debe seleccionar el tipo de pedido.\n"
	if ( term_id       == 0 )	msg += "- Debe seleccionar los términos de pago.\n"
	if ( pay_term_id   == 0 )	msg += "- Debe seleccionar la condicion de pago.\n"
	
	validarNumeroPedido(msg);
}

//----------------------------------------------------------------------

function nuevoAjaxEdit()
{
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
	
//----------------------------------------------------------------------

function validarNumeroPedido(mensaje) 
{
	
	var numero = $('txt_numero_pedido').value;

	mensaje += (!isNaN(Number(numero)) && numero != '' ) ? 
		'' : "- Ingrese el numero de pedido de formulario (borde superior derecho).\n";

	if (mensaje == '')
	{
		var obj_ajax = nuevoAjaxEdit();
		
		var url = "xml_numero_pedido.php?param=";
		url += numero;
		url += "&t=" + new Date().getTime();
	
		obj_ajax.open("GET", url, true);
		
		obj_ajax.onreadystatechange = function()
		{
			if (obj_ajax.readyState == 4) 
			{
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

//----------------------------------------------------------------------

function enviarOrden () 
{
	validar();
}

//----------------------------------------------------------------------

var productos_agregados = [];

function agregar_producto ()
{
	var id = document.frmProducto.id_producto.value;
	if ( id <= 0 || id == undefined )
		return;

	var producto = productos [ idxProductos[id] ];

	// TODO: Me fijo si ya esta agregado.

	productos_agregados.push ( producto );

	var indice = productos_agregados.length - 1;

	var tr  = document.createElement ("tr");
	var td  = document.createElement ("td");

	td.setAttribute ("width" , 50);

	var cantidad = document.createElement ( "input" );
	var hidden   = document.createElement ( "input" );
	var modo     = document.createElement ( "input" );

	cantidad.setAttribute ("name"  , "txt_" + indice );
	cantidad.setAttribute ("id"    , "txt_" + indice );
	cantidad.setAttribute ("style" , "border:0;font-size:12px;width:100%");

	hidden.setAttribute ("type" , "hidden" );
	hidden.setAttribute ("name" , "check_" + indice );
	hidden.value = id;

	modo.setAttribute ( "type" , "hidden" );
	modo.setAttribute ( "name" , "modo_" + indice );
	modo.value = producto.Modo;

	if ( producto.Modo > 0 )
	{
		cantidad.setAttribute ("tipo" , producto.Modo);
	}

	cantidad.onblur = function()
	{
		updatePrice ( indice );	
	};

	td.appendChild ( cantidad );
	td.appendChild ( hidden   );
	td.appendChild ( modo     );
	tr.appendChild ( td       );

	td = document.createElement("td");
	td.innerHTML = producto.Descripcion;
	tr.appendChild ( td );

	td = document.createElement("td");

	var precio = document.createElement("input");

	precio.setAttribute ("name"  , "precio_"    + indice );
	precio.setAttribute ("id"    , "unit_neto_" + indice );
	precio.setAttribute ("style" , "border:0;font-size:12px;width:100%");

	if ( producto.Modo > 0 )
	{
		precio.setAttribute ( "disabled" , "true" );
	}

	precio.value = producto.Precio;

	precio.onblur = function()
	{
		updatePrice ( indice );	
	};

	td.setAttribute ("width" , 70);

	td.appendChild ( precio );

	tr.appendChild ( td );

	td = document.createElement("td");
	td.innerHTML = producto.PrecioIva;
	td.setAttribute ("id" , "unit_" + indice );
	td.setAttribute ("width" , 70);
	tr.appendChild ( td );

	td = document.createElement("td");
	td.setAttribute ( "id" , "neto_" + indice );
	td.innerHTML = "0";
	tr.appendChild ( td );

	td = document.createElement("td");
	td.setAttribute ( "id" , "total_" + indice );
	td.innerHTML = "0";
	tr.appendChild ( td );

	document.getElementById("tabla_productos").appendChild ( tr );

	cantidad.focus();

	document.frmEdit.iter.value = productos_agregados.length;
}