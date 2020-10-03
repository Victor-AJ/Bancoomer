
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">

<html>    
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: CSI: Bancomer Control Servicios Inform&aacute;ticos v2.0 ::.</title>
<!-- Estilos -->
<link rel="stylesheet" type="text/css" media="screen" href="css/ui-personal/jquery-ui-1.7.2.custom.css"/> 
<!-- <link type="text/css" href="css/dark-hive/jquery-ui-1.7.3.custom.css" rel="stylesheet" />	-->
<link rel="stylesheet" type="text/css" media="screen" href="css/ui.jqgrid.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="css/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="css/estilos.css"/> 

<!-- Librerias -->
<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>
<!-- <script src="js/jquery-1.4.4.min.js" type="text/javascript"></script> -->
<script src="js/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script>
<!-- <script src="js/layout/jquery.layout.js" type="text/javascript"></script> -->
<script src="js/layout/jquery.layout-latest.js" type="text/javascript"></script>
<script src="js/grid/grid.locale-sp.js" type="text/javascript"></script>
<script src="js/grid/jquery.jqGrid.min.js" type="text/javascript"></script>
<script src="js/autocomplete/jquery.autocomplete.js" type="text/javascript"></script> 
<script src="js/validate/jquery.meio.mask.min.js" type="text/javascript"></script>
<!-- <script src="js/validate/jquery.maskedinput-1.2.2.js" type="text/javascript"></script> -->
<!-- <script src="js/validate/jquery.meio.mask.js" type="text/javascript"></script> -->
<!--<script src="js/bgiframe/jquery.bgiframe" type="text/javascript"></script> -->
<script src="js/bgiframe/jquery.bgiframe.min.js" type="text/javascript"></script> 
<script src="js/user.js" type="text/javascript"></script>
<!-- <script src="FusionCharts/FusionCharts.js" type="text/javascript"></script> -->
<!-- <script src="js/chrome.js" type="text/javascript"></script> -->
<!-- <script src="js/menu-for-applications.js" type="text/javascript"></script> -->

<!-- Inicializa -->
<script type="text/javascript">
	var myLayout;
    var gridimgpath = '/css/ui-personal/images';
    var editing = false;

    jQuery(document).ready(function(){

//	$('#switcher').themeswitcher({
//    	buttonPreText: 'Tema: ',
//        initialText: 'Dise�os disponibles'
//    });

     myLayout = $('body').layout({
     	south__initClosed: true,
        west__initClosed: true,
        east__initClosed: true,
        spacing_open: 7,
        spacing_closed: 7,
        togglerTip_open: "Cerrar panel",
        togglerTip_closed: "Abrir panel",
        resizerTip: "Cambiar el tama�o del panel",
        sliderTip: "Mostrar el panel"
        //north__spacing_open: 8,
     });
                
     $.jgrid.defaults = $.extend($.jgrid.defaults,{loadui:"enable",altRows:true});

     });

     jQuery(function($){
     	$.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '&#x3c;Ant',
        nextText: 'Sig&#x3e;',
        currentText: 'Hoy',
        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
        'Jul','Ago','Sep','Oct','Nov','Dic'],
        dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
        dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
        dateFormat: 'dd/mm/yy', firstDay: 0,
        isRTL: false};
        $.datepicker.setDefaults($.datepicker.regional['es']);
     });
