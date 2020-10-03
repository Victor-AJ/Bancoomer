<?
include_once("acceso_ini.php");

function conexion_db()
{
	
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
	
	//return $mysql;
}  
?>