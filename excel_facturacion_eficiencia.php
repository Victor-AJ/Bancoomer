<?php
	
	session_start();
	include("includes/funciones.php");
	include_once  ("Bitacora.class.php");  
	$mysql=conexion_db();

	$id_login =NULL;
	if 	(isset($_SESSION["sess_user"])) 
		$id_login = $_SESSION['sess_iduser'];
	
	
	?>
<html>
<head>
<title>.:: CSI: Bancomer Control Servicios Inform&aacute;ticos v2.0 ::.</title>
<link rel="stylesheet" type="text/css" media="screen" href="css/load.css"/> 
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script language="javascript">
$(document).ready(function() {
	$(".botonExcel").click(function(event) {
		$("#datos_a_enviar").val( $("<div>").append( $("#Exportar_a_Excel").eq(0).clone()).html());
		$("#FormularioExportacion").submit();
	});
});
</script>
<style type="text/css">
.botonExcel{cursor:pointer;}
</style>
</head>
<body>
<form action="excel_inventario_eficiencia_e.php" method="post" target="_blank" id="FormularioExportacion">
<input type="hidden" id="datos_a_enviar" name="datos_a_enviar"/>
    <div id="divLoading" align="center"><strong>Por favor espere...<br><br></strong><img alt="" src="images/ajax-loader-bert-1.gif"/></div>
	<table width="100%">
    	<tr>
        	<td width="50%" align="left"><img alt="" src="images/bbvabancomer.png" height="41px"></td>
            <td width="50%" align="right"><img alt="" src="images/asset.png" height="41px"></td>
        </tr>
       	<tr><td colspan="2" align="center" style="font-size:16px;width:100%;">REPORTE DE FACTURACION</td></tr>
    </table>   
