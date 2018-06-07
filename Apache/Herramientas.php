<?php
/**
* Clase herramientas
*/
class Herramientas
{
	
	public static function formatearCadena($cadena){
		$aux=str_split($cadena);
		$record=false;
		$limpia="";

		for($i=0; $i<count($aux); $i++){
			if($record and $aux[$i]=="^"){
				break;
			}

			if($record){
				$limpia.=$aux[$i];
			}

			if($aux[$i]=="*" and $record==false){
				$record=true;
			}
		}

		if(count(str_split($limpia))>0){
			$limpia=trim($limpia);
			return $limpia;
		}else{
			return 'Fallo en la recepción de datos';
		}
	}
}
?>