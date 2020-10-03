<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
session_start();
include("includes/funciones.php");  
include_once  ("Bitacora.class.php"); 
$mysql=conexion_db();

if 	(isset($_SESSION['sess_iduser']))
	$id_login = $_SESSION['sess_iduser'];
	

# Recibo variables
$id_usuario		= trim($_GET["sel_usuario"]); 
$cap_usuario	= trim($_GET["cap_usuario"]); 

# Aactualiza contraseña
$tx_contrasena=sha1($cap_usuario);
$fh_ultimoacceso=date("Y-m-j, g:i");
			
$sql = " UPDATE tbl_usuario SET " ;  
$sql.= " 	tx_password		= sha1(tx_usuario) ";
$sql.= " WHERE id_usuario 	= $id_usuario ";

//echo "sql", $sql;
					
if (mysqli_query($mysql, $sql))
{					

	$myBitacora = new Bitacora();
	$myBitacora->anotaBitacora ($mysql, "RESETEO" , "TBL_USUARIO" , "$id_login" ,  "id_usuario=$id_usuario" , "$id_usuario"  ,  "process_reseteo.php");
		
	$data = array("error" => false, "message" => "La contrase&ntilde;a del usuario $cap_usuario se cambio correctamente");	
	echo json_encode($data);	
} 
else 
{
	$data = array("error" => true, "message" => "ERROR al actualizar la contrase&ntilde;a del usuario $cap_usuario");	
	echo json_encode($data);
}

?>
