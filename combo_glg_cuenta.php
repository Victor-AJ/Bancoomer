<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id		= $_GET["id"];
	
$sql = "   SELECT id_glg, tx_cuenta ";
$sql.= "     FROM tbl_glg ";
$sql.= "    WHERE tx_glg = '$id' ";
$sql.= " ORDER BY tx_glg ";

//echo " sql ",$sql;
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(
		'id_glg'	=> $row["id_glg"],
	  	'tx_cuenta'	=> $row["tx_cuenta"]
	);
} 	

	echo "<option value=''>--- S e l e c c i o n e ---</option>"; 
	for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_glg		=$TheCatalogo[$i]['id_glg'];		
			$tx_cuenta	=$TheCatalogo[$i]['tx_cuenta'];				
			echo "<option value=$id_glg	>$tx_cuenta</option>";		
	}
		
mysqli_close($mysql);
?>