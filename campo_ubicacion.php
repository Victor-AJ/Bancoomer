<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

include("includes/funciones.php");  
$mysql=conexion_db();

$dispatch		= $_GET["dispatch"];
$par_ubicacion	= $_GET["par_ubicacion"];

if ($dispatch=="save") {

	$sql = "   SELECT id_ubicacion, tx_ubicacion ";
	$sql.= "     FROM tbl_ubicacion ";
	$sql.= "    WHERE id_ubicacion = $par_ubicacion ";
	
	//echo "sql", $sql;
	
	if ($par_ubicacion==0) {
	?>
    	<input name="va_ubicacion" id="va_ubicacion" type="text" size="60" title="Ubicaci&oacute;n" value="<? echo $tx_ubicacion ?>" />
    <?	
	} else {
		$result = mysqli_query($mysql, $sql);		
			
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogo[] = array(
				'id_ubicacion'=>$row["id_ubicacion"],
				'tx_ubicacion'=>$row["tx_ubicacion"]
			);
		} 
			
		for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$id_ubicacion	=$TheCatalogo[$i]['id_ubicacion'];		
				$tx_ubicacion	=$TheCatalogo[$i]['tx_ubicacion'];				
		} 	
	?>	
	<input name="va_ubicacion" id="va_ubicacion" type="text" size="60" title="Ubicaci&oacute;n" value="<? echo $tx_ubicacion ?>" />
	<?
	}
} else {
?>    
	<input name="va_ubicacion" id="va_ubicacion" type="text" size="60" title="Ubicaci&oacute;n" value="<? echo $tx_ubicacion ?>" />
<?
}
?>    
    