</script>
</head>
<body>	
	<div id="NorthPane" class="ui-layout-north ui-widget ui-widget-content">
    	<!-- <div> -->
            <table width="100%">
                <tr>
                    <td width="50%" align="left"><img alt="" src="images/bbvabancomer.png" width="200px" height="41px"></td>
                    <td width="50%" align="right"><img alt="" src="images/asset.png"></td>
                </tr>
            </table>    
            <!-- <div id="divAppName">CIS Bancomer</div> -->
            
            <div id="divSwitcher"><div id="switcher"></div></div> 
            <!-- Para activar los dise�os -->
            <!--     <script type="text/javascript" src="http://ui.jquery.com/themeroller/themeswitchertool/"></script> -->
        <!--  </div> -->
   	</div> <!-- #NorthPane -->

   	<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
   		<div id="divSecondMenu" class="ui-widget ui-widget-content ui-helper-clearfix"></div>
	</div> <!-- #LeftPane -->

    <div id="RightPane" class="ui-layout-east ui-widget ui-widget-content">
    	<div id="divMenu" class="ui-widget ui-widget-content ui-helper-clearfix"></div>
    </div> <!-- #RightPane -->

    <div id="SouthPane" class="ui-layout-south ui-helper-reset ui-widget-content" ><!-- Tabs pane -->
    	<div style="text-align:right;"><img alt="" src="images/themeroller_ready_white_200px.gif" width="200px"/></div>
	</div> <!-- #SouthPane -->

    <div id="CenterPane" class="ui-layout-center ui-helper-reset ui-widget-content" ><!-- Tabs pane -->
    <div id="divLoading" class="ui-helper-hidden" >
		    <table border=0 cellspacing=0 cellpadding=0  >
		    <tr>
		    <td  valign="middle">
		    <img alt="" src="images/ajax-loader.gif"/>
		    </td>
		    <td  valign="middle">
		   <strong>&nbsp;CARGANDO ...</strong>
		    </td>
		    </tr>
		    </table>
    </div>
    	<div id="dialogMain" class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-helper-hidden"  title="Mensaje">
        	<div id="dialogContent" class="ui-corner-all" style="padding: 0pt 0.7em;"></div>
    	</div>
        <div id="divMenuPrincipal"></div>           
   		<div id="divContent">        
        <div id="divTitleApp" class="ui-state-default" style="font-family:Georgia,'Times New Roman',times,serif;">Direcci&oacute;n de Producci&oacute;n</div>
        <div id="divTitleApp" class="ui-state-default" style="font-family:Georgia,'Times New Roman',times,serif;">Control Servicios Inform&aacute;ticos</div>
        <div id="divTitleApp" class="ui-state-default" style="font-family:Georgia,'Times New Roman',times,serif;">CSI Bancomer v2.0</div>
        <table width="100%">
        	<tr>
            	<td width="50%" align="center"> 
        			<div id="divImgPrincipal">
                    	<br><br><br>
                        	<img src="images/imagen.png"/>        
                        <br><br><br>
        			</div>
            	</td>
				<td width="50%" align="center"> 
            		<div id="divStart">
            			<form id="loginForm"  action="">
                			<table width="300px">
                            	<tr>
                        			<td colspan="2" align="center" class="ui-state-highlight"><div style="font-family:Verdana,Arial,sans-serif;font-size: 13px;font-weight:bold;border:none;"><span class='ui-icon ui-icon-locked' style='float: left; margin-right: 0.3em;'></span>Acceso a Usuarios Autorizados</div></td>                                                                		
                       			</tr>
                    			<tr>
                        			<td align="right" class="ui-state-highlight"><label for="txtlogin" style="font-family:Verdana,Arial,sans-serif;font-size: 13px;font-weight:bold;border:none;">Usuario:</label></td>
                            		<td align="left">
                            			<input id="txtlogin" name="txtlogin" type="text"
                                			value="" size="18" maxlength="15"
                                    		title="Ingrese el nombre de usuario"/>
									</td>
                       			</tr>
                        		<tr><td colspan="2" align="center"><div id="errtxtlogin"></div></td></tr>
                        		<tr>
                        			<td width="50%" align="right" class="ui-state-highlight"><label for="txtpassword" style="font-family:Verdana,Arial,sans-serif;font-size: 13px;font-weight:bold;border:none;">Contrase&ntilde;a:</label></td>
                            		<td align="left">
                            			<input id="txtpassword" name="txtpassword" type="password"
                                			value="" size="18" maxlength="15"
                                    		title="Ingrese la contrase&ntilde;a"/>
									</td>
                        		</tr>
                        		<tr><td colspan="2"><div id="errtxtpassword"></div></td></tr>  
                                <tr><td colspan="2"><div id="newpass"></div></td></tr>                        		
                        		<tr>
                        			<td colspan="2" align="center">
                            			<a id="btnIngresar" class="fm-button ui-state-default ui-corner-all fm-button-icon-left"
                                			href="#" title="Presione para ingresar al sistema">
                                    		Ingresar
                                  			<span class="ui-icon ui-icon-home"></span>
										</a>
									</td>
                       			</tr>
                    		</table>
              			</form>								 	
					</div>
           	  </td>
          	</tr>
       	</table> 
		</div>
       	<script type="text/javascript">
       		jQuery(document).ready(function(){
            	var minlengthLogin = 1;
                var minlengthPass = 6;
                var login = $("#txtlogin");
                var errlogin = $("#errtxtlogin");
                var pass = $("#txtpassword");
                var errpass = $("#errtxtpassword");
                var ingresa = $("#btnIngresar");				
				var passnew = $("#txtpassword_new");
                var errpassnew = $("#errtxtpassword");
				
				$("#txtlogin").focus();

                $("a, button, ul#icons li").hover(function () {$(this).addClass('ui-state-hover');},function () {$(this).removeClass("ui-state-hover");});

                $("input:text, input:password").focus(function() {
                	$(this).addClass('ui-state-focus');
                });

          		$("input:text, input:password").blur(function() {
                	$(this).removeClass('ui-state-focus');
                });

                // Funciones para eventos escritura y perdida enfoque Login
                login.keyup(function (e) {
                	validText(true, $(this), errlogin, minlengthLogin);
                });
                login.blur(function () {
                    validText(false, $(this), errlogin, minlengthLogin);
                });

                // Funciones para eventos escritura y perdida enfoque Password
                pass.keyup(function (e) {
					if (e.which == 13) {
						ingresa.click();
					}else {
						validText(true, $(this), errpass, minlengthPass);
					}
           		});
            	pass.blur(function () {
            	validText(false, $(this), errpass, minlengthPass);
            	});

            // Funciones para ingreso de usuario
            ingresa.click(function () {
            	checkLogin();
            });

            function checkLogin(){
            var form = $('#loginForm');
            if(validText(false, login, errlogin, minlengthLogin) && validText(false, pass, errpass, minlengthPass)){
				
				//=====================
				//if (login.val()==pass.val()) {	
					//alert ("Entre");
				//	openNewPass();				
				//} else {
				//=====================
			
					var dispatch = "?dispatch=checkLogin&"+form.serialize();
					//alert ("aaa"+dispatch);
			
					var func = function(data){
						if(data.error == true){
							var fAceptar = function(){
								$('#dialogMain').dialog("close");
						};
				
						if(data.message != null){
							login.val("");
							pass.val("");
							jAlert(true,true,data.message,fAceptar);
						}else{
							logout();
							jAlert(true,false,"Sesi\u00f3n finalizada",fAceptar);
						}
					}else{
						var container;
						if(data.message != null){
							container = $("#divStart");
							//container = $("#divSecondMenu");
						}else{
							if (login.val()==pass.val()) {	
								//alert ("Entre");
								container = $("#divMenuPrincipal");							
							} else {
							//alert ("Entre");
								container = $("#divSecondMenu");
							}
					}				
					loadHtmlAjax(true, container, data.html);
				}
            };	
			
			dispatch="acceso.php"+dispatch;
			executeAjax("post", false ,dispatch, "json", func);
			//alert ("Entre"+dispatch);
            //}
			}
      }
      });
	</script>	
	</div> <!-- #CenterPane -->		
	</body>
</html>
