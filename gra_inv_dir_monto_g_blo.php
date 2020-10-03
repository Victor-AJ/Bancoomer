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
	$id_tiempo		= $_GET['id_tiempo'];
	$fl_tipo_cambio = $_GET['fl_tipo_cambio'];		
	$tx_moneda 		= $_GET['tx_moneda'];		
	$in_meses 		= 12;
	
	//echo "id_grafica", 
	
	if ($id_tiempo==1) {
		$tx_tiempo_may = "MENSUAL";
		$tx_tiempo_min = "Mensual";
	} else {
		$tx_tiempo_may = "ANUAL";
		$tx_tiempo_min = "Anual";
	}	
	
	if ($id_grafica==1) $grafica="Pie2D";
	else if ($id_grafica==2) $grafica="Pie3D";	
	else if ($id_grafica==3) $grafica="Column3D";
	else if ($id_grafica==4) $grafica="Column2D";
	else if ($id_grafica==5) $grafica="Bar2D";	
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
	$FC = new FusionCharts($grafica,"950","350"); 
	# set the relative path of the swf file
	$FC->setSWFPath("FusionCharts/Charts/");	
	# Store chart attributes in a variable 
	$strParam="caption=;subcaption=;xAxisName=Direcciones;yAxisName=Monto;numberPrefix=$;decimalPrecision=0";
	# Set chart attributes
	$FC->setChartParams($strParam);			
	
	if ($id_direccion==0) $dispatch="insert";
	else if (is_null($id_direccion)) { $dispatch="insert"; $id_direccion=0; }
	else $dispatch="save";	
	
	if ($id_tiempo==1) {
		if ($tx_moneda=="USD") $sql = "   SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn/$fl_tipo_cambio) AS total_precio_usd ";
		else $sql = "   SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";			
	} else if ($id_tiempo==2) {
		if ($tx_moneda=="USD") $sql = "   SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, (SUM(fl_precio) + SUM(e.fl_precio_mxn/$fl_tipo_cambio)) * $in_meses AS total_precio_usd ";
		else $sql = "   SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, (SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn)) * $in_meses AS total_precio_usd ";
	}	
	$sql.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
	$sql.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1'";
	$sql.= "   AND a.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	$sql.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
	$sql.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1'";
	$sql.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1' ";
	$sql.= "      AND e.id_proveedor 		= f.id_proveedor and f.tx_indicador='1'";
	if ($id_proveedor==3) $sql.= " AND f.tx_proveedor_corto NOT IN ('$tx_proveedor1','$tx_proveedor2') ";
	else $sql.= " AND f.tx_proveedor_corto = '$tx_proveedor' ";	
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
			$FC->addChartData("$total_precio_usd","label=$tx_nombre");
	}	

	

	
	$valBita  = "id_grafica=$id_grafica ";	
	$valBita.="id_proveedor=$id_proveedor ";		
	$valBita.="id_tiempo=$id_tiempo ";			
	$valBita.="fl_tipo_cambio=$fl_tipo_cambio ";
	$valBita.="tx_moneda=$tx_moneda ";

   	
   	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "GRAFICA" , "TBL_LICENCIA" , "$id_login" ,   $valBita  ,"" ,"gra_inv_dir_monto_g_blo.php");
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
            <li>Monto <? echo "$tx_tiempo_min" ?> calculado en base al Inventario.</li>
            <li>Monto en <? echo "$tx_moneda" ?>.</li>            
            <li>Tipo de cambio <? echo "$fl_tipo_cambio" ?> pesos por d&oacute;lar.</li>
            <li>Monto en Miles.</li>
          </ul>
      </td>
  </tr>  	
</table>