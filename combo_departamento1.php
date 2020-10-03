<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id		= $_GET["id"];
$id1	= $_GET["id1"];
	
$sql = "   SELECT id_departamento, tx_departamento ";
$sql.= "     FROM tbl_departamento ";
$sql.= "    WHERE id_subdireccion 	= $id1 ";
$sql.= " ORDER BY tx_departamento ";

//echo " sql ",$sql;
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(
		'id_departamento'=>$row["id_departamento"],
	  	'tx_departamento'=>$row["tx_departamento"]
	);
} 	

	echo "<option value=''>--- S e l e c c i o n e ---</option>"; 
	echo "<option value='0'>TODAS</option>"; 
	for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_departamento	=$TheCatalogo[$i]['id_departamento'];		
			$tx_departamento	=$TheCatalogo[$i]['tx_departamento'];				
			echo "<option value=$id_departamento>$tx_departamento</option>";		
	}
		
mysqli_close($mysql);
?>

<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$par_direccion		= $_GET["id"];
$par_departamento	= $_GET["id1"];
	
$sql = "   SELECT id_departamento, tx_departamento ";
$sql.= "     FROM tbl_departamento ";
$sql.= "    WHERE id_departamento = $par_departamento ";
$sql.= " ORDER BY tx_departamento ";

//echo " sql ",$sql;
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(
		'id_departamento'=>$row["id_departamento"],
	  	'tx_departamento'=>$row["tx_departamento"]
	);
} 	
?>	
<select id="selDepartamento" name="selDepartamento";>                            
<?
	//if ($dispatch=="insert") 
	echo "<option value='0' class=''>--- S e l e c c i o n e ---</option>"; 
	for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_departamento	=$TheCatalogo[$i]['id_departamento'];		
			$tx_departamento	=$TheCatalogo[$i]['tx_departamento'];	
			if ($par_departamento == $id_departamento ) echo "<option value=$id_departamento selected='selected'>$tx_departamento</option>";
			else echo "<option value=$id_departamento>$tx_departamento</option>";		
	}
?>
</select>
<?
mysqli_close($mysql);
?>