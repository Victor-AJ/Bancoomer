<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id	= $_GET["id"];
	
$sql = "   SELECT tx_marca ";
$sql.= "     FROM tbl_telefonia ";
$sql.= "    WHERE tx_tipo = '$id' ";
$sql.= " GROUP BY tx_marca ";

//echo " sql ",$sql;
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(		
	  	'tx_marca'=>$row["tx_marca"]
	);
} 	

	echo "<option value='0' class=''>--- S e l e c c i o n e ---</option>"; 
	for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  					
			$tx_marca	=$TheCatalogo[$i]['tx_marca'];				
			echo "<option value=$tx_marca>$tx_marca</option>";		
	}
		
mysqli_close($mysql);
?>