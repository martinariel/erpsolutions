var neto = 0;


function updatePrice ( id ) 
{

	var txt   = $ ( 'txt_'	     + id );

	var total = $ ( 'total_'	 + id );

	var neto  = $ ( 'neto_'	     + id );

	var unit  = $ ( 'unit_'	     + id );

	var lNeto = $ ( 'unit_neto_' + id );

	

	var producto = productos_agregados[id];



	if ( validarEntrada(txt) && validarEntrada(lNeto) ) 

	{

		var valorNeto = Number( lNeto.value );

		var cantidad  = Number( txt.value   );

		var valorIva  = producto.Exento ? valorNeto : valorNeto * 1.21;



		var nTotal = cantidad * valorIva;

		var nNeto  = cantidad * valorNeto;

		

		nTotal   = Math.round(nTotal  *1000)/1000;

		nNeto    = Math.round(nNeto   *1000)/1000;

		valorIva = Math.round(valorIva*1000)/1000;

		

		neto.innerHTML  = nNeto;

		total.innerHTML = nTotal;

		unit.innerHTML  = valorIva;

	}

	else 

	{

		neto.innerHTML  = "0";

		total.innerHTML = "0";

	}

	

	sumarPrecios();

	sumarCantidades();

}



//----------------------------------------------------------------------



function updateNoPriceMuestras(id) 

{

	var check = $('check_'+id);

	var txt   = $('txt_'  +id);



	if (check.checked) { validarEntrada(txt); }

	sumarPrecios();

	sumarCantidades();

}



//----------------------------------------------------------------------



function validarEntrada(txt)

{

	if ( isNaN(Number(txt.value))) 

	{

		txt.value= 0;

		alert ('La cantidad debe ser un numero');

		txt.focus();

		return false;

	}

	else

	{

		return true;

	}

}



//----------------------------------------------------------------------



function sumarCantidades() 

{

	var total            = productos_agregados.length;

	var cantidad         = 0;

	var cantidadMuestras = 0;

	var txt;

	var cantidad_p;

	var check;

	

	for ( var i = 0 ; i < total ; i++) 

	{

		txt        = $('txt_'+i);

		cantidad_p = txt.value;

		

		if ( txt.getAttribute( "tipo" ) ) 

		{

			cantidadMuestras += Number(cantidad_p);

		}

		else 

		{	

			cantidad  += Number(cantidad_p);

		}

	}

	$( 'cantidad_muestras' ).innerHTML = cantidadMuestras;

	$( 'cantidad'          ).innerHTML = cantidad;

}



//----------------------------------------------------------------------



function sumarPrecios() 

{

	var total = productos_agregados.length;

	var precio = 0;

	var layer;

	var precio_p ;

	var check;

	

	var layer_neto;

	var precio_neto = 0;

	var precio_neto_p;

	

	for ( var i = 0 ; i < total ; i++) 

	{

		check = $('check_' + i);

		layer = $('total_' + i);

		

		precio_p = layer.innerHTML;

		precio  += Number(precio_p);

		

		layer_neto    = $('neto_'+i);

		precio_neto_p = layer_neto.innerHTML;

		

		precio_neto  += Number(precio_neto_p);	

	}

	

	var iva;

	precio_neto = Math.round(precio_neto*1000)/1000;

	

	precio = Math.round(precio*1000)/1000;

	iva = precio-precio_neto;

	iva = Math.round(iva*1000)/1000;

	

	$('iva').innerHTML	  = iva;

	$('neto').innerHTML   = precio_neto;

	$('precio').innerHTML = precio;

}



//----------------------------------------------------------------------



function setCheck (valor, indice) 

{

	var check = $('check_'+indice);

	

	if (check)

	{

		check.setAttribute ('value', valor);

	}

}