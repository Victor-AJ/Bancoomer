<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
session_start(); 
include("includes/funciones.php");
include('acceso_ini.php');
include_once  ("Bitacora.class.php"); 
$mysql = conexion_db();	
	
$txtlogin 		= trim($_GET['txtlogin']);
$txtpassword 	= trim($_GET['txtpassword']);
	
// Verifica si existe el usuario
// =============================	
$sql = " SELECT a.id_usuario, a.tx_expira, a.tx_conectado, a.tx_bloqueado, a.tx_indicador, b.tx_nombre as tx_perfil ";
$sql.= "   FROM tbl_usuario a, tbl_perfil b ";
$sql.= "  WHERE tx_usuario 	= '$txtlogin' ";	
$sql.= "    AND a.id_perfil = b.id_perfil ";	

//echo "sql",$sql;		
	
$result = mysqli_query($mysql, $sql);
$num_rows = mysqli_num_rows($result);
//echo "aaaa",$num_rows ;
	
if ($num_rows<1) 
{	
	$data = array("error" => true, "message" => "El usuario $txtlogin no esta registrado en el sistema.");				
	echo json_encode($data);			
} else {

	while($row = mysqli_fetch_array($result))
	{  	
		$id_usuario		=$row["id_usuario"];	
		$tx_expira		=$row["tx_expira"];						
		$tx_conectado	=$row["tx_conectado"];
		$tx_bloqueado	=$row["tx_bloqueado"];
		$tx_indicador	=$row["tx_indicador"];
		$tx_perfil		=$row["tx_perfil"];
	}			
	
	//echo "Expira",$tx_expira;
	//echo "<br>";
	//echo "Conectado",$tx_conectado;
	//echo "<br>";
	//echo "Bloqueado",$tx_bloqueado;
	//echo "<br>";
	//echo "Indicador",$tx_indicador;
	
	if ($tx_indicador=="0") {
	
		$data = array("error" => true, "message" => "El usuario $txtlogin esta INACTIVO");				
		echo json_encode($data);	
		
	//} //elseif ($tx_bloqueado=="1") {
	
	  //	$data = array("error" => true, "message" => "El usuario $txtlogin fue BLOQUEADO" );							
	  //	echo json_encode($data);
		
	//} 
	//elseif ($tx_conectado=="1") {
		
	//	$data = array("error" => true, "message" => "El usuario $txtlogin esta CONECTADO en otra terminal");				
	//	echo json_encode($data);	
			
	} else {
	
	
		// Verifica si existe el usuario y contrase�a
		// ==========================================
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_usuario ";
		$sql.= "  WHERE tx_usuario = '$txtlogin' ";		
		$sql.= "    AND tx_password = sha1('$txtpassword') ";  
			
		//echo "sql",$sql;
								
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
			
		if ($count<1) 
		{			
				
			if (!isset($_SESSION["contador"])){ 
				$_SESSION["contador"] = 1; 
			}else{ 
				$_SESSION["contador"]++; 
			} 
			
			$contador=$_SESSION["contador"];
					
			if ($contador>=3) {
			
				$tx_bloqueado_a="1";
			
				$sql = " UPDATE tbl_usuario SET " ;  
				$sql.= " 		tx_bloqueado= '$tx_bloqueado_a' ";
				$sql.= " WHERE tx_usuario 	= '$txtlogin' ";
							   
				//echo "aaa", $sql;   
						  
				if (mysqli_query($mysql, $sql))	{				
					$data = array("error" => true, "message" => "El usuario $txtlogin fue bloqueado" );							
					echo json_encode($data);
				} else {  
					$data = array("error" => false, "message" => "El usuario $txtlogin no se bloqueo" );				
					echo json_encode($data);
				}
					
			} else {
			
				$data = array("error" => true, "message" => "La contrase&ntilde;a del usuario $txtlogin es incorrecta, despu&eacute;s de 3 intentos err&oacute;neos se bloquear&aacute");	
				echo json_encode($data);
				
			}						
			
		} else {
		
			if ($txtlogin==$txtpassword) {
			
				$_SESSION['sess_user'] 		= "$txtlogin";				
				$data = array("error" => false, "html" => "cambio_password.php" );		
				echo json_encode($data);
				
			} else {	
		
				// ACTUALIZA EL ESTATUS DE CONEXION
				// =========================================
						
				$tx_conectado_a="1";
				$fh_ultimoacceso=date("Y-m-j, g:i");
				
				$sql = " UPDATE tbl_usuario SET " ;  
				$sql.= " 	tx_conectado	= '$tx_conectado_a', ";
				$sql.= " 	fh_ultimoacceso	= '$fh_ultimoacceso' ";			
				$sql.= " WHERE tx_usuario 	= '$txtlogin' ";
						
				if (mysqli_query($mysql, $sql))		
				{							
					$_SESSION['sess_user'] = "$txtlogin";
					$_SESSION['sess_iduser'] = "$id_usuario";
					$_SESSION['sess_perfil'] 	= "$tx_perfil";
					//$data = array("error" => false, "html" => "menu.php" );				
					//$data = array("error" => false, "html" => "menu_nuevo.php" );		

					
					 //<BITACORA>
					 if (isset($_SESSION["sess_user"])) 
						 $id_login = $_SESSION['sess_iduser'];
					 $myBitacora = new Bitacora();
				 	 $myBitacora->anotaBitacora ($mysql, "ACCESO" , "TBL_USUARIO" , "$id_login" ,   "" ,"" ,"acceso.php");
	 				//<\BITACORA>
					
					$data = array("error" => false, "html" => "menu_lado.php" );					
					echo json_encode($data);
				} 
					//else {
					//$_SESSION['sess_user'] = "$txtlogin";
					//$data = array("error" => false, "html" => "menu.php" );				
					//$data = array("error" => false, "html" => "menu_nuevo.php" );				
					//echo json_encode($data);
				//}	
			}	
		}	
	}
}
mysqli_close($mysql);	
?> 