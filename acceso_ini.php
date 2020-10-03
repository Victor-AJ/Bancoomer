<?php	
	$login="root"; $pass="root"; $db="csi"; $host="localhost";$db_name="csi";
	//$login="root"; $pass="admin"; $db="csi"; $host="localhost";
	//$login="root"; $pass="S3rverd0s"; $db="csi"; $host="server-dos";
	//$login="root"; $pass="S3rverd0s"; $db="csi"; $host="localhost";
	//$login="root"; $pass="root"; $db="csi"; $host="servernp";
	//150.105.32.137

$mysqli = mysqli_connect($host, $login, $pass, $db_name);

if(mysqli_connect_errno()){
	echo 'Error, no se pudo conectar a la base de datos: '.mysqli_connect_error();
}  
else{
	echo'conectado exitosamente';
}

?>