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
						
  	$sql = " SELECT a.tx_descripcion, fl_precio, fl_precio_mxn, tx_moneda ";
	$sql.= "   FROM tbl_producto a, tbl_moneda b  ";
	$sql.= "  WHERE a.id_producto 	= '$id' ";
	$sql.= "    AND a.id_moneda		= b.id_moneda ";
							
	//echo "aaa",$sql;			
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(			
	  		'tx_descripcion'	=>$row["tx_descripcion"],
			'fl_precio'			=>$row["fl_precio"],
			'fl_precio_mxn'		=>$row["fl_precio_mxn"],
			'tx_moneda'			=>$row["tx_moneda"]
		);
	} 	
	
	for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$tx_descripcion	=$TheCatalogo[$i]['tx_descripcion'];		
			$fl_precio		=$TheCatalogo[$i]['fl_precio'];		
			$fl_precio_mxn	=$TheCatalogo[$i]['fl_precio_mxn'];		
			$tx_moneda		=$TheCatalogo[$i]['tx_moneda'];		
	}	
	
	if ($tx_moneda=="MXN") $fl_precio=$fl_precio_mxn;
		
	$data = array("pasa" => true, "data1" => "$tx_descripcion", "data2" => "$fl_precio", "data3" => "$tx_moneda" );	
	echo json_encode($data);		
	
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      