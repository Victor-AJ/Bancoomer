<?
	session_start();
	include("includes/funciones.php");  
	include("includes/FusionCharts_Gen.php");
	include_once  ("Bitacora.class.php");	
	if 	(isset($_SESSION["sess_user"])) 
		$id_login = $_SESSION['sess_iduser'];
	
	$mysql=conexion_db();	

	$id_direccion	= $_GET['id_direccion'];	
	$fl_tipo_cambio = $_GET['fl_tipo_cambio'];		
	$tx_moneda 		= $_GET['tx_moneda'];			

	$grafica = "MSColumn3DLineDY";	
	
	if ($id_proveedor==1) $tx_proveedor = "BLOOMBERG";
	else if ($id_proveedor==2) $tx_proveedor = "REUTERS";
	else if ($id_proveedor==3) {
		$tx_proveedor1 = "BLOOMBERG";
		$tx_proveedor2 = "REUTERS";
	}
	
	# Create Column3D chart object 
	$FC = new FusionCharts($grafica,"950","380"); 
	# set the relative path of the swf file
	$FC->setSWFPath("FusionCharts/Charts/");	
	# Store chart attributes in a variable
	$strParam="caption=;subcaption=;xAxisName=PROVEEDORES;pYAxisName=# de Licencias;sYAxisName=Monto;numberPrefix=;sNumberSuffix=;decimalPrecision=0";		
	# Set chart attributes
	$FC->setChartParams($strParam);		
	
	# Total por Direccion
	# ======================	
	if ($tx_moneda=="USD") $sql = "   SELECT a.id_direccion, a.tx_nombre, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn/$fl_tipo_cambio) AS total_precio_usd ";
	else $sql = "   SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";		
	$sql.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e ";
	$sql.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1' ";
	$sql.= "      AND a.id_direccion 		= $id_direccion ";
	$sql.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
	$sql.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1'";
	$sql.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1'";
	$sql.= " GROUP BY a.id_direccion ";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeDireccion[] = array(
			'id_direccion'		=>$row["id_direccion"],
			'tx_nombre'			=>$row["tx_nombre"],
			'total_licencia'	=>$row["total_licencia"],
	  		'total_precio_usd'	=>$row["total_precio_usd"]
		);
	} 	
	
	$registros=count($TheInformeDireccion);	
	
	for ($i=0; $i < count($TheInformeDireccion); $i++)	{ 	        			 
		while ($elemento = each($TheInformeDireccion[$i]))				
			$id_direccion		=$TheInformeDireccion[$i]['id_direccion'];	  		
			$tx_nombre			=$TheInformeDireccion[$i]['tx_nombre'];
			$total_licencia		=$TheInformeDireccion[$i]['total_licencia'];
			$total_precio_usd	=$TheInformeDireccion[$i]['total_precio_usd'];
	}
	
	# ============================		
	# Busco Licencias de BLOOMBERG	
	# =============================		
	$tx_proveedor1 = "BLOOMBERG";
				
	if ($tx_moneda=="USD" ) $sql1 = "   SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
	else $sql1 = "  SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";
	$sql1.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
	$sql1.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1' ";
	$sql1.= "      AND a.id_direccion 		= $id_direccion ";
	$sql1.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
	$sql1.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1' ";
	$sql1.= "      AND a.id_direccion 		= $id_direccion  ";
	$sql1.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1' ";
	$sql1.= "      AND e.id_proveedor 		= f.id_proveedor and f.tx_indicador='1' ";
	$sql1.= "      AND f.tx_proveedor_corto = '$tx_proveedor1' ";
	$sql1.= " GROUP BY a.id_direccion ";	
				
	//echo "sql",$sql1;	
				
	$result1 = mysqli_query($mysql, $sql1);	
	while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
	{	
		$TheInformeDireccion_1[] = array(
			'total_licencia_1'		=>$row1["total_licencia"],
			'total_precio_usd_1'	=>$row1["total_precio_usd"]
		);
	} 
				
	$num_result1=mysqli_num_rows($result1);	
				
	for ($j=0; $j < count($TheInformeDireccion_1); $j++)	{ 	        			 
		while ($elemento = each($TheInformeDireccion_1[$j]))				
			$total_licencia_1	=$TheInformeDireccion_1[$j]['total_licencia_1'];	  		
			$total_precio_usd_1	=$TheInformeDireccion_1[$j]['total_precio_usd_1'];	
	}
				
	if ($num_result1==0)
	{
		$total_licencia_1=0;
		$total_precio_usd_1=0;
	}	
		
	# ============================					
	# Busco Licencias de REUTERS
	# ============================
				
	$tx_proveedor2 = "REUTERS";
	
	if ($tx_moneda=="USD") $sql2 = "   SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
	else $sql2 = "  SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";
	$sql2.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
	$sql2.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1' ";
	$sql2.= "      AND a.id_direccion 		= $id_direccion ";
	$sql2.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
	$sql2.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1' ";
	$sql2.= "      AND a.id_direccion 		= $id_direccion ";
	$sql2.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1' ";
	$sql2.= "      AND e.id_proveedor 		= f.id_proveedor and f.tx_indicador='1' ";
	$sql2.= "      AND f.tx_proveedor_corto = '$tx_proveedor2' ";
	$sql2.= " GROUP BY a.id_direccion ";	
				
	//echo "<br>";
	//echo "sql 2 ",$sql2;	
								
	$result2 = mysqli_query($mysql, $sql2);						
					
	while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
	{	
		$TheInformeDireccion_2[] = array(
			'total_licencia_2'		=>$row2["total_licencia"],
			'total_precio_usd_2'	=>$row2["total_precio_usd"]
		);
	} 
				
	$num_result2=mysqli_num_rows($result2);	
				
	for ($k=0; $k < count($TheInformeDireccion_2); $k++)	{ 	        			 
		while ($elemento = each($TheInformeDireccion_2[$k]))				
			$total_licencia_2	=$TheInformeDireccion_2[$k]['total_licencia_2'];	  		
			$total_precio_usd_2	=$TheInformeDireccion_2[$k]['total_precio_usd_2'];	
	}				
					
	if ($num_result2==0)
	{
		$total_licencia_2=0;
		$total_precio_usd_2=0;
	}
		
	# Busco Licencias de OTROS
	# ============================
				
	$tx_proveedor3 = "BLOOMBERG";
	$tx_proveedor4 = "REUTERS";
	$tx_proveedor5 = "OTROS";
				
	if ($tx_moneda=="USD") $sql3 = "   SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
	else $sql3 = "  SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";
	$sql3.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
	$sql3.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1' ";
	$sql3.= "      AND a.id_direccion 		= $id_direccion ";
	$sql3.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
	$sql3.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1' ";
	$sql3.= "      AND a.id_direccion 		= $id_direccion ";
	$sql3.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1'";
	$sql3.= "      AND e.id_proveedor 		= f.id_proveedor and f.tx_indicador='1' ";
	$sql3.= "      AND f.tx_proveedor_corto NOT IN ('$tx_proveedor3','$tx_proveedor4') ";
	$sql3.= " GROUP BY a.id_direccion ";	
				
	//echo "<br>";
	//echo "sql 3 ",$sql3;	
								
	$result3 = mysqli_query($mysql, $sql3);						
					
	while ($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC))
	{	
		$TheInformeDireccion_3[] = array(
			'total_licencia_3'		=>$row3["total_licencia"],
			'total_precio_usd_3'	=>$row3["total_precio_usd"]
		);
	} 
				
	$num_result3=mysqli_num_rows($result3);	
				
	for ($l=0; $l < count($TheInformeDireccion_3); $l++)	{ 	        			 
		while ($elemento = each($TheInformeDireccion_3[$l]))				
			$total_licencia_3	=$TheInformeDireccion_3[$l]['total_licencia_3'];	  		
			$total_precio_usd_3	=$TheInformeDireccion_3[$l]['total_precio_usd_3'];	
	}						
				
	if ($num_result3==0)
	{
		$total_licencia_3=0;
		$total_precio_usd_3=0;
	}
	

	# Add category names
   	$FC->addCategory("$tx_proveedor1");
   	$FC->addCategory("$tx_proveedor2");
   	$FC->addCategory("$tx_proveedor5");
   	$FC->addCategory("TOTAL");
	
	# Add a new dataset with dataset parameters 
   	$FC->addDataset("# de Licencias","showValues=0"); 
   	# Add chart data for the above dataset
   	$FC->addChartData("$total_licencia_1");
   	$FC->addChartData("$total_licencia_2");
   	$FC->addChartData("$total_licencia_3");
   	$FC->addChartData("$total_licencia");	
	
	# Add third dataset for the secondary axis
   	$FC->addDataset("Monto","parentYAxis=S"); 
   	# Add data values for the secondary axis 
   	$FC->addChartData("$total_precio_usd_1");
   	$FC->addChartData("$total_precio_usd_2");
   	$FC->addChartData("$total_precio_usd_3");
   	$FC->addChartData("$total_precio_usd");	
	
   	
   	$valBita  = "id_direcion=$id_direccion";	
	$valBita.="fl_tipo_cambio=$fl_tipo_cambio";		
	$valBita.="tx_moneda=$tx_moneda";			
   	
   	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "GRAFICA" , "TBL_LICENCIA" , "$id_login" ,   $valBita  ,"" ,"gra_inv_dir_monto_lic_g.php");
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
        <td align='left'>	
        <ul type='square'> 
            <li>Licencias calculadas en base al Inventario.</li>
            <li>Monto mensual calculado en base al Inventario.</li>
            <li>Monto en <? echo "$tx_moneda" ?>.</li>            
            <li>Tipo de cambio <? echo "$fl_tipo_cambio" ?> pesos por d&oacute;lar.</li>
          </ul>
      </td>
  </tr>  	
</table>