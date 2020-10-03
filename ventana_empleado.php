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
</head>
<body>	
<?	
	$id= $_GET['id'];		
?>
	<input id="id" name="id" type="hidden" value="<? echo $id ?>" /> 

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
		
		var id="id="+$("#id").val();

		loadHtmlAjax(true, $("#divGrafica"), "ventana_empleado_datos.php?"+id); 
				
    });
</script>

	<form id="loginForm" method="" action="">
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
        <div id="SouthPane" class="ui-layout-south ui-helper-reset ui-widget-content"><!-- Tabs pane -->
            <div style="text-align:right;"></div>
        </div> <!-- #SouthPane -->
        <div id="CenterPane" class="ui-layout-center ui-helper-reset ui-widget-content"><!-- Tabs pane -->
            <div id="dialogMain" class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-helper-hidden" title="Mensaje">                
                <div class="ui-widget-header align-center" style="font-size:13px" >INVENTARIO POR EMPLEADO</div>	  
            </div>           
            <div id="divContent">        
            	<table width="100%">                	 
                    <tr>
                    	<td id="divGrafica"></td>
                    </tr>
                    <tr>
                        <td class="EditButton ui-widget-content" style="text-align:center">                            
                            <a id="btnSalir" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" onclick="window.close()" title="Presione para salir ...">
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