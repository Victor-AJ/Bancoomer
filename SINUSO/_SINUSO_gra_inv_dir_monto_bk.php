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
	include("includes/FusionCharts.php");

	$mysql=conexion_db();
	
	$id				= $_GET['id'];	
	$id_direccion	= $_GET['id_direccion'];
	
	$fl_tipo_cambio = 13;
	
	if ($id_direccion==0) $dispatch="insert";
	else if (is_null($id_direccion)) { $dispatch="insert"; $id_direccion=0; }
	else $dispatch="save";		
	
	$strXML = "<chart caption='GRAFICA' subCaption='' pieSliceDepth='30' showBorder='1' formatNumberScale='1' numberSuffix=''>";
	
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
	
	$strXML .= "</chart>";
?>  
<table width="100%">
	<tr>
       	<td width="50%" align="left"><img src="images/bbvabancomer.png" width="200px" height="41px"></td>
       	<td width="50%" align="right"><img src="images/asset.png" width="200px" height="41px"></td>
     </tr>
</table>   
<br/>
<div class="ui-widget-header align-center" style="font-size:14px" >INVENTARIO POR DIRECCION - MONTO MENSUAL</div>	  
<br/>
<table cellspacing="1px" border="0" cellpadding="0" width="100%">      		
	<tr>
    	<td>
		<? 
			echo renderChart("FusionCharts/Charts/Pie3D.swf", "", $strXML, "", 950, 480, false, false);
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
    <tr>
    	<td>&nbsp;</td>
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
</div>   
</body>
</html>