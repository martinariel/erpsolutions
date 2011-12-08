<?php

	/*
	Clase abm para istancias derivadas de la clase cls_sql_table
	
	
	TODO: update de la tabla e insert, seleccion de campos visibles 
	
	@Author: Martín Fernández. ERP Solutions 2007
	@E-Mail: martin.fernandez@erp-solutions.com
	@Last Update: 2007/08/14
	
	*/

	Class cls_abm {
		
		private $obj_table;
		
		function __construct (cls_sql_table &$obj){
			$this->obj_table = $obj;
		}
		
		public function modeSelector(){
			$mode = $_POST['mode'];
			$this->obj_table->set_id( cls_sql::numeroSQL( $_POST['id'] ) );	
			
			switch ($mode){
				case 'update' : $this->update();break;
				case 'delete' : $this->delete();break;
				case 'insert' : $this->insert();break;
				default		  : $this->show();
			}
			
		}
		
		private function update(){
			$id = $this->obj_table->get_id();
		}
		
		private function delete(){
			$id = $this->obj_table->get_id();
			
			if ( $this->obj_table->execute_delete($id) ) {
				echo 'Se elimino el registro con exito';
			}
			
			$this->show();
		}
		
		private function insert(){
			$id = $this->obj_table->get_id();
		}
		
		private function pager ( $rows,$currentPage, $rows_pp){
			$y = $rows / $rows_pp;
			$x = floor($y);
			
			if ( $x != $y) { 
				$x++; 
			}
			else
			{
				//nothing to do
			}
			$total_pages = $x; $url = cls_page::get_fileName();
			
			$i = $currentPage - 1; 
			
			if ($i>0) echo "<a href=$url?page=$i> << </a>&nbsp;";
			
			for ( $i = ($currentPage-4) ; $i<=($currentPage+4); $i++) {
				if ( $i>0 && $i <= $total_pages) {
				
					if ($i == $currentPage){ 
						echo "<b>[$i]</b>&nbsp;"; }
					else { 
						echo "<a href=$url?page=$i>[$i]</a>&nbsp;"; 
					}
					
				}
			}
			
			$i = $currentPage + 1; 
			
			if ($i <= $total_pages) echo "<a href=$url?page=$i> >> </a>&nbsp;";
			echo "&nbsp;Registros: $rows";
		}
		
		private function show (){
			
			$rows_pp = 10;
			$total_rows = $this->obj_table->get_total_rows();
			
			$pagina = $_REQUEST['page'];
			if (!$pagina) $pagina = 1; 
			
			$sql = $this->obj_table->get_select_query();
			$rs  = $this->obj_table->db->ejecutar_sql_pagina($sql,$pagina,$rows_pp);
			
			addJs('js/abm.js');
			iniciarForm ('abm',cls_page::get_fileName(),'post','');
			
			if ($rs && !$rs->EOF) {
		
				$fldNumber = $rs->FieldCount();
				
				hidden ('mode','view');
				hidden ('id',0);
				
				echo '<div style=width:100%;overflow:auto>';
				echo '<table align=center class=tableAbm cellspacing=1 cellpadding=2>'.$retorno;
				
				echo '<tr>'.$retorno;
				for ($i = 0; $i < $fldNumber ; $i++){
					echo '<th>' . ucwords( str_replace ( "_" , "&nbsp;" , $rs->FetchField($i)->name ) ) . '</th>'.$retorno;
				}
				echo '</tr>';
				
				while ( !$rs->EOF ){
				
					echo '<tr>'.$retorno;
					for ($i =0 ; $i < $fldNumber; $i++){
						echo '<td>' . $rs->fields[$i] . '</td>'.$retorno;
					}
					echo '<td><a href=javascript:_update('.$rs->fields[0].')><img src=img/edit.gif alt=Edit></a></td>'.$retorno;
					echo '<td><a href=javascript:_delete('.$rs->fields[0].')><img src=img/delete.gif alt=Delete></a></td>'.$retorno;
					
					echo '</tr>';
				
					$rs->MoveNext();
				}
				
				echo '</table>'.$retorno;
				echo '</div>';
				
				$this->pager($total_rows,$pagina,$rows_pp);
			}
			else
			{
				echo 'La tabla no tien registros';
			}
			
			
			echo '<div style=width:600px align=right>';
			button ('Nuevo Registro','_add()');	
			echo '</div>';
			cerrarForm();
		
		}
	}
	
?>