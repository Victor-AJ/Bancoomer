<?	
include("includes/funciones.php");  
$mysql=conexion_db();
$id= $_GET['id'];
$in_anio= $_GET['in_anio'];

	$sql = "SELECT id_archivo, tx_archivo FROM tbl40_archivos_upload where in_tipo=0  and tx_status='OK' and in_anio=$in_anio   ORDER BY tx_archivo " ; 	
	$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoGPS[] = array(
			'id_archivo'	=>$row["id_archivo"],
			'tx_archivo'	=>$row["tx_archivo"]
			);
	} 


?>

      						
Archivo GPS <select name="theGPSQueryFileFac"  id="theGPSQueryFileFac"  >   
<?
	echo "<option value='0' class=''>-Seleccione-</option>"; 	 
	for ($i=0; $i < count($TheCatalogoGPS); $i++)	
	{         			 
			$id_archivo	=$TheCatalogoGPS[$i]['id_archivo'];		
			$tx_archivo	=$TheCatalogoGPS[$i]['tx_archivo'];
			$selected=($id_archivo==$id)?"selected='selected'":"";	
			echo "<option value=$id_archivo  $selected >$tx_archivo</option>";	
	}
?>
</select>
<?
mysqli_close($mysql);
?>