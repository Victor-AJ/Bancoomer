<?
//Inicia Session
//====================================
session_start(); 

include("includes/funciones.php");  
include_once  ("Bitacora.class.php"); 
$mysql=conexion_db();
if (isset($_SESSION["sess_user"])) 
						 $id_login = $_SESSION['sess_iduser'];
						 
// Recibo variables
// ============================
$dispatch		= $_GET['dispatch']; 
$txtpassword1	= $_GET['txtpassword1']; 
$txtpassword2	= $_GET['txtpassword2']; 

$tx_usuario=$_SESSION['sess_user'];	

// ACTUALIZA PASSWORD
// =========================================					

$tx_password=sha1($txtpassword1);
$fh_ultimoacceso=date("Y-m-j, g:i");
			
$sql = " UPDATE tbl_usuario SET " ;  
$sql.= " 	tx_password		= '$tx_password' ";
$sql.= " WHERE tx_usuario 	= '$tx_usuario' ";
					
if (mysqli_query($mysql, $sql))		{								
	//$data = array("error" => false, "html" => "menu.php" );					
	
	//<BITACORA>
		 $myBitacora = new Bitacora();
		 $myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_USUARIO" , "$id_login" ,   "tx_usuario=$tx_usuario" ,"" ,"process_password.php");
		//<\BITACORA>
		
	$data = array("error" => false, "message" => "La contrase&ntilde;a del usuario $txtlogin se cambio correctamente");	
	echo json_encode($data);
	
} else {

	$data = array("error" => true, "message" => "ERROR al actualizar la contrase&ntilde;a del usuario $txtlogin");	
	echo json_encode($data);
}

?>
