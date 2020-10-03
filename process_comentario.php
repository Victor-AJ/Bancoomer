<?php
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
session_start(); 
include("includes/funciones.php");
include_once  ("Bitacora.class.php"); 
$mysql = conexion_db();	
if 	(isset($_SESSION["sess_user"])) 
	$id_login = $_SESSION['sess_iduser'];

$idDireccion  		= $_GET['idDireccion'];
$idDireccionQy = ($idDireccion ==0)?"is null":"=$idDireccion" ;
$idDireccionVal = ($idDireccion ==0)?"null":$idDireccion ;

$inAnio 			= $_GET['anoQueryInf']; 
$inMes				= $_GET['mesQueryInf'];
$rango				= $_GET['rango'];
$idArchivoGPS		= $_GET['theGpsQueryFile'];
$idArchivoESB		= $_GET['theEssbaseQueryFile'];
$idCuenta			= $_GET['tipoCuenta'];

$comentario 		= trim($_GET['comentario']);
$modulo				= $_GET['modulo'];


$factura			= $_GET['factura'];
$facturaQy  = ($factura == null)?"is null":"='$factura'" ;
$facturaVal = ($factura == null)?"null":"'$factura'" ;

$refpadregps		= $_GET['refpadregps'];
$refpadregpsQy  = ($refpadregps == null)?"is null":"='$refpadregps'" ;
$refpadregpsVal = ($refpadregps == null)?"null":"'$refpadregps'" ;

$anoQueryFac		= $_GET['anoQueryFac'];
$mesQueryFac		= $_GET['mesQueryFac'];
$estatusFac			= $_GET['estatusFac']	;
$proveedorFac		= $_GET['proveedorFac'];
$theFacturaCSI		= $_GET['theFacturaCSI'];
$theFacturaGPS		= $_GET['theFacturaGPS'];
$theGPSQueryFileFac	= $_GET['theGPSQueryFileFac'];

//<BITACORA>
$accionBita="NONE";
$keyBita=0;
$valores="NONE";
$myBitacora = new Bitacora();
//</BITACORA>

if ( $modulo <> "F")
{

		$sql=" select  ifnull(id_coment,0) as id_coment from tbl44_comentarios_concilia where id_direccion ".$idDireccionQy." and in_anio=".$inAnio." and id_mes=".$inMes."  and";  
		$sql.=" tx_rango='".$rango."' and id_archivo_g=".$idArchivoGPS."  and id_archivo_e=".$idArchivoESB." and id_cuenta=".$idCuenta." and tx_tipo='".$modulo	."'" ;
		$error=false;
		$descripcion="";
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$clave = $row[0];	
		  
		if ($clave ==0)
		{
			$sql2="insert into  tbl44_comentarios_concilia  (id_direccion, in_anio, id_mes, tx_rango, id_archivo_g, id_archivo_e, id_cuenta,tx_comentario,tx_tipo, id_usuarioalta,  fh_alta)  values ($idDireccionVal,$inAnio,$inMes,'$rango', $idArchivoGPS,$idArchivoESB,$idCuenta, '$comentario','$modulo',$id_login,CURRENT_TIMESTAMP)";
			$accionBita= "ALTA";
			$valores = "id_direccion=$idDireccion in_anio=$inAnio id_mes=$inMes tx_rango=$rango id_archivo_g=$idArchivoGPS id_archivo_e=$idArchivoESB id_cuenta=$idCuenta tx_comentario=$comentario tx_tipo=$modulo";
		}
			else if ($clave >=1)
			{
				$sql2="update tbl44_comentarios_concilia set tx_comentario = '$comentario' , fh_mod= CURRENT_TIMESTAMP, id_usuariomod=$id_login  where id_direccion ".$idDireccionQy." and in_anio=".$inAnio." and id_mes=".$inMes."  and  tx_rango='".$rango."' and id_archivo_g=".$idArchivoGPS." and id_archivo_e=".$idArchivoESB." and id_cuenta=".$idCuenta." and tx_tipo='".$modulo."'" ;
				$accionBita= "MODIFICACION";
				$valores=$myBitacora->obtenvalores ($mysql, "TBL44_COMENTARIOS_CONCILIA",$clave );
			}
				
}
else
{
		$sql=" select ifnull(id_coment,0) from tbl44_comentarios_concilia where tx_factura ".$facturaQy." and tx_ref_padre_gps   ".$refpadregpsQy."  and in_anio=".$anoQueryFac." and id_mes=".$mesQueryFac."  and";  
		$sql.=" id_estatus_fac=".$estatusFac	." and id_proveedor = ".$proveedorFac."  and id_archivo_g=".$theGPSQueryFileFac	." and tx_factura_csi='".$theFacturaCSI."' and tx_factura_gps ='".$theFacturaGPS	."'  and tx_tipo='F' " ;
		$error=false;
		$descripcion="";
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$clave = $row[0];	
		  
		if ($clave==0)
		{
			$sql2="insert into  tbl44_comentarios_concilia  (tx_factura, tx_ref_padre_gps, in_anio, id_mes,id_estatus_fac, id_proveedor , id_archivo_g , tx_factura_csi , tx_factura_gps , tx_comentario, tx_tipo, id_usuarioalta,  fh_alta) values ($facturaVal,$refpadregpsVal,$anoQueryFac,$mesQueryFac,$estatusFac, $proveedorFac,$theGPSQueryFileFac, '$theFacturaCSI' ,'$theFacturaGPS' , '$comentario','F',$id_login,CURRENT_TIMESTAMP)";
			$accionBita= "ALTA";
			$valores = "tx_factura=$factura tx_ref_padre_gps=$refpadregps in_anio=$anoQueryFac id_mes=$mesQueryFac id_estatus_fac=$estatusFac id_proveedor=$proveedorFac id_archivo_g=$theGPSQueryFileFac tx_factura_csi=$theFacturaCSI tx_factura_gps=$theFacturaGPS tx_comentario=$comentario tx_tipo=F ";
		}
			else if ($clave>=1)
			{
				$sql2="update tbl44_comentarios_concilia set tx_comentario = '$comentario' , fh_mod= CURRENT_TIMESTAMP, id_usuariomod=$id_login  where tx_factura ".$facturaQy." and tx_ref_padre_gps   ".$refpadregpsQy."  and in_anio=".$anoQueryFac." and id_mes=".$mesQueryFac."  and id_estatus_fac=".$estatusFac	." and id_proveedor = ".$proveedorFac."  and id_archivo_g=".$theGPSQueryFileFac	." and tx_factura_csi='".$theFacturaCSI."' and tx_factura_gps ='".$theFacturaGPS	."'  and tx_tipo='F' " ;
				$accionBita= "MODIFICACION";
				$valores=$myBitacora->obtenvalores ($mysql, "TBL44_COMENTARIOS_CONCILIA",$clave );
			} 
				
	
}		
		

	if (mysqli_query($mysql, $sql2))
	{
		$error=false;
	
	 //<BITACORA>
	 $myBitacora->anotaBitacora ($mysql, $accionBita , "TBL44_COMENTARIOS_CONCILIA" , "$id_login" ,  $valores , $clave  ,  "process_comentario.php");
	 //<\BITACORA>
	 
	}
	 else
		$error=true;

		

		
		
		
$data = array("error" => $error, "message" => $descripcion);				
echo json_encode($data);

mysqli_close($mysql);
?>