<?php 
echo "PRUEBA DE CONEXION<HR>";
?>
<hr>
<a href="javascript:alert('test');"> testing</a>
<hr>
<?
echo "validando acceso a BD...<br>";

echo "including...<br>";

include("includes/funciones.php");
/*
function conexion_db()
{
	include_once("acceso_ini.php");
	
	$mysql = mysqli_connect("$host","$login","$pass");	
	if (!$mysql) {
		echo "Problemas con la Conexión.  Notifiquelo al Administrador del Sistema";
		exit;
	}
	
	$mysql_bd = mysqli_select_db($mysql,"$db");	
	if (!$mysql_bd) {
		echo "Problemas con la Base de Datos.  Notifiquelo al Administrador del Sistema";
		exit;
	}	
	
	return $mysql;
} */ 


//include_once  ("Bitacora.class.php"); 


echo "usando parametros ...<br>";

echo "login: ".$login."<br>"; 
echo "pass: ".$pass."<br>"; 
echo "db: ".$db."<br>";  
echo "host: ".$host."<br>"; 

echo "ejecutando conexion... <br>";
$mysql = conexion_db();	

echo "fin  conexion... <br>";
echo "buscando usuario : MB07647 <br>";	
	
// Verifica si existe el usuario
// =============================	
$sql = " SELECT a.id_usuario, a.tx_expira, a.tx_conectado, a.tx_bloqueado, a.tx_indicador, b.tx_nombre as tx_perfil ";
$sql.= "   FROM tbl_usuario a, tbl_perfil b ";
$sql.= "  WHERE tx_usuario 	= 'MB07647' ";	
$sql.= "    AND a.id_perfil = b.id_perfil ";	

echo "sql",$sql;		
echo "<BR>";
	
$result = mysqli_query($mysql, $sql);
$num_rows = mysqli_num_rows($result);
//echo "aaaa",$num_rows ;
	
if ($num_rows<1) 
	{	
	echo "No se encuentra el user<br>";				
	} 
else 
	{

		while($row = mysqli_fetch_array($result))
		{  	
		$id_usuario		=$row["id_usuario"];	
		$tx_expira		=$row["tx_expira"];						
		$tx_conectado	=$row["tx_conectado"];
		$tx_bloqueado	=$row["tx_bloqueado"];
		$tx_indicador	=$row["tx_indicador"];
		$tx_perfil		=$row["tx_perfil"];
		 
		}			
	
	echo "NOMBRE:",$tx_perfil;
	echo "<br>";
	echo "Expira:",$tx_expira;
	echo "<br>";
	echo "Conectado:",$tx_conectado;
	echo "<br>";
	echo "Bloqueado:",$tx_bloqueado;
	echo "<br>";
	echo "Indicador:",$tx_indicador;
	}
	
mysqli_close($mysql);	
?> 