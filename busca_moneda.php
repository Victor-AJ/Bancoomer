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
	
	$id	= trim($_GET['id']); 	
						
  	$sql = " SELECT id_moneda ";
	$sql.= "   FROM tbl_moneda  ";
	$sql.= "  WHERE tx_moneda = '$id' ";

	//echo "aaa",$sql;			
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(			
	  		'id_moneda'	=> $row["id_moneda"]
		);
	} 	
	
	for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogo[$i]))					  				
			$id_moneda		=$TheCatalogo[$i]['id_moneda'];		
	}	
		
	$data = array("pasa" => true, "data1" => "$id_moneda");	
	echo json_encode($data);		
	
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      