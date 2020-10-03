<?php
session_start();
include("includes/funciones.php");
include_once  ("Bitacora.class.php");  
$mysql=conexion_db();

// Recibo variables
// ============================
$idArchivo			= $_GET['idArchivo'];
$tipofile 			= $_GET['tipofile'];
$tablaBitacora="";

$id_login =NULL;
if 	(isset($_SESSION["sess_user"])) 
	$id_login = $_SESSION['sess_iduser'];

if ($tipofile=="0")  // 0 es GPS
{
	$sql.=" select * from tbl42_gps where id_archivo= $idArchivo";
	$tablaBitacora="TBL42_GPS";
}
else
{
	$sql.=" select * from tbl43_essbase where id_archivo= $idArchivo";
	$tablaBitacora="TBL13_ESSBASE";

}
	
//gps mostrar 16
//essbase mostrar 6
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<title> CSI [Archivo cargado]</title>
<head>

<link rel="stylesheet" type="text/css" media="screen" href="css/ui-personal/jquery-ui-1.7.2.custom.css"/> 
 
</head>

<table border=1 cellpadding="0" cellspacing="0" >

<?php 
$cat=" </td> <td class='ui-state-white align-left' > ";
$i=1;
	$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	

		if ($tipofile==0)
				echo "<tr><td class='ui-state-verde align-left'>".$i.$cat.$row['in_anio'].$cat.$row['id_mes'].$cat.$row['tx_nombre'].$cat.$row['tx_cta_gps'].$cat.$row['tx_cuenta_local'].$cat.$row['tx_cr'].$cat.$row['tx_fecha_contable'].$cat.$row['tx_clase'].$cat.$row['tx_numero_doc'].$cat.$row['tx_referencia'].$cat.$row['tx_ct'].$cat.$row['tx_moneda'].$cat.$row['im_monto_destino'].$cat.$row['im_monto_local'].$cat.$row['id_archivo'].$cat.$row['id_cuenta_contable']."</td></tr>";	
		
		else
			echo "<tr><td class='ui-state-verde align-left'>".$i.$cat.$row['in_anio'].$cat.$row['id_mes'].$cat.$row['tx_cr'].$cat.$row['tx_descripcion'].$cat.$row['im_monto'].$cat.$row['id_archivo'].$cat.$row['id_cuenta_contable']."</td></tr>";
		
		$i++;
	} 
	
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , $tablaBitacora , "$id_login" ,  " "  , "$idArchivo", "inf_viewfilexls.php");
	 //<\BITACORA>
?>

</table>
</body>
</html>




