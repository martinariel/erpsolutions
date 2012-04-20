
function actionTransaction (id) {
	if (confirm ('Modificar el estado?')) {
		$('modo').value = $('cmb_est_'+id).value;
		$('t_id').value = id;
		$('frmTransaction').submit();
	}
}

var win;
function openDetails(id) {
	if (win) win.close();
	
	win = window.open ("detail_transaction.php?id="+id,'win','width=780,height=500,scrollbars=yes');
}

function openDetailsUpdate(id) {
	if (win) win.close();
	win = window.open ("detail_transaction_update.php?id="+id,'win','width=780,height=500,scrollbars=yes');
}