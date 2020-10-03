<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	// Recibo variables
	// ============================
	$id	= $_GET['id']; 	
						
  	$sql = " SELECT tx_nombre, tx_subdireccion, tx_departamento ";
	$sql.= "   FROM tbl_centro_costos a, tbl_direccion b, tbl_subdireccion c, tbl_departamento d ";
	$sql.= "  WHERE a.id_centro_costos 	= $id ";
	$sql.= "    AND a.id_direccion 		= b.id_direccion ";
	$sql.= "	AND a.id_subdireccion 	= c.id_subdireccion ";
	$sql.= "	AND a.id_departamento 	= d.id_departamento ";
							
	//echo "aaa",$sql;			
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(			
	  		'tx_nombre'			=>$row["tx_nombre"],
			'tx_subdireccion'	=>$row["tx_subdireccion"],
			'tx_departamento'	=>$row["tx_departamento"]
		);
	} 	
	
	for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$tx_nombre		=$TheCatalogo[$i]['tx_nombre'];		
			$tx_subdireccion=$TheCatalogo[$i]['tx_subdireccion'];		
			$tx_departamento=$TheCatalogo[$i]['tx_departamento'];		
	}	
	
	$data = array("pasa" => true, "data1" => "$tx_nombre", "data2" => "$tx_subdireccion", "data3" => "$tx_departamento" );	
	echo json_encode($data);		
	
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      