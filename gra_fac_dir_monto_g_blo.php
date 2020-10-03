<?
session_start();
	include("includes/funciones.php");  
	include("includes/FusionCharts_Gen.php");	
	include_once  ("Bitacora.class.php");

	if 	(isset($_SESSION['sess_user']))
		$id_login = $_SESSION['sess_iduser'];
	
	$mysql=conexion_db();
	
	$id_grafica		= trim($_GET["id"]);
	$par_proveedor	= trim($_GET["par_proveedor"]);	
	$par_anio		= trim($_GET["par_anio"]);
	$par_moneda		= trim($_GET["par_moneda"]);	
	$par_estatus	= trim($_GET["par_estatus"]);	
	$par_cuenta		= $_GET["par_cuenta"];
		
	
	//echo "proveedor",$par_proveedor;
	//echo "<br>";
	//echo "anio",$par_anio;
	//echo "<br>";
	//echo "moneda",$par_moneda;
	//echo "<br>";
	//echo "estatus",$par_estatus;
	//echo "<br>";
	
	if ($par_anio=="2011") $in_tc = 13;
	else $in_tc = 14;
	
	# ===============================================================================
	# NOTAS	
	# ===============================================================================
	if ($par_moneda=="USD") $nota0 ="Monto en USD (D&oacute;lares Americanos).";
	else if ($par_moneda=="MXN") $nota0 ="Monto en MXN (Pesos Mexicanos).";
	else if ($par_moneda=="EUR") $nota0 ="Monto en EUR (EUROS).";
	
	if ($id_grafica==1) $grafica="Pie2D";
	else if ($id_grafica==2) $grafica="Pie3D";	
	else if ($id_grafica==3) $grafica="Column3D";
	else if ($id_grafica==4) $grafica="Column2D";
	else if ($id_grafica==5) $grafica="Bar2D";	
	else if ($id_grafica==6) $grafica="Doughnut2D";
	else if ($id_grafica==7) $grafica="Doughnut3D";
	else if ($id_grafica==8) $grafica="Area2D";
	else if ($id_grafica==9) $grafica="Line";
	
	if ($par_proveedor==1) $tx_proveedor = "BLOOMBERG";
	else if ($par_proveedor==2) $tx_proveedor = "REUTERS";
	else if ($par_proveedor==3) 
	{
		$tx_proveedor1 = "BLOOMBERG";
		$tx_proveedor2 = "REUTERS";
	}
	
	if ($par_estatus==0)
	{
	}
	else 
	{
		$sql = "   SELECT tx_estatus ";
		$sql.= "	 FROM tbl_factura_estatus ";
		$sql.= "    WHERE id_factura_estatus = $par_estatus  ";		
		//echo "aaaa",$sql;
		
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{	
			$TheEstatus[] = array(
				"tx_estatus"	=>$row["tx_estatus"]
			);
		} 			
		for ($i=0; $i < count($TheEstatus); $i++)
		{ 	        			 
			while ($elemento = each($TheEstatus[$i]))				
				$tx_estatus	=$TheEstatus[$i]["tx_estatus"];
		}		
		$nota1 = "Facturas - ".$tx_estatus;
	}
	
	# Create Column3D chart object 
	$FC = new FusionCharts($grafica,"950","370"); 
	# set the relative path of the swf file
	$FC->setSWFPath("FusionCharts/Charts/");	
	# Store chart attributes in a variable 
	$strParam="caption=;subcaption=;xAxisName=Direcciones;yAxisName=Monto;numberPrefix=;decimalPrecision=0";
	# Set chart attributes
	$FC->setChartParams($strParam);		
	
	if ($par_moneda=="USD") $sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
	elseif ($par_moneda=="MXN") $sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	 
	elseif ($par_moneda=="EUR") $sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,(b.fl_precio_mxn*$in_tc),(b.fl_precio_eur))) AS total_precio_usd ";	 
	$sql.= "   FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_proveedor e , tbl_producto p  ";
	$sql.= "  WHERE tx_anio 			= '$par_anio' and a.tx_indicador='1' ";
	$sql.= "    AND a.id_factura		= b.id_factura  and b.tx_indicador='1'";
	$sql.= " 	AND b.id_centro_costos 	= c.id_centro_costos  and c.tx_indicador='1'";
	$sql.= " 	AND c.id_direccion 		= d.id_direccion  and d.tx_indicador='1'";
	
	$sql.= "    AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	
	$sql.= "    AND a.id_proveedor 		= e.id_proveedor  and e.tx_indicador='1'";
	$sql.= " 	AND b.id_producto 		= p.id_producto and p.tx_indicador='1' ";
					
	if ($par_proveedor==0) {}
	else {
		if ($par_proveedor==3) $sql.= "      AND e.tx_proveedor_corto NOT IN ('$tx_proveedor1','$tx_proveedor2') ";
		else $sql.= "      AND e.tx_proveedor_corto = '$tx_proveedor' ";	
	}	
	if ($par_estatus==0) { }
	else $sql.= " 	AND a.id_factura_estatus= $par_estatus ";

	if ($par_cuenta <> 0)
					$sql.= " 	AND p.id_cuenta_contable=  $par_cuenta ";
					
					
	$sql.= " GROUP BY c.id_direccion ";	
	$sql.= " ORDER BY total_precio_usd DESC ";	
	
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeDireccion[] = array(
			"id_direccion"		=>$row["id_direccion"],
			"tx_nombre"			=>$row["tx_nombre_corto"],
	  		"total_precio_usd"	=>$row["total_precio_usd"]
		);
	} 	
	
	$registros=count($TheInformeDireccion);	
	
	for ($i=0; $i < count($TheInformeDireccion); $i++)
	{ 	        			 
		while ($elemento = each($TheInformeDireccion[$i]))				
			$id_direccion		=$TheInformeDireccion[$i]["id_direccion"];	  		
			$tx_nombre			=$TheInformeDireccion[$i]["tx_nombre"];
			$total_precio_usd	=$TheInformeDireccion[$i]["total_precio_usd"];
			
			# Add chart values and category names for the First Chart
			$FC->addChartData("$total_precio_usd","label=$tx_nombre");
	}	

	$valBita="id_grafica=$id_grafica ";	
	$valBita.="par_proveedor=$par_proveedor ";		
	$valBita.="par_anio=$par_anio ";		
	$valBita.="par_moneda=$par_moneda ";
	$valBita.="par_estatus=$par_estatus ";		
	$valBita.="par_cuenta=$par_cuenta ";		
	
	
	
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "GRAFICA" , "TBL_FACTURA TBL_FACTURA_DETALLE", "$id_login" ,   $valBita  ,"" ,"gra_fac_dir_monto_g_blo.php");
	 //<\BITACORA>
	
	
?> 
<table cellspacing="0" border="0" cellpadding="0" width="100%"> 
	<tr>
    	<td align="center">        
		<? 
		# Render First Chart
		$FC->renderChart();
		?>
        </td>
   	</tr>    
    <tr>
        <td align="left">	
        <ul type="square"> 
            <li>Monto calculado en base a la Facturaci&oacute;n.</li>
            <li><?php echo $nota0; ?></li>
            <li>Monto en Miles.</li>
            <li>Tipo de cambio 13.00 pesos por d&oacute;lar (Fuente: Control Econ&oacute;mico).</li>
            <?php 
            if ($par_estatus==0){}
			else 
			{
				echo "<li>$nota1</li>";		
            }
			?>
          </ul>
      </td>
  </tr>  	
</table>