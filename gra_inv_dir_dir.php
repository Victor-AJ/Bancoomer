<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">

<html>    
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: CSI: Bancomer Control Servicios Inform&aacute;ticos v2.0 ::.</title>
</head>
<!-- Estilos -->
<link rel="stylesheet" type="text/css" media="screen" href="css/ui-personal/jquery-ui-1.7.2.custom.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="css/ui.jqgrid.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="css/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="css/estilos.css"/> 
<!-- Librerias -->
<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script>
<script src="js/user.js" type="text/javascript"></script>
<script src="FusionCharts/FusionCharts.js" type="text/javascript"></script>
<body>
<?
	include("includes/funciones.php");  
	include("includes/FusionCharts_Gen.php");

	$mysql=conexion_db();
	
	$id	= $_GET['id'];	
	
	$FC = new FusionCharts("StackedColumn3D","350","300"); 

	
?>
	<script type="text/javascript">
                    
        $(function() {   
            $("#tabs").tabs();						
        });	
        
        $("#btnSalir").click(function(){               	 
            window.close();
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        });
             
    </script>
<?

	$id_direccion	= $_GET['id_direccion'];	
	
	$fl_tipo_cambio = 13;
	
	if ($id_direccion==0) $dispatch="insert";
	else if (is_null($id_direccion)) { $dispatch="insert"; $id_direccion=0; }
	else $dispatch="save";			
	

	
	$sql = "   SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, (SUM(fl_precio_usd) + SUM(fl_precio_mxn/$fl_tipo_cambio))/1000 AS total_precio_usd ";
	$sql.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e ";
	$sql.= "    WHERE a.id_direccion 		= b.id_direccion ";
	$sql.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
	$sql.= "      AND c.id_empleado 		= d.id_empleado ";
	$sql.= "      AND c.tx_indicador 		= '1' ";
	$sql.= "      AND d.id_producto 		= e.id_producto ";
	$sql.= " GROUP BY a.id_direccion ";
	$sql.= " ORDER BY total_precio_usd DESC, total_licencia DESC, a.id_direccion ";
	
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
				
			$strXML .= "<set label='" . $tx_nombre . "' value='" . $total_precio_usd . "' />";

	}	
	
	$FC = new FusionCharts("Column3D","300","250"); 

	# add chart values and category names
	$FC->addChartData("48200", "label=Week 1;alpha=40;showlabel=0;showValue=0"); 
	$FC->addChartData("32100", "label=Week 2;alpha=40;showlabel=0;showValue=0"); 
	$FC->addChartData("21400", "label=Week 3;hoverText=Lowest;link=tooLow.php"); 
	$FC->addChartData("54400", "label=Week 4;showlabel=0;showValue=0; alpha=40; hoverText=Highest");
	
	


?>  
<table width="100%">
	<tr>
       	<td width="50%" align="left"><img src="images/bbvabancomer.png" width="200px" height="41px"></td>
       	<td width="50%" align="right"><img src="images/asset.png" width="200px" height="41px"></td>
     </tr>
</table>   
<div class='ui-widget-header align-center'>INVENTARIO POR DIRECCION - MONTO MENSUAL</div>	   
<table cellspacing="1px" border="0" cellpadding="0" width="100%">      		
	<tr>
    	<td>
		<? 			
			$FC->addCategory($TheTitulo4);	
			$strXML1 .= "<set name='".$TheTitulo4."' value='".$TheValor4."' color='".$TheColor4."'/>";		
					
			# Create a new dataset 
			$FC->addDataset("Bloomerg"); 
			# Add chart values for the above dataset
			$FC->addChartData("1");
			$FC->addChartData("2");
			$FC->addChartData("3");
			$FC->addChartData("4");
			$FC->addChartData("5");
			$FC->addChartData("6");
			$FC->addChartData("7");
			$FC->addChartData("8");
			$FC->addChartData("9");
			$FC->addChartData("10");
			$FC->addChartData("11");
			$FC->addChartData("12");
			
			# Create second dataset 
			$FC->addDataset("Reuters"); 
			# Add chart values for the second dataset
			$FC->addChartData("11");
			$FC->addChartData("12");
			$FC->addChartData("13");
			$FC->addChartData("14");
			$FC->addChartData("15");
			$FC->addChartData("16");
			$FC->addChartData("17");
			$FC->addChartData("18");
			$FC->addChartData("19");
			$FC->addChartData("20");
			$FC->addChartData("21");
			$FC->addChartData("22");
						
			# Create second dataset 
			$FC->addDataset("Otros"); 
			# Add chart values for the second dataset
			$FC->addChartData("2");
			$FC->addChartData("3");
			$FC->addChartData("4");
			$FC->addChartData("5");
			$FC->addChartData("6");
			$FC->addChartData("7");
			$FC->addChartData("8");
			$FC->addChartData("9");
			$FC->addChartData("10");
			$FC->addChartData("11");
			$FC->addChartData("12");
			$FC->addChartData("13");

		
		
			//$strXML1 .= "</graph>";				
			//echo renderChart("includes/FCF_Pie3D.swf", "", $strXML1, "chart1", 400, 200, false, false); 
			//echo renderChart("includes/FCF_Column3D.swf", "", $strXML1, "chart1", 700, 300, false, false); 
			//echo renderChart("includes/FCF_Line.swf", "", $strXML, "chart2", 200, 200, false, false); 							
			# Render Chart 
			$FC->renderChart();
		?>
        </td>
   	</tr>
    <tr>
        <td align='left'>	
        <ul type='square'> 
            <li>Monto calculado en base al Inventario.</li>
            <li>Monto en USD (D&oacute;lare Americano).</li>
            <li>Monto en Miles.</li>
            <li>Tipo de cambio 13.00 pesos por d&oacute;lar (Fuente: Control Econ&oacute;mico).</li>
        </ul>     
    </td>
	</tr>      	
	<tr id="Act_Buttons">
    	<td class="EditButton ui-widget-content" style="text-align:center">                            
        	<a id="btnSalir" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" onclick="window.close();" title="Presione para salir ...">
            Salir
            <span class="ui-icon ui-icon-cancel"/>
            </a>
        </td>
 	</tr>
</table>     
</body>
</html>