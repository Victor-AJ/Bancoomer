<?
session_start();
if 	(isset($_SESSION['sess_user']))
{
?>

<script type="text/javascript">
	
	$("#divContent").html("");	

    jQuery(document).ready(function(){
        var minlengthPass = 6;
        var password1 = $("#txtpassword1");
        var errpassword1 = $("#errtxtpassword1");
        var password2 = $("#txtpassword2");
        var errpassword2 = $("#errtxtpassword2");
        var guarda = $("#btnOk");

        $("a, button, ul#icons li").hover(function () {$(this).addClass('ui-state-hover');},function () {$(this).removeClass("ui-state-hover");});

        $("input:text, input:password").focus(function() {
            $(this).addClass('ui-state-focus');
        });

        $("input:text, input:password").blur(function() {
            $(this).removeClass('ui-state-focus');
        });

        // Funciones para eventos escritura y perdida enfoque Login
        password1.keyup(function (e) {
            validText(true, $(this), errpassword1, minlengthPass);
        });
        password1.blur(function () {            
            //var controller = "userProcess.do?dispatch=validUniqueField&"+$("#passwordForm").serialize();
            //validUniqueField(password1, errpassword1, minlengthPass, controller);
			validText(false, $(this), errpassword1, minlengthPass);
        });

        // Funciones para eventos escritura y perdida enfoque Password
        password2.keyup(function (e) {
            if (e.which == 13) {
                guarda.click();
            }else {
                validText(true, $(this), errpassword2, minlengthPass);
            }
        });
        password2.blur(function () {
            validEqualFields(password1,password2, errpassword2);
        });

        // Funciones para ingreso de usuario
        guarda.click(function () {
            goChangePassword();
        });

        function goChangePassword(){
            var form = $('#passwordForm');
            //var controller = "userProcess.do?dispatch=validUniqueField&"+$("#passwordForm").serialize();
			var controller = "process_password.php?dispatch=change&"+$("#passwordForm").serialize();
            if(validText(false, password1, errpassword1, minlengthPass) && validText(false, password2, errpassword2, minlengthPass) && validEqualFields(password1,password2, errpassword2)){
                var func = function(data){					   			
					var fAceptar = function(){
						$('#dialogMain').dialog("close");
						logout();
					}					
					if(data.error == true){						
						if(data.message != null){							
							jAlert(true,true,data.message,fAceptar);
						}else{
							logout();
						}
					} else {						
						if(data.message != null){					
							jAlert(true,false,data.message,fAceptar);
						}
					}	
				}	
				//alert ("Entre"+controller);
                executeAjax("post", false , controller , "json", func);
            }
            return;
        }
    });
</script>
<div id="divEspacios"><br/><br/><br/><br/>
<div id="divTitleApp" class="ui-state-default" style="font-family:Georgia,'Times New Roman',times,serif;">Bienvenido !</div>
</div>
<div align="center"><br/><br/><br/>
<form id="passwordForm" method="" action="">
    <table width="300px">
        <tr>
            <td colspan="2" align="center" class="ui-state-highlight">
                <label style="font-family:Verdana,Arial,sans-serif;font-size: 13px;font-weight:bold;border:none;"><span class='ui-icon ui-icon-info' style='float: left; margin-right: 0.3em;'/>Cambio de contrase&ntilde;a&nbsp;</label>
            </td>
        </tr>
        <tr>
            <td align="left"><label for="txtpassword1" class="ui-state-focus" style="font-family:Verdana,Arial,sans-serif;font-size: 13px;font-weight:bold;border:none;">Nueva Contrase&ntilde;a:</label></td>
            <td align="left">
                <input id="txtpassword1" name="txtpassword1" type="password"
                       value="" size="15" maxlength="20"
                       title="Ingrese la contrase&ntilde;a"/>
            </td>
        </tr>
        <tr><td colspan="2"><div id="errtxtpassword1"></div></td></tr>
        <tr>
            <td align="left"><label for="txtpassword2" class="ui-state-focus" style="font-family:Verdana,Arial,sans-serif;font-size: 13px;font-weight:bold;border:none;">Confirmaci&oacute;n:</label></td>
            <td align="left">
                <input id="txtpassword2" name="txtpassword2" type="password"
                       value="" size="15" maxlength="20"
                       title="Confirmaci\u00f3n de la contrase&ntilde;a"/>
            </td>
        </tr>
        <tr><td colspan="2"><div id="errtxtpassword2"></div></td></tr>
        <tr>
            <td colspan="2" align="center">
                <a id="btnOk" class="fm-button ui-state-default ui-corner-all fm-button-icon-left "
                   href="#" title="Presione guardar los cambios">
                    Guardar
                    <span class="ui-icon ui-icon-home"/>
                </a>
            </td>
        </tr>
    </table>
</form>
</div>
<?
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>
