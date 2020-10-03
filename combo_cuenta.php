<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id					= $_GET["id"];
	
$sql = "   SELECT id_cuenta, tx_cuenta ";
$sql.= "     FROM tbl_cuenta ";
$sql.= "    WHERE id_proveedor = $id ";
$sql.= " ORDER BY tx_cuenta ";

//echo " sql ",$sql;
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(
		'id_cuenta'=>$row["id_cuenta"],
	  	'tx_cuenta'=>$row["tx_cuenta"]
	);
} 	

	echo "<option value='0' class=''>--- S e l e c c i o n e ---</option>"; 
	for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_cuenta	=$TheCatalogo[$i]['id_cuenta'];		
			$tx_cuenta	=$TheCatalogo[$i]['tx_cuenta'];				
			echo "<option value=$id_cuenta>$tx_cuenta</option>";		
	}
		
mysqli_close($mysql);
?>