<div align="center"><img src="images/logo_excel.jpg" class="botonExcel" alt="Presione para exportar a EXCEL"></div>
<?php
			
		
	$par_anio 		= $_GET["par_anio"];	
	$par_agrupacion	= $_GET["par_agrupacion"];	
	$par_moneda		= $_GET["par_moneda"];	
	$par_estatus	= $_GET["par_estatus"];	
	$par_direccion	= $_GET["par_direccion"];	
	$par_cuenta		= $_GET["par_cuenta"];
		
		
	//echo "par_estatus", $par_estatus;
	//echo "<br>";	
	if ($par_anio=="2011") $in_tc = 13.5;
	else $in_tc = 14;	
	$fecha_hoy 	= date("j-m-Y");		
	
	# Notas	
	if ($par_moneda=="USD") $nota0 ="Monto en USD (D&oacute;lares Americanos).";
	else if ($par_moneda=="MXN") $nota0 ="Monto en MXN (Pesos Mexicanos).";
	else if ($par_moneda=="EUR") $nota0 ="Monto en EUR (EUROS).";
	
	# Busca Estatus de Facturas
	$sql = "   SELECT tx_estatus ";
	$sql.= "	 FROM tbl_factura_estatus ";
	$sql.= "    WHERE id_factura_estatus = $par_estatus ";
	//echo " sql ",$sql;
			
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheFacturaEstatus[] = array(
			"tx_estatus" =>$row["tx_estatus"]
		);
	} 	
	
	for ($k=0; $k < count($TheFacturaEstatus); $k++)
	{ 	        			 
		while ($elemento = each($TheFacturaEstatus[$k]))				
			$tx_estatus	=$TheFacturaEstatus[$k]["tx_estatus"];	
	}
	
	if ($par_estatus==0) $nota1 ="Estatus de Facturas: TODAS ";
	else $nota1 ="Estatus de Facturas: $tx_estatus ";
	
	
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
	
	if ($par_moneda=="USD") $sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
	elseif ($par_moneda=="MXN") $sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	 
	elseif ($par_moneda=="EUR") $sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	 
	$sql.= "   FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d , tbl_producto p ";
	$sql.= "  WHERE tx_anio 			= '$par_anio' and a.tx_indicador = '1' ";
	$sql.= "    AND a.id_factura		= b.id_factura  and b.tx_indicador = '1'  ";
	$sql.= " 	AND b.id_centro_costos 	= c.id_centro_costos  and c.tx_indicador='1'";
	$sql.= " 	AND c.id_direccion 		= d.id_direccion  and d.tx_indicador='1'";
	$sql.= " 	AND c.id_direccion		= '$par_direccion' ";
	$sql.= "    AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	$sql.= " 	AND b.id_producto 		= p.id_producto  and p.tx_indicador='1' ";
		
	if ($par_estatus==0) { }
	else $sql.= " 	AND a.id_factura_estatus = $par_estatus ";	
	
	if ($par_cuenta <> 0)
					$sql.= " 	AND p.id_cuenta_contable=  $par_cuenta "; 
					
					
	$sql.= " GROUP BY c.id_direccion ";		
	$sql.= " ORDER BY total_precio_usd DESC ";		 
	
	//echo " sql ",$sql;
	//echo "<br>";	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeDireccionFac[] = array(
			'id_direccion'		=>$row["id_direccion"],
			'tx_nombre_corto'	=>$row["tx_nombre_corto"],
	  		'total_precio_usd'	=>$row["total_precio_usd"]
		);
	} 	
	
	$registros=count($TheInformeDireccionFac);	
	
	if ($registros==0) {	
		echo "<table align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<br/>";
		echo "<tr>";
		echo "<td align='right'><em><b>Sin Informaci&oacute;n ...</b></em></td>";
		echo "</tr>";	
		echo "<br/>";		
		echo "</table>";	 
	} else {		
		echo "<table id='Exportar_a_Excel' align='center' width='100%' border='1' cellspacing='1' cellpadding='1' style='font-size:11px;width:100%;'>";
        echo "<tr><td colspan='7' align='center' bgcolor='#003366' style='font-size:16px;width:100%;'><font color=white><strong>FACTURACION POR DIRECCION</strong></font></td></tr>";
		echo "<tr>";
		echo "<td width='3%' rowspan='2' align='center' bgcolor='#003366'><font color=white>#</font></td>";							 
		echo "<td width='31%' rowspan='2' align='center' bgcolor='#003366'><font color=white>DIRECCION</font></td>";
		echo "<td width='48%' colspan='4' align='center' bgcolor='#003366'><font color=white>GASTO ACUMULADO - $tx_mes $par_anio</font></td>";							 
		echo "<td width='12%' rowspan='2' align='center' bgcolor='#003366'><font color=white>PRESUPUESTO $par_anio</font></td>";							 
		echo "</tr>";
		echo "<tr>";
		echo "<td width='12%'  align='center' bgcolor='#003366'><font color=white>BLOOMBERG</font></td>";	
		echo "<td width='12%' align='center' bgcolor='#003366'><font color=white>REUTERS</font></td>";
		echo "<td width='12%' align='center' bgcolor='#003366'><font color=white>OTROS</font></td>";	
		echo "<td width='12%' align='center' bgcolor='#003366'><font color=white>TOTAL</font></td>";		
		echo "</tr>";
		
		$c=0;		
		$fl_total_precio_usd=0;		
		$fl_total_precio_usd_1=0;			
		$fl_total_precio_usd_2=0;			
		$fl_total_precio_usd_3=0;	
		
		for ($i=0; $i < count($TheInformeDireccionFac); $i++)
		{ 	        			 
			while ($elemento = each($TheInformeDireccionFac[$i]))				
				$id_direccion		=$TheInformeDireccionFac[$i]["id_direccion"];	  		
				$tx_nombre_corto	=$TheInformeDireccionFac[$i]["tx_nombre_corto"];				
				$total_precio_usd	=$TheInformeDireccionFac[$i]["total_precio_usd"];				
				
				$fl_total_precio_usd=$fl_total_precio_usd+$total_precio_usd;
				
				$c++;
				
				# ============================					
				# Busco Licencias de BLOOMBERG
				# ============================
				
				$tx_proveedor1 = "BLOOMBERG";
				
				if ($par_moneda=="USD") $sql1 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,(b.fl_precio_mxn/$in_tc),(b.fl_precio_usd))) AS total_precio_usd ";	
				else if ($par_moneda=="MXN") $sql1 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	 
				else if ($par_moneda=="EUR") $sql1 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	
				$sql1.= "   FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_proveedor e  , tbl_producto p ";
				$sql1.= "  WHERE tx_anio 			= '$par_anio' and a.tx_indicador='1' ";
				$sql1.= "    AND a.id_factura		= b.id_factura   and b.tx_indicador='1' ";
				$sql1.= " 	 AND b.id_centro_costos = c.id_centro_costos  and c.tx_indicador='1' ";
				$sql1.= " 	 AND c.id_direccion 	= d.id_direccion  and d.tx_indicador='1' ";
				$sql1.= "    AND d.id_direccion 	= $id_direccion ";
				$sql1.= "    AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
				$sql1.= "    AND a.id_proveedor 	= e.id_proveedor  and e.tx_indicador='1' ";
				$sql1.= "    AND tx_proveedor_corto	= '$tx_proveedor1' "; 
				$sql1.= " 	AND b.id_producto 		= p.id_producto  and p.tx_indicador='1' ";
				
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
						$total_precio_usd_1	=$TheInformeDireccionFac_1[$j]["total_precio_usd_1"];	
				}
				
				if ($num_result1==0) $total_precio_usd_1=0;
			
				$fl_total_precio_usd_1	=$fl_total_precio_usd_1+$total_precio_usd_1;
								
				# ============================					
				# Busco Licencias de REUTERS
				# ============================
				
				$tx_proveedor2 = "REUTERS";
				
				if ($par_moneda=="USD") $sql2 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
				elseif ($par_moneda=="MXN") $sql2 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";
				elseif ($par_moneda=="EUR") $sql2 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,(b.fl_precio_mxn/$in_tc),(b.fl_precio_usd))) AS total_precio_eur ";		 				
				
				$sql2.= "   FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_proveedor e  , tbl_producto p ";
				$sql2.= "  WHERE tx_anio 			= '$par_anio' and a.tx_indicador='1' ";
				$sql2.= "    AND a.id_factura		= b.id_factura  and b.tx_indicador='1'  ";
				$sql2.= " 	 AND b.id_centro_costos = c.id_centro_costos  and c.tx_indicador='1' ";
				$sql2.= " 	 AND c.id_direccion 	= d.id_direccion  and d.tx_indicador='1' ";
				$sql2.= "    AND d.id_direccion 	= $id_direccion ";
				$sql2.= "    AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
				$sql2.= "    AND a.id_proveedor 	= e.id_proveedor  and e.tx_indicador='1' ";
				$sql2.= "    AND tx_proveedor_corto	= '$tx_proveedor2' ";
				$sql2.= " 	AND b.id_producto 		= p.id_producto  and p.tx_indicador='1'  ";
				
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
				
				for ($k=0; $k < count($TheInformeDireccionFac_2); $k++)
				{ 	        			 
					while ($elemento = each($TheInformeDireccionFac_2[$k]))				
						$total_precio_usd_2	=$TheInformeDireccionFac_2[$k]["total_precio_usd_2"];	
				}		
				
				if ($num_result2==0) $total_precio_usd_2=0;
			
				$fl_total_precio_usd_2	=$fl_total_precio_usd_2+$total_precio_usd_2;
				
				# ============================					
				# Busco Licencias de OTROS
				# ============================
				
				$tx_proveedor3 = "BLOOMBERG";
				$tx_proveedor4 = "REUTERS";			
				
				if ($par_moneda=="USD") 	$sql3 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
				else if ($par_moneda=="MXN") $sql3 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	 
				else if ($par_moneda=="EUR") $sql3 = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,(b.fl_precio_mxn*$in_tc),(b.fl_precio_eur))) AS total_precio_usd ";	 
				$sql3.= "   FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_proveedor e  , tbl_producto p ";
				$sql3.= "  WHERE tx_anio 			= '$par_anio' and a.tx_indicador='1' ";
				$sql3.= "    AND a.id_factura		= b.id_factura and b.tx_indicador='1'";
				$sql3.= " 	 AND b.id_centro_costos = c.id_centro_costos and c.tx_indicador='1'";
				$sql3.= " 	 AND c.id_direccion 	= d.id_direccion and d.tx_indicador='1'";
				$sql3.= "    AND d.id_direccion 	= $id_direccion ";
				$sql3.= "    AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
				
				$sql3.= "    AND a.id_proveedor 	= e.id_proveedor and e.tx_indicador='1'";
				$sql3.= "    AND tx_proveedor_corto NOT IN ('$tx_proveedor3','$tx_proveedor4')  ";
				$sql3.= " 	AND b.id_producto 		= p.id_producto and p.tx_indicador='1' ";
				
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
				for ($a=0; $a<7; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$c; 									
								echo "<td align='right' valign='top'>$TheColumn</td>";
						break;						
						case 1: $TheColumn = $tx_nombre_corto;
								echo "<td align='left' valign='top'>$TheColumn</td>";
						break;																																		
						case 2: if ($total_precio_usd_1==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_1,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";
						break;																									
						case 3: if ($total_precio_usd_2==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_2,0); 										 
								echo "<td align='right' valign='top'>$TheColumn</td>";
						break;																									
						case 4: if ($total_precio_usd_3==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_3,0); 									
								echo "<td align='right' valign='top'>$TheColumn</td>";
						break;																		
						case 5: $TheColumn=number_format($total_precio_usd,0); 
								echo "<td class='ui-state-default' align='right' valign='top'>$TheColumn</td>";
						break;
						case 6: $TheColumn=number_format($fl_presupuesto,0); 
								if ($fl_presupuesto < $total_precio_usd) echo "<td class='ui-state-rojo align-right' valign='top'>$TheColumn</td>";
								else echo "<td class='ui-state-verde' align='right' valign='top'>$TheColumn</td>";
						break;						
					}							
				}				
				echo "</tr>";					
			}	
		echo "<tr>";								  
		for ($a=0; $a<6; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='TOTAL';
					echo "<td colspan='2' align='center' bgcolor='#003366'><font color=white>$TheField&nbsp;&nbsp;&nbsp;</font></td>";						 
				break;				
				case 1 : $TheField=number_format($fl_total_precio_usd_1,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;				
				case 2 : $TheField=number_format($fl_total_precio_usd_2,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;				
				case 3 : $TheField=number_format($fl_total_precio_usd_3,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;			
				case 4 : $TheField=number_format($fl_total_precio_usd,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;		
				case 5 : $TheField=number_format($fl_total_presupuesto,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;					
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		echo "<td colspan='7' align='left'>";	
        echo "<ul type='square'> ";
        echo "<li>Gasto calculado en base a la derrama.</li> ";
        echo "<li>$nota0</li>";
       	echo "<li>$nota1</li>";
        echo "<li>Actualizado al $fecha_hoy.</li>";
        echo "</ul>";      
        echo "</td>";
		echo "</tr>";	
		
		$tx_mes1='ENERO';
		$tx_mes2='FEBRERO';
		$tx_mes3='MARZO';
		$tx_mes4='ABRIL';
		$tx_mes5='MAYO';
		$tx_mes6='JUNIO';
		$tx_mes7='JULIO';
		$tx_mes8='AGOSTO';
		$tx_mes9='SEPTIEMBRE';
		$tx_mes10='OCTUBRE';
		$tx_mes11='NOVIEMBRE';
		$tx_mes12='DICIEMBRE';	
		
		if ($par_moneda=="USD") $sql = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
		else $sql = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	 
		
		$sql.= "     FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c,tbl_direccion d,  tbl_subdireccion e, tbl_factura_estatus f  , tbl_producto p ";
		$sql.= "    WHERE tx_anio 				= '$par_anio' and a.tx_indicador='1' ";
		$sql.= "      AND a.id_factura 			= b.id_factura and b.tx_indicador='1' ";
		$sql.= "      AND b.id_centro_costos	= c.id_centro_costos and c.tx_indicador='1'  ";
		$sql.= " 	 AND c.id_direccion 	= d.id_direccion and d.tx_indicador='1'";
		$sql.= "    AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	
		$sql.= "      AND c.id_direccion 		= $par_direccion ";
		$sql.= "      AND c.id_subdireccion 	= e.id_subdireccion  and e.tx_indicador='1' ";
		$sql.= " 	  AND a.id_factura_estatus	= f.id_factura_estatus  and f.tx_indicador='1' ";	
		$sql.= " 	AND b.id_producto 		= p.id_producto and p.tx_indicador='1'";
		
		if ($par_estatus==0) { }
		else $sql.= " AND f.id_factura_estatus	= $par_estatus ";	
		
		if ($par_cuenta <> 0)
					$sql.= " 	AND p.id_cuenta_contable=  $par_cuenta ";
					
		$sql.= " GROUP BY e.id_subdireccion ";
		$sql.= " ORDER BY total_precio_usd DESC ";

		//echo "sql",$sql;
	
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{	
			$TheInformeSubdireccionFac[] = array(
				'id_subdireccion'	=>$row["id_subdireccion"],
				'tx_subdireccion'	=>$row["tx_subdireccion"],
				'total_precio_usd'	=>$row["total_precio_usd"]
			);
		} 	
		
		echo "<tr>";
		echo "<td width='3%' align='center' bgcolor='#003366'><font color=white>#</font></td>";							 
		echo "<td width='10%' align='center' bgcolor='#003366'><font color=white>DIRECCION</font></td>";	
		echo "<td width='7%' align='center' bgcolor='#003366'><font color=white>PROVEEDOR</font></td>";				
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>$tx_mes1</font></td>";	
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>$tx_mes2</font></td>";						 
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>$tx_mes3</font></td>";	
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>$tx_mes4</font></td>";	
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>$tx_mes5</font></td>";						 
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>$tx_mes6</font></td>";	
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>$tx_mes7</font></td>";	
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>$tx_mes8</font></td>";						 
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>$tx_mes9</font></td>";	
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>$tx_mes10</font></td>";	
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>$tx_mes11</font></td>";						 
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>$tx_mes12</font></td>";	
		echo "<td width='8%' align='center' bgcolor='#003366'><font color=white>TOTAL</font></td>";						 
		echo "</tr>";		
			
		$fl_total_precio_usd=0;		
		$fl_total_precio_usd_1=0;			
		$fl_total_precio_usd_2=0;			
		$fl_total_precio_usd_3=0;	
		
		
		
		for ($i=0; $i < count($TheInformeSubdireccionFac); $i++)
		{ 	        			 
			while ($elemento = each($TheInformeSubdireccionFac[$i]))				
				$id_subdireccion	=$TheInformeSubdireccionFac[$i]['id_subdireccion'];	  		
				$tx_subdireccion	=$TheInformeSubdireccionFac[$i]['tx_subdireccion'];						
				
				$total_precio_usd_b_ren=0;
				$total_precio_usd_r_ren=0;
				$total_precio_usd_o_ren=0;
				
				$c++;	
				
				# ========================================
				#  Busco BLOOMBERG por mes
				# ========================================
				
				$tx_proveedor1 = "BLOOMBERG";
				$nu_proveedor1 = 1;
				
				for ($m=1; $m<13; $m++)
				{ 										
					if ($par_moneda=="USD") $sql1 = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
					else if ($par_moneda=="MXN") $sql1 = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd  ";	 
					$sql1.= "     FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_subdireccion e, tbl_proveedor f, tbl_factura_estatus g  , tbl_producto p ";
					$sql1.= "    WHERE tx_anio 				= '$par_anio' and a.tx_indicador='1' ";
					$sql1.= "      AND a.id_factura 		= b.id_factura and b.tx_indicador='1' ";
					$sql1.= "      AND b.id_centro_costos	= c.id_centro_costos and c.tx_indicador='1' ";
					$sql1.= "      AND c.id_direccion 		= $par_direccion ";
					$sql1.= " 	 AND c.id_direccion 	= d.id_direccion and d.tx_indicador='1'";
					$sql1.= "    AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
					
					$sql1.= "      AND c.id_subdireccion 	= e.id_subdireccion and  e.tx_indicador='1'";
					$sql1.= "      AND c.id_subdireccion 	= $id_subdireccion and f.tx_indicador='1' ";
					$sql1.= "      AND a.id_mes 			= $m ";
					$sql1.= "      AND a.id_proveedor 		= f.id_proveedor ";
					$sql1.= "      AND tx_proveedor_corto	= '$tx_proveedor1' "; 
					$sql1.= " 	   AND a.id_factura_estatus	= g.id_factura_estatus  and g.tx_indicador='1'";		
					$sql1.= " 	AND b.id_producto 		= p.id_producto  and p.tx_indicador='1'";
									
					if ($par_estatus==0) { }
					else $sql1.= " AND g.id_factura_estatus = $par_estatus ";	
					
					if ($par_cuenta <> 0)
					$sql1.= " 	AND p.id_cuenta_contable=  $par_cuenta ";
					
					$sql1.= " GROUP BY e.id_subdireccion ";
					
					//echo "sql ",$sql1;
					//echo "<br>";
					
					$result1 = mysqli_query($mysql, $sql1);	
					while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{	
						$TheInformeSubdireccionFac_1[] = array(						
							'total_precio_usd_b_0'	=>$row1["total_precio_usd"]
						);
					} 
					
					$num_result1=mysqli_num_rows($result1);	
					//echo "<br/>";
					//echo "num_result1 = ",$num_result1;
					//echo "<br/>";
					
					for ($j=0; $j < count($TheInformeSubdireccionFac_1); $j++)
					{ 	        			 
						while ($elemento = each($TheInformeSubdireccionFac_1[$j]))	
							$total_precio_usd_b_0 =$TheInformeSubdireccionFac_1[$j]['total_precio_usd_b_0'];	
					}							
					
					if ($m==1)
					{
						if ($num_result1==0) { $total_precio_usd_b_1=0; }
						else{
							$total_precio_usd_b_1		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_1	=$fl_total_precio_usd_b_1+$total_precio_usd_b_1;
						}	
					} else if ($m==2) {
					
					/*	echo " m ", $m;
						echo "<br>";
						echo " resultado del query ", $num_result1;
						echo "<br>";
						echo " Precio ", $total_precio_usd_b_0;
						echo "<br>";	*/				
					
						if ($num_result1==0)  { $total_precio_usd_b_2=0;  }
						else{							
							$total_precio_usd_b_2		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_2	=$fl_total_precio_usd_b_2+$total_precio_usd_b_2;
							
						/*	echo " total_precio_usd_b_0 ", $total_precio_usd_b_0;
							echo "<br>";
							echo " total_precio_usd_b_2 ", $total_precio_usd_b_2;
							echo "<br>"; */
							
						}					
					} else if ($m==3){
						if ($num_result1==0)  { $total_precio_usd_b_3=0; }
						else{
							$total_precio_usd_b_3		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_3	=$fl_total_precio_usd_b_3+$total_precio_usd_b_3;
						}						
					} else if ($m==4){
						if ($num_result1==0)  { $total_precio_usd_b_4=0; }
						else{
							$total_precio_usd_b_4		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_4	=$fl_total_precio_usd_b_4+$total_precio_usd_b_4;
						}						
					} else if ($m==5){
						if ($num_result1==0)  { $total_precio_usd_b_5=0; }
						else{
							$total_precio_usd_b_5		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_5	=$fl_total_precio_usd_b_5+$total_precio_usd_b_5;
						}						
					} else if ($m==6){
						if ($num_result1==0)  { $total_precio_usd_b_6=0; }
						else{
							$total_precio_usd_b_6		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_6	=$fl_total_precio_usd_b_6+$total_precio_usd_b_6;
						}						
					} else if ($m==7){
						if ($num_result1==0)  { $total_precio_usd_b_7=0; }
						else{
							$total_precio_usd_b_7		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_7	=$fl_total_precio_usd_b_7+$total_precio_usd_b_7;
						}						
					} else if ($m==8){
						if ($num_result1==0)  { $total_precio_usd_b_8=0; }
						else{
							$total_precio_usd_b_8		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_8	=$fl_total_precio_usd_b_8+$total_precio_usd_b_8;
						}						
					} else if ($m==9){
						if ($num_result1==0)  { $total_precio_usd_b_9=0; }
						else{
							$total_precio_usd_b_9		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_9	=$fl_total_precio_usd_b_9+$total_precio_usd_b_9;
						}						
					} else if ($m==10){
						if ($num_result1==0)  { $total_precio_usd_b_10=0; }
						else{
							$total_precio_usd_b_10		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_10	=$fl_total_precio_usd_b_10+$total_precio_usd_b_10;
						}						
					} else if ($m==11){
						if ($num_result1==0)  { $total_precio_usd_b_11=0; }
						else{
							$total_precio_usd_b_11		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_11	=$fl_total_precio_usd_b_11+$total_precio_usd_b_11;
						}						
					} else if ($m==12){
						if ($num_result1==0)  { $total_precio_usd_b_12=0; }
						else{
							$total_precio_usd_b_12	 	=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_12	=$fl_total_precio_usd_b_12+$total_precio_usd_b_12;
						}						
					}					
					$total_precio_usd_b_ren=$total_precio_usd_b_1+$total_precio_usd_b_2+$total_precio_usd_b_3+$total_precio_usd_b_4+$total_precio_usd_b_5+$total_precio_usd_b_6+$total_precio_usd_b_7+$total_precio_usd_b_8+$total_precio_usd_b_9+$total_precio_usd_b_10+$total_precio_usd_b_11+$total_precio_usd_b_12;
				}				
				
				# ========================================
				#  Busco REUTERS por mes
				# ========================================
				
				$tx_proveedor2 = "REUTERS";
				$nu_proveedor2 = 2;
								
				for ($m=1; $m<13; $m++)	{ 	
				
					if ($par_moneda=="USD") $sql1 = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
					else if ($par_moneda=="MXN") $sql1 = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	 
					$sql1.= "     FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_subdireccion e, tbl_proveedor f, tbl_factura_estatus g  , tbl_producto p";
					$sql1.= "    WHERE tx_anio 				= '$par_anio' and a.tx_indicador ='1' ";
					$sql1.= "      AND a.id_factura 		= b.id_factura  and b.tx_indicador ='1'  ";
					$sql1.= "      AND b.id_centro_costos	= c.id_centro_costos  and c.tx_indicador ='1' ";
					$sql1.= "      AND c.id_direccion 		= $par_direccion ";
					$sql1.= " 	 AND c.id_direccion 	= d.id_direccion and d.tx_indicador='1'";
					
					$sql1.= "    AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
			
					$sql1.= "      AND c.id_subdireccion 	= e.id_subdireccion  and e.tx_indicador ='1' ";
					$sql1.= "      AND c.id_subdireccion 	= $id_subdireccion ";
					$sql1.= "      AND a.id_mes 			= $m ";
					$sql1.= "      AND a.id_proveedor 		= f.id_proveedor  and f.tx_indicador ='1' ";
					$sql1.= "      AND tx_proveedor_corto	= '$tx_proveedor2' "; 
					$sql1.= " 	   AND a.id_factura_estatus	= g.id_factura_estatus  and g.tx_indicador ='1' ";
					$sql1.= " 	AND b.id_producto 		= p.id_producto  and p.tx_indicador ='1' ";
											
					if ($par_estatus==0) { }
					else $sql1.= " AND g.id_factura_estatus = $par_estatus ";	
					
					if ($par_cuenta <> 0)
					$sql1.= " 	AND p.id_cuenta_contable=  $par_cuenta ";
					
					
					$sql1.= " GROUP BY e.id_subdireccion ";
					
					//echo "sql ",$sql1;
					
					$result1 = mysqli_query($mysql, $sql1);	
					while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{	
						$TheInformeSubdireccionFac_1[] = array(						
							'total_precio_usd_r_0'	=>$row1["total_precio_usd"]
						);
					} 
					
					$num_result1=mysqli_num_rows($result1);	
					
					for ($j=0; $j < count($TheInformeSubdireccionFac_1); $j++)	{ 	        			 
						while ($elemento = each($TheInformeSubdireccionFac_1[$j]))	
							$total_precio_usd_r_0 =$TheInformeSubdireccionFac_1[$j]['total_precio_usd_r_0'];	
					}							
					
					if ($m==1) {
						if ($num_result1==0) { $total_precio_usd_r_1=0; }
						else{
							$total_precio_usd_r_1		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_1	=$fl_total_precio_usd_r_1+$total_precio_usd_r_1;
						}	
					} else if ($m==2) {
						if ($num_result1==0)  { $total_precio_usd_r_2=0;  }
						else{
							$total_precio_usd_r_2		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_2	=$fl_total_precio_usd_r_2+$total_precio_usd_r_2;
						}					
					} else if ($m==3){
						if ($num_result1==0)  { $total_precio_usd_r_3=0; }
						else{
							$total_precio_usd_r_3		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_3	=$fl_total_precio_usd_r_3+$total_precio_usd_r_3;
						}						
					} else if ($m==4){
						if ($num_result1==0)  { $total_precio_usd_r_4=0; }
						else{
							$total_precio_usd_r_4		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_4	=$fl_total_precio_usd_r_4+$total_precio_usd_r_4;
						}						
					} else if ($m==5){
						if ($num_result1==0)  { $total_precio_usd_r_5=0; }
						else{
							$total_precio_usd_r_5		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_5	=$fl_total_precio_usd_r_5+$total_precio_usd_r_5;
						}						
					} else if ($m==6){
						if ($num_result1==0)  { $total_precio_usd_r_6=0; }
						else{
							$total_precio_usd_r_6		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_6	=$fl_total_precio_usd_r_6+$total_precio_usd_r_6;
						}						
					} else if ($m==7){
						if ($num_result1==0)  { $total_precio_usd_r_7=0; }
						else{
							$total_precio_usd_r_7		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_7	=$fl_total_precio_usd_r_7+$total_precio_usd_r_7;
						}						
					} else if ($m==8){
						if ($num_result1==0)  { $total_precio_usd_r_8=0; }
						else{
							$total_precio_usd_r_8		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_8	=$fl_total_precio_usd_r_8+$total_precio_usd_r_8;
						}						
					} else if ($m==9){
						if ($num_result1==0)  { $total_precio_usd_r_9=0; }
						else{
							$total_precio_usd_r_9		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_9	=$fl_total_precio_usd_r_9+$total_precio_usd_r_9;
						}						
					} else if ($m==10){
						if ($num_result1==0)  { $total_precio_usd_r_10=0; }
						else{
							$total_precio_usd_r_10		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_10	=$fl_total_precio_usd_r_10+$total_precio_usd_r_10;
						}						
					} else if ($m==11){
						if ($num_result1==0)  { $total_precio_usd_r_11=0; }
						else{
							$total_precio_usd_r_11		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_11	=$fl_total_precio_usd_r_11+$total_precio_usd_r_11;
						}						
					} else if ($m==12){
						if ($num_result1==0)  { $total_precio_usd_r_12=0; }
						else{
							$total_precio_usd_r_12	 	=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_12	=$fl_total_precio_usd_r_12+$total_precio_usd_r_12;
						}						
					}
					$total_precio_usd_r_ren=$total_precio_usd_r_1+$total_precio_usd_r_2+$total_precio_usd_r_3+$total_precio_usd_r_4+$total_precio_usd_r_5+$total_precio_usd_r_6+$total_precio_usd_r_7+$total_precio_usd_r_8+$total_precio_usd_r_9+$total_precio_usd_r_10+$total_precio_usd_r_11+$total_precio_usd_r_12;
				}	
				
				# ========================================
				#  Busco OTROS por mes
				# ========================================
				
				$tx_proveedor3 = "BLOOMBERG";
				$tx_proveedor4 = "REUTERS";
				$tx_proveedor5 = "OTROS";					
				$nu_proveedor3 = 3;
								
				for ($m=1; $m<13; $m++)
				{ 					
					if ($par_moneda=="USD") $sql1 = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
					else 					$sql1 = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	 
					$sql1.= "    FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_subdireccion e, tbl_proveedor f, tbl_factura_estatus g , tbl_producto p  ";
					$sql1.= "    WHERE tx_anio 				= '$par_anio' and a.tx_indicador='1' ";
					$sql1.= "    AND a.id_factura 		= b.id_factura  and b.tx_indicador='1' ";
					$sql1.= "    AND b.id_centro_costos	= c.id_centro_costos  and c.tx_indicador='1' ";
					$sql1.= "    AND c.id_direccion 		= $par_direccion ";
					$sql1.= " 	 AND c.id_direccion 	= d.id_direccion and d.tx_indicador='1'";
					
					$sql1.= "    AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	
									
					$sql1.= "      AND c.id_subdireccion 	= e.id_subdireccion and e.tx_indicador='1'";
					$sql1.= "      AND c.id_subdireccion 	= $id_subdireccion ";
					$sql1.= "      AND a.id_mes 			= $m ";
					$sql1.= "      AND a.id_proveedor 		= f.id_proveedor and f.tx_indicador='1'";
					$sql1.= "      AND tx_proveedor_corto NOT IN ('$tx_proveedor3','$tx_proveedor4')  "; 
					$sql1.= " 	   AND a.id_factura_estatus	= g.id_factura_estatus and g.tx_indicador='1'";		
					$sql1.= " 	AND b.id_producto 		= p.id_producto and p.tx_indicador='1'";

					
					if ($par_estatus==0) { }
					else $sql1.= " AND g.id_factura_estatus = $par_estatus ";	
					
					if ($par_cuenta <> 0)
						$sql1.= " 	AND p.id_cuenta_contable=  $par_cuenta ";
					
					$sql1.= " GROUP BY e.id_subdireccion ";
					
					//echo "<br>";
					//echo "sql ",$sql1;
					
					$result1 = mysqli_query($mysql, $sql1);	
					while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{	
						$TheInformeSubdireccionFac_1[] = array(						
							'total_precio_usd_o_0'	=>$row1["total_precio_usd"]
						);
					} 
					
					$num_result1=mysqli_num_rows($result1);	
					
					for ($j=0; $j < count($TheInformeSubdireccionFac_1); $j++)	{ 	        			 
						while ($elemento = each($TheInformeSubdireccionFac_1[$j]))	
							$total_precio_usd_o_0 =$TheInformeSubdireccionFac_1[$j]['total_precio_usd_o_0'];	
					}							
					
					if ($m==1) {
						if ($num_result1==0) { $total_precio_usd_o_1=0; }
						else{
							$total_precio_usd_o_1		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_1	=$fl_total_precio_usd_o_1+$total_precio_usd_o_1;
						}	
					} else if ($m==2) {
						if ($num_result1==0)  { $total_precio_usd_o_2=0;  }
						else{
							$total_precio_usd_o_2		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_2	=$fl_total_precio_usd_o_2+$total_precio_usd_o_2;
						}					
					} else if ($m==3){
						if ($num_result1==0)  { $total_precio_usd_o_3=0; }
						else{
							$total_precio_usd_o_3		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_3	=$fl_total_precio_usd_o_3+$total_precio_usd_o_3;
						}						
					} else if ($m==4){
						if ($num_result1==0)  { $total_precio_usd_o_4=0; }
						else{
							$total_precio_usd_o_4		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_4	=$fl_total_precio_usd_o_4+$total_precio_usd_o_4;
						}						
					} else if ($m==5){
						if ($num_result1==0)  { $total_precio_usd_o_5=0; }
						else{
							$total_precio_usd_o_5		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_5	=$fl_total_precio_usd_o_5+$total_precio_usd_o_5;
						}						
					} else if ($m==6){
						if ($num_result1==0)  { $total_precio_usd_o_6=0; }
						else{
							$total_precio_usd_o_6		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_6	=$fl_total_precio_usd_o_6+$total_precio_usd_o_6;
						}						
					} else if ($m==7){
						if ($num_result1==0)  { $total_precio_usd_o_7=0; }
						else{
							$total_precio_usd_o_7		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_7	=$fl_total_precio_usd_o_7+$total_precio_usd_o_7;
						}						
					} else if ($m==8){
						if ($num_result1==0)  { $total_precio_usd_o_8=0; }
						else{
							$total_precio_usd_o_8		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_8	=$fl_total_precio_usd_o_8+$total_precio_usd_o_8;
						}						
					} else if ($m==9){
						if ($num_result1==0)  { $total_precio_usd_o_9=0; }
						else{
							$total_precio_usd_o_9		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_9	=$fl_total_precio_usd_o_9+$total_precio_usd_o_9;
						}						
					} else if ($m==10){
						if ($num_result1==0)  { $total_precio_usd_o_10=0; }
						else{
							$total_precio_usd_o_10		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_10	=$fl_total_precio_usd_o_10+$total_precio_usd_o_10;
						}						
					} else if ($m==11){
						if ($num_result1==0)  { $total_precio_usd_o_11=0; }
						else{
							$total_precio_usd_o_11		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_11	=$fl_total_precio_usd_o_11+$total_precio_usd_o_11;
						}						
					} else if ($m==12){
						if ($num_result1==0)  { $total_precio_usd_o_12=0; }
						else{
							$total_precio_usd_o_12	 	=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_12	=$fl_total_precio_usd_o_12+$total_precio_usd_o_12;
						}						
					}					
					$total_precio_usd_o_ren=$total_precio_usd_o_1+$total_precio_usd_o_2+$total_precio_usd_o_3+$total_precio_usd_o_4+$total_precio_usd_o_5+$total_precio_usd_o_6+$total_precio_usd_o_7+$total_precio_usd_o_8+$total_precio_usd_o_9+$total_precio_usd_o_10+$total_precio_usd_o_11+$total_precio_usd_o_12;
				}					
				
				echo "<tr>";
									
				for ($a=0; $a<16; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$c; 									
								echo "<td rowspan='3' align='right' valign='center'>$TheColumn</td>";
						break;						
						case 1: $TheColumn =$tx_subdireccion;							
								echo "<td rowspan='3' align='left' valign='center'>$TheColumn</td>";
						break;																																		
						case 2: $TheColumn=$tx_proveedor1;							
								echo "<td align='left' valign='top'>$TheColumn</td>";					
						break;																									
						case 3: $total_precio_usd_ren_tot_1=$total_precio_usd_ren_tot_1+$total_precio_usd_b_1; 
								if ($total_precio_usd_b_1==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_b_1,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																									
						case 4: $total_precio_usd_ren_tot_2=$total_precio_usd_ren_tot_2+$total_precio_usd_b_2; 
								if ($total_precio_usd_b_2==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_b_2,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																		
						case 5: $total_precio_usd_ren_tot_3=$total_precio_usd_ren_tot_3+$total_precio_usd_b_3; 
								if ($total_precio_usd_b_3==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_b_3,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
						case 6: $total_precio_usd_ren_tot_4=$total_precio_usd_ren_tot_4+$total_precio_usd_b_4; 
								if ($total_precio_usd_b_4==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_b_4,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																									
						case 7: $total_precio_usd_ren_tot_5=$total_precio_usd_ren_tot_5+$total_precio_usd_b_5; 
								if ($total_precio_usd_b_5==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_b_5,0);									
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																		
						case 8: $total_precio_usd_ren_tot_6=$total_precio_usd_ren_tot_6+$total_precio_usd_b_6; 
								if ($total_precio_usd_b_6==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_b_6,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
						case 9: $total_precio_usd_ren_tot_7=$total_precio_usd_ren_tot_7+$total_precio_usd_b_7; 
								if ($total_precio_usd_b_7==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_b_7,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																									
						case 10: $total_precio_usd_ren_tot_8=$total_precio_usd_ren_tot_8+$total_precio_usd_b_8; 
								 if ($total_precio_usd_b_8==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_b_8,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																		
						case 11: $total_precio_usd_ren_tot_9=$total_precio_usd_ren_tot_9+$total_precio_usd_b_9; 
								 if ($total_precio_usd_b_9==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_b_9,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
						case 12: $total_precio_usd_ren_tot_10=$total_precio_usd_ren_tot_10+$total_precio_usd_b_10; 
								 if ($total_precio_usd_b_10==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_b_10,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																									
						case 13: $total_precio_usd_ren_tot_11=$total_precio_usd_ren_tot_11+$total_precio_usd_b_11; 
								 if ($total_precio_usd_b_11==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_b_11,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																		
						case 14: $total_precio_usd_ren_tot_12=$total_precio_usd_ren_tot_12+$total_precio_usd_b_12; 
								 if ($total_precio_usd_b_12==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_b_12,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
						case 15: $total_precio_usd_ren_tot=$total_precio_usd_ren_tot+$total_precio_usd_b_ren;
								 $TheColumn=number_format($total_precio_usd_b_ren,0); 								 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;						
					}							
				}				
				echo "</tr>";	
				echo "<tr>";
				for ($a=0; $a<16; $a++)
				{
					switch ($a) 
					{   																																			
						case 2: $TheColumn=$tx_proveedor2;							
								echo "<td align='left' valign='top'>$TheColumn</td>";					
						break;																									
						case 3: $total_precio_usd_ren_tot_1=$total_precio_usd_ren_tot_1+$total_precio_usd_r_1; 
								if ($total_precio_usd_r_1==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_r_1,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																									
						case 4: $total_precio_usd_ren_tot_2=$total_precio_usd_ren_tot_2+$total_precio_usd_r_2; 
								if ($total_precio_usd_r_2==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_r_2,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																		
						case 5: $total_precio_usd_ren_tot_3=$total_precio_usd_ren_tot_3+$total_precio_usd_r_3; 
								if ($total_precio_usd_r_3==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_r_3,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
						case 6: $total_precio_usd_ren_tot_4=$total_precio_usd_ren_tot_4+$total_precio_usd_r_4; 
								if ($total_precio_usd_r_4==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_r_4,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																									
						case 7: $total_precio_usd_ren_tot_5=$total_precio_usd_ren_tot_5+$total_precio_usd_r_5; 
								if ($total_precio_usd_r_5==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_r_5,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																		
						case 8: $total_precio_usd_ren_tot_6=$total_precio_usd_ren_tot_6+$total_precio_usd_r_6; 
								if ($total_precio_usd_r_6==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_r_6,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
						case 9: if ($total_precio_usd_r_7==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_r_7,0);
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																									
						case 10: if ($total_precio_usd_r_8==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_r_8,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																		
						case 11: if ($total_precio_usd_r_9==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_r_9,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
						case 12: if ($total_precio_usd_r_10==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_r_10,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																									
						case 13: if ($total_precio_usd_r_11==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_r_11,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																		
						case 14: if ($total_precio_usd_r_12==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_r_12,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
						case 15: $total_precio_usd_ren_tot=$total_precio_usd_ren_tot+$total_precio_usd_r_ren; 
						 		 $TheColumn=number_format($total_precio_usd_r_ren,0);
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
					}							
				}				
				echo "</tr>";	
				echo "<tr>";
				for ($a=0; $a<16; $a++)
				{
					switch ($a) 
					{   																																			
						case 2: $TheColumn="OTROS";							
								echo "<td align='left' valign='top'>$TheColumn</td>";					
						break;																									
						case 3: $total_precio_usd_ren_tot_1=$total_precio_usd_ren_tot_1+$total_precio_usd_o_1; 
								if ($total_precio_usd_o_1==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_o_1,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																									
						case 4: $total_precio_usd_ren_tot_2=$total_precio_usd_ren_tot_2+$total_precio_usd_o_2; 
								if ($total_precio_usd_o_2==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_o_2,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																		
						case 5: $total_precio_usd_ren_tot_3=$total_precio_usd_ren_tot_3+$total_precio_usd_o_3; 
								if ($total_precio_usd_o_3==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_o_3,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
						case 6: $total_precio_usd_ren_tot_4=$total_precio_usd_ren_tot_4+$total_precio_usd_o_4; 
								if ($total_precio_usd_o_4==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_o_4,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																									
						case 7: $total_precio_usd_ren_tot_5=$total_precio_usd_ren_tot_5+$total_precio_usd_o_5; 
								if ($total_precio_usd_o_5==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_o_5,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																		
						case 8: $total_precio_usd_ren_tot_6=$total_precio_usd_ren_tot_6+$total_precio_usd_o_6; 
								if ($total_precio_usd_o_6==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_o_6,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
						case 9: $total_precio_usd_ren_tot_7=$total_precio_usd_ren_tot_7+$total_precio_usd_o_7; 
								if ($total_precio_usd_o_7==0) $TheColumn="-";
								else $TheColumn=number_format($total_precio_usd_o_7,0); 
								echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																									
						case 10: $total_precio_usd_ren_tot_8=$total_precio_usd_ren_tot_8+$total_precio_usd_o_8; 
								 if ($total_precio_usd_o_8==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_o_8,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																		
						case 11: $total_precio_usd_ren_tot_9=$total_precio_usd_ren_tot_9+$total_precio_usd_o_9; 
								 if ($total_precio_usd_o_9==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_o_9,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
						case 12: $total_precio_usd_ren_tot_10=$total_precio_usd_ren_tot_10+$total_precio_usd_o_10; 
								 if ($total_precio_usd_o_10==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_o_10,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																									
						case 13: if ($total_precio_usd_o_11==0) $TheColumn="-";
								 else $TheColumn = number_format($total_precio_usd_o_11,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;																		
						case 14: if ($total_precio_usd_o_12==0) $TheColumn="-";
								 else $TheColumn=number_format($total_precio_usd_o_12,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
						case 15: $total_precio_usd_ren_tot=$total_precio_usd_ren_tot+$total_precio_usd_o_ren;
								 $TheColumn=number_format($total_precio_usd_o_ren,0); 
								 echo "<td align='right' valign='top'>$TheColumn</td>";							
						break;
					}							
				}				
				echo "</tr>";		
			}			
			echo "<tr>";								  
			for ($a=0; $a<16; $a++)
			{
				switch ($a) 
				{   
					case 0 : $TheField='TOTAL';
							 echo "<td colspan='3' align='center' bgcolor='#003366'><font color=white>$TheField&nbsp;&nbsp;&nbsp;</font></td>";						 
					break;				
					case 1 : if ($total_precio_usd_ren_tot_1==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_1,0); 
							 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;				
					case 2 : if ($total_precio_usd_ren_tot_2==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_2,0); 
							 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;				
					case 3 : if ($total_precio_usd_ren_tot_3==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_3,0); 
							 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;			
					case 4 : if ($total_precio_usd_ren_tot_4==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_4,0); 
							 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;		
					case 5 : if ($total_precio_usd_ren_tot_5==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_5,0); 
							 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;		
					case 6 : if ($total_precio_usd_ren_tot_6==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_6,0); 
							 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;		
					case 7 : if ($total_precio_usd_ren_tot_7==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_7,0); 							 
							 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;
					case 8 : if ($total_precio_usd_ren_tot_8==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_8,0); 
							 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;
					case 9 : if ($total_precio_usd_ren_tot_9==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_9,0); 
							 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;
					case 10 : if ($total_precio_usd_ren_tot_10==0) $TheField="-";
							  else $TheField=number_format($total_precio_usd_ren_tot_10,0); 
							  echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;	
					case 11 : if ($total_precio_usd_ren_tot_11==0) $TheField="-";
							  else $TheField=number_format($total_precio_usd_ren_tot_11,0);  
							  echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;	
					case 12 : if ($total_precio_usd_ren_tot_12==0) $TheField="-";
							  else $TheField=number_format($total_precio_usd_ren_tot_12,0); 
							  echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;	
					case 13 : $TheField=number_format($total_precio_usd_ren_tot,0); 
							  echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
					break;						
				}							
			}				
        	echo "</td>";
			echo "</tr>";	
			echo "<tr>";	
			echo "<td colspan='16' align='left'>";	
        	echo "<ul type='square'> ";
        	echo "<li>Gasto calculado en base a la derrama.</li> ";
        	echo "<li>$nota0</li>";
        	echo "<li>$nota1</li>";
        	echo "<li>Actualizado al $fecha_hoy.</li>";
        	echo "</ul>";      
        	echo "</td>";
			echo "</tr>";
			
			# Detalle
			//CAMBIO CUENTA CONTABLE			
			echo "<tr>";
			if ($par_moneda=="USD") 	$sql1 = " 	SELECT f.tx_nombre_corto, g.tx_subdireccion, h.tx_departamento, tx_factura, tx_mes, b.tx_registro, b.tx_empleado, tx_centro_costos,          					 i.tx_proveedor_corto,tx_producto, 
							(IF(j.id_moneda=1,IF(j.fl_tipo_cambio=0,(a.fl_precio_mxn/$in_tc),(a.fl_precio_mxn/j.fl_tipo_cambio)),(a.fl_precio_usd))) AS fl_precio_usd, 
							z.tx_valor as tx_concepto_contable, tx_cr_estado, tx_afectable, b.tx_indicador "; 
			else $sql1 = "   						SELECT f.tx_nombre_corto, g.tx_subdireccion, h.tx_departamento, tx_factura, tx_mes, b.tx_registro, b.tx_empleado, tx_centro_costos,          					                             i.tx_proveedor_corto,tx_producto, 
							(IF(j.id_moneda=2,IF(j.fl_tipo_cambio=0,(a.fl_precio_usd*$in_tc),(a.fl_precio_usd*j.fl_tipo_cambio)),(a.fl_precio_mxn))) AS fl_precio_usd,
							 z.tx_valor as tx_concepto_contable, tx_cr_estado, tx_afectable, b.tx_indicador ";
			$sql1.= "     FROM tbl_factura_detalle a ";
			 
			
			$sql1.= "inner join  tbl_empleado b         on a.id_empleado		= b.id_empleado  ";
			$sql1.= "inner join  tbl_centro_costos c    on ( a.id_centro_costos	= c.id_centro_costos and c.tx_indicador='1'  )";
			$sql1.= "inner join  tbl_direccion f        on (c.id_direccion 		= f.id_direccion and f.tx_indicador='1'  	)";
			$sql1.=" inner join tbl_perfil_direccion DIR on  ( f.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  
			
			$sql1.= "inner join  tbl_subdireccion g     on ( c.id_subdireccion 	= g.id_subdireccion and g.tx_indicador='1' 	)";
			$sql1.= "inner join  tbl_producto d         on (a.id_producto		= d.id_producto and d.tx_indicador='1' 	)";
			$sql1.= "inner join  tbl_cr_estado e        on (c.id_cr_estado		= e.id_cr_estado and e.tx_indicador='1' )";
			$sql1.= "inner join  tbl_departamento h     on (c.id_departamento 	= h.id_departamento and h.tx_indicador='1' )";
			$sql1.= "inner join  tbl_proveedor i        on ( d.id_proveedor		= i.id_proveedor and  i.tx_indicador='1'  )";
			$sql1.= "inner join  tbl_factura j          on (a.id_factura			= j.id_factura and j.tx_indicador='1'  )";
			$sql1.= "inner join  tbl_mes k              on ( k.id_mes 			= j.id_mes    )";
			$sql1.= "left outer join  tbl45_catalogo_global z on (z.id=d.id_cuenta_contable and z.tx_indicador='1'  )";
			$sql1.= "    WHERE j.tx_anio			= '$par_anio'  AND c.id_direccion 		= $par_direccion and a.tx_indicador='1'  ";	
			
			if ($par_estatus==0) { }
			else $sql1.= " AND j.id_factura_estatus	= $par_estatus ";	
			
			if ($par_cuenta <> 0)
					$sql1.= " 	AND d.id_cuenta_contable=  $par_cuenta ";
					
			$sql1.= " ORDER BY k.id_mes, f.id_direccion, g.id_subdireccion, h.id_departamento, tx_factura, b.tx_empleado, tx_centro_costos, tx_producto ";   			
			//echo " sql1 ",$sql1;						
			
			$result = mysqli_query($mysql, $sql1);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(
					"tx_nombre_corto"		=>$row["tx_nombre_corto"],
					"tx_subdireccion"		=>$row["tx_subdireccion"],
					"tx_departamento"		=>$row["tx_departamento"],
					"tx_factura"			=>$row["tx_factura"],
					"tx_mes"				=>$row["tx_mes"],
					"tx_registro"			=>$row["tx_registro"],
					"tx_empleado"			=>$row["tx_empleado"],
					"tx_centro_costos"		=>$row["tx_centro_costos"],
					"tx_proveedor_corto"	=>$row["tx_proveedor_corto"],
					"tx_producto"			=>$row["tx_producto"],
					"fl_precio_usd"			=>$row["fl_precio_usd"],
					"tx_concepto_contable"	=>$row["tx_concepto_contable"],
					"tx_cr_estado"			=>$row["tx_cr_estado"],
					"tx_afectable"			=>$row["tx_afectable"],
					"tx_indicador"			=>$row["tx_indicador"]
				);
			} 	
			
			$registros=count($TheCatalogo);	
	?>
    <br>    
	<?
	if ($registros==0) { }
	else
	{			
		echo "<tr>";
		for ($a=0; $a<15; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
						 echo "<td width='2%' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";							 
				break;
				case 1 : $TheField='DIRECCION'; 
						 echo "<td width='7%' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 2 : $TheField='SUBDIRECCION';
						 echo "<td width='21%' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";	
				break;
				case 3 : $TheField='ESTATUS'; 
					  	echo "<td width='8%' align='centert' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 4 : $TheField='REGISTRO';
						 echo "<td width='10%' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 5 : $TheField='EMPLEADO';
						 echo "<td width='20%' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 6 : $TheField='CR';
						 echo "<td width='7%' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 7 : $TheField='DEPARTAMENTO';
						 echo "<td width='5%' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 8 : $TheField='MES';
						 echo "<td width='10%' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 9 : $TheField='FACTURA';
						 echo "<td width='10%' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 10 : $TheField='PROVEEDOR';
						  echo "<td width='7%' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 11 : $TheField='PRODUCTO'; 
						  echo "<td width='8%' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 12 : $TheField='MONTO'; 
						  echo "<td width='8%' align='centert' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 13 : $TheField='CONCEPTO CONTABLE'; 
						  echo "<td width='8%' align='centert' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 14 : $TheField='CR ESTADO'; 
						  echo "<td width='8%' align='centert' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		
		$j=0;		
		$in_total_licencias=0;	
		$fl_total_precio_usd=0;	
		$fl_total_precio_mxn=0;	
		
		for ($i=0; $i < count($TheCatalogo); $i++)
		{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_nombre_corto		= $TheCatalogo[$i]["tx_nombre_corto"];
				$tx_subdireccion		= $TheCatalogo[$i]["tx_subdireccion"];
				$tx_departamento		= $TheCatalogo[$i]["tx_departamento"];
				$tx_factura				= $TheCatalogo[$i]["tx_factura"];
				$tx_mes					= $TheCatalogo[$i]["tx_mes"];
				$tx_registro			= $TheCatalogo[$i]["tx_registro"];
				$tx_empleado			= $TheCatalogo[$i]["tx_empleado"];						
				$tx_centro_costos		= $TheCatalogo[$i]["tx_centro_costos"];						
				$tx_proveedor_corto		= $TheCatalogo[$i]["tx_proveedor_corto"];			
				$tx_producto			= $TheCatalogo[$i]["tx_producto"];	  						
				$fl_precio_usd			= $TheCatalogo[$i]["fl_precio_usd"];	  						
				$tx_concepto_contable	= $TheCatalogo[$i]["tx_concepto_contable"];	
				$tx_cr_estado			= $TheCatalogo[$i]["tx_cr_estado"];	  						
				$tx_afectable			= $TheCatalogo[$i]["tx_afectable"];	  						
				$tx_indicador			= $TheCatalogo[$i]["tx_indicador"];	  						
				
				$j++;				

				$fl_total_precio_usd = $fl_total_precio_usd+$fl_precio_usd;
				
				if ($tx_indicador=="0") $tx_color = "#FF3333";
				else $tx_color = "";
				
				for ($a=0; $a<15; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$j; 																	
								echo "<td bgcolor='$tx_color' aling='center'>$TheColumn</td>";
						break;						
						case 1: $TheColumn=$tx_nombre_corto;
								echo "<td bgcolor='$tx_color' align='left'>$TheColumn</td>";
						break;
						case 2: $TheColumn=$tx_subdireccion;
								echo "<td bgcolor='$tx_color' align='left'>$TheColumn</td>";
						break;
						case 3:	if ($tx_indicador=="0") $TheColumn="BAJA"; 
								else $TheColumn="ACTIVO";										
								echo "<td bgcolor='$tx_color' align='center'>$TheColumn</td>";
						break;
						case 4: $TheColumn="$tx_registro";																	
								echo "<td bgcolor='$tx_color' align='center'>$TheColumn</td>";
						break;
						case 5: $TheColumn=$tx_empleado;
								echo "<td align='left' bgcolor='$tx_color'>$TheColumn</td>";
						break;
						case 6: $TheColumn=$tx_centro_costos;
								echo "<td align='center' bgcolor='$tx_color'>$TheColumn</td>";
						break;
						case 7: $TheColumn=$tx_departamento;
								echo "<td bgcolor='$tx_color' align='left'>$TheColumn</td>";
						break;
						case 8: $TheColumn=$tx_mes;								
								echo "<td bgcolor='$tx_color' align='left'>$TheColumn</td>";
						break;
						case 9: $TheColumn=$tx_factura;
								echo "<td bgcolor='$tx_color' align='right'>$TheColumn</td>";
						break;
						case 10: $TheColumn=$tx_proveedor_corto;
								 echo "<td align='left' bgcolor='$tx_color'>$TheColumn</td>";
						break;
						case 11: $TheColumn=$tx_producto;
								 echo "<td align='left' bgcolor='$tx_color'>$TheColumn</td>";
						break;
						case 12: $TheColumn=number_format($fl_precio_usd,0); 
								 if($TheColumn=="0") echo "<td align='right' bgcolor='$tx_color'>-</td>";	
								 else echo "<td align='right' bgcolor='$tx_color'>$TheColumn</td>";
						break;					
						case 13: $TheColumn=$tx_concepto_contable; 
								 if($TheColumn=="") echo "<td align='right' bgcolor='$tx_color'>&nbsp;</td>";	
								 else echo "<td align='right' bgcolor='$tx_color'>$TheColumn</td>"; 

						break;
						case 14: $TheColumn=$tx_cr_estado; 
								 if($TheColumn=="") echo "<td calign='right' bgcolor='$tx_color'>&nbsp;</td>";	
								 else echo "<td align='right' bgcolor='$tx_color'>$TheColumn</td>";
						break; 
						}
					}	
					echo "</tr>";
					}
					echo "<tr>";		
											  
					for ($a=0; $a<3; $a++)
					{
						switch ($a) 
						{   
							case 0 : $TheField='Totales';
								echo "<td colspan='12' align='center' bgcolor='#003366'><font color=white>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
							break;
							case 1 : $TheField=number_format($fl_total_precio_usd ,0); 	
								echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</td>";						 
							break;
							case 2 : $TheField=""; 
								echo "<td colspan='2' align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
							break;
						}							
					}	
					echo "</tr>";	
		}			
		echo "<tr>";
		echo "<td colspan='7' align='left'>";	
        echo "<ul type='square'> ";
        echo "<li>Gasto calculado en base a la derrama.</li> ";
        echo "<li>$nota0</li>";
       	echo "<li>$nota1</li>";
        echo "<li>Actualizado al $fecha_hoy.</li>";
        echo "</ul>";      
        echo "</td>";
		echo "</tr>";	
		echo "</table>";
	} 	
	
	$valBita ="par_anio=$par_anio ";	
	$valBita.="par_agrupacion=$par_agrupacion ";	
	$valBita.="par_moneda=$par_moneda ";	
	$valBita.="par_estatus=$par_estatus ";	
	$valBita.="par_direccion=$par_direccion ";	
	$valBita.="par_cuenta=$par_cuenta ";
	
	
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_FACTURA TBL_FACTURA_DETALLE" , "$id_login" , $valBita ,"" ,"excel_facturacion_eficiencia.php");
	 //<\BITACORA>
	
	mysqli_close($mysql);
	?>
    <script language="JavaScript">	
		$("#divLoading").hide();	
	</script> 
</form>
</body>
</html>