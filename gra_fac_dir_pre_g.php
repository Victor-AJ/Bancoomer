<?
session_start();
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");
	$mysql=conexion_db();
	$id_login =NULL;
	if 	(isset($_SESSION["sess_user"])) 
		$id_login = $_SESSION['sess_iduser'];
	
	$par_direccion	= $_GET["par_direccion"];		
	$par_anio		= $_GET["par_anio"];		
	$par_moneda		= $_GET["par_moneda"];		
	$par_estatus	= $_GET["par_estatus"];		
	$par_cuenta		= $_GET["par_cuenta"];
	
	
	if ($par_anio=="2011") 
		$in_tc = 13;
	else 
		$in_tc = 14;
	
	//echo "aaa",$par_direccion;
	//echo "<br>";
	//echo "bbb",$par_anio;
	//echo "<br>";
	//echo "ccc",$par_moneda;
	//echo "<br>";	
	
	# ===============================================================================
	# NOTAS	
	# ===============================================================================
	if ($par_moneda=="USD") $nota0 ="Monto en USD (D&oacute;lares Americanos).";
	else 					$nota0 ="Monto en MXN (Pesos Mexicanos).";
	
	if ($par_moneda=="USD") $sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
	else 					$sql = " SELECT c.id_direccion, d.tx_nombre_corto, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	
	$sql.= "     FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d ,   tbl_producto p";
	$sql.= "    WHERE tx_anio 			= '$par_anio' and a.tx_indicador='1' ";
	$sql.= "      AND a.id_factura		= b.id_factura and b.tx_indicador='1' ";
	$sql.= " 	  AND b.id_centro_costos= c.id_centro_costos  and c.tx_indicador='1' ";
	$sql.= " 	  AND c.id_direccion 	= d.id_direccion  and d.tx_indicador='1' ";
	
	$sql1.= "     AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
			
	$sql.= " 	  AND c.id_direccion 	= $par_direccion ";
	$sql.= " 	AND b.id_producto 		= p.id_producto  and p.tx_indicador='1' ";
	
		
	if ($par_estatus==0) { }
	else $sql.= " AND a.id_factura_estatus = $par_estatus ";

	if ($par_cuenta <> 0)
		$sql.= " 	AND p.id_cuenta_contable=  $par_cuenta "; 
	
	
	
	$sql.= " GROUP BY c.id_direccion ";		
	$sql.= " ORDER BY total_precio_usd DESC ";			
		
	//echo "sql",$sql;	
		
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

	for ($i=0; $i < count($TheInformeDireccionFac); $i++)
	{ 	        			 
		while ($elemento = each($TheInformeDireccionFac[$i]))				
			$id_direccion		=$TheInformeDireccionFac[$i]["id_direccion"];	  		
			$tx_nombre_corto	=$TheInformeDireccionFac[$i]["tx_nombre_corto"];				
			$total_precio_usd	=$TheInformeDireccionFac[$i]["total_precio_usd"];		
	}
	
	# ==============================					
	# Busco MONTO del Presupuesto
	# ==============================
				
	if ($par_moneda=="USD") $sql4 = " SELECT fl_presupuesto_usd AS fl_presupuesto ";	
	else 					$sql4 = " SELECT fl_presupuesto_mxn AS fl_presupuesto ";	
	$sql4.= "	FROM tbl_presupuesto_2010 ";
	$sql4.= "  WHERE id_direccion 	= $par_direccion ";		
	$sql4.= "    AND tx_anio 		= '$par_anio' ";		
	
	//echo "sql",$sql4;
	//echo "<br>";				
												
								
	$result4 = mysqli_query($mysql, $sql4);						
					
	while ($row4 = mysqli_fetch_array($result4, MYSQLI_ASSOC))
	{	
		$TheInformePresupuesto[] = array(						
			"fl_presupuesto"	=>$row4["fl_presupuesto"]
		);
	} 
				
	$num_result4=mysqli_num_rows($result4);	
				
	for ($m=0; $m < count($TheInformePresupuesto); $m++)
	{ 	        			 
		while ($elemento = each($TheInformePresupuesto[$m]))	
			$fl_presupuesto	=$TheInformePresupuesto[$m]["fl_presupuesto"];	
	}						
				
	if ($num_result4==0) 	$fl_presupuesto=1;
	if ($fl_presupuesto==0)	$fl_presupuesto=1;
	
	//echo "precio",$total_precio_usd;
	//echo "<br>";
	//echo "presupuesto",$fl_presupuesto;
		
	$porcentaje = (100 * $total_precio_usd) / $fl_presupuesto;	
	$porcentaje_nota = number_format($porcentaje,0); 
	
	$nota1 = "$porcentaje_nota %"; 
	
	$strXML = "<chart lowerLimit='0' upperLimit='100' lowerLimitDisplay='Inicial' upperLimitDisplay='M&aacute;ximo' gaugeStartAngle='180' gaugeEndAngle='0' palette='1' numberSuffix='%' tickValueDistance='30' showValue='6'>";
	$strXML .= "	<colorRange>";
	$strXML .= "		<color minValue='0' maxValue='90' code='8BBA00'/>";
	$strXML .= "		<color minValue='90' maxValue='99' code='F6BD0F'/>";
	$strXML .= "		<color minValue='99' maxValue='100' code='FF654F'/>";
	$strXML .= "	</colorRange> ";
	$strXML .= "	<dials> ";
	$strXML .= "		<dial value='$porcentaje' rearExtension='10'/>";
	$strXML .= "	</dials>";
	$strXML .= " </chart>";

	$valBita="par_direccion=$par_direccion "; 		
	$valBita.="par_anio=$par_anio ";		
	$valBita.="par_moneda=$par_moneda ";		
	$valBita.="par_estatus=$par_estatus ";		
	$valBita.="par_cuenta=$par_cuenta ";
	
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "GRAFICA" , "TBL_FACTURA TBL_FACTURA_DETALLE" , "$id_login" ,   $valBita  ,"" ,"gra_fac_dir_pre_g.php");
	 //<\BITACORA>
	
	?> 
