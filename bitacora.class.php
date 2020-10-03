<?php
class Bitacora
{
	var $status;
	
	function Bitacora ()
	{
		$this->status=true;
	}
	
	function anotaBitacora ($myConn, $accion, $tabla, $login , $valores , $key, $programa)
	{
	
	$sql = "   INSERT INTO TBL46_BITACORA (TX_TIPO_OPERACION, TX_TABLA, FH_EVENTO, ID_USUARIO, TX_VALORES, ID_KEY,  TX_PROGRAMA, TX_CLIENT_IP, TX_FORWARD_IP, TX_REMOTE_IP) VALUES ('$accion','$tabla' ,CURRENT_TIMESTAMP ,'$login', '$valores' ,'$key', '$programa', '".$_SERVER['HTTP_CLIENT_IP']."','".$_SERVER['HTTP_X_FORWARDED_FOR']."','".$_SERVER['REMOTE_ADDR']."' ) ";
	if (mysqli_query($myConn, $sql))
		$this->status=true;
	else
		$this->status=false;
	}
	
	function obtenvalores ($myConn, $tabla,  $key)
	{
	//OBTENER METADATOS CAMPOS DE TABLA
	$sql="SHOW COLUMNS FROM $tabla";
	$result = mysqli_query($myConn, $sql);
	//POR CADA METADATO COLOCAR QUE VALOR TIENE PARA LA KEY DADA
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheResultset[] = array(
			'Field'	=>$row["Field"],
			'Null'	=>$row["Null"],
			'Key'	=>$row["Key"]
			);
	} 
	
	$cadSQL=" select concat(";
	for ($i = 0; $i < count($TheResultset); $i++)
	{
		$cadSQL.="' ".$TheResultset[$i]['Field']."=' , ifnull(".$TheResultset[$i]['Field']. ",'null'),"; 
	}
	$cadSQL.=" '') from $tabla where ".$TheResultset[0]['Field']." = $key ";
		
	
	$result = mysqli_query($myConn, $cadSQL);
	$row = mysqli_fetch_row($result);
	$cadena = $row[0];	
		
	
	return $cadena;
	}
	
	
	
	
	function getStatus()
	{
		return $this->status;
	}
	
}
?>
