<?php
session_start();
include "../modelo/Herramientas.php";

$enlace=Herramientas::conexion();

$temperatura=Herramientas::temperatura($enlace);
$humedad=Herramientas::humedad($enlace);

$tiempo=Herramientas::tiempo($temperatura, $humedad);

$parcelas=Herramientas::valvulas($enlace);

$id=mysqli_escape_string($enlace, filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_FLOAT));

$registros=Herramientas::obtener_registros($enlace, $id);
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="../css/valvulas.css">
	<!--<link href="https://fonts.googleapis.com/css?family=Rubik" rel="stylesheet">-->
	<meta name="viewport" content="user-scalable=yes">
</head>
<body>
	<script type="text/javascript">
		document.body.onload=start;
		function start(){
			for(var i=0; i<document.getElementsByTagName('input').length; i++){
				if(document.getElementsByTagName('input')[i].type=='checkbox'){
					document.getElementsByTagName('input')[i].onclick=accionar;
				}
			}

			for(var i=0; i<document.getElementsByClassName('a').length; i++){
				document.getElementsByClassName('a')[i].onclick=meter;
				document.getElementsByClassName('a')[i].style.marginLeft='2%';
				document.getElementsByClassName('a')[i].style.cursor='pointer';
				document.getElementsByClassName('a')[i].title='Pincha para modificar';
			}

			for(var i=0; i<document.getElementsByClassName('img-v-m').length; i++){
				document.getElementsByClassName('img-v-m')[i].onclick=salir;
			}

			for(var i=0; i<document.getElementsByClassName('modulos-pc-box').length; i++){
				document.getElementsByClassName('modulos-pc-box')[i].onclick=salir;
			}

			document.getElementById('boton-reg').onclick=activar;

			document.getElementById('h1titulo').onclick=function(){location.href='../'};
			document.getElementById('triangulo').onclick=function(){location.href='../'};
		}

		function meter(event){
			var aux=document.createElement('input');
			aux.onblur=sacar;
			aux.value=event.target.innerText;
			aux.style.textAlign='right';
			aux.style.width='5%';
			event.target.innerHTML='';
			event.target.appendChild(aux);
		}

		function sacar(event){
			var padre=event.target.parentNode, texto=event.target.value;

			padre.innerHTML='';
			padre.appendChild(document.createTextNode(texto));
		}

		function activar(event){
			document.getElementById('losRegistros').style.display='initial';
		}

		function salir(event){
			location.href='../html/valvula.html';
		}

		function accionar(event){
			var aux=event.target.parentNode.parentNode;

			if(event.target.parentNode.getAttribute('class')=='switch-grande'){
				var aux=event.target.parentNode.childNodes[1];
				if(event.target.checked){
					aux.value='ON';
					document.getElementById('contenido-pc').style.borderColor='green';
					document.getElementById('honoff').innerText='Encendido';
				}else if(event.target.checked==false){
					aux.value='OFF';
					document.getElementById('contenido-pc').style.borderColor='red';
					document.getElementById('honoff').innerText='Apagado';
				}
				return;
			}

			if(event.target.checked){
				aux.style.backgroundColor='#558758';
			}else{
				aux.style.backgroundColor='#875755';
			}
		}

		function cerrarRegistros(){
			document.getElementById('losRegistros').style.display='none';
		}
	</script>

	<div id="movil">
		<div id="tiempo-m">
			<img src="../multimedia/termometro.png" class="iconos">
			<a><?php
			echo $temperatura;
			?> ºC</a>
			<br><br>
			<img src="../multimedia/humedad.png" class="iconos">
			<a><?php
			echo $humedad;
			?>%</a>

			<div id="barra-movil"></div>

			<a id="frase-movil"><?php
			echo $tiempo['texto'];
			?></a>
			<img src="../multimedia/pack_iconos_descargado/<?php
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
				<img src="../multimedia/valvula.png" class="img-v-m" href="/html/valvula.php?id='.$i.'">
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

	<div id="fondo-pc"></div>
	<div id="pc">

		<div id="triangulo" title="Volver a la página principal"></div>
		<h1 id="h1titulo" title="Volver a la página principal">Parcela <?php
		echo intval($_GET['id'])+1;
		?></h1>
		<div id="boton">
			<h2 id="honoff"><?php
			if($parcelas[$id][0]<$parcelas[$id][1]){
				echo 'Abierta';
			}else{
				echo 'Cerrada';
			}
			?></h2>
			<label class="switch-grande">
				<input type="checkbox" id="input-grande" <?php
				if($parcelas[$id][0]<$parcelas[$id][1]){
					echo 'checked ';
				}
				?>disabled>
				<span class="slider round"></span>
			</label>
		</div>

		<div id="contenido-pc">

			<?php
			for($i=0; $i<count($registros); $i++){
				echo '<div class="opciones">
				<h2>'.$registros[$i]['hora'].'</h2>
				<p><a class="a">Humedad obtenida en ese momento: '.$registros[$i]['humedad'].'%</a></p>
				<p><a class="a">Humedad predeterminada: '.$registros[$i]['default'].'%</a></p>
			</div>
			<hr>';
			}
			?>
		</div>

		<div id="registros">
			<button id="boton-reg">Explicacion</button>
			<div class="datos">
				<h3>Humedad actual</h3>
				<p><?php
				echo $parcelas[$id][0].'%';
				?></p>
				<hr>
			</div>
			<div class="datos">
				<h3>Humedad preestablecida</h3>
				<p><?php
				echo $parcelas[$id][1].'%';
				?></p>
				<hr>
			</div>
		</div>

		<div id="losRegistros">
			<img src="../multimedia/x.png" id="xSalir" onclick="cerrarRegistros()">
			<div id="losRegistrosTexto">
				La humedad actual es la humedad que tiene guardada la base de datos y que se actualiza cada cinco minutos. Por otro lado, la humedad preestablecida es la discriminación que hace el sistema para abrir la válvula, es decir: si supera la humedad preestablecida en esa parcela, la válvula se cierra. En cambio, si no la super la válvula se abre y deja pasar el agua.
			</div>
		</div>
	</div>
</body>
</html>