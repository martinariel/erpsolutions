function combos(tabla,valor,dest,url) {
	var pars = 'dest='+dest+'&tabla='+tabla+'&valor='+valor+"&ms="+new Date().getTime();
	var dest_id = 'fld_combo_'+dest;
	
	var globalCallbacks = {
                onCreate: function(){
                        $('loader').show();
                },
                onComplete: function() {
                        if(Ajax.activeRequestCount == 0){
                                $('loader').hide();
                        }
                }
        };
		
	Ajax.Responders.register( globalCallbacks );
	var ajax =  new Ajax.Updater( dest_id , url, { method: 'get', parameters: pars });
}
