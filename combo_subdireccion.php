<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id					= $_GET["id"];
$par_subdireccion	= $_GET["par_subdireccion"];
$dispatch			= $_GET["dispatch"];

//echo " dispatch ", $dispatch;
//echo " <br> ";
//echo " subdireccion ", $par_subdireccion;
	
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
?>	
<select id="selSubdireccion" name="selSubdireccion" onchange="openDepartamento($('#selSubdireccion').val(),$('#par_departamento').val(),$('#par_dispatch').val())";>   
<?
	//if ($dispatch=="insert") 
	echo "<option value='0' class=''>--- S e l e c c i o n e ---</option>"; 	 
	for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_subdireccion	=$TheCatalogo[$i]['id_subdireccion'];		
			$tx_subdireccion	=$TheCatalogo[$i]['tx_subdireccion'];	
			if ($par_subdireccion == $id_subdireccion ) echo "<option value=$id_subdireccion selected='selected'>$tx_subdireccion</option>";
			else echo "<option value=$id_subdireccion>$tx_subdireccion</option>";	
	}
?>
</select>
<?
mysqli_close($mysql);
?>