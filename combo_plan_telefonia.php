<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id	= $_GET["id"];
	
$sql = "   SELECT tx_plan ";
$sql.= "     FROM tbl_telefonia_plan ";
$sql.= "    WHERE tx_proveedor = '$id' ";
$sql.= " GROUP BY tx_plan ";

//echo " sql ",$sql;
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(		
	  	'tx_plan'=>$row["tx_plan"]
	);
} 	

	echo "<option value='0' class=''>--- S e l e c c i o n e ---</option>"; 
	for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  					
			$tx_plan	=$TheCatalogo[$i]['tx_plan'];				
			echo "<option value='$tx_plan'>$tx_plan</option>";		
	}
		
mysqli_close($mysql);
?>