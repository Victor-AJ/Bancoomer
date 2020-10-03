<?
 header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    session_cache_limiter("must-revalidate");
  	$fileName = 'Factura '.$tx_nombre_archivo.' Carta Aceptacion.xls';
	header("Content-type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=$fileName");
	$actionBita="EXCEL";
		

	
	session_start();
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");

	$mysql=conexion_db();
	if 	(isset($_SESSION["sess_user"])) 
		$id_login = $_SESSION['sess_iduser'];
	
	$id_factura	= $_GET['id']; 	
	
	# =======================================================================
	# Informacion de los Datos Fijos 
	# =======================================================================
	
	$sql = "   SELECT * ";
	$sql.= "   	 FROM tbl_carta_aceptacion ";
	$sql.= " ORDER BY id_carta_aceptacion ";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCarta[] = array(
  			'tx_empresa'			=>$row["tx_empresa"],
			'tx_nombre_glg'			=>$row["tx_nombre_glg"],
	  		'tx_codigo_glg'			=>$row["tx_codigo_glg"],
	  		'tx_linea_gasto'		=>$row["tx_linea_gasto"],
			'tx_codigo_cuenta'		=>$row["tx_codigo_cuenta"],
			'tx_familia'			=>$row["tx_familia"],
			'tx_orden_compra'		=>$row["tx_orden_compra"],
			'tx_requisicion_numero'	=>$row["tx_requision_numero"],
			'tx_numero_partida'		=>$row["tx_numero_partida"],
			'tx_pais'				=>$row["tx_pais"]
		);
	} 	
	
	for ($i=0; $i < count($TheCarta); $i++)	{ 	        			 
		while ($elemento = each($TheCarta[$i]))	
			$tx_empresa				=$TheCarta[$i]['tx_empresa'];				  		
			$tx_nombre_glg			=$TheCarta[$i]['tx_nombre_glg'];
			$tx_codigo_glg			=$TheCarta[$i]['tx_codigo_glg'];
			$tx_linea_gasto			=$TheCarta[$i]['tx_linea_gasto'];
			$tx_codigo_cuenta		=$TheCarta[$i]['tx_codigo_cuenta'];
			$tx_familia				=$TheCarta[$i]['tx_familia'];
			$tx_orden_compra		=$TheCarta[$i]['tx_orden_compra'];
			$tx_requisicion_numero	=$TheCarta[$i]['tx_requisicion_numero'];
			$tx_numero_partida		=$TheCarta[$i]['tx_numero_partida'];
			$tx_pais				=$TheCarta[$i]['tx_pais'];
	}	
	
	# =======================================================================
	# Informacion de la Cabecera de la Factura
	# =======================================================================
		
	$sql = "   SELECT a.*, tx_proveedor, tx_rfc, b.tx_descripcion, tx_contrato, tx_extranjero, tx_iva, tx_cuenta ";
	$sql.= "   	 FROM tbl_factura a, tbl_proveedor b, tbl_cuenta c ";
	$sql.= "    WHERE a.id_factura 		= $id_factura ";
	$sql.= "      AND a.id_proveedor 	= b.id_proveedor ";
	$sql.= "      AND a.id_cuenta 		= c.id_cuenta ";
	
	//echo "sql ",$sql;
	//echo "<br>";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheFactura[] = array(
  			'tx_factura'	=>$row["tx_factura"],
			'fh_alta'		=>$row["fh_alta"],
			'fh_inicio'		=>$row["fh_inicio"],
			'fh_final'		=>$row["fh_final"],
	  		'fl_precio_usd'	=>$row["fl_precio_usd"],
	  		'fl_precio_mxn'	=>$row["fl_precio_mxn"],
			'fl_precio_eur'	=>$row["fl_precio_eur"],
			'fl_tipo_cambio'=>$row["fl_tipo_cambio"],
			'tx_proveedor'	=>$row["tx_proveedor"],
			'tx_rfc'		=>$row["tx_rfc"],
			'tx_descripcion'=>$row["tx_descripcion"],
			'tx_contrato'	=>$row["tx_contrato"],
			'tx_extranjero'	=>$row["tx_extranjero"],
			'tx_iva'		=>$row["tx_iva"],
			'tx_cuenta'		=>$row["tx_cuenta"]
		);
	} 	
	
	for ($i=0; $i < count($TheFactura); $i++) { 	        			 
		while ($elemento = each($TheFactura[$i]))	
			$tx_factura			=$TheFactura[$i]['tx_factura'];				  		
			$fh_alta			=$TheFactura[$i]['fh_alta'];
			$fh_inicio			=$TheFactura[$i]['fh_inicio'];
			$fh_final			=$TheFactura[$i]['fh_final'];
			$fl_precio_usd		=$TheFactura[$i]['fl_precio_usd'];
			$fl_precio_mxn		=$TheFactura[$i]['fl_precio_mxn'];
			$fl_precio_eur		=$TheFactura[$i]['fl_precio_eur'];
			$fl_tipo_cambio		=$TheFactura[$i]['fl_tipo_cambio'];
			$tx_proveedor		=$TheFactura[$i]['tx_proveedor'];
			$tx_rfc				=$TheFactura[$i]['tx_rfc'];
			$tx_descripcion		=$TheFactura[$i]['tx_descripcion'];
			$tx_contrato		=$TheFactura[$i]['tx_contrato'];
			$tx_extranjero		=$TheFactura[$i]['tx_extranjero'];
			$tx_iva				=$TheFactura[$i]['tx_iva'];
			$tx_cuenta			=$TheFactura[$i]['tx_cuenta'];
	}	
	
	$fh_alta 		= cambiaf_a_normal($fh_alta);
	$fh_inicio 		= cambiaf_a_normal($fh_inicio);
	$fh_final 		= cambiaf_a_normal($fh_final);
	
	//$fh_factura = ereg_replace( ("/"), "", $fh_factura ); 
	
	if ($fl_precio_usd <> 0 ) {
		$tx_moneda = "USD";
		$tx_moneda_usd = "X";
		$fl_precio = $fl_precio_usd;
	} else if ($fl_precio_mxn <> 0 ) { 
		$tx_moneda = "MXN";
		$tx_moneda_mxn = "X";
		$fl_precio = $fl_precio_mxn;
	} else if ($fl_precio_eur <> 0 ) {
		$tx_moneda = "EUR";
		$tx_moneda_eur = "X";
		$fl_precio = $fl_precio_eur;
	}	
	
	if ($tx_extranjero==0) $tx_extranjero_nal="X";
	else if ($tx_extranjero==1) $tx_extranjero_ext="X";		
	
	$tx_nombre_archivo = $tx_factura;

	# ==========================	
	# Si es nota de credito
	# ==========================
	
	if ($fl_precio < 0) {
		
		$fl_precio_nota 	= $fl_precio * -1;
		$fl_precio			= "";			
		$tx_factura_nota 	= $tx_factura;
		$tx_factura 		= "";		
		
		if ($tx_iva==1) {
			$fl_iva_nota	= round($fl_precio_nota * .16,2);
			$fl_monto_nota	= $fl_precio_nota + $fl_iva_nota;
		} else {
			$fl_iva_nota 	= 0;
			$fl_monto_nota 	= $fl_precio_nota + $fl_iva_nota;
		}
		$tx_precio_letra = num2letras($fl_monto_nota, $tx_moneda); 	
		$fl_precio_nota	='$'.number_format($fl_precio_nota,2);	
		$fl_iva_nota	='$'.number_format($fl_iva_nota,2);	
		$fl_monto_nota	='$'.number_format($fl_monto_nota,2);	
		
	} else {
	
		if ($tx_iva==1) {
			$fl_iva 	= round($fl_precio * .16,2);
			$fl_monto 	= $fl_precio + $fl_iva;
		} else {
			$fl_iva 	= 0;
			$fl_monto 	= $fl_precio + $fl_iva;
		}
		$tx_precio_letra = num2letras($fl_monto, $tx_moneda); 	
		$fl_precio	='$'.number_format($fl_precio,2);	
		$fl_iva		='$'.number_format($fl_iva,2);	
		$fl_monto	='$'.number_format($fl_monto,2);	
	}	
	
	
	# ============================================
	# Busco CR Unico 
	# ============================================
				
	$sql = "   SELECT a.id_centro_costos, tx_centro_costos ";
   	$sql.= " 	  FROM tbl_factura_detalle a, tbl_centro_costos b ";
   	$sql.= "    WHERE a.id_factura			= $id_factura "; 
	$sql.= "      AND a.id_centro_costos	= b.id_centro_costos ";
	
	//	INACTIVOS
		$sql.= "      AND a.tx_indicador= '1'  ";
	
	$sql.= " GROUP BY a.id_centro_costos ";	
	
	# =======================================================================
	# Busca CR Unico
	# =======================================================================
	
	//$sql = "   SELECT d.id_centro_costos, tx_centro_costos ";
	//$sql.= "	 FROM tbl_factura a, tbl_licencia b, tbl_empleado c, tbl_centro_costos d ";
	//$sql.= "    WHERE id_factura 			= $id_factura ";
	//$sql.= "      AND a.id_cuenta 			= b.id_cuenta ";
	//$sql.= "      AND b.id_empleado 		= c.id_empleado ";
	//$sql.= "      AND c.id_centro_costos	= d.id_centro_costos ";
	//$sql.= " GROUP BY d.id_centro_costos ";
	
	//echo "aaa", $sql;

	$result = mysqli_query($mysql, $sql);	
	$num_rows = mysqli_num_rows($result);	
	
	if ($num_rows == 1) {	
	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{	
			$TheCRUnico[] = array(
				'tx_centro_costos'	=>$row["tx_centro_costos"]	
			);
		} 	
		
		for ($i=0; $i < count($TheCRUnico); $i++)	{ 	        			 
			while ($elemento = each($TheCRUnico[$i]))	
				$tx_centro_costos		=$TheCRUnico[$i]['tx_centro_costos'];				  		
		}	
	} else {
		
		$tx_centro_costos 	= "7202";
		$tx_derrama			= "X";
	}	
	
	// echo "sql",$sql;				
	$tsv  = array();
	$html = array();		

	$titulos ="<tr>";
	$titulos.="	<td align='center' colspan='9' style='font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold;'>CARTA ACEPTACI&Oacute;N DE BIEN O SERVICIO</font></td>";
	$titulos.="</tr>";		
	$titulos.="<tr>";
	$titulos.="	<td align='center' colspan='9' style='font-family:Arial, Helvetica, sans-serif; font-size:18px; font-weight:bold;' bgcolor='#CCCCCC'>DATOS GENERALES</td>";
	$titulos.="</tr>";		
	$titulos.="<tr>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Empresa / Sociedad receptora del servicio:</td>";
	$titulos.="	<td align='left' colspan='3' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_empresa</td>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Fecha (D&iacute;a/mes/año):</td>";
	$titulos.="	<td align='left' colspan='4' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$fh_alta</td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Nombre de Proveedor:</td>";
	$titulos.="	<td align='left' colspan='3' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_proveedor</td>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>L&iacute;nea de Gasto asignada:</td>";
	#$titulos.="	<td align='left' colspan='4' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_linea_gasto</td>";
	#$titulos.="	<td align='left' colspan='4' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>GB.00009168-001</td>";
	$titulos.="	<td align='left' colspan='4' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>GB.00029656-001</td>";
	//2016 GB.00021772-001
	//GB.00016416-001
	$titulos.="</tr>";		
	$titulos.="<tr>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>R.F.C. del Proveedor:</td>";
	$titulos.="	<td align='left' colspan='3' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_rfc</td>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Codigo de Cta:</td>";
	$titulos.="	<td align='left' colspan='4' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_codigo_cuenta</td>";
	$titulos.="</tr>";		
	$titulos.="<tr>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>N&uacute;mero de Contrato y Anexo:</td>";
	$titulos.="	<td align='left' colspan='3' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_contrato</td>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Familia (2):</td>";
	$titulos.="	<td align='left' colspan='4' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_familia</td>";
	$titulos.="</tr>";		
	$titulos.="<tr>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Nombre de la GLG:</td>";
	$titulos.="	<td align='left' colspan='3' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_nombre_glg</td>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Orden de compra n&uacute;mero:</td>";
	$titulos.="	<td align='left' colspan='4' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_orden_compra</td>";
	$titulos.="</tr>";		
	$titulos.="<tr>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Codigo GLG:</td>";
	$titulos.="	<td align='left' colspan='3' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_codigo_glg</td>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Requisici&oacute;n n&uacute;mero:</td>";
	$titulos.="	<td align='left' colspan='4' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_requisicion_numero</td>";
	$titulos.="</tr>";		
	$titulos.="<tr>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Nombre Partida / Proyecto:</td>";
	$titulos.="	<td align='left' colspan='3' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_descripcion $tx_cuenta</td>";
	$titulos.="	<td align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>N&uacute;mero Partida / Proyecto:</td>";
	$titulos.="	<td align='left' colspan='4' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_numero_partida</td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' colspan='9' bgcolor='#CCCCCC' style='font-family:Arial, Helvetica, sans-serif; font-size:18px; font-weight:bold;'>DESCRIPCION DE LOS SERVICIOS RECIBIDOS DE CONFORMIDAD</td>";
	$titulos.="</tr>";
	$titulos.="<tr>";
	$titulos.="	<td align='center' colspan='9' bgcolor='#CCCCCC' style='font-family:Arial, Helvetica, sans-serif; font-size:10px;'>(Especificar claramente el servicio, ya que esta Descripci&oacute;n es la que se registra en el sistema)</td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' valign='top' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Descripci&oacute;n del Servicio</td>";
	$titulos.="	<td align='center' valign='middle' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Fecha Inicio servicio<br/>(d&iacute;a/mes/año)</td>";
	$titulos.="	<td align='center' valign='middle' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Fecha Fin servicio<br/>(d&iacute;a/mes/año)</td>";
	$titulos.="	<td align='center' valign='middle' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Estado del pa&iacute;s en donde se <br/>proporcion&oacute; el servicio<br/>(d&iacute;a/mes/año)</td>";
	$titulos.="	<td align='center' valign='top' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>N&uacute;m. de unidades</td>";
	$titulos.="	<td align='center' valign='top' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>N&uacute;m. de Factura</td>";
	$titulos.="	<td align='center' valign='top' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Importe</td>";
	$titulos.="	<td align='center' valign='top' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>N&uacute;mero nota de cr&eacute;dito</td>";
	$titulos.="	<td align='center' valign='top' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Importe</td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_descripcion</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$fh_inicio</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$fh_final</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_pais</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_factura</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>&nbsp;&nbsp;&nbsp;&nbsp;$fl_precio</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_factura_nota</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>&nbsp;&nbsp;&nbsp;&nbsp;$fl_precio_nota</td>";
	$titulos.="</tr>";	
	for ($i=0; $i<10; $i++)
	{	
		$titulos.="<tr>";
		$titulos.="	<td align='center'></td>";
		$titulos.="	<td align='center'></td>";
		$titulos.="	<td align='center'></td>";
		$titulos.="	<td align='center'></td>";
		$titulos.="	<td align='center'></td>";
		if ($i==9) 
		{	
			$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'><em>Subtotal</em></td>"; 
			$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>&nbsp;&nbsp;&nbsp;&nbsp;$fl_precio</td>";
			$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'></td>"; 
			$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>&nbsp;&nbsp;&nbsp;&nbsp;$fl_precio_nota</td>";
		} else {
			$titulos.=" <td align='center'></td>"; 
			$titulos.=" <td align='center'></td>";
			$titulos.="	<td align='center'></td>";
			$titulos.="	<td align='center'></td>";
		}	
		$titulos.="</tr>";	
	}	
	$titulos.="<tr>";	
	$titulos.="	<td align='right' colspan='4' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Factor/Porcentaje</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>16%</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>I.V.A.</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$fl_iva</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>I.V.A.</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$fl_iva_nota</td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='left' rowspan='4' valign='top' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Tipo Moneda:</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Moneda Nacional</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>EUR</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Factor/Porcentaje</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>0%</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>I.S.R. Retenido (-)</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>I.S.R. Retenido (-)</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";	
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>$tx_moneda_mxn</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>$tx_moneda_eur</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Factor/Porcentaje</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>0%</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>I.V.A. Retenido (-)</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>I.V.A. Retenido (-)</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>USD</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Otra Divisa (Especificar)</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Factor/Porcentaje</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>0%</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Impuesto Cedular</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>I.S.R. Retenido (-)</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>$tx_moneda_usd</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Factor/Porcentaje</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>0%</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Otros Impuestos</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Otros Impuestos</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' colspan='5'></td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Importe Total</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>$fl_monto</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Importe Total</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>$fl_monto_nota</td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' colspan='7'></td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold;'><em>Importe Neto a Pagar<br/>(Facturas-Notas de Cr&eacute;dito)</em></td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' rowspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>CR aplicable gasto:</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>&Uacute;nico</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Derrama</td>";
	$titulos.="	<td align='right' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Importe Neto a Pagar con letra:</td>";
	$titulos.="	<td align='center' colspan='3' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_precio_letra</td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>$tx_centro_costos</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>$tx_derrama</td>";
	$titulos.="	<td align='right' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'></td>";
	$titulos.="	<td colspan='3' align='right'></td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' colspan='9' bgcolor='#CCCCCC' style='font-family:Arial, Helvetica, sans-serif; font-size:18px;'>Datos Adicionales</td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Tipo de Pago:</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Ordinario</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>X</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Extraordinario (1)</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' colspan='4'></td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Factura correpondente a Anticipo:</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>No</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>X</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Si</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>N&uacute;mero de Anticipo</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Fecha de Anticipo</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Proveedor Nacional:</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Serv. Nacional</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>X</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Serv. Fronterizo</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='right' rowspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Calif. R.O.</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>X</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Proveedor Extranjero:</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>No</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>$tx_extranjero_nal</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Si</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>$tx_extranjero_ext</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>No</td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>Si</td>";
	$titulos.="</tr>";
	$titulos.="<tr>";
	$titulos.="	<td align='center' colspan='9' bgcolor='#CCCCCC' style='font-family:Arial, Helvetica, sans-serif; font-size:18px;'>Recibimos los servicios descritos en esta carta y autorizamos la liberaci&oacute;n</td>";
	$titulos.="</tr>";	
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'><br>M75730<br>ESP</td>";
	$titulos.="	<td align='center' colspan='8'>&nbsp;<br/><br/><br/><br/></td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center'></td>";
	$titulos.="	<td align='center' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'><em></em></td>";	
	$titulos.="	<td align='center'></td>";
	$titulos.="	<td align='center' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'><em>Registrado en la herramienta de Control Servicios Inform&aacute;ticos (CSI).</em></td>";
	$titulos.="	<td align='center' colspan='3'></td>";	
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Firma (AUTORIZA)</td>";	
	$titulos.="	<td align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";
	$titulos.="	<td align='center' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Firma (REVISO)</td>";
	$titulos.="	<td align='center' colspan='3' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'>Firma Proveedor</td>";	
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'><em>Nombre:</em></td>";
	//$titulos.="	<td align='center' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>SANDRA MEDRANO MORENO</td>";
	$titulos.="	<td align='center' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>VICTOR DIAZ GRAJALES</td>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'><em>Nombre:</em></td>";
	//$titulos.="	<td align='center' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>VICTOR DIAZ GRAJALES</td>";	
	$titulos.="	<td align='center' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";	
	$titulos.="	<td align='center' colspan='3' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>$tx_proveedor</td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'><em>Puesto:</em></td>";
	$titulos.="	<td align='center' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>UDA 014 de Servicios de Información</td>"; //SD Control Financiera Informática
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'><em>Puesto:</em></td>";
	$titulos.="	<td align='center' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";	
	//UDA 014 de Servicios de Información
	$titulos.="	<td align='center' colspan='3' style='font-family:Arial, Helvetica, sans-serif; font-size:10px;'>Adicionalmente Sello de la empresa en caso de persona Moral</td>";
	$titulos.="</tr>";	
	$titulos.="<tr>";
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'><em>Registro:</em></td>";
	$titulos.="	<td align='center' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'>8207412</td>";
	//9215455
	$titulos.="	<td align='right' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;'><em>Registro:</em></td>";
	$titulos.="	<td align='center' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:12px;'></td>";	
	//8207412
	$titulos.="	<td colspan='3'></td>";
	$titulos.="</tr>";	
	
	$pie_pagina ="<tr>";
	$pie_pagina.="	<td align='left' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:10px;'>Obligatorio en caso de ser inversi&oacute;n:</td>";
	$pie_pagina.="	<td align='left' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:10px;'>(1) Sujeto de Autorizaci&oacute;n</td>";
	$pie_pagina.="	<td align='left' colspan='2' style='font-family:Arial, Helvetica, sans-serif; font-size:10px;'>(2) Pendiente por Definir</td>";
	$pie_pagina.="</tr>";	

	$html = "<table border='1'>". $titulos ."</table>";
	$html.= "<table border='0'>". $pie_pagina ."</table>";
	
	//	<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql,"EXCEL" , "TBL_FACTURA TBL_FACTURA_DETALLE" , "$id_login" ,   "id_factura=$id_factura" , "$id_factura"  ,  "excel_carta_aceptacion.php");
	 //<\BITACORA>
 

	//echo $tsv;	
	//echo "<br>";	
	echo $html;
		
	mysqli_close($mysql);
?>      

<tr align="right">