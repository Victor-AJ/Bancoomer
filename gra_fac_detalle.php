<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">

<html>    
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: CSI: Bancomer Control Servicios Inform&aacute;ticos v2.0 ::.</title>
<!-- Estilos -->
<link rel="stylesheet" type="text/css" media="screen" href="css/ui-personal/jquery-ui-1.7.2.custom.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="css/ui.jqgrid.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="css/estilos.css"/> 

<!-- Librerias -->
<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script>
<script src="js/layout/jquery.layout.js" type="text/javascript"></script>
<script src="js/user.js" type="text/javascript"></script>
<script src="FusionCharts/FusionCharts.js" type="text/javascript"></script>
</head>
<body>	
	<form id="loginForm" method="" action="">
<?
	$id_factura	= $_GET['id'];	
	echo "Factura ",$id_factura;
?>
 <input id="id_factura" name="id_factura" type="hidden" value="<? echo $id_factura ?>" /> 
 
<!-- Inicializa -->
<script type="text/javascript">
	var myLayout;  
    jQuery(document).ready(function(){

		 myLayout = $('body').layout({
			south__initClosed: true,
			west__initClosed: true,
			east__initClosed: true,
			spacing_open: 7,
			spacing_closed: 7,
			togglerTip_open: "Cerrar panel",
			togglerTip_closed: "Abrir panel",
			resizerTip: "Cambiar el tamaño del panel",
			sliderTip: "Mostrar el panel"       
		 });
            	
       	$("a, button, ul#icons li").hover(function () {$(this).addClass('ui-state-hover');},function () {$(this).removeClass("ui-state-hover");});
		
		//var aa= $("#id_factura").val()
		//alert ("aaa"+aa);
		
		loadHtmlAjax(true, $("#divGrafica"), "gra_fac_detalle_g.php?id=1&id_factura="+$("#id_factura").val()); 
		
	 	$("#sel_grafica").change(function () {
			$("#sel_grafica option:selected").each(function () {	
				//alert ("Entre")			;
				var id="id="+$(this).val();
				var id1="&id_factura="+$("#id_factura").val();
				//alert ("aaa"+id1);
				$("#divGrafica").html("");	
				loadHtmlAjax(true, $("#divGrafica"), "gra_fac_detalle_g.php?"+id+id1); 
			});
   		});	
		
		$("#verCabecera").find("tr").hover(		 
        	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
         );
		
    });
