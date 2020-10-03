<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	// ============================
	// Recibo variables
	// ============================
	$id	= $_GET['id']; 	
	$id1= $_GET['id1']; 	
						
  	$sql = " SELECT fl_precio_mxn ";
	$sql.= "   FROM tbl_telefonia_plan ";
	$sql.= "  WHERE tx_proveedor= '$id1' ";	
	$sql.= "    AND tx_plan		= '$id' ";	
							
	//echo "aaa",$sql;			
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(			
	  		'fl_precio_mxn'	=>$row["fl_precio_mxn"]
		);
	} 	
	
	for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$fl_precio_mxn	=$TheCatalogo[$i]['fl_precio_mxn'];		
	}	
	
	$data = array("pasa" => true, "data1" => "$fl_precio_mxn" );	
	echo json_encode($data);		
	
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      