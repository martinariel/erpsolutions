<?php

	/*
	Clase de busqueda avanzada en una instancia  de una clase  heredada de cls_sql_table
	Las busquedas se hacen por like '%busqueda%', para ello se convierte el campo-criterio
	seleccionado a texto usando la  funcion de mySQL  "convert (campo using utf8) "
	
	@author Martin Fernandez
	
	TODO: ordenamiento de resultados, paginacion de resultados.
	*/
	
	class cls_search 
	{
		
		protected $tabla;
		protected $camposView;
		protected $camposSearch;
		protected $camposSearchCaption;
		protected $camposAlias;
		protected $where;
		protected $campoClick;
		protected $accionClick;
		protected $noview;
		
		protected $arrCamposClick;
		
		const search_query='search';
		const search_field='field';
		
		
		//----------------------------------------------------------------------
	
		function __construct ( cls_sql_table &$tabla , $campoClick = 0, 
			                   $accionClick = '', $datosCampoClick = array(0),
			                   $noView = array() ) 
	    {
			$this->tabla          = $tabla;
			$this->campoClick     = $campoClick;
			$this->accionClick    = $accionClick;
			$this->arrCamposClick = $datosCampoClick;
			$this->noview         = $noView;
		}

		//----------------------------------------------------------------------
		
		public function iniciar ( $CamposView ,$CamposAlias, $CamposSearch, $arrWhere, $CaptionOpciones ) 
		{
			
			$this->camposView  		   = $CamposView;
			$this->camposSearch		   = $CamposSearch;
			$this->camposSearchCaption = $CaptionOpciones;
			$this->camposAlias		   = $CamposAlias;
			$this->where		       = $arrWhere;
			
			$this->modeSelector ($_GET [self::search_query], $_GET [self::search_field] );
		}

		//----------------------------------------------------------------------
		
		private function modeSelector ( $busqueda , $idFld )
		{
			$this->buscador($busqueda , $idFld);
			
			$sql = $this->sqlTranslator($busqueda,$idFld);
			
			if ( $sql != '' ) 
				$this->tablaResultados ($sql);
		}

		//----------------------------------------------------------------------
			
		/*
		Formulario de busqueda
		*/
		private function buscador($busqueda , $idFld) 
		{
			addDiv ('linea');
			iniciarForm ('frmBuscador',cls_page::get_filename(), 'GET','',true);
			
			textBox('<b>Buscar:</b> ',self::search_query ,$busqueda, true,'', "onKeyPress='return submitenter(this,event)'");
			
			echo '<tr><td><b>Criterio:</b><td>';
			
			
			echo "<select class=combo name=".self::search_field.'>';
			
			$i = 0;
			foreach ($this->camposSearch as $opcion){
				$checked ='';
				if ( strtolower($opcion) == strtolower($idFld) || $i == 0 ) 
					$checked = 'selected';
				
				echo "<option $checked value=$opcion>";
				//radio (self::search_field ,$opcion,'',$checked);
				
				if (isset($this->camposSearchCaption[$i]))
					echo $this->camposSearchCaption[$i];
				else
					echo $opcion;
					
				echo '</option>';
				$i++;
			}
			
			echo '</select>';
			
			echo '</td></tr>';
			
			?>
			<SCRIPT TYPE="text/javascript">
				<!--
				function submitenter(myfield,e){
					var keycode;
					if (window.event) keycode = window.event.keyCode;
					else if (e) keycode = e.which;
					else return true;
					if (keycode == 13)  {
						myfield.form.submit();
						return false;
					}
					else
						return true;
				}
				//-->
				</SCRIPT>
			<?php
			buttonTable ('Buscar', "$('frmBuscador').submit()");
			cerrarForm(true);
			addDiv ('linea');
			echo '<br>';
		
		}

		//----------------------------------------------------------------------
		
		/*
		Tabla de resultados de la busqueda
		*/
		private function tablaResultados ($sql) 
		{
			//echo $sql;			
			$rs = $this->tabla->db->ejecutar_sql($sql);
			
			if ($rs && !$rs->EOF ) {
				echo '<table class=tableAbm cellspacing=1>';
				
				$nFields = $rs->FieldCount();
				
				echo '<tr>';
				
				for ($i = 0; $i < $nFields ; $i++){
					if ( !in_array($i, $this->noview))
						echo '<th>' . $rs->FetchField($i)->name . '</th>'.$retorno;
				}
				
				echo '</tr>';
				
				while (!$rs->EOF){
					
					echo '<tr>';
					
					for ( $i = 0 ; $i < $nFields ; $i++ ){
						$valorCampo = $rs->fields[$i] ;
						$jsAction = '';
						$j = 0;
						
						foreach ($this->arrCamposClick as $campo){
							if ($j==0) {
								$jsAction = $rs->fields[$campo];
							}
							else
							{
								$jsAction = $jsAction .','.$rs->fields[$campo];
							}
							$j++;
						}
						
						if ( !in_array($i, $this->noview)){
						if ( $i == $this->CampoClick ){
							if ($this->accionClick != '' )
								echo '<td><a href=javascript:'.$this->accionClick."($jsAction)><u>$valorCampo</u></td>";
							else
								echo "<td>$valorCampo</td>";
						}
						else
						{
							echo "<td>$valorCampo</td>";
						}
						}
					}
					echo '</tr>';
					
					$rs->MoveNext();
				}
				echo '</table>';
			}
			else {
				echo 'No hubo resultados para su b&uacute;squeda.<br><br>';
			}
		}

		//----------------------------------------------------------------------
		
		/*
		Traduce a sentencia sql segun el parametro de campo de busqueda, la busqueda, las condiciones iniciales
		y los alias de los campos.
		*/
		protected function sqlTranslator ( $busqueda, $idFld ) 
		{
			$tabla = $this->tabla->table_name;
			$campos = '';
			$where  = '';
			$sql 	= '';
			$i		= 0;
			
			$encontre = false;
			
			while ( !$encontre &&  ($i<count($this->camposSearch)) ) {
				if ( strtolower ( $this->camposSearch[$i]) == strtolower ($idFld)){
					$encontre = true;
					$busqueda = cls_sql::cadenaLike ($busqueda) ;
					$where = "convert($idFld using utf8) like $busqueda";
				}
				$i++;
			}
			
			if ($encontre) {
				$i=0;
				
				foreach ($this->camposView as $campo) { 
					$alias = '';
					if ( isset ($this->camposAlias [$i] ) ) {
						$alias = ' as '. cls_sql::cadenaSQL ($this->camposAlias [$i]);
					}
				
					if ( $i > 0 )
						$campos = "$campos , $campo $alias";
					else 
						$campos = "$campo $alias";
					$i++;
				}
				
				$i = 0;
				foreach ( $this->where as $condicion ) {
					if ($where != '')
						$where = "$where  and  $condicion";
					else
						$where = $condicion;
					$i++;
				}
				
				$sql = "select $campos from $tabla";
				
				if ( $i > 0 ) {
					$sql = "$sql where $where";
				}
			}
			
			return $sql;
		}
		
	}
?>