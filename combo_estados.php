<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id			= $_GET["id"];
$dispatch	= $_GET["dispatch"];
$par_estado	= $_GET["par_estado"];
	
$sql = "   SELECT id_estado, tx_estado ";
$sql.= "     FROM tbl_estado ";
$sql.= "    WHERE id_pais = $id ";
$sql.= " ORDER BY tx_estado ";

//echo " sql ",$sql;
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(
		'id_estado'=>$row["id_estado"],
	  	'tx_estado'=>$row["tx_estado"]
	);
} 	
?>	
<select id="selEstado" name="selEstado" onchange="openUbicacion($('#dispatch').val())";>
<?
	echo "<option value='0' class=''>--- S e l e c c i o n e ---</option>"; 
	for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_estado	=$TheCatalogo[$i]['id_estado'];		
			$tx_estado	=$TheCatalogo[$i]['tx_estado'];	
			if ($par_estado == $id_estado ) echo "<option value=$id_estado selected='selected'>$tx_estado</option>";
			else echo "<option value=$id_estado>$tx_estado</option>";		
	}
?>
</select>
<?
mysqli_close($mysql);
?>