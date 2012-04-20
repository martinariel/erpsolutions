<?php

	class cls_ftp {
	
		private $host;
		private $pto = 21;
		private $user;
		private $pwd;
		
		function __construct ( $host = '127.0.0.1' , $user = 'user', $pwd = 'pwd') {
			$this->host = $host;
			$this->user = $user;
			$this->pwd  = $pwd;
		}
		
		private function connect() {
			$id_ftp = ftp_connect($this->host);
			$login  = ftp_login($id_ftp,$this->user,$this->pwd); 
			
			if ( (!$id_ftp) || (!$login) ) {
				echo 'Error en la conexion'; die;
			}
			else {
				ftp_pasv ($id_ftp, true) ;
				return $id_ftp; 
			}
		}
		
		public function download($remote_file, $local_file) {
			$conexion = $this->connect();
			$result   = ftp_get($conexion,$remote_file,$local_file,FTP_BINARY);
			ftp_close($conexion);
			
			return $result;
		}
		
		public function upload ($local_file, $remote_file) {
			$conexion = $this->connect();
			$result   = ftp_put($conexion,$remote_file,$local_file,FTP_BINARY);
			ftp_close($conexion);
			
			return $result;
		}
		
		
	}
?>