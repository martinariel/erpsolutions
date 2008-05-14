/*
	Funciones usadas por la clase cls_abm
	
	@author Martin Fernandez
*/

function _delete(id){
	if (confirm('Confirma eliminar el usuario?')){
		$('mode').value = 'delete';
		$('id').value   = id;
		$('abm').submit();
	}
}

function _update(id){
	window.location = 'edit_user.php?user_id='+id;
/*
	$F('mode') = 'update';
	$F('id')   = id;
	$F('abm').submit();
	*/
}

function _add(){
window.location = 'edit_user.php?user_id=0';
/*
	$F('mode') = 'insert';
	$F('id')   = 0;
	$F('abm').submit();
	*/
}