</script>
<?
	include("includes/funciones.php");  

	$mysql=conexion_db();
	
	$sql = "   SELECT id_factura, tx_anio, b.id_proveedor, tx_proveedor_corto, c.id_cuenta, tx_cuenta, tx_factura, fh_factura, fh_inicio, fh_final, fl_precio_usd, fl_precio_mxn, fl_precio_eur, fl_tipo_cambio, tx_mes, tx_estatus ";
	$sql.= "     FROM tbl_factura a, tbl_proveedor b, tbl_cuenta c, tbl_mes d, tbl_factura_estatus e ";
	$sql.= "  	WHERE id_factura 			= $id_factura ";
	$sql.= "      AND a.id_proveedor		= b.id_proveedor ";
	$sql.= "      AND a.id_cuenta 			= c.id_cuenta ";
	$sql.= "      AND a.id_mes 				= d.id_mes ";
	$sql.= "      AND a.id_factura_estatus	= e.id_factura_estatus ";
	
	//echo "aaa", $sql;
			
	$result = mysqli_query($mysql, $sql);		
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			'id_factura'		=>$row["id_factura"],
			'tx_anio'			=>$row["tx_anio"],
	  		'id_proveedor'		=>$row["id_proveedor"],
	  		'tx_proveedor_corto'=>$row["tx_proveedor_corto"],
	  		'id_cuenta'			=>$row["id_cuenta"],
	  		'tx_cuenta'			=>$row["tx_cuenta"],
	  		'tx_factura'		=>$row["tx_factura"],
	  		'fh_factura'		=>$row["fh_factura"],
			'fh_inicio'			=>$row["fh_inicio"],
	  		'fh_final'			=>$row["fh_final"],
	  		'fl_precio_usd'		=>$row["fl_precio_usd"],
	  		'fl_precio_mxn'		=>$row["fl_precio_mxn"],
	  		'fl_precio_eur'		=>$row["fl_precio_eur"],
	  		'fl_tipo_cambio'	=>$row["fl_tipo_cambio"],
  			'tx_mes'			=>$row["tx_mes"],	  		
  			'tx_estatus'		=>$row["tx_estatus"]	  		
		);
	} 	
		
	for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_factura			=$TheCatalogo[$i]['id_factura'];
			$tx_anio			=$TheCatalogo[$i]['tx_anio'];
			$id_proveedor		=$TheCatalogo[$i]['id_proveedor'];
			$tx_proveedor_corto	=$TheCatalogo[$i]['tx_proveedor_corto'];
			$tx_cuenta			=$TheCatalogo[$i]['tx_cuenta'];
			$id_cuenta			=$TheCatalogo[$i]['id_cuenta'];
			$tx_factura			=$TheCatalogo[$i]['tx_factura'];
			$fh_factura			=$TheCatalogo[$i]['fh_factura'];
			$fh_inicio			=$TheCatalogo[$i]['fh_inicio'];
			$fh_final			=$TheCatalogo[$i]['fh_final'];
			$fl_precio_usd		=$TheCatalogo[$i]['fl_precio_usd'];
			$fl_precio_mxn		=$TheCatalogo[$i]['fl_precio_mxn'];
			$fl_precio_eur		=$TheCatalogo[$i]['fl_precio_eur'];
			$fl_tipo_cambio		=$TheCatalogo[$i]['fl_tipo_cambio'];	  		
			$tx_mes				=$TheCatalogo[$i]['tx_mes'];
			$tx_estatus			=$TheCatalogo[$i]['tx_estatus'];
	}
	
	$fh_perido = $fh_inicio."   ".$fh_final;		
	$fl_precio_usd_cabecera=$fl_precio_usd;	
	$fl_precio_mxn_cabecera=$fl_precio_mxn;	
	$fl_precio_eur_cabecera=$fl_precio_eur;	
	
	if ($fl_precio_usd<>0) 		{ $fl_precio=number_format($fl_precio_usd,2); $tx_moneda="USD";	}
	else if ($fl_precio_mxn<>0) { $fl_precio=number_format($fl_precio_mxn,2); $tx_moneda="MXN";	}
	else if ($fl_precio_eur<>0) { $fl_precio=number_format($fl_precio_eur,2); $tx_moneda="EUR";	}
	
	if ($fh_factura==NULL) $fh_factura="-";

