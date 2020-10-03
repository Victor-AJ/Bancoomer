<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id	= $_GET["id"];
	
$sql = "   SELECT id_direccion, tx_nombre ";
$sql.= "     FROM tbl_direccion ";
$sql.= "    WHERE id_entidad = $id ";
$sql.= " ORDER BY tx_nombre ";
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(
		'id_direccion'=>$row["id_direccion"],
	  	'tx_nombre'=>$row["tx_nombre"]
	);
} 	
	
for ($i=0; $i < count($TheCatalogo); $i++)
{         			 
	while ($elemento = each($TheCatalogo[$i]))					  		
		$id_direccion=$TheCatalogo[$i]['id_direccion'];		
		$tx_nombre=$TheCatalogo[$i]['tx_nombre'];						
		echo "<input name='checks$i' type='checkbox' value={$TheCatalogo[$i]['id_direccion']}>";
		echo "<option value=$id_direccion>$tx_nombre</option>";	
		echo "</br>";		
}	
mysqli_close($mysql);
?>