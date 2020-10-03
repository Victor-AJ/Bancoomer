<?
   header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    session_cache_limiter("must-revalidate");
    header("Content-Type: application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename="fileToExport.xls"');
session_start();
include("includes/funciones.php");
include_once  ("Bitacora.class.php"); 

	
	if 	(isset($_SESSION["sess_user"])) 
		$id_login = $_SESSION['sess_iduser'];
	
		
	$mysql=conexion_db();
	
	$id_factura	= $_GET['id']; 			
	
	$sql = "   SELECT a.*, tx_gps ";
	$sql.= "   	 FROM tbl_factura a, tbl_proveedor b ";
	$sql.= "    WHERE id_factura 		= $id_factura ";
	$sql.= "      AND a.id_proveedor 	= b.id_proveedor ";	
	
	//echo "<br>";
	//echo "sql", $sql;
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheFactura[] = array(
  			'tx_factura'	=>$row["tx_factura"],
			'fh_factura'	=>$row["fh_factura"],
			'fh_contable'	=>$row["fh_contable"],
	  		'fl_precio_usd'	=>$row["fl_precio_usd"],
	  		'fl_precio_mxn'	=>$row["fl_precio_mxn"],
			'fl_precio_eur'	=>$row["fl_precio_eur"],
			'fl_tipo_cambio'=>$row["fl_tipo_cambio"],
			'tx_referencia' =>$row["tx_referencia"],
			'tx_gps' 		=>$row["tx_gps"]
		);
	} 	
	
	for ($i=0; $i < count($TheFactura); $i++)	{ 	        			 
		while ($elemento = each($TheFactura[$i]))	
			$tx_factura			=$TheFactura[$i]['tx_factura'];				  		
			$fh_factura			=$TheFactura[$i]['fh_factura'];
			$fh_contable		=$TheFactura[$i]['fh_contable'];
			$fl_precio_usd		=$TheFactura[$i]['fl_precio_usd'];
			$fl_precio_mxn		=$TheFactura[$i]['fl_precio_mxn'];
			$fl_precio_eur		=$TheFactura[$i]['fl_precio_eur'];
			$fl_tipo_cambio		=$TheFactura[$i]['fl_tipo_cambio'];
			$tx_referencia		=$TheFactura[$i]['tx_referencia'];
			$tx_gps				=$TheFactura[$i]['tx_gps'];
	}	
	
	$fh_factura = cambiaf_a_normal($fh_factura);
	$fh_factura = ereg_replace( ("/"), "", $fh_factura ); 
	
	$fh_contable = cambiaf_a_normal($fh_contable);
	$fh_contable = ereg_replace( ("/"), "", $fh_contable ); 
	
	if ($fl_precio_usd > 0.00) {
		
		$tx_moneda 		= "USD";
		$fl_precio 		= $fl_precio_usd;
		$fl_precio_mxn	= $fl_precio_mxn;		
			
	//} else if ($fl_precio_usd > 0.00 && $fl_precio_mxn > 0.00 ) {
	
	} else if ($fl_precio_mxn > 0.00 ) {
		
		$tx_moneda 		= "	MXN";
		$fl_precio 		= $fl_precio_mxn;
		$fl_precio_mxn 	= $fl_precio_mxn;	
			
	//} else if ($fl_precio_usd == 0.00 && $fl_precio_mxn < 0.00 ) {
	
	} else {
	
		$tx_moneda 		= "EUR";
		$fl_precio 		= $fl_precio_mxn;
			
//	} else {
		
//		$tx_moneda 		= "EUR";
//		$fl_precio 		= $fl_precio_eur * $fl_tipo_cambio;	
		
	}
	
	//echo "Moneda",$tx_moneda; 
	//echo "<br>";
						
	# ============================
	# Factura Detalle
	# ============================					
	if ($tx_moneda=="USD") {

		//$sql = "  SELECT SUM( a.fl_precio_mxn ) AS fl_precio_usd_det, CONCAT( 'MX1100', tx_centro_costos ) AS tx_centro_costos ";
		//$sql = "  SELECT SUM( a.fl_precio_usd * $fl_precio_mxn ) / $fl_precio AS fl_precio_usd_det, CONCAT( 'MX1100', tx_centro_costos ) AS tx_centro_costos ";	
		$sql = "  SELECT SUM( a.fl_precio_mxn) AS fl_precio_usd_det, CONCAT( 'MX1100', tx_centro_costos ) AS tx_centro_costos ";	
		$sql.= "     FROM tbl_factura_detalle a, tbl_centro_costos b, tbl_factura c ";
		$sql.= "    WHERE a.id_factura 			= $id_factura ";
		$sql.= "      AND a.id_centro_costos 	= b.id_centro_costos ";
		$sql.= "      AND a.id_factura 			= c.id_factura ";
		//	INACTIVOS
		$sql.= "      AND a.tx_indicador= '1'  ";
				
				
		$sql.= " GROUP BY tx_centro_costos ";
		
		//echo "<br>";
		//echo "Entre 1";
		
	} elseif ($tx_moneda=="EUR") {

		$sql = "  SELECT SUM( a.fl_precio_mxn) AS fl_precio_usd_det, CONCAT( 'MX1100', tx_centro_costos ) AS tx_centro_costos ";
		$sql.= "     FROM tbl_factura_detalle a, tbl_centro_costos b, tbl_factura c ";
		$sql.= "    WHERE a.id_factura 			= $id_factura ";
		$sql.= "      AND a.id_centro_costos 	= b.id_centro_costos ";
		$sql.= "      AND a.id_factura 			= c.id_factura ";
		//	INACTIVOS
		$sql.= "      AND a.tx_indicador= '1'  ";
		
		$sql.= " GROUP BY tx_centro_costos ";
		
		//echo "<br>";
		//echo "Entre 2";

	} else {

		$sql = "  SELECT SUM( a.fl_precio_mxn ) AS fl_precio_usd_det, CONCAT( 'MX1100', tx_centro_costos ) AS tx_centro_costos ";
		$sql.= "     FROM tbl_factura_detalle a, tbl_centro_costos b, tbl_factura c ";
		$sql.= "    WHERE a.id_factura 			= $id_factura ";
		$sql.= "      AND a.id_centro_costos 	= b.id_centro_costos ";
		$sql.= "      AND a.id_factura 			= c.id_factura ";
		
		//	INACTIVOS
		$sql.= "      AND a.tx_indicador= '1'  ";
		
		$sql.= " GROUP BY tx_centro_costos ";
		
		//echo "<br>";
		//echo "Entre 3";
	}

	//echo "<br>";
	//echo "sql", $sql;
	
	
	$fl_precio 			= number_format($fl_precio_mxn,2);
	$fl_precio_usd_det 	= number_format($fl_precio_usd_det,2);
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheFacturaDetalle[] = array(
			'tx_centro_costos'	=>$row["tx_centro_costos"],				
	 		'fl_precio_usd_det'	=>$row["fl_precio_usd_det"],
		);
	} 	

	$html = array();	

	$c1 = "1";									# No. Docto
	$c2 = "MX11";								# Sociedad  
	$c3 = $fh_factura;							# Fecha Docto  
	$c4 = $fh_contable;							# Fecha Contable
	$c5 = "SD";									# Tipo Doc
	$c6 = "MXN";								# Moneda
	$c7 = "$tx_gps";							# Numero GPS
	$c8 = $tx_referencia;						# Referencia
	$c91= "50";									# Clave Cont 1
	$c92= "40";									# Clave Cont 2
	$c10= "32533003";							# Cuenta
	$c11= "";									# Importe
	$c12= "-";									# Iva
	$c13= "";									# CeCo
	$c14= "Servicios de Informaci&oacute;n";	# Texto Posicion
	$c15= "$tx_factura";						# Asignacion
	
	if ($c3=="") $c3="-";	
	if ($c4=="") $c4="-";	
			
	$j=0;
	
	for ($i=0; $i < count($TheFacturaDetalle); $i++)	{ 	        			 
		while ($elemento = each($TheFacturaDetalle[$i]))					  		
			$fl_precio_usd_det	=$TheFacturaDetalle[$i]['fl_precio_usd_det'];	
			$tx_centro_costos	=$TheFacturaDetalle[$i]['tx_centro_costos'];						
			
			$j++;
			$fl_precio_usd_det = number_format($fl_precio_usd_det,2);
		
			if ($j==1) {
				$html[] = "<tr>";
				$html[].= "<td align='center'>$c1</td>";
				$html[].= "<td align='center'>$c2</td>";
				$html[].= "<td align='center'>$c3</td>";
				$html[].= "<td align='center'>$c4</td>";
				$html[].= "<td align='center'>$c5</td>";			
				$html[].= "<td align='center'>$c6</td>";
				$html[].= "<td align='center'>$c7</td>";
				$html[].= "<td align='center'>$c8</td>";
				$html[].= "<td align='center'>$c91</td>";
				$html[].= "<td align='center'>$c10</td>";
				$html[].= "<td align='right'>$fl_precio_mxn</td>";
				$html[].= "<td align='center'>$c12</td>";
				$html[].= "<td align='center'>MX11007202</td>";
				$html[].= "<td align='center'>$c14</td>";
				$html[].= "<td align='center'>$c15</td>";
				$html[].= "</tr>";
			
				$j++;
			}
		
			$html[] = "<tr>";
			$html[].= "<td align='center'>$c1</td>";
			$html[].= "<td align='center'>$c2</td>";
			$html[].= "<td align='center'>$c3</td>";
			$html[].= "<td align='center'>$c4</td>";
			$html[].= "<td align='center'>$c5</td>";
			$html[].= "<td align='center'>$c6</td>";
			$html[].= "<td align='center'>$c7</td>";
			$html[].= "<td align='center'>$c8</td>";
			$html[].= "<td align='center'>$c92</td>";
			$html[].= "<td align='center'>$c10</td>";
			$html[].= "<td align='right'>$fl_precio_usd_det</td>";
			$html[].= "<td align='center'>$c12</td>";
			$html[].= "<td align='center'>$tx_centro_costos</td>";
			$html[].= "<td align='center'>$c14</td>";
			$html[].= "<td align='center'>$c15</td>";
			$html[].= "</tr>";	
		}	

		$titulos ="<tr>";
		$titulos.="	<td align='center' bgcolor='#003399'><font color=white>No. Docto</font></td>";
		$titulos.=" <td align='center' bgcolor='#003399'><font color=white>Sociedad</font></td>";
		$titulos.=" <td align='center' bgcolor='#003399'><font color=white>Fecha Docto</font></td>";
		$titulos.=" <td align='center' bgcolor='#003399'><font color=white>Fecha Contable</font></td>";
		$titulos.=" <td align='center' bgcolor='#003399'><font color=white>Tipo Docto</font></td>";
		$titulos.="	<td align='center' bgcolor='#003399'><font color=white>Moneda</font></td>";
		$titulos.="	<td align='center' bgcolor='#003399'><font color=white>Texto Cabecera</font></td>";
		$titulos.="	<td align='center' bgcolor='#003399'><font color=white>Referencia</font></td>";
		$titulos.="	<td align='center' bgcolor='#003399'><font color=white>Clave Cont</font></td>";
		$titulos.="	<td align='center' bgcolor='#003399'><font color=white>Cuenta</font></td>";
		$titulos.="	<td align='center' bgcolor='#003399'><font color=white>Importe</font></td>";
		$titulos.="	<td align='center' bgcolor='#003399'><font color=white>Ind Iva</font></td>";
		$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CeCo</font></td>";
		$titulos.="	<td align='center' bgcolor='#003399'><font color=white>Texto Posici&oacute;n</font></td>";
		$titulos.="	<td align='center' bgcolor='#003399'><font color=white>Asignaci&oacute;n</font></td>";
		$titulos.="</tr>";		
		
		
		//<BITACORA>
		$myBitacora = new Bitacora();
		$myBitacora->anotaBitacora ($mysql, "EXCEL" , "TBL_FACTURA TBL_FACTURA_DETALLE" , "$id_login" ,  "" , $id_factura   ,  "excel_derrama.php");
		//<\BITACORA
				
		
		
		$html = "<table border='1'>". $titulos . implode("\r\n", $html) ."</table>";
	
	//	$fileName = "Factura ".$tx_factura." Derrama.xls";
//		header("Content-type: application/vnd.ms-excel"); 
	//	header("Content-Disposition: attachment; filename=$fileName");

	echo $html;
		
	mysqli_close($mysql);
?>      

