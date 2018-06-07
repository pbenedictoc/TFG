<?php
/**
* Clase herramientas
*/
class Herramientas
{
	
	public static function conexion(){
		define("MYSQL_USER", "arduino");
		define("MYSQL_PWD", "Arduin0");
		define("MYSQL_DB", "arduino");

		return mysqli_connect('localhost', MYSQL_USER, MYSQL_PWD, MYSQL_DB);
	}

	public static function temperatura($enlace){
		$consulta=mysqli_query($enlace, 'SELECT MAX(hora) FROM registro_regadio_actual');
		$max_hora=mysqli_fetch_assoc($consulta)['MAX(hora)'];

		if($max_hora){
			//Hay datos guardados en la tabla
			$consulta=mysqli_query($enlace, 'SELECT temperatura FROM registro_regadio_actual WHERE hora=\''.$max_hora.'\'');
			$return=mysqli_fetch_assoc($consulta)['temperatura'];
		}else{
			//No hay datos guardados en la tabla
			$consulta=mysqli_query($enlace, 'SELECT MAX(fecha) FROM registros');
			$max_fecha=mysqli_fetch_assoc($consulta)['MAX(fecha)'];

			$consulta=mysqli_query($enlace, 'SELECT temperatura_media FROM registros WHERE fecha=\''.$max_fecha.'\'');
			$return=mysqli_fetch_assoc($consulta)['temperatura_media'];
		}

		return $return;
	}

	public static function humedad($enlace){
		$consulta=mysqli_query($enlace, 'SELECT MAX(hora) FROM registro_regadio_actual');
		$max_hora=mysqli_fetch_assoc($consulta)['MAX(hora)'];

		if($max_hora){
			//Hay datos guardados en la tabla
			$consulta=mysqli_query($enlace, 'SELECT humedad FROM registro_regadio_actual WHERE hora=\''.$max_hora.'\'');
			$return=mysqli_fetch_assoc($consulta)['humedad'];
		}else{
			//No hay datos guardados en la tabla
			$consulta=mysqli_query($enlace, 'SELECT MAX(fecha) FROM registros');
			$max_fecha=mysqli_fetch_assoc($consulta)['MAX(fecha)'];

			$consulta=mysqli_query($enlace, 'SELECT humedad_media FROM registros WHERE fecha=\''.$max_fecha.'\'');
			$return=mysqli_fetch_assoc($consulta)['humedad_media'];
		}

		return $return;
	}

	public static function tiempo($temperatura, $humedad){
		$return=array();
		$date=getdate();

		if(intval($date['hours'])>7 and intval($date['hours'])<20){
			$situacion='-d';
		}else if(intval($date['hours'])==7){
			if(intval($date['minutes'])>=30){
				$situacion='-d';
			}else{
				$situacion='-n';
			}
		}else if(intval($date['hours'])==20){
			if(intval($date['minutes'])<=30){
				$situacion='-d';
			}else{
				$situacion='-n';
			}
		}else{
			$situacion='-n';
		}

		if(intval($humedad)<40){
			if(intval($temperatura)<15){
				$return['imagen']='frio';
				$return['texto']='Frío';
			}else if(intval($temperatura)>=15 and intval($temperatura)<25){
				$return['imagen']='despejado'.$situacion;
				$return['texto']='Despejado';
			}else if(intval($temperatura)>=25){
				$return['imagen']='calor';
				$return['texto']='Calor';
			}
		}else if(intval($humedad)>=40 and intval($humedad)<60){
			if(intval($temperatura)<15){
				$return['imagen']='bruma'.$situacion;
				$return['texto']='Bruma';
			}else if(intval($temperatura)>=15){
				$return['imagen']='tormenta';
				$return['texto']='Tormenta';
			}
		}else if(intval($humedad>=60 and intval($humedad)<90)){
			if(intval($temperatura)<3){
				$return['imagen']='nieve';
				$return['texto']='Nieve';
			}else if(intval($temperatura)>=3 and intval($temperatura)<25){
				$return['imagen']='lluvia'.$situacion;
				$return['texto']='Lluvia';
			}else if(intval($temperatura)>=25){
				$return['imagen']='nuboso'.$situacion;
				$return['texto']='Nuboso';
			}
		}else if(intval($humedad)>=90){
			if(intval($temperatura)<3){
				$return['imagen']='nieve';
				$return['texto']='Nieve';
			}else if(intval($temperatura)>=3 and intval($temperatura)<25){
				$return['imagen']='temporal';
				$return['texto']='Temporal';
			}else if(intval($temperatura)>=25){
				$return['imagen']='encapotado';
				$return['texto']='Encapotado';
			}
		}

		return $return;
	}

	private static function objeto_valvulas($enlace){
		$consulta=mysqli_query($enlace, 'SELECT MAX(fecha) FROM registros');
		$max_fecha=mysqli_fetch_assoc($consulta)['MAX(fecha)'];

		$cache=mysqli_fetch_assoc(mysqli_query($enlace, 'SELECT * FROM registros WHERE fecha=\''.$max_fecha.'\''))['registros_valvulas'];

		$cache=json_decode($cache);
		$valvulas=json_decode($cache[0][0]);

		return $valvulas;
	}

	public static function valvulas($enlace){
		$consulta=mysqli_query($enlace, 'SELECT MAX(hora) FROM registro_regadio_actual');
		$max_hora=mysqli_fetch_assoc($consulta)['MAX(hora)'];

		if($max_hora){
			//Hay datos guardados en la tabla
			$consulta=mysqli_query($enlace, 'SELECT valvulas FROM registro_regadio_actual WHERE hora=\''.$max_hora.'\'');
			$cache=mysqli_fetch_assoc($consulta)['valvulas'];
			$valvulas=json_decode($cache);
		}else{
			//No hay datos guardados en la tabla
			$valvulas=self::objeto_valvulas($enlace);
		}

		return $valvulas;
	}

	public static function obtener_registros($enlace, $id){
		$return=array();

		$consulta=mysqli_query($enlace, 'SELECT * FROM registro_regadio_actual ORDER BY hora DESC');

		if($consulta){
			while($fila=mysqli_fetch_array($consulta)){
				$cache=json_decode($fila['valvulas']);
				array_push($return, array('hora'=>$fila['hora'],'humedad'=>$cache[$id][0],'default'=>$cache[$id][1]));
			}
		}

		$consulta=mysqli_query($enlace, 'SELECT * FROM registros ORDER BY fecha DESC');
		while($fila=mysqli_fetch_array($consulta)){
			$cache=json_decode($fila['registros_valvulas']);
			for($i=0; $i<count($cache); $i++){
				$cache_valvulas=json_decode($cache[$i][0]);
				array_push($return, array('hora'=>$cache[$i][1],'humedad'=>$cache_valvulas[$id][0],'default'=>$cache_valvulas[$id][1]));
			}
		}

		return $return;


	}
}
?>