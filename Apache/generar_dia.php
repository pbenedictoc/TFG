<?php
$enlace=mysqli_connect('localhost', 'arduino', 'Arduin0', 'arduino');

if(intval(mysqli_fetch_assoc(mysqli_query($enlace, 'SELECT COUNT(*) FROM registro_regadio_actual'))["COUNT(*)"])!=0){

	$temperatura_max=mysqli_fetch_assoc(mysqli_query($enlace, "SELECT MAX(temperatura) FROM registro_regadio_actual"))["MAX(temperatura)"];
	$temperatura_min=mysqli_fetch_assoc(mysqli_query($enlace, "SELECT MIN(temperatura) FROM registro_regadio_actual"))["MIN(temperatura)"];
	$temperatura_med=mysqli_fetch_assoc(mysqli_query($enlace, "SELECT AVG(temperatura) FROM registro_regadio_actual"))["AVG(temperatura)"];

	$humedad_max=mysqli_fetch_assoc(mysqli_query($enlace, "SELECT MAX(humedad) FROM registro_regadio_actual"))["MAX(humedad)"];
	$humedad_min=mysqli_fetch_assoc(mysqli_query($enlace, "SELECT MIN(humedad) FROM registro_regadio_actual"))["MIN(humedad)"];
	$humedad_med=mysqli_fetch_assoc(mysqli_query($enlace, "SELECT AVG(humedad) FROM registro_regadio_actual"))["AVG(humedad)"];

	$registros_valvulas=mysqli_query($enlace, "SELECT valvulas, hora FROM registro_regadio_actual");


	$valvulas=array();
	while($fila=mysqli_fetch_array($registros_valvulas)){
		array_push($valvulas, [$fila['valvulas'],$fila['hora']]);
	}

	$valvulas=json_encode($valvulas);

	$direccion='/var/www/html/cache.txt';

	if(file_exists($direccion)){
		unlink($direccion);
	}

	$archivo=fopen($direccion, 'w+');
	fwrite($archivo, $valvulas);
	fclose($archivo);

	$archivo=fopen($direccion, 'rb');
	$tamanyo=filesize($direccion);

	$valvulas=mysqli_real_escape_string($enlace, $valvulas);

	$binario=addslashes(fread($archivo, $tamanyo));

	$insert='INSERT INTO registros (temperatura_maxima, temperatura_minima, temperatura_media, humedad_maxima, humedad_minima, humedad_media, registros_valvulas) VALUES ( '.$temperatura_max.', '.$temperatura_min.', '.$temperatura_med.', '.$humedad_max.', '.$humedad_min.', '.$humedad_med.', "'.$binario.'")';

	mysqli_query($enlace, $insert);

	fclose($archivo);

	mysqli_query($enlace, 'TRUNCATE TABLE registro_regadio_actual');
}

mysqli_close($enlace);
?>