<?
	session_start();
	include("includes/funciones.php");  
	include("includes/FusionCharts_Gen.php");	
	include_once  ("Bitacora.class.php");
	 
	if 	(isset($_SESSION["sess_user"])) 
		$id_login = $_SESSION['sess_iduser'];
	
	$mysql=conexion_db();
	
	$id_grafica	= $_GET['id'];		
	$id_factura	= $_GET['id_factura'];	
	
	//echo "id_grafica", 
	
	if ($id_grafica==1) $grafica="Pie2D";
	else if ($id_grafica==2) $grafica="Pie3D";	
	else if ($id_grafica==3) $grafica="Column3D";
	else if ($id_grafica==4) $grafica="Column2D";
	else if ($id_grafica==5) $grafica="Bar2D";	
	else if ($id_grafica==6) $grafica="Doughnut2D";
	else if ($id_grafica==7) $grafica="Doughnut3D";
	else if ($id_grafica==8) $grafica="Area2D";
	else if ($id_grafica==9) $grafica="Line";	
	
	# Create Column3D chart object 
	$FC = new FusionCharts($grafica,"950","380"); 
	# set the relative path of the swf file
	$FC->setSWFPath("FusionCharts/Charts/");	
	# Store chart attributes in a variable 
	$strParam="caption=;subcaption=;xAxisName=Direcciones;yAxisName=Monto;numberPrefix=;decimalPrecision=2";
	# Set chart attributes
	$FC->setChartParams($strParam);				
	
	$sql = "   SELECT f.tx_nombre_corto, sum( a.fl_precio_usd ) AS total_precio_usd, sum( a.fl_precio_mxn ) AS total_precio_mxn, sum( a.fl_precio_eur ) AS total_precio_eur ";
	$sql.= "     FROM tbl_factura_detalle a, tbl_empleado b, tbl_centro_costos c, tbl_producto d, tbl_cr_estado e, tbl_direccion f, tbl_subdireccion g, tbl_departamento h ";
	$sql.= "    WHERE a.id_factura 			= $id_factura  and a.tx_indicador='1' ";
	$sql.= "      AND a.id_empleado 		= b.id_empleado ";
	$sql.= "      AND a.id_centro_costos 	= c.id_centro_costos ";
	$sql.= "      AND c.id_cr_estado 		= e.id_cr_estado ";
	$sql.= "      AND a.id_producto 		= d.id_producto ";
	$sql.= "      AND c.id_direccion 		= f.id_direccion ";
	$sql.= "      AND c.id_subdireccion 	= g.id_subdireccion ";
	$sql.= "      AND c.id_departamento 	= h.id_departamento ";
	$sql.= " GROUP BY f.id_direccion ";
	$sql.= " ORDER BY total_precio_usd DESC, total_precio_mxn DESC, total_precio_eur DESC ";
	
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeDireccion[] = array(				
			'tx_nombre'			=>$row["tx_nombre_corto"],
			'total_precio_usd'	=>$row["total_precio_usd"],
			'total_precio_mxn'	=>$row["total_precio_mxn"],
			'total_precio_eur'	=>$row["total_precio_eur"]
		);
	} 	
	
	$registros=count($TheInformeDireccion);	
			
	for ($i=0; $i < count($TheInformeDireccion); $i++)	{ 	        			 
		while ($elemento = each($TheInformeDireccion[$i]))								
			$tx_nombre			=$TheInformeDireccion[$i]['tx_nombre'];
			$total_precio_usd	=$TheInformeDireccion[$i]['total_precio_usd'];
			$total_precio_mxn	=$TheInformeDireccion[$i]['total_precio_mxn'];
			$total_precio_eur	=$TheInformeDireccion[$i]['total_precio_eur'];
			
			if ($total_precio_usd <> 0) { $fl_precio = $total_precio_usd; $tx_moneda = "USD"; }
			else if ($total_precio_mxn <> 0) { $fl_precio = $total_precio_mxn; $tx_moneda = "MXN"; }
			else if ($total_precio_eur <> 0) { $fl_precio = $total_precio_eur; $tx_moneda = "EUR"; }
			
			# Add chart values and category names for the First Chart
			$FC->addChartData("$fl_precio","label=$tx_nombre");
	}	

	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "GRAFICA" , "TBL_FACTURA_DETALLE" , "$id_login" ,   "id_factura=$id_factura" ,"$id_factura" ,"gra_fac_detalle_g.php");
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
        	<li>Monto calculado en base al Derrama.</li>
            <li>Monto en <? echo "$tx_moneda"; ?>.</li>
        </ul>
      	</td>
  	</tr>  	
</table>