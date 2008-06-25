<?php


	
	class cls_search_customer_view extends cls_search {
		
		protected function sqlTranslator ( $busqueda, $idFld ) {
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
				
				$sql = "select $campos from $tabla s left join salesreps v on (s.salesrep_id = v.salesrep_id) ";
				
				if ( $i > 0 ) {
					$sql = "$sql where $where";
				}
			}
			
			//echo $sql;
			
			return $sql;
			
			
		}
		
	
		
	}
?>