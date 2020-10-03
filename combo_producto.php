<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id	= $_GET["id"];
	
$sql = "   SELECT id_producto, tx_producto ";
$sql.= "     FROM tbl_producto ";
$sql.= "    WHERE id_proveedor = $id ";
$sql.= " ORDER BY tx_producto ";

//echo " sql ",$sql;
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(
		'id_producto'=>$row["id_producto"],
	  	'tx_producto'=>$row["tx_producto"]
	);
} 	

	echo "<option value='0' class=''>--- S e l e c c i o n e ---</option>"; 
	for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_producto	=$TheCatalogo[$i]['id_producto'];		
			$tx_producto	=$TheCatalogo[$i]['tx_producto'];				
			echo "<option value=$id_producto>$tx_producto</option>";		
	}
		
mysqli_close($mysql);
?>