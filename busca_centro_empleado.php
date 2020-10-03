<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	# ============================
	# Recibo variables
	# ============================
	$id	= $_GET['id']; 	
		
	# ============================
	# Busca el CR
	# ============================	
	$sql = " SELECT id_centro_costos ";
	$sql.= "   FROM tbl_empleado ";
	$sql.= "  WHERE id_empleado = $id ";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoCR[] = array(			
	  		'id_centro_costos'	=>$row["id_centro_costos"]
		);
	} 	
	
	for ($i=0; $i < count($TheCatalogoCR); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogoCR[$i]))					  		
			$id_centro_costos	=$TheCatalogoCR[$i]['id_centro_costos'];		
	}	


	# ============================
	# Busca informacion del CR
	# ============================							
  	$sql = " SELECT b.id_direccion, tx_nombre, c.id_subdireccion, tx_subdireccion, d.id_departamento, tx_departamento ";
	$sql.= "   FROM tbl_centro_costos a, tbl_direccion b, tbl_subdireccion c, tbl_departamento d ";
	$sql.= "  WHERE a.id_centro_costos 	= $id_centro_costos ";
	$sql.= "    AND a.id_direccion 		= b.id_direccion ";
	$sql.= "	AND a.id_subdireccion 	= c.id_subdireccion ";
	$sql.= "	AND a.id_departamento 	= d.id_departamento ";
							
	//echo "aaa",$sql;			
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(			
	  		'id_direccion'		=>$row["id_direccion"],
	  		'tx_nombre'			=>$row["tx_nombre"],
	  		'id_subdireccion'	=>$row["id_subdireccion"],
			'tx_subdireccion'	=>$row["tx_subdireccion"],
			'id_departamento'	=>$row["id_departamento"],
			'tx_departamento'	=>$row["tx_departamento"]
		);
	} 	
	
	for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_direccion	 = $TheCatalogo[$i]['id_direccion'];		
			$tx_nombre		 = $TheCatalogo[$i]['tx_nombre'];		
			$id_subdireccion = $TheCatalogo[$i]['id_subdireccion'];		
			$tx_subdireccion = $TheCatalogo[$i]['tx_subdireccion'];		
			$id_departamento = $TheCatalogo[$i]['id_departamento'];		
			$tx_departamento = $TheCatalogo[$i]['tx_departamento'];		
	}	
	
	$data = array("pasa" => true, "data1" => "$tx_nombre", "data2" => "$tx_subdireccion", "data3" => "$tx_departamento", "data4" => "$id_direccion", "data5" => "$id_subdireccion", "data6" => "$id_departamento" );	
	echo json_encode($data);		
	
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      