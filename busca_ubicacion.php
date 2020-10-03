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
	
	$sql = " SELECT tx_estado, tx_pais ";
	$sql.= "   FROM tbl_ubicacion a, tbl_estado b, tbl_pais c ";
	$sql.= "  WHERE a.id_ubicacion 	= $id ";
	$sql.= "    AND a.id_estado 	= b.id_estado ";
	$sql.= "    AND a.id_pais 		= c.id_pais ";
							
	//echo "aaa",$sql;			
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(			
	  		'tx_estado'	=>$row["tx_estado"],
			'tx_pais'	=>$row["tx_pais"]
		);
	} 	
	
	for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$tx_estado	=$TheCatalogo[$i]['tx_estado'];					
			$tx_pais	=$TheCatalogo[$i]['tx_pais'];		
	}	
	
	$data = array("pasa" => true, "data1" => "$tx_estado", "data2" => "$tx_pais");	
	echo json_encode($data);		
	
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      