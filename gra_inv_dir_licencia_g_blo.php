<?
	session_start();
	include("includes/funciones.php");  
	include("includes/FusionCharts_Gen.php");
	include_once  ("Bitacora.class.php");	
	if 	(isset($_SESSION["sess_user"])) 
		$id_login = $_SESSION['sess_iduser'];	

	$mysql=conexion_db();
	
	$id_grafica		= $_GET['id'];		
	$id_proveedor	= $_GET['id_proveedor'];	
	$fl_tipo_cambio = 13;	
	
	//echo "id_grafica", 
		
	if($id_grafica==1) $grafica="Column3D";
	else if ($id_grafica==2) $grafica="Column2D";
	else if ($id_grafica==3) $grafica="Bar2D";
	else if ($id_grafica==4) $grafica="Pie2D";
	else if ($id_grafica==5) $grafica="Pie3D";
	else if ($id_grafica==6) $grafica="Doughnut2D";
	else if ($id_grafica==7) $grafica="Doughnut3D";
	else if ($id_grafica==8) $grafica="Area2D";
	else if ($id_grafica==9) $grafica="Line";	
	
	if ($id_proveedor==1) $tx_proveedor = "BLOOMBERG";
	else if ($id_proveedor==2) $tx_proveedor = "REUTERS";
	else if ($id_proveedor==3) {
		$tx_proveedor1 = "BLOOMBERG";
		$tx_proveedor2 = "REUTERS";
	}
	
	# Create Column3D chart object 
	$FC = new FusionCharts($grafica,"950","400"); 
	# set the relative path of the swf file
	$FC->setSWFPath("FusionCharts/Charts/");	
	# Store chart attributes in a variable 
	$strParam="caption=;subcaption=;xAxisName=Direcciones;yAxisName=# de Licencias;numberPrefix=;decimalPrecision=0";
	# Set chart attributes
	$FC->setChartParams($strParam);			
	
	if ($id_direccion==0) $dispatch="insert";
	else if (is_null($id_direccion)) { $dispatch="insert"; $id_direccion=0; }
	else $dispatch="save";
	
	$sql = "   SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, (SUM(fl_precio_usd) + SUM(e.fl_precio_mxn/$fl_tipo_cambio))/1000 AS total_precio_usd ";
	$sql.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
	$sql.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1'";
	$sql.= "   AND a.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	$sql.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
	$sql.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1' ";
	$sql.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1' ";
	$sql.= "      AND e.id_proveedor 		= f.id_proveedor and f.tx_indicador='1' ";
	if ($id_proveedor==3) $sql.= "      AND f.tx_proveedor_corto NOT IN ('$tx_proveedor1','$tx_proveedor2') ";
	else $sql.= "      AND f.tx_proveedor_corto = '$tx_proveedor' ";	
	$sql.= " GROUP BY a.id_direccion ";
	$sql.= " ORDER BY total_licencia DESC, total_precio_usd DESC,  a.id_direccion ";
	
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeDireccion[] = array(
			'id_direccion'		=>$row["id_direccion"],
			'tx_nombre'			=>$row["tx_nombre_corto"],
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
			
			# Add chart values and category names for the First Chart
			$FC->addChartData("$total_licencia","label=$tx_nombre");
	}	

	

	$valBita  = "id_grafica=$id_grafica ";	
	$valBita.="id_proveedor=$id_proveedor ";		
	
   	
   	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "GRAFICA" , "TBL_LICENCIA" , "$id_login" ,   $valBita  ,"" ,"gra_inv_dir_licencia_g_blo.php");
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
            <li># de Licencias calculadas en base al Inventario.</li>
        </ul>
        </td>
	</tr>  	
</table>