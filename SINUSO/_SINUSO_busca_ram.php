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
	$id1= $_GET['id1']; 	
	$id2= $_GET['id2']; 	
						
  	$sql = " SELECT tx_ram ";
	$sql.= "   FROM tbl_computo ";
	$sql.= "  WHERE tx_modelo	= '$id' ";	
	$sql.= "    AND tx_marca	= '$id1' ";	
	$sql.= "    AND tx_equipo	= '$id2' ";	
							
	//echo "aaa",$sql;			
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(			
	  		'tx_ram'	=>$row["tx_ram"]
		);
	} 	
	
	for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$tx_ram	=$TheCatalogo[$i]['tx_ram'];		
	}	
	
	$data = array("pasa" => true, "data1" => "$tx_ram" );	
	echo json_encode($data);		
	
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      