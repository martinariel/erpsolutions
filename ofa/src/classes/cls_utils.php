<?php

	class cls_utils {
		public static function fechaHoraActual(){
			$fechaHora = getdate();
			$strFechaHora = $fechaHora['year'].'-'.$fechaHora['mon'].'-'.$fechaHora['mday'];
			$strFechaHora = $strFechaHora .' '.$fechaHora['hours'].':'.$fechaHora['minutes'].':'.$fechaHora['seconds'];
			return $strFechaHora;
		}
	}
?>