?>
    <div id="NorthPane" class="ui-layout-north ui-widget ui-widget-content">
    	<table width="100%">
        	<tr>
            	<td width="50%" align="left"><img alt="" src="images/bbvabancomer.png" width="200px" height="41px"></td>
                <td width="50%" align="right"><img alt="" src="images/asset.png" width="200px" height="41px"></td>
            </tr>
       	</table>    
        <div id="divLoading" class="ui-helper-hidden"><img alt="" src="images/ajax-loader.gif"/><strong>&nbsp;&nbsp;Por favor espere...</strong></div>
        <div id="divSwitcher"><div id="switcher"></div></div>         	
   		</div> <!-- #NorthPane -->
   		<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
   			<div id="divSecondMenu" class="ui-widget ui-widget-content ui-helper-clearfix"></div>
		</div> <!-- #LeftPane -->
        <div id="RightPane" class="ui-layout-east ui-widget ui-widget-content">
            <div id="divMenu" class="ui-widget ui-widget-content ui-helper-clearfix"></div>
        </div> <!-- #RightPane -->
        <div id="SouthPane" class="ui-layout-south ui-helper-reset ui-widget-content" ><!-- Tabs pane -->
            <div style="text-align:right;"></div>
        </div> <!-- #SouthPane -->
        <div id="CenterPane" class="ui-layout-center ui-helper-reset ui-widget-content" ><!-- Tabs pane -->
            <div id="dialogMain" class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-helper-hidden"  title="Mensaje">                
                <div class="ui-widget-header align-center" style="font-size:14px" >FACTURACION POR DIRECCION - MONTO</div>	  
          </div>           
            <div id="divContent">  
            	<table id="verCabecera" width="100%" cellspacing="1px" border="1" cellpadding="1">
                	<tr>
                    	<td width="4%" class="ui-state-highlight align-center">A&ntilde;o</td>
                        <td width="11%" class="ui-state-highlight align-center">Proveedor</td>
                        <td width="16%" class="ui-state-highlight align-center">Cuenta</td>
                        <td width="8%" class="ui-state-highlight align-center">Factura</td>                                
                        <td width="10%" class="ui-state-highlight align-center">Monto</td>
                        <td width="5%" class="ui-state-highlight align-center">Moneda</td>
                        <td width="8%" class="ui-state-highlight align-center">Tipo Cambio</td>
                        <td width="9%" class="ui-state-highlight align-center">Fecha Factura</td>
                        <td width="16%" class="ui-state-highlight align-center">Periodo</td>
                        <td width="7%" class="ui-state-highlight align-center">Mes de Pago</td>
                        <td width="6%" class="ui-state-highlight align-center">Estatus</td>
                  	</tr>
                    <tr>
                       	<td class="align-center"><? echo "$tx_anio" ?></td>
                        <td class="align-center"><? echo "$tx_proveedor_corto" ?></td>
                        <td class="align-center"><? echo "$tx_cuenta" ?></td>
                        <td class="align-center"><? echo "$tx_factura" ?></td>
                        <td class="align-right"><? echo "$fl_precio" ?></td>
                       	<td class="align-center"><? echo "$tx_moneda" ?></td>
                        <td class="align-right"><? echo "$fl_tipo_cambio" ?></td>
                        <td class="align-center"><? echo "$fh_factura" ?></td>
                        <td class="align-center"><? echo "$fh_perido" ?></td>
                        <td class="align-center"><? echo "$tx_mes" ?></td>
                        <td class="align-center"><? echo "$tx_estatus" ?></td>
                  	</tr>                            
             	</table> 
              <!--	<div id="divTitleApp" class="ui-state-default" style="font-family:Georgia,'Times New Roman',times,serif;"><? //echo "$tx_proveedor" ?></div> -->
            	<table width="100%">
                	<tr>                    	
                    	<td align="center"><br/>GRAFICA DE                        	                        	
                        	<select id="sel_grafica" name="sel_grafica" size="1px" style="font-size:12px">
                             	<option value="1">PIE 2D</option>
                                <option value="2">PIE 3D</option>
                                <option value="3">COLUMNAS 3D</option>
                                <option value="4">COLUMNAS 2D</option>
                                <option value="5">BARRAS 2D</option>                                
                                <option value="6">ANILLOS 2D</option>
                                <option value="7">ANILLOS 3D</option>
                                <option value="8">AREA</option>
                                <option value="9">LINE</option>
                         	 </select>
                        </td>                                 	
                   	</tr>                   
                    <tr>
                    	<td id="divGrafica"></td>
                    </tr>
                    <tr id="Act_Buttons">
                        <td class="EditButton ui-widget-content" style="text-align:center">                            
                            <a id="btnSalir" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" onclick="window.close()" title="Presione para salir ...">
                            Salir
                            <span class="ui-icon ui-icon-cancel"/></span>
                            </a>
                       </td>
                	</tr>
                </table>                     
         </div>			
	</div> 
    </form>    
<!-- #CenterPane -->		
	</body>
</html>