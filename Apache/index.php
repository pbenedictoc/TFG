<?php
session_start();
include "modelo/Herramientas.php";

$enlace=Herramientas::conexion();

$temperatura=Herramientas::temperatura($enlace);
$humedad=Herramientas::humedad($enlace);

$tiempo=Herramientas::tiempo($temperatura, $humedad);

$parcelas=Herramientas::valvulas($enlace);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Index - TFC</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="./css/comun.css">
	<link rel="stylesheet" type="text/css" href="./css/estilos-index.css">
	<link href="https://fonts.googleapis.com/css?family=Rubik" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Nanum+Brush+Script" rel="stylesheet">
	<meta name="viewport" content="user-scalable=yes">
</head>
<body>
	<script type="text/javascript">
		document.body.onload=start;
		function start(){
			for(var i=0; i<document.getElementsByTagName('input').length; i++){
				document.getElementsByTagName('input')[i].onclick=accionar;
			}

			for(var i=0; i<document.getElementsByClassName('modulos-pc-box').length; i++){
				document.getElementsByClassName('modulos-pc-box')[i].onclick=salir;
				document.getElementsByClassName('modulos-pc-box')[i].onmouseover=filtro;
				document.getElementsByClassName('modulos-pc-box')[i].onmouseout=quitarFiltro;
				for(var x=0; x<document.getElementsByClassName('modulos-pc-box')[i].childNodes.length; x++){
					document.getElementsByClassName('modulos-pc-box')[i].childNodes[x].onmouseover=filtro;
					document.getElementsByClassName('modulos-pc-box')[i].childNodes[x].onmouseout=quitarFiltro;
				}
			}
		}

		function filtro(event){
			for(var i=0; i<document.getElementsByClassName('modulos-pc-box').length; i++){
				if(document.getElementsByClassName('modulos-pc-box')[i]!=event.target && document.getElementsByClassName('modulos-pc-box')[i].childNodes[0]!=event.target && document.getElementsByClassName('modulos-pc-box')[i].childNodes[1]!=event.target && document.getElementsByClassName('modulos-pc-box')[i].childNodes[2]!=event.target){
					document.getElementsByClassName('modulos-pc-box')[i].style.filter='blur(1px)';
				}
			}
		}

		function quitarFiltro(event){
			for(var i=0; i<document.getElementsByClassName('modulos-pc-box').length; i++){
				document.getElementsByClassName('modulos-pc-box')[i].style.filter="";
			}
		}

		function salir(event){
			location.href='./html/valvula.php?id='+event.target.getAttribute('id_v');
		}

		function accionar(event){
			var aux=event.target.parentNode.parentNode;

			if(event.target.checked){
				aux.style.backgroundColor='#558758';
			}else{
				aux.style.backgroundColor='#875755';
			}
		}
	</script>

	<div id="movil">
		<div id="tiempo-m">
			<img src="./multimedia/termometro.png" class="iconos">
			<a><?php
			echo $temperatura;
			?> ºC</a>
			<br><br>
			<img src="./multimedia/humedad.png" class="iconos">
			<a><?php
			echo $humedad;
			?>%</a>

			<div id="barra-movil"></div>

			<a id="frase-movil"><?php
			echo $tiempo['texto'];
			?></a>
			<img src="./multimedia/pack_iconos_descargado/<?php
			echo $tiempo['imagen'];
			?>.png" class="iconos-grandes">
		</div>
		<div id="modulos-m">
			<div id="margen">
				
			</div>
			<?php
			for($i=0; $i<count($parcelas); $i++){
				echo '<div class="modulos-m" style="background-color: ';
				if($parcelas[$i][0]<$parcelas[$i][1]){
					echo '#558758';
				}else{
					echo '#875755';
				}
				echo '">
				<img src="./multimedia/valvula.png" class="img-v-m" href="/html/valvula.php?id='.$i.'">
				<a>Parcela '.($i+1).'</a>
				<label class="switch">
				<input type="checkbox"';
				if($parcelas[$i][0]<$parcelas[$i][1]){
					echo 'checked';
				}
				echo ' disabled>
				<span class="slider round"></span>
				</label>
				</div>';
			}
			?>
		</div>
	</div>

	<div id="pc">
		<div id="fondo-pc"></div>

		<div id="titulo-pc">
			<h1 style="font-size: 300%; margin-left: 3%;">Panel de control</h1>
		</div>
		<div id="contenido-pc"><?php
		for($i=0; $i<count($parcelas); $i++){
			echo '<div class="modulos-pc-box" id_v="'.$i.'">
			<h2 id_v="'.$i.'">Parcela num.'.($i+1).'</h2>
			<div class="circulo" style="background-color: ';
			if($parcelas[$i][0]<$parcelas[$i][1]){
				echo 'green';
			}else{
				echo 'brown';
			}
			echo ';" id_v="'.$i.'"><a id_v="'.$i.'">';
			if($parcelas[$i][0]<$parcelas[$i][1]){
				echo 'Abierta';
			}else{
				echo 'Cerrada';
			}
			echo '</a></div>
			<a id_v="'.$i.'">';
			echo 'Humedad: '.$parcelas[$i][0].'% / Default: '.$parcelas[$i][1].'%';
			echo '</a>
			</div>';
		}
		?>
	</div>

	<div id="tiempo-pc">
		<div id="aux-pc">
			<img src="./multimedia/termometro.png" class="iconos">
			<a><?php
			echo $temperatura;
			?> Cº</a>
			<br><br>
			<img src="./multimedia/humedad.png" class="iconos">
			<a><?php
			echo $humedad;
			?>%</a>
		</div>

		<div id="barra-pc"></div>

		<a id="frase-pc"><?php
		echo $tiempo['texto'];
		?></a>
		<img src="./multimedia/pack_iconos_descargado/<?php
		echo $tiempo['imagen'];
		?>.png" class="iconos-grandes">
	</div>
</div>
</body>
</html>