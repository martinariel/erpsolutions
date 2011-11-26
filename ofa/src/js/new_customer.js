function guardar() {
	if(validar() ) { 
		$('frmNew').submit();
	}
}

function isCUIT(a,b,c) {
     if ((isNaN(a))||(isNaN(b))||(isNaN(c))) {
       return false;
     }else{
		return true;
		
       longo=a+b; Suma=0;
	   if (longo.length < 10) return false;
       for (i=0 ; i < longo.length ; i++) {
         Suma = Suma + Number(longo.charAt(i)) * (i<4?-i+5:-i+11);
       }
       v = 11- (Suma % 11);
       alerta="Digito verificador correspondiente: "+v+".\n";
       alerta+="STATUS: "+(v==c?"CORRECTO":"INCORRECTO");
       //alert(alerta);
	   return (v==c);
     }
     return false;
}

   
function validar() {

	var txtRazon 	= $('txtRazon').value;
	var txtFantasia = $('txtFantasia').value;
	
	var txtCuit 	= $('txtCuit');
	
	var txtDir 		= $('txtDir').value;
	var txtLoc 		= $('txtLoc').value;
	var txtProv 	= $('txtProv').value;
	var txtCp 		= $('txtCp').value;
	var txtTel 		= $('txtTel').value;
	
	var a = $('val1').value;
	var b = $('val2').value;
	var c = $('val3').value;
	
	txtCuit.value = a + b +c + '';


	//alert(txtCuit.value);
	var msg = '';
	
	if (txtRazon 	== '') msg += "- Complete Razón Social.\n";
	if (txtFantasia == '') msg += "- Complete el nombre de fantasia\n";
	
	if (!isCUIT(a,b,c)) msg += "- Ingrese un numero de cuit valido\n";
	
	if (txtDir 		== '') msg += "- Complete la dirección\n";
	if (txtLoc		== '') msg += "- Complete la localidad\n";
	if (txtProv		== '') msg += "- Complete la provincia\n";
	if (txtCp 		== '') msg += "- Complete el cp\n";
	if (txtTel 		== '') msg += "- Complete el telefono\n";
	
	
	
	
	if (msg == "" ) {
		return true;
	}
	else
	{
		alert (msg);
		return false;
	}
	
}