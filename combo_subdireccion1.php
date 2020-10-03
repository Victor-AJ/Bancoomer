<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id		= $_GET["id"];
	
$sql = "   SELECT id_subdireccion, tx_subdireccion ";
$sql.= "     FROM tbl_subdireccion ";
$sql.= "    WHERE id_direccion = $id ";
$sql.= " ORDER BY tx_subdireccion ";

//echo " sql ",$sql;
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(
		'id_subdireccion'=>$row["id_subdireccion"],
	  	'tx_subdireccion'=>$row["tx_subdireccion"]
	);
} 	

	echo "<option value=''>--- S e l e c c i o n e ---</option>"; 
	echo "<option value='0'>TODAS</option>"; 
	for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_subdireccion	=$TheCatalogo[$i]['id_subdireccion'];		
			$tx_subdireccion	=$TheCatalogo[$i]['tx_subdireccion'];				
			echo "<option value=$id_subdireccion>$tx_subdireccion</option>";		
	}
		
mysqli_close($mysql);
?>