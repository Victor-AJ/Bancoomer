<?
session_start();
if 	(isset($_SESSION['sess_user']))
{
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();
	if (isset($_SESSION["sess_user"])) 
						 $id_login = $_SESSION['sess_iduser'];
						 
	$tx_usuario=$_SESSION['sess_user'];	
	
	$tx_conectado="0";	
			
	$sql = " UPDATE tbl_usuario SET " ;  
	$sql.= " 	tx_conectado	= '$tx_conectado' ";
	$sql.= " WHERE tx_usuario 	= '$tx_usuario' ";
				   
	//echo "aaa", $sql;      
			  
	if (mysqli_query($mysql, $sql))	{
		
		$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente" );

		//<BITACORA>
		 $myBitacora = new Bitacora();
		 $myBitacora->anotaBitacora ($mysql, "LOGOUT" , "TBL_USUARIO" , "$id_login" ,   "" ,"" ,"process_logout.php");
		//<\BITACORA>
		
		
		echo json_encode($data);
	} else {  
		$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Por favor verifique ..." );				
		echo json_encode($data);
	}		
	
	unset ($_SESSION['sess_user']); 
	session_destroy();
	
	mysqli_close($mysql);	
	
} else {	
	echo "Sessi&oacute;n Invalida";
}
?>