<input id="strXML" name="strXML" type="hidden" value="<? echo $strXML ?>" /> 

<table cellspacing="0" border="0" cellpadding="0" width="100%"> 
	<tr>
    	<td>&nbsp;</td>
    </tr>   
	<tr>
    	<td align="center">        
        	<div id="chartdiv" align="center"></div> 
        		<script type="text/javascript">
			
             		var myChart = new FusionCharts("FusionCharts/Charts/AngularGauge.swf", "myChartId", "550", "350", "0", "0");				
					var datos = $("#strXML").val();
					myChart.setDataXML(datos);             	
             		myChart.render("chartdiv");
				
          		</script> 
       	</td>
   	</tr>
    <tr><td>&nbsp;</td></tr> 
    <tr><td>&nbsp;</td></tr> 
    <tr align="center">
        <td>	
        	<table width="30%">
            	<tr>
            	  <td width="15%" class="ui-state-default" align="left">Presupuesto:</td><td width="15%" class="ui-state-default" align="right"><?php echo number_format($fl_presupuesto,0); ?></td></tr>
            	<tr>
            	  <td class="ui-state-default" align="left">Gasto Acumulado:</td><td class="ui-state-default" align="right"><?php echo number_format($total_precio_usd,0); ?></td></tr>
 				<tr>
 				  <td class="ui-state-default" align="left">Porcentaje:</td><td class="ui-state-default" align="right"><?php echo $nota1 ?></td></tr>
                <tr><td class="ui-state-default" align="left" colspan="2"><?php echo $nota0 ?></td></tr>
            </table>
        </td>
	</tr> 
    <tr><td>&nbsp;</td></tr> 
</table>