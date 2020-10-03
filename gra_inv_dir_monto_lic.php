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
	$id_direccion	= $_GET['id_direccion'];		
	$fl_tipo_cambio	= $_GET['fl_tipo_cambio'];		
	$tx_moneda		= $_GET['tx_moneda'];		
	
	//echo "Direccion",$id_direccion;
	//echo "<br>";
	//echo "Tipo Cambio",$fl_tipo_cambio;
	//echo "<br>";
	//echo "Moneda",$tx_moneda;
	
	include("includes/funciones.php");  
	$mysql=conexion_db();	

 	# ==========================================
	# Busco Direccion
	# ==========================================
	$sql = "   SELECT tx_nombre_corto ";
	$sql.= "	 FROM tbl_direccion ";
	$sql.= "    WHERE id_direccion = $id_direccion ";
	
	//echo " sql  ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeDireccion[] = array(
			'tx_direccion'	=>$row["tx_nombre_corto"]
		);
	} 	
	
	for ($i=0; $i < count($TheInformeDireccion); $i++)	{ 	        			 
		while ($elemento = each($TheInformeDireccion[$i]))				
			$tx_direccion	=$TheInformeDireccion[$i]['tx_direccion'];
	}	
	
?>
 <input id="id_direccion" name="id_direccion" type="hidden" value="<? echo $id_direccion ?>" /> 
 <input id="fl_tipo_cambio" name="fl_tipo_cambio" type="hidden" value="<? echo $fl_tipo_cambio ?>" /> 
 <input id="tx_moneda" name="tx_moneda" type="hidden" value="<? echo $tx_moneda ?>" /> 
 
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
		
		var id0="id_direccion="+$("#id_direccion").val();
		var id1="&fl_tipo_cambio="+$("#fl_tipo_cambio").val();
		var id2="&tx_moneda="+$("#tx_moneda").val();
		
		//alert("Valores"+id0+id1+id2);		

		loadHtmlAjax(true, $("#divGrafica"), "gra_inv_dir_monto_lic_g.php?"+id0+id1+id2); 
		//loadHtmlAjax(true, $("#divGrafica"), "gra_inv_dir_monto_lic_g.php?id=1&id_direccion="+$("#id_direccion").val()); 
		
	 	//	$("#sel_grafica").change(function () {
		//		$("#sel_grafica option:selected").each(function () {	
		//		alert ("Entre");
		//		var id="id="+$(this).val();
		//		var id1="&id_direccion="+$("#id_direccion").val();
		//		alert ("aaa"+id1);
		//		$("#divGrafica").html("");	
		//		loadHtmlAjax(true, $("#divGrafica"), "gra_inv_dir_monto_lic_g.php?"+id+id1); 
		//	});
   		//	});	
		
		$("#btnSalir").click(function(){  	
			
			window.close();	
		
		}).hover(function(){
     		$(this).addClass("ui-state-hover")
    	},function(){
        	$(this).removeClass("ui-state-hover")
    	});
		
    });
</script>
    <?
	
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
                <div class="ui-widget-header align-center" style="font-size:14px"><? echo "$tx_direccion" ?></div>	 
                <div class="ui-state-default">&nbsp;</div>
                <div id="divTitleApp" class="ui-state-default" style="font-family:Georgia,'Times New Roman',times,serif; font-size:16px">INVENTARIO LICENCIAS vs MONTO MENSUAL</div> 
          	</div>         
            <div id="divContent">   
            	<table width="100%">
                	<tr>                    	
                    	<td align="center">
                        <!-- GRAFICA DE
                        <br/>                        	                        	
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
                         	 </select> -->
                        </td>                                 	
                   	</tr>                   
                    <tr>
                    	<td id="divGrafica"></td>
                    </tr>
                    <tr id="Act_Buttons">
                        <td class="EditButton ui-widget-content" style="text-align:center">                            
                            <a id="btnSalir" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Presione para salir ...">
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