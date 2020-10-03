<?
 	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    session_cache_limiter("must-revalidate");
    $fileName = "CSI Facturacion $id_anio.xls";
	header("Content-type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=$fileName");
	$actionBita="EXCEL";
	
	session_start();
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();
	if 	(isset($_SESSION["sess_user"])) 
		$id_login = $_SESSION['sess_iduser'];
	
	$id_anio	= $_GET['id']; 	
	
	$sql = " SELECT tx_anio, tx_factura, tx_proveedor, tx_cuenta, tx_producto, FORMAT(a.fl_precio_usd,2), FORMAT(a.fl_precio_mxn,2), FORMAT(a.fl_precio_eur,2), ";
	$sql.= "        FORMAT(fl_tipo_cambio,4), DATE_FORMAT(fh_factura,'%d/%m/%Y'), DATE_FORMAT(fh_inicio,'%d/%m/%Y'), DATE_FORMAT(fh_final,'%d/%m/%Y'), tx_mes, a.tx_notas, ";
	$sql.= "        tx_estatus, tx_registro, tx_empleado, FORMAT(b.fl_precio_usd,2) as fl_precio_usd_det, ";
	$sql.= " 		FORMAT(b.fl_precio_mxn,2) as fl_precio_mxn_det, FORMAT(b.fl_precio_eur,2) as fl_precio_eur_det, tx_centro_costos, tx_nombre, tx_subdireccion, k.tx_departamento ";
	$sql.= " FROM tbl_factura a, tbl_factura_detalle b, tbl_proveedor c, tbl_cuenta d, tbl_empleado e, tbl_centro_costos f, tbl_direccion g, tbl_subdireccion h, tbl_mes i, tbl_producto j, tbl_departamento k, tbl_factura_estatus l ";
	$sql.= " WHERE tx_anio 				= '$id_anio' ";
	$sql.= "   AND a.id_factura 		= b.id_factura and a.tx_indicador= '1' and b.tx_indicador='1' ";
	$sql.= "   AND a.id_proveedor 		= c.id_proveedor ";
	$sql.= "   AND a.id_cuenta 			= d.id_cuenta "; 
	$sql.= "   AND b.id_empleado 		= e.id_empleado ";
	$sql.= "   AND b.id_centro_costos 	= f.id_centro_costos ";
	$sql.= "   AND f.id_direccion 		= g.id_direccion ";
	$sql.= "   AND f.id_subdireccion 	= h.id_subdireccion  ";
	$sql.= "   AND a.id_mes 			= i.id_mes ";
	$sql.= "   AND b.id_producto 		= j.id_producto ";
	$sql.= "   AND f.id_departamento 	= k.id_departamento ";
	$sql.= "   AND a.id_factura_estatus = l.id_factura_estatus ";
	
	//echo "sql", $sql;
	//echo "<br>";
		
	$result = mysqli_query($mysql, $sql);		
	
	$tsv  = array();
	$html = array();		
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{		
		$tsv[]  = implode("\t", $row);
   		$html[] = "<tr><td>" .implode("</td><td>", $row) ."</td></tr>";		
	} 
	
	$titulos ="<tr>";
	$titulos.="	<td align='center' colspan='24' style='font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold;'>CSI - FACTURACION $id_anio</td>";
	$titulos.="</tr>";
	$titulos.="<tr>";
	$titulos.="	<td align='center' bgcolor='#003366' colspan='15' style='font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold;'><font color=white>CABECERA</font></td>";
	$titulos.="	<td align='center' bgcolor='#003333' colspan='9' style='font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold;'><font color=white>DETALLE</font></td>";
	$titulos.="</tr>";
	$titulos.="<tr>";
	$titulos.="	<td align='center' bgcolor='#003366'><font color=white>AÑO</font></td>";
	$titulos.=" <td align='center' bgcolor='#003366'><font color=white>FACTURA</font></td>";
	$titulos.=" <td align='center' bgcolor='#003366'><font color=white>PROVEEDOR</font></td>";
	$titulos.=" <td align='center' bgcolor='#003366'><font color=white>CUENTA</font></td>";
	$titulos.=" <td align='center' bgcolor='#003366'><font color=white>PRODUCTO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003366'><font color=white>MONTO USD</font></td>";
	$titulos.="	<td align='center' bgcolor='#003366'><font color=white>MONTO MXN</font></td>";
	$titulos.="	<td align='center' bgcolor='#003366'><font color=white>MONTO EUR</font></td>";
	$titulos.="	<td align='center' bgcolor='#003366'><font color=white>TIPO DE CAMBIO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003366'><font color=white>FECHA FACTURA</font></td>";
	$titulos.="	<td align='center' bgcolor='#003366'><font color=white>FECHA INICIO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003366'><font color=white>FECHA FINAL</font></td>";
	$titulos.="	<td align='center' bgcolor='#003366'><font color=white>MES PAGO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003366'><font color=white>NOTAS</font></td>";
	$titulos.="	<td align='center' bgcolor='#003366'><font color=white>ESTATUS</font></td>";
	$titulos.="	<td align='center' bgcolor='#003333'><font color=white>REGISTRO</font></td>";		
	$titulos.="	<td align='center' bgcolor='#003333'><font color=white>NOMBRE</font></td>";
	$titulos.="	<td align='center' bgcolor='#003333'><font color=white>PRECIO USD</font></td>";
	$titulos.="	<td align='center' bgcolor='#003333'><font color=white>PRECIO MXN</font></td>";
	$titulos.="	<td align='center' bgcolor='#003333'><font color=white>PRECIO EUR</font></td>";		
	$titulos.="	<td align='center' bgcolor='#003333'><font color=white>CR</font></td>";
	$titulos.="	<td align='center' bgcolor='#003333'><font color=white>CR-DIRECCION</font></td>";
	$titulos.="	<td align='center' bgcolor='#003333'><font color=white>CR-SUBDIRECCION</font></td>";
	$titulos.="	<td align='center' bgcolor='#003333'><font color=white>CR-DEPARTAMENTO</font></td>";
	$titulos.="</tr>";		
		
	$html = "<table border='1'>". $titulos . implode("\r\n", $html) ."</table>";
	
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "EXCEL", "TBL_FACTURA TBL_FACTURA_DETALLE" , "$id_login" ,   "tx_anio=$id_anio" , ""  ,  "excel_facturacion.php");
	 //<\BITACORA>
	 

	echo $html;
		
	mysqli_close($mysql);
?>      

