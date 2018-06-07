<?php
include '/var/www/html/serial_port.php';
include '/var/www/html/Herramientas.php';
$serial= new PhpSerial;

	//Marco la dirección
$serial->deviceSet("/dev/ttyACM0");

	//Ajustes de conexión
$serial->confBaudRate(9600);
$serial->confParity('none');
$serial->confCharacterLength(8);
$serial->confStopBits(1);
$serial->confFlowControl('none');

	//Abro el canal
$serial->deviceOpen();

if($serial->_ckOpened()){
	sleep(10);
	$texto='';
	$serial->sendMessage('1');
	$texto=$serial->readPort();

	$texto=Herramientas::formatearCadena($texto);

	$enlace=mysqli_connect('localhost', 'arduino', 'Arduin0', 'arduino');

	$datos=json_decode($texto);
	$humedad=$datos[1];
	$temperatura=$datos[2];
	$valvulas=array();

	for($i=0; $i<count($datos[0]); $i++){
		array_push($valvulas, $datos[0][$i]);
	}

	mysqli_query($enlace, 'INSERT INTO registro_regadio_actual (temperatura, humedad, valvulas) VALUES ('.$temperatura.', '.$humedad.', "'.json_encode($valvulas).'")');


	mysqli_close($enlace);
}

$serial->deviceClose();
?>