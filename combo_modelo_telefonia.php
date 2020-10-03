<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id	= $_GET["id"];
$id1= $_GET["id1"];
	
$sql = "   SELECT tx_modelo ";
$sql.= "     FROM tbl_telefonia ";
$sql.= "    WHERE tx_tipo 	= '$id1' ";
$sql.= "      AND tx_marca 	= '$id' ";
$sql.= " GROUP BY tx_modelo ";

//echo " sql ",$sql;
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(		
	  	'tx_modelo'=>$row["tx_modelo"]
	);
} 	

	echo "<option value='0'>--- S e l e c c i o n e ---</option>"; 
	for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  					
			$tx_modelo	=$TheCatalogo[$i]['tx_modelo'];				
			echo "<option value='$tx_modelo'>$tx_modelo</option>";		
	}
		
mysqli_close($mysql);
?>