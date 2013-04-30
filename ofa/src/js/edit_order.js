function validar () 
{
	//Reglas de validacion
	var msg = ""

	var pedidos     = Number ( $('cantidad'         ).innerHTML);
	var muestras    = Number ( $('cantidad_muestras').innerHTML);

	var term_id       = $('term_id'       ).value;
	var pay_term_id   = $('pay_term_id'   ).value;
	var order_type_id = $('order_type_id' ).value; 
    var cpv2_id       = $('cpv2_id'       ).value;
    var cpv3_id       = $('cpv3_id'       ).value;
    
	if ( pedidos       <= 0 && muestras <= 0 )	msg += "- Por lo menos debe seleccionar un producto.\n";

	if ( order_type_id == 0 )	msg += "- Debe seleccionar el tipo de pedido.\n"
	if ( term_id       == 0 )	msg += "- Debe seleccionar los términos de pago.\n"
	if ( pay_term_id   == 0 )	msg += "- Debe seleccionar la condicion de pago.\n"
	if ( cpv2_id       == 0 )   msg += "- Debe seleccionar CPV2.\n";
	if ( cpv3_id       == 0 )   msg += "- Debe seleccionar CPV3.\n";

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
		alert ( mensaje );	
	}
}

//----------------------------------------------------------------------

function enviarOrden () 
{
	validar();
}

//----------------------------------------------------------------------

function swap_id ( old_id , new_id, type , value ) 
{
	document.getElementById ( old_id ).setAttribute ( "id" , new_id );

	if ( type != "id" && name != undefined )
	{
		document.getElementById ( new_id ).setAttribute ( type , value )	
	}
}

//----------------------------------------------------------------------

function reasignar_ids ( indice )
{
	for ( i = indice + 1 ; i < productos_agregados.length  ; i++)
	{
		var n = i - 1;

		swap_id ( "fila_"      + i , "fila_"      + n , "id"   );
		swap_id ( "txt_"       + i , "txt_"       + n , "name" , "txt_"    + n );
		swap_id ( "check_"     + i , "check_"     + n , "name" , "check_"  + n );
		swap_id ( "modo_"      + i , "modo_"      + n , "name" , "modo_"   + n );
		swap_id ( "unit_neto_" + i , "unit_neto_" + n , "name" , "precio_" + n );

		document.getElementById ( "unit_neto_" + n ).setAttribute ("indice" , n );
		document.getElementById ( "txt_"       + n ).setAttribute ("indice" , n );

		swap_id ( "unit_"   + i , "unit_"   + n , "id" );
		swap_id ( "neto_"   + i , "neto_"   + n , "id" );
		swap_id ( "total_"  + i , "total_"  + n , "id" );
		swap_id ( "borrar_" + i , "borrar_" + n , "id" );

		document.getElementById ("borrar_" + n ).setAttribute ("indice" , n );
	}


}

//----------------------------------------------------------------------

var old_price = 0;

//----------------------------------------------------------------------

var productos_agregados = [];

function agregar_producto ()
{
	var id = z.getSelectedValue();
	if ( id <= 0 || id == undefined )
		return;

	z.setComboText("");
	z.unSelectOption();

	var producto = productos [ idxProductos[id] ];

	// TODO: Me fijo si ya esta agregado.

	productos_agregados.push ( producto );

	var indice = productos_agregados.length - 1;

	var tr  = document.createElement ("tr");
	tr.setAttribute ( "id" , "fila_" + indice );

	var td  = document.createElement ("td");

	td.setAttribute ("width" , 50);

	var cantidad = document.createElement ( "input" );
	var hidden   = document.createElement ( "input" );
	var modo     = document.createElement ( "input" );

	cantidad.setAttribute ( "name"  , "txt_" + indice );
	cantidad.setAttribute ( "id"    , "txt_" + indice );
	cantidad.setAttribute ( "indice", indice );
	cantidad.setAttribute ( "style" , "border:0;font-size:12px;width:100%");

	hidden.setAttribute ("type" , "hidden" );
	hidden.setAttribute ("name" , "check_" + indice );
	hidden.setAttribute ("id"   , "check_" + indice );
	hidden.value = id;

	modo.setAttribute ( "type" , "hidden" );
	modo.setAttribute ( "name" , "modo_" + indice );
	modo.setAttribute ( "id"   , "modo_" + indice );
	modo.value = producto.Modo;

	if ( producto.Modo > 0 )
	{
		cantidad.setAttribute ("tipo" , producto.Modo);
	}

	cantidad.onblur = function()
	{
		updatePrice ( Number ( this.getAttribute("indice")) );	
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

	precio.setAttribute ( "name"  , "precio_"    + indice );
	precio.setAttribute ( "id"    , "unit_neto_" + indice );
	precio.setAttribute ( "style" , "border:0;font-size:12px;width:100%");
	precio.setAttribute ( "indice" , indice );

	if ( producto.Modo > 0 )
	{
		precio.setAttribute ( "disabled" , "true" );
	}

	precio.value = producto.Precio;

	precio.onfocus = function ()
	{
		old_price = this.value;	
	}

	precio.onblur = function()
	{
		if ( this.value != old_price && Number ( this.value ) != 0 )
		{
			this.value = old_price;
			alert ( "El precio solamente se puede editar a 0.")
			return;
		}

		updatePrice ( Number ( this.getAttribute("indice")));	
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

	td = document.createElement ( "td" );
	td.setAttribute ( "id" , "borrar_" + indice );
	td.innerHTML = "<img src='img/delete.gif'>";
	td.setAttribute ("indice" , indice);
	tr.appendChild ( td );

	td.onclick = function ()
	{
		var idx = this.getAttribute ("indice");
		
		$('fila_' + idx ).remove();

		reasignar_ids ( Number ( idx ) );
		productos_agregados.splice ( idx , 1 ); 

		sumarPrecios();
		sumarCantidades();
		
	}

	document.getElementById("tabla_productos").firstChild.appendChild ( tr );

	cantidad.focus();

	document.frmEdit.iter.value = productos_agregados.length;
}
