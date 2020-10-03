<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id		= $_GET["id"];
	
$sql = "   SELECT tx_tipo_gasto ";
$sql.= "     FROM tbl_glg a, tbl_tipo_gasto b ";
$sql.= "    WHERE id_glg = $id ";
$sql.= "      AND a.id_tipo_gasto = b.id_tipo_gasto ";

//echo " sql ",$sql;
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(
		'tx_tipo_gasto'	=> $row["tx_tipo_gasto"]
	);
} 	
	
for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
	while ($elemento = each($TheCatalogo[$i]))					  		
		$tx_tipo_gasto	=$TheCatalogo[$i]['tx_tipo_gasto'];				
		//echo $tx_tipo_gasto;		
}
	
$data = array("pasa" => true, "data1" => "$tx_tipo_gasto");	
echo json_encode($data);
		
mysqli_close($mysql);
?>