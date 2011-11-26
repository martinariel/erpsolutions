<?php

	/*
	Clase de manejo de modulos (paginas php ) del sistema
	
	Al instanciarse se valida el nivel del usuario contra el nivel requerido del modulo
	Si no existe el registro de la pagina en la tabla pages el acceso es irestringido
	
	@author Martin Fernandez
	*/

	class cls_page {
		private $id;
		private $db;
		private $page_name;
		private $min_user_level_id;
		
		const table='pages';
		
		function __construct ( &$db){
			$this->db = $db;
			$this->validate();
		}
		
		//Devuelve el nombre de archivo de la pagina sin el path
		public static function get_filename()
		{
			$dir_array = explode("/", $_SERVER['SCRIPT_NAME']);
			return $dir_array[count($dir_array)-1];
		}
		
		//Devuelve el nombre de la pagina, registro page_name de la tabla pages
		//Se usa como titulo de la pagina y titulo del menu
		public function get_page_name () 
		{
			return $this->page_name;
		}
		
		//Imprime el menu de modulos, se muestran solo los modulos con acceso posible para el usuario
		public function menu()
		{
			$user_level = cls_sql::numeroSQL($_SESSION['user_level']);
			$sql = "select page_url,page_name from pages where min_user_level_id <= $user_level and show_menu = true";
			
			$rs = $this->db->ejecutar_sql($sql);
			
			if ($rs) {
				while(!$rs->EOF){
					echo '<a href='.$rs->fields['page_url'] .'>'.$rs->fields['page_name'].'</a>';
					$rs->MoveNext();
					if (!$rs->EOF){
						echo " | ";
					}
				}
			}	
		}
		
		//Carga los datos de la pagina
		private function loadData()
		{
			$file = self::get_filename();
			$file = cls_sql::cadenaSQL($file);
			$strsql = "select page_id,page_name, min_user_level_id from " . self::table;
			$strsql = $strsql . " where page_url = $file ";
			
			$rs = $this->db->ejecutar_sql($strsql);
			
			if ( $rs && !$rs->EOF){
				$this->id = $rs->fields['page_id']+0;
				$this->min_user_level_id = $rs->fields['min_user_level_id']+0;
				$this->page_name = $rs->fields['page_name'];
				return true;
			}
			else
			{
				return false;
			}
		}
		
		//Valida el permiso de la pagina
		private function validate(){
			if ($this->loadData()) {
				if ( $_SESSION['user_level'] < $this->min_user_level_id){
					echo ('No tiene permisos para ver esta pagina');
					exit();
				}
			}
			
		}
	}
?>