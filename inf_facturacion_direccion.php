<?php
session_start();
if 	(isset($_SESSION["sess_user"]))
{
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");  
	$mysql=conexion_db();		
	$id_login = $_SESSION['sess_iduser'];
	
		
	$par_anio 		= $_GET["par_anio"];	
	$par_agrupacion	= $_GET["par_agrupacion"];	
	$par_moneda		= $_GET["par_moneda"];	
	$par_estatus	= $_GET["par_estatus"];	
	$par_cuenta		= $_GET["par_cuenta"];
	
	
	if ($par_anio=="2011") $in_tc = 13.5;
	else $in_tc = 14;
	
	$fecha_hoy 		= date("j-m-Y");	
	
	?>
    <input id="par_anio" name="par_anio" type="hidden" value="<? echo $par_anio ?>" /> 
	<script type="text/javascript">	
	
		$("#verFacturacionDireccion").find("tr").hover(		 
        	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
         );
		
		function btnInformesDirFac(v1)
		{
			var p1= parseInt(v1); 			
			var id0="par_direccion="+p1;
			var id1="&par_anio="+$("#par_anio").val();
			var id2="&par_moneda="+$("#sel_moneda_d").val();
			var id3="&par_estatus="+$("#sel_estatus_d").val();
			var id4="&par_cuenta="+$("#sel_cta_contable").val();
			//alert ("Entre "+id0+id1+id2+id3);
	
			$("#divFacturacionDirN1").html("");
			$("#divFacturacionDirN2").html("");
			$("#divDetalleFac").html("");
			$("#divDetalleFacDir").html("");
	
			loadHtmlAjax(true, $("#divFacturacionDirN1"), "inf_facturacion_dir_sub.php?"+id0+id1+id2+id3+id4); 
		}	
		
		function btnInformesFacDetFac(v1,v2)
		{
			var p1= parseInt(v1);
			var p2= parseInt(v2);
			var id0="par_direccion="+p1;
			var id1="&par_anio="+$("#par_anio").val();
			var id2="&par_moneda="+$("#sel_moneda_d").val();
			var id3="&par_estatus="+$("#sel_estatus_d").val();
			var id4="&par_proveedor="+p2;			
			var id5="&par_cuenta="+$("#sel_cta_contable").val();
			//alert ("Entre "+id0+id1+id2+id3+id4);	
			$("#divFacturacionDirN1").html("");
			$("#divFacturacionDirN2").html("");
			$("#divDetalleFac").html("");
			$("#divDetalleFacDir").html("");		
	
			loadHtmlAjax(true, $("#divFacturacionDirN1"), "inf_facturacion_dir_sub_mes_fac.php?"+id0+id1+id2+id3+id4+id5); 
		}	 
				
		// Graficas de Montos
		// =========================================				
		function btnGraDirMontoBlo(v1)
		{ 			
			var v1 = parseInt(v1); 	
			var p1 = "par_proveedor="+v1; 	
			var p2 = "&par_anio="+$("#par_anio").val();
			var p3 = "&par_moneda="+$("#sel_moneda_d").val();
			var p4 = "&par_estatus="+$("#sel_estatus_d").val();
			var p5 = "&par_cuenta="+$("#sel_cta_contable").val();
			var url = "gra_fac_dir_monto_blo.php?"+p1+p2+p3+p4+p5;
			var windowprops = "top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1000, height=700";
			var winName = "_blank";  
			//alert (url);
			window.open(url,winName,windowprops); 
		}
		
		function btnGraDirFacPre(v1)
		{ 					
			var p1 = "id="+v1; 	
			var p2 = "&par_anio="+$("#par_anio").val();
			var p3 = "&par_moneda="+$("#sel_moneda_d").val();	
			var p4 = "&par_estatus="+$("#sel_estatus_d").val();
			var p5 = "&par_cuenta="+$("#sel_cta_contable").val();								
			var url = "gra_fac_dir_pre.php?"+p1+p2+p3+p4+p5;
			//alert ("url"+url);
			var windowprops = "top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1000, height=700";
			var winName = "_blank";  
			window.open(url,winName,windowprops); 
		}
		
		function btnExpFac(v0)
		{ 					
			var id0 ="par_direccion="+v0;
			var id1 ="&par_moneda="+$("#sel_moneda_d").val();				
			var id2 ="&par_anio="+$("#par_anio").val();
			var id3 ="&par_estatus="+$("#sel_estatus_d").val();
			var id4 = "&par_cuenta="+$("#sel_cta_contable").val();		
			var url ="excel_facturacion_eficiencia.php?"+id0+id1+id2+id3+id4;
			//alert("url"+url);
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1100, height=700";
			var winName='_blank';  						
			window.open(url,winName,windowprops); 
		}
	
	</script> 
	<?	
	
	# ===============================================================================
	# NOTAS	
	# ===============================================================================
	if ($par_moneda=="USD") $nota0 ="Monto en USD (D&oacute;lares Americanos).";
	else if ($par_moneda=="MXN") $nota0 ="Monto en MXN (Pesos Mexicanos).";
	else if ($par_moneda=="EUR") $nota0 ="Monto en EUR (EUROS).";
	
	# ===============================================================================
	# BUSCA ULTIMO MES DE CAPTURA DE FACTURAS	
	# ===============================================================================
	$sql = "   SELECT tx_mes ";
	$sql.= "	 FROM tbl_factura a, tbl_mes b ";
	$sql.= "    WHERE tx_anio 	= '$par_anio' ";
	$sql.= "      AND a.id_mes 	= b.id_mes ";
	$sql.= " GROUP BY a.id_mes ";
	$sql.= " ORDER BY a.id_mes ASC ";
	
	//echo " sql ",$sql;
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeMes[] = array(
			"tx_mes"		=>$row["tx_mes"]
		);
	} 	
	
	for ($k=0; $k < count($TheInformeMes); $k++)
	{ 	        			 
		while ($elemento = each($TheInformeMes[$k]))				
			$tx_mes	=$TheInformeMes[$k]["tx_mes"];	
	}
	
	//Query que genera los totales por direccion de acuerdo a filtros
	if ($par_moneda=="USD") 
		$sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
		elseif ($par_moneda=="MXN") 
				$sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	 
			elseif ($par_moneda=="EUR") 
					$sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,(b.fl_precio_mxn*$in_tc),(b.fl_precio_eur))) AS total_precio_usd ";
						 
	$sql.= "   FROM  tbl_factura_detalle b , tbl_factura a, tbl_centro_costos c, tbl_direccion d , tbl_producto p";
	$sql.= "  WHERE tx_anio 			= '$par_anio' and a.tx_indicador='1'  ";
	$sql.= "    AND a.id_factura		= b.id_factura and b.tx_indicador='1'  ";
	$sql.= " 	AND b.id_centro_costos 	= c.id_centro_costos and c.tx_indicador='1'  ";
	$sql.= " 	AND c.id_direccion 		= d.id_direccion  and d.tx_indicador='1' ";
	$sql.= "      AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	$sql.= " 	AND b.id_producto 		= p.id_producto and p.tx_indicador='1'  ";

	if ($par_agrupacion==2) { }
		else $sql.= " 	AND d.tx_agrupacion	= '$par_agrupacion' ";
	if ($par_estatus==0) { }
		else $sql.= " 	AND a.id_factura_estatus = $par_estatus ";	
	
		
	if ($par_cuenta <> 0)
		$sql.= " 	AND p.id_cuenta_contable=  $par_cuenta "; 
	
	$sql.= " GROUP BY c.id_direccion ";		
	$sql.= " ORDER BY total_precio_usd DESC ";		 
	
	//echo "sql",$sql;
	
	//if ($par_moneda=="USD") 		$sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(b.fl_precio_usd) + SUM(b.fl_precio_mxn / a.fl_tipo_cambio) AS total_precio_usd ";	
	
    /*	if ($par_moneda=="USD") 		$sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(b.fl_precio_usd) AS total_precio_usd ";	
	else if ($par_moneda=="MXN")	$sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(b.fl_precio_usd * a.fl_tipo_cambio) + SUM(b.fl_precio_mxn) AS total_precio_usd ";	
	$sql.= "   FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d ";
	$sql.= "  WHERE tx_anio 			= '$par_anio' ";
	$sql.= "    AND a.id_factura		= b.id_factura ";
	$sql.= " 	AND b.id_centro_costos 	= c.id_centro_costos ";
	$sql.= " 	AND c.id_direccion 		= d.id_direccion ";
	if ($par_agrupacion==2) { }
	else $sql.= " 	AND d.tx_agrupacion	= '$par_agrupacion' ";
	if ($par_estatus==0) { }
	else $sql.= " 	AND a.id_factura_estatus = $par_estatus ";	
	$sql.= " GROUP BY c.id_direccion ";		
	$sql.= " ORDER BY total_precio_usd DESC ";		*/
	//echo "sql",$sql;			
	//echo "<br>";			
		
	//$sql= " DROP VIEW tmp_facturacion ";
	//$result = mysqli_query($mysql, $sql);	
	
	/*			
	if ($par_moneda=="USD") 	$sql_a = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(b.fl_precio_usd) AS total_precio_usd ";	
	elseif ($par_moneda=="MXN")	$sql_a = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(b.fl_precio_mxn /13) AS total_precio_usd ";	

	$sql = " CREATE VIEW ALGORITHM = TEMPTABLE tmp_facturacion AS ";	
	$sql.= "   $sql_a ";
	$sql.= "   FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d ";
	$sql.= "  WHERE tx_anio 			= '$par_anio' ";
	$sql.= "    AND a.id_factura		= b.id_factura ";
	$sql.= " 	AND b.id_centro_costos 	= c.id_centro_costos ";
	$sql.= " 	AND c.id_direccion 		= d.id_direccion ";
	if ($par_agrupacion==2) { }
	else $sql.= " 	AND d.tx_agrupacion	= '$par_agrupacion' ";
	if ($par_estatus==0) { }
	else $sql.= " 	AND a.id_factura_estatus = $par_estatus ";	
	$sql.= " 	AND a.id_moneda 		= 2 ";
	$sql.= " GROUP BY c.id_direccion ";		
	$sql.= " UNION ALL ";	
	$sql.= "   $sql_a ";	
	$sql.= "   FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d ";
	$sql.= "  WHERE tx_anio 			= '$par_anio' ";
	$sql.= "    AND a.id_factura		= b.id_factura ";
	$sql.= " 	AND b.id_centro_costos 	= c.id_centro_costos ";
	$sql.= " 	AND c.id_direccion 		= d.id_direccion ";
	if ($par_agrupacion==1) { }
	else $sql.= " 	AND d.tx_agrupacion	= '$par_agrupacion' ";
	if ($par_estatus==0) { }
	else $sql.= " 	AND a.id_factura_estatus = $par_estatus ";	
	$sql.= " 	AND a.id_moneda 		= 1 ";	
	$sql.= " GROUP BY c.id_direccion ";		
	$sql.= " ORDER BY total_precio_usd DESC ";		
	
	echo "sql_1",$sql;
	
	mysqli_query($mysql, $sql);	
	
	$sql = "   SELECT id_direccion, tx_nombre_corto, sum(total_precio_usd) as total_precio_usd ";
	$sql.= "     FROM tmp_facturacion ";
	$sql.= " GROUP BY id_direccion ";
	$sql.= " ORDER BY total_precio_usd desc ";

	echo "sql",$sql;	
	*/
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeDireccionFac[] = array(
			"id_direccion"		=>$row["id_direccion"],
			"tx_nombre_corto"	=>$row["tx_nombre_corto"],
	  		"total_precio_usd"	=>$row["total_precio_usd"]
		);
	} 	
	
	$registros=count($TheInformeDireccionFac);	
	
	if ($registros==0) {	
		echo "<table align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<br/>";
		echo "<tr>";
		echo "<td class='align-center'><em><b>Sin Informaci&oacute;n ...</b></em></td>";
		echo "</tr>";	
		echo "<br/>";		
		echo "</table>";	 
	} else {		
		echo "<table id='verFacturacionDireccion' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		echo "<td width='3%' rowspan='2' class='ui-state-highlight align-center'>#</td>";							 
		echo "<td width='31%' rowspan='2' class='ui-state-highlight align-center'>DIRECCION</td>";
		echo "<td width='48%' colspan='4' class='ui-state-highlight align-center'>GASTO ACUMULADO - $tx_mes $par_anio</td>";							 
		echo "<td width='12%' rowspan='2' class='ui-state-highlight align-center'>PRESUPUESTO $par_anio</td>";							 
		echo "<td width='3%' rowspan='2' class='ui-state-highlight align-center'>Gr&aacute;fica</td>";						 
		echo "<td width='3%' rowspan='2' class='ui-state-highlight align-center'>Exportar</td>";						 
		echo "</tr>";
		echo "<tr>";
		$MontoB = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMontoBlo(1)' title='Presione para ver la Gr&aacute;fica'>BLOOMBERG</a>";					
		echo "<td width='12%'  class='ui-state-highlight align-center'>$MontoB</td>";	
		$MontoR = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMontoBlo(2)' title='Presione para ver la Gr&aacute;fica'>REUTERS</a>";
		echo "<td width='12%' class='ui-state-highlight align-center'>$MontoR</td>";
		$MontoO = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMontoBlo(3)' title='Presione para ver la Gr&aacute;fica'>OTROS</a>";						 
		echo "<td width='12%' class='ui-state-highlight align-center'>$MontoO</td>";	
		$MontoT = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMontoBlo(0)' title='Presione para ver la Gr&aacute;fica'>TOTAL</a>";
		echo "<td width='12%' class='ui-state-highlight align-center'>$MontoT</td>";		
		echo "</tr>";
		
		$c=0;		
		$fl_total_precio_usd=0;		
		$fl_total_precio_usd_1=0;			
		$fl_total_precio_usd_2=0;			
		$fl_total_precio_usd_3=0;	
		
		for ($i=0; $i < count($TheInformeDireccionFac); $i++)
		{ 	        			 
			while ($elemento = each($TheInformeDireccionFac[$i]))				
				$id_direccion		=$TheInformeDireccionFac[$i]['id_direccion'];	  		
				$tx_nombre_corto	=$TheInformeDireccionFac[$i]['tx_nombre_corto'];				
				$total_precio_usd	=$TheInformeDireccionFac[$i]['total_precio_usd'];				
				
				$fl_total_precio_usd=$fl_total_precio_usd+$total_precio_usd;
				
				$c++;
				
				# ============================					
				# Busco Licencias de BLOOMBERG
				# ============================
				//Query que genera el contabilizado por direccion de acuerdo a filtros Y SOlo BLOOMERG
				$tx_proveedor1 = "BLOOMBERG";
				
				if ($par_moneda=="USD") 
					$sql1 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
				else if 
					($par_moneda=="MXN") $sql1 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	 
				else if 
					($par_moneda=="EUR") 
						$sql1 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,(b.fl_precio_mxn/$in_tc),(b.fl_precio_eur))) AS total_precio_usd ";
							
				$sql1.= "   FROM tbl_factura_detalle b, tbl_factura a,  tbl_centro_costos c, tbl_direccion d, tbl_proveedor e , tbl_producto p ";
				$sql1.= "  WHERE tx_anio 			= '$par_anio' and a.tx_indicador='1' ";
				$sql1.= "    AND a.id_factura		= b.id_factura and b.tx_indicador='1'  ";
				$sql1.= " 	 AND b.id_centro_costos = c.id_centro_costos  and c.tx_indicador='1'  ";
				$sql1.= " 	 AND c.id_direccion 	= d.id_direccion  and d.tx_indicador='1' ";
				$sql1.= "    AND d.id_direccion 	= $id_direccion ";
				$sql1.= "      AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
				$sql1.= "    AND a.id_proveedor 	= e.id_proveedor  and e.tx_indicador='1'  ";
				$sql1.= "    AND tx_proveedor_corto	= '$tx_proveedor1' "; 
				$sql1.= " 	 AND b.id_producto 		= p.id_producto  and p.tx_indicador='1'  ";
				
				if ($par_estatus==0) { }
				else $sql1.= " 	AND a.id_factura_estatus= $par_estatus ";	
				
				if ($par_cuenta <> 0)
					$sql1.= " 	AND p.id_cuenta_contable=  $par_cuenta "; 
				$sql1.= " GROUP BY c.id_direccion ";
				
				//echo "sql",$sql1;	
				//echo "<br>";	
				
				$result1 = mysqli_query($mysql, $sql1);	
				while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
				{	
					$TheInformeDireccionFac_1[] = array(						
						'total_precio_usd_1'	=>$row1["total_precio_usd"]
					);
				} 
				
				$num_result1=mysqli_num_rows($result1);	
				
				for ($j=0; $j < count($TheInformeDireccionFac_1); $j++)	{ 	        			 
					while ($elemento = each($TheInformeDireccionFac_1[$j]))	
						$total_precio_usd_1	=$TheInformeDireccionFac_1[$j]['total_precio_usd_1'];	
				}
				
				if ($num_result1==0) $total_precio_usd_1=0;
			
				$fl_total_precio_usd_1	=$fl_total_precio_usd_1+$total_precio_usd_1;
								
				# ============================					
				# Busco Licencias de REUTERS
				# ============================
				//Query que genera  el contabilizado  por direccion de acuerdo a filtros Y SOlo  REUTERS
				
				$tx_proveedor2 = "REUTERS";
				
				if ($par_moneda=="USD") $sql2 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
				elseif ($par_moneda=="MXN") $sql2 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";
				elseif ($par_moneda=="EUR") $sql2 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,(b.fl_precio_mxn/$in_tc),(b.fl_precio_usd))) AS total_precio_eur ";		 				
				
				$sql2.= "   FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_proveedor e , tbl_producto p ";
				$sql2.= "  WHERE tx_anio 			= '$par_anio'  and a.tx_indicador='1' ";
				$sql2.= "    AND a.id_factura		= b.id_factura and b.tx_indicador='1' ";
				$sql2.= " 	 AND b.id_centro_costos = c.id_centro_costos  and c.tx_indicador='1' ";
				$sql2.= " 	 AND c.id_direccion 	= d.id_direccion and d.tx_indicador='1'  ";
				$sql2.= "    AND d.id_direccion 	= $id_direccion ";
				$sql2.= "      AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
				$sql2.= "    AND a.id_proveedor 	= e.id_proveedor and e.tx_indicador='1'  ";
				$sql2.= "    AND tx_proveedor_corto	= '$tx_proveedor2' "; 
				$sql2.= " 	 AND b.id_producto 		= p.id_producto and p.tx_indicador='1'  ";
				
				if ($par_estatus==0) { }
				else $sql2.= " 	AND a.id_factura_estatus= $par_estatus ";	
				
				if ($par_cuenta <> 0)
					$sql2.= " 	AND p.id_cuenta_contable=  $par_cuenta "; 
					
				$sql2.= " GROUP BY c.id_direccion ";
				
				//echo "<br>";
				//echo "sql 2 ",$sql2;	
								
				$result2 = mysqli_query($mysql, $sql2);						
					
				while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
				{	
					$TheInformeDireccionFac_2[] = array(
						'total_precio_usd_2'	=>$row2["total_precio_usd"]
					);
				} 
				
				$num_result2=mysqli_num_rows($result2);	
				
				for ($k=0; $k < count($TheInformeDireccionFac_2); $k++)	{ 	        			 
					while ($elemento = each($TheInformeDireccionFac_2[$k]))				
						$total_precio_usd_2	=$TheInformeDireccionFac_2[$k]['total_precio_usd_2'];	
				}		
				
				if ($num_result2==0) $total_precio_usd_2=0;
			
				$fl_total_precio_usd_2	=$fl_total_precio_usd_2+$total_precio_usd_2;
				
				# ============================					
				# Busco Licencias de OTROS
				# ============================
				//Query que genera  el contabilizado  por direccion de acuerdo a filtros Y SOlo OTROS
								
				$tx_proveedor3 = "BLOOMBERG";
				$tx_proveedor4 = "REUTERS";			
				
				if ($par_moneda=="USD") 	$sql3 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
				else if ($par_moneda=="MXN") $sql3 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	 
				else if ($par_moneda=="EUR") $sql3 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,(b.fl_precio_mxn*$in_tc),(b.fl_precio_eur))) AS total_precio_usd ";	 
				$sql3.= "   FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_proveedor e  , tbl_producto p ";
				$sql3.= "  WHERE tx_anio 			= '$par_anio'  and a.tx_indicador='1' ";
				$sql3.= "    AND a.id_factura		= b.id_factura  and b.tx_indicador='1' ";
				$sql3.= " 	 AND b.id_centro_costos = c.id_centro_costos  and c.tx_indicador='1'";
				$sql3.= " 	 AND c.id_direccion 	= d.id_direccion  and d.tx_indicador='1'";
				$sql3.= "    AND d.id_direccion 	= $id_direccion ";
				$sql3.= "      AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
		
				$sql3.= "    AND a.id_proveedor 	= e.id_proveedor  and e.tx_indicador='1'";
				$sql3.= " 	 AND b.id_producto 		= p.id_producto  and p.tx_indicador='1'";
				
				$sql3.= "    AND tx_proveedor_corto NOT IN ('$tx_proveedor3','$tx_proveedor4')  ";
				 
				if ($par_estatus==0) { }
				else $sql3.= " 	AND a.id_factura_estatus= $par_estatus ";	
				
				
				if ($par_cuenta <> 0)
					$sql3.= " 	AND p.id_cuenta_contable=  $par_cuenta "; 
					
				
				$sql3.= " GROUP BY c.id_direccion ";
									
				//echo "<br>";
				//echo "sql 3 ",$sql3;	
								
				$result3 = mysqli_query($mysql, $sql3);						
					
				while ($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC))
				{	
					$TheInformeDireccionFac_3[] = array(						
						'total_precio_usd_3'	=>$row3["total_precio_usd"]
					);
				} 
				
				$num_result3=mysqli_num_rows($result3);	
				
				for ($l=0; $l < count($TheInformeDireccionFac_3); $l++)
				{ 	        			 
					while ($elemento = each($TheInformeDireccionFac_3[$l]))	
						$total_precio_usd_3	=$TheInformeDireccionFac_3[$l]['total_precio_usd_3'];	
				}						
				
				if ($num_result3==0) $total_precio_usd_3=0;
								
				$fl_total_precio_usd_3	=$fl_total_precio_usd_3+$total_precio_usd_3;
				
				# ==============================					
				# Busco MONTO del Presupuesto
				# ==============================
				
				if ($par_moneda=="USD") $sql4 = " SELECT fl_presupuesto_usd AS fl_presupuesto ";	
				else 					$sql4 = " SELECT fl_presupuesto_mxn AS fl_presupuesto ";	
				$sql4.= "	FROM tbl_presupuesto_2010 ";
				$sql4.= "  WHERE id_direccion 	= $id_direccion ";	
				$sql4.= "    AND tx_anio 		= '$par_anio' ";	
									
				//echo "<br>";
				//echo "sql 4 ",$sql4;	
								
				$result4 = mysqli_query($mysql, $sql4);						
					
				while ($row4 = mysqli_fetch_array($result4, MYSQLI_ASSOC))
				{	
					$TheInformePresupuesto[] = array(						
						'fl_presupuesto'	=>$row4["fl_presupuesto"]
					);
				} 
				
				$num_result4=mysqli_num_rows($result4);	
				
				for ($m=0; $m < count($TheInformePresupuesto); $m++)
				{ 	        			 
					while ($elemento = each($TheInformePresupuesto[$m]))	
						$fl_presupuesto	=$TheInformePresupuesto[$m]["fl_presupuesto"];	
				}						
				
				if ($num_result4==0) $fl_presupuesto=0;
												
				$fl_total_presupuesto	=$fl_total_presupuesto+$fl_presupuesto;
				
				# ============================	
							
				echo "<tr>";
				for ($a=0; $a<9; $a++)
					{
						switch ($a) 
						{   
							case 0: $TheColumn=$c; 									
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;						
							case 1: $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesDirFac($id_direccion)' title='Presione para ver el detalle de la $tx_nombre_corto ...'>$tx_nombre_corto</a>";
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;																																		
							case 2: if ($total_precio_usd_1==0) $TheColumn="-";
									else {
										$TheColumn=number_format($total_precio_usd_1,0); 
										$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetFac($id_direccion,1)' title='Presione para ver el detalle de $tx_direccion - $tx_proveedor1 - $par_anio ... '>$TheColumn</a>";
									}	
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;																									
							case 3: if ($total_precio_usd_2==0) $TheColumn="-";
									else {
										$TheColumn=number_format($total_precio_usd_2,0); 
										$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetFac($id_direccion,2)' title='Presione para ver el detalle de $tx_direccion - $tx_proveedor1 - $par_anio ... '>$TheColumn</a>";
									}	
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;																									
							case 4: if ($total_precio_usd_3==0) $TheColumn="-";
									else { 
										$TheColumn=number_format($total_precio_usd_3,0); 
										$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetFac($id_direccion,3)' title='Presione para ver el detalle de $tx_direccion - $tx_proveedor2 - $par_anio ... '>$TheColumn</a>";
									}	
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;																		
							case 5: $TheColumn=number_format($total_precio_usd,0); 
									echo "<td class='ui-state-default align-right' valign='top'>$TheColumn</td>";
							break;
							case 6: $TheColumn=number_format($fl_presupuesto,0); 
									if ($fl_presupuesto < $total_precio_usd) echo "<td class='ui-state-rojo align-right' valign='top'>$TheColumn</td>";
									else echo "<td class='ui-state-verde align-right' valign='top'>$TheColumn</td>";
							break;
							case 7 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirFacPre($id_direccion)'><span class='ui-icon ui-icon-signal' title='Presione para ver Gr&aacute;fica ...'></span></a>";				
								echo "<td class='ui-widget-header' align='center' valign='top'>$TheColumn</td>";
							break;
							case 8 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnExpFac($id_direccion)' title='Presione para Exportar'><span class='ui-icon ui-icon-extlink'></span></a>";							
									echo "<td class='ui-widget-header' align='center' valign='top'>$TheColumn</td>";
							break;	
						}							
					}				
				echo "</tr>";					
			}	
		echo "<tr>";								  
		for ($a=0; $a<8; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='TOTAL';
					echo "<td colspan='2' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
				break;				
				case 1 : $TheField=number_format($fl_total_precio_usd_1,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;				
				case 2 : $TheField=number_format($fl_total_precio_usd_2,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;				
				case 3 : $TheField=number_format($fl_total_precio_usd_3,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;			
				case 4 : $TheField=number_format($fl_total_precio_usd,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;		
				case 5 : $TheField=number_format($fl_total_presupuesto,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;	
				case 6: 									
					echo "<td class='ui-state-highlight align-right'>&nbsp;</td>";
				break;		
				case 7: 									
					echo "<td class='ui-state-highlight align-right'>&nbsp;</td>";
				break;		
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		echo "<td rowspan='2' class='ui-state-highlight align-center'>#</td>";							 
		echo "<td rowspan='2' class='ui-state-highlight align-center'>DIRECCION</td>";					
		echo "<td class='ui-state-highlight align-center'>$MontoB</td>";	
		echo "<td class='ui-state-highlight align-center'>$MontoR</td>";						 
		echo "<td class='ui-state-highlight align-center'>$MontoO</td>";	
		echo "<td class='ui-state-highlight align-center'>$MontoT</td>";	
		echo "<td rowspan='2' class='ui-state-highlight align-center'>PRESUPUESTO $par_anio</td>";					 
		echo "<td rowspan='2' class='ui-state-highlight align-center'>Gr&aacute;fica</td>";	
		echo "<td width='3%' rowspan='2' class='ui-state-highlight align-center'>Exportar</td>";						 
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan='4' class='ui-state-highlight align-center'>GASTO ACUMULADO - $tx_mes $par_anio</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan='12' align='left'>";	
        echo "<ul type='square'> ";
        echo "<li>Gasto calculado en base a la derrama.</li> ";
        echo "<li>$nota0</li>";
        echo "<li>Actualizado al $fecha_hoy.</li>";
        echo "</ul>";      
        echo "</td>";
		echo "</tr>";		
	echo "</table>";
	?>   
    <div id="divFacturacionDirN1"></div>
    <div id="divFacturacionDirN2"></div>
<!--	<div id="divDetalleFac" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>
    <div id="divDetalleFacDir" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div> -->                   
    <?
	}
	
	
	$valBita="par_anio=$par_anio ";	
	$valBita.="par_agrupacion=$par_agrupacion";	
	$valBita.="par_moneda=$par_moneda";	
	$valBita.="par_estatus=$par_estatus";	
	$valBita.="par_cuenta$par_cuenta";
	
	
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql,"CONSULTA" , "TBL_FACTURA TBL_FACTURA_DETALLE" , "$id_login" ,  $valBita ,"" ,"inf_facturacion_direccion.php");
	 //<\BITACORA>
	 
	 
	mysqli_close($mysql);

} 
else 
{
	echo "Sessi&oacute;n Invalida";
}	
?>      