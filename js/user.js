/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
Escape.....: 27
Enter......: 13
Tabulador..: 9
Retroceso..: 8
\u00e1 -> �
\u00e9 -> �
\u00ed -> �
\u00f3 -> �
\u00fa -> �
\u00c1 -> ��
\u00c9 -> �
\u00cd -> �
\u00d3 -> �
\u00da -> �
\u00f1 -> �
\u00d1 -> �
\u00bf -> �
 */

/*function logout() {	
	var fAceptar = function(){
		executeAjax("post", true, "userProcess.do", "dispatch=logout", "html", func);
        window.open("index.php", "_self");
    }
	var fCancelar = function(){
        $('#dialogMain').dialog("close");
    }
 	jConfirm(true,"\u00bf Desea salir de la herramienta<br/>CSI: Bancomer ?", fAceptar, fCancelar);    
}*/

function logout() {
    var func = function(){
		//alert ("Entre");
        window.open("index.php", "_self");
    }
    executeAjax("post", true, "process_logout.php", "json", func);
	//executeAjax("post", sync, url, "json", func);
}


function unplugged(){
    var fAceptar = function(){
        $('#dialogMain').dialog("close");
    }
    jAlert(true,false,"Por inactividad se ha terminado su sesi\u00f3n",fAceptar);
    window.open("login.do", "_self");
}

// SENTENCIAS AJAX

function executeAjax(smethod, syncMethod, surl, sdatatype, func){
    $.ajax({
        method: smethod,
        url: surl,
        async: syncMethod,
        dataType: sdatatype,
        cache: false,
        beforeSend: function(){
            $("#divLoading").show();
        },
        complete: function(){
            $("#divLoading").hide();
        },
        error: function (xhr, ajaxOptions, thrownError){
            var fAceptar = function(){
                $('#dialogMain').dialog("close");
            }
            jAlert(true,true,"Problema al ejecutar sentencia AJAX "+xhr.status +" "+thrownError,fAceptar);
        },
        success: func
    });
}

function loadHtmlAjax(syncMethod, container, url){
    var func = function(data){
        container.html(data).hide().fadeIn('slow');
    }
    executeAjax("post", syncMethod, url, "html", func);
}

function executeAjaxJSON(url, sync, container){
    var fAceptar = function(){
        $('#dialogMain').dialog("close");
    }
    var func = function(data){
        if(data.error == true){
            if(data.message != null){
                jAlert(true,true,data.message,fAceptar);
            }else{
                logout();
            }
        }else{
            if(data.message != null){
                jAlert(true,false,data.message,fAceptar);
            }
            if(data.id != null){
                $("#id").val(data.id);
            }
            if(data.html != null){
                executeLoadAjax(container, data.html);
            }
        }
    }
    executeAjax("post", sync, url, "json", func);
}

// VENTANA MODAL DE MENSAJES

function jAlert(show,error,message, func){
    // Dialog
    $("#dialogMain").dialog({
        autoOpen: false,
        bgiframe: true,
        height: 200,
        modal: true,
        show: 'fold',
        zIndex: 1500
    });

    $('#dialogMain').dialog('option', 'buttons', { 
        "Aceptar": func
    });
    $('#dialogContent').removeClass("ui-state-error");
    $('#dialogContent').removeClass("ui-state-highlight");
    $('#dialogContent').html("");
    if(error){
        $('#ui-dialog-title-dialogMain').text("Mensaje de error");
        $('#dialogContent').addClass("ui-state-error");
        $('#dialogContent').append("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 50px 0;'></span>"+message+"</p>");
    }else{
        $('#ui-dialog-title-dialogMain').text("Mensaje de confirmaci\u00f3n");
        $('#dialogContent').addClass("ui-state-highlight");
        $('#dialogContent').append("<p><span class='ui-icon ui-icon-info' style='float:left; margin:0 7px 50px 0;'></span>"+message+"</p>");
    }
    if(show){
        $('#dialogMain').dialog('open');
    }else{
        $('#dialogMain').dialog('close');
    }
    return false;
}

function jConfirm(show,message, funcOk, funcCancel){
    // Dialog
    $("#dialogMain").dialog({
        autoOpen: false,
        bgiframe: true,
        height: 200,
        modal: true,
        show: 'fold',
        zIndex: 1500
    });

    $('#dialogMain').dialog('option', 'buttons', {
        "Cancelar": funcCancel,
        "Aceptar": funcOk
    });

    $('#ui-dialog-title-dialogMain').text("Confirmaci\u00f3n");
    $('#dialogContent').removeClass("ui-state-error");
    $('#dialogContent').removeClass("ui-state-highlight");
    $('#dialogContent').html("");

    $('#dialogContent').addClass("ui-state-highlight");
    $('#dialogContent').append("<p><span class='ui-icon ui-icon-info' style='float:left; margin:0 7px 50px 0;'></span>"+message+"</p>");
    if(show){
        $('#dialogMain').dialog('open');
    }else{
        $('#dialogMain').dialog('close');
    }
}

// VALIDACIONES

function validText(editing, field, fielderror, min){
    var tam = field.val().length;
    var max = parseInt(field.attr("maxlength"));
    var error = false;
    var mensaje = "";
    if( tam == 0 ){
        mensaje = "El campo no puede dejarse en blanco";
        error = true;
    }else if( tam < parseInt(min)  ){
        mensaje = "El campo debe tener un m\u00ednimo de "+min;
        error = true;
    }else if( tam > parseInt(max) ){
        mensaje = "El campo debe tener un m\u00e1ximo de "+max;
        error = true;
    }
    if(error){
        field.removeClass("ui-state-focus");
        field.addClass("ui-state-error");
        fielderror.addClass("ui-state-error ui-state-error-text");
        fielderror.html("<span class='ui-icon ui-icon-alert' style='float: left; margin-right: 0.3em;'/>"+mensaje);
    }else{
        field.removeClass("ui-state-error");
        fielderror.removeClass("ui-state-error");
        fielderror.html("");
        if(!editing){
            field.removeClass("ui-state-focus");
        }else{
            field.addClass("ui-state-focus");
        }
    } 
    return !error;
}

function validSelect(field, fielderror){
    var valor = field.val();
    field.removeClass("ui-state-focus");
    if(valor == 0){
        field.addClass("ui-state-error");
        fielderror.addClass("ui-state-error ui-state-error-text");
        fielderror.html("<span class='ui-icon ui-icon-alert' style='float: left; margin-right: 0.3em;'/>Debe seleccionar una opci\u00f3n");
        return false;
    }else{
        field.removeClass("ui-state-error");
        fielderror.removeClass("ui-state-error");
        fielderror.html("");
    }
    return true;
}

function validNumeric(field, fielderror){
    var er_num = /^([0-9]|\.)+$/    
    var limpia = "";
    var valor = field.val();
    for(var i = 0;i < valor.length; i++){
        if(er_num.test(valor.charAt(i))) {
            limpia += valor.charAt(i);
        }
    }
    var valfloat = parseFloat(limpia);
    if( valfloat == NaN){
        field.addClass("ui-state-error");
        fielderror.addClass("ui-state-error ui-state-error-text");
        fielderror.html("<span class='ui-icon ui-icon-alert' style='float: left; margin-right: 0.3em;'/>No es un n\u00famero v\u00e1lido");
        return false;
    } else if(valfloat == 0){
        field.addClass("ui-state-error");
        fielderror.addClass("ui-state-error ui-state-error-text");
        fielderror.html("<span class='ui-icon ui-icon-alert' style='float: left; margin-right: 0.3em;'/>Debe escribir un n\u00famero diferente a 0");
        return false;
    }
    field.removeClass("ui-state-error");
    fielderror.removeClass("ui-state-error");
    fielderror.html("");
    return true;
}

function validSelectCeros(field, fielderror){
    var valor = field.val();
    field.removeClass("ui-state-focus");
    if(valor == 0){
        field.addClass("ui-state-error");
        fielderror.addClass("ui-state-error ui-state-error-text");
        fielderror.html("<span class='ui-icon ui-icon-alert' style='float: left; margin-right: 0.3em;'/>Debe escribir una cantidad diferente de cero");
        return false;
    }else{
        field.removeClass("ui-state-error");
        fielderror.removeClass("ui-state-error");
        fielderror.html("");
    }
    return true;
}

function validUniqueField(field, fielderror, min,  controller){
    if(validText(false,field,fielderror,min)){
        var func = function(data){
            if(data.error == true){
                field.addClass("ui-state-error");
                fielderror.addClass("ui-state-error ui-state-error-text");
                fielderror.html("<span class='ui-icon ui-icon-alert' style='float: left; margin-right: 0.3em;'/>"+data.message);
                return false;
            }else{
                field.removeClass("ui-state-error");
                fielderror.removeClass("ui-state-error");
                fielderror.html("");
                return true;
            }
        }
        executeAjax("post", false , controller , "json", func);
    }
}

function validEqualFields(field1, field2, fielderror){
    if(field1.val()!=field2.val()){
        field1.addClass("ui-state-error");
        field2.addClass("ui-state-error");
        fielderror.addClass("ui-state-error ui-state-error-text");
        fielderror.html("<span class='ui-icon ui-icon-alert' style='float: left; margin-right: 0.3em;'/>Las contrase\u00f1as no son iguales");
        field1.val("");
        field2.val("");
        return false;
    }else{
        field1.removeClass("ui-state-error");
        field2.removeClass("ui-state-error");
        fielderror.removeClass("ui-state-error");
        fielderror.html("");
    }
    return true;
}

function openDireccion(valor)
{
	var id="?id="+valor;	
	loadHtmlAjax(true, $("#divDireccion"), "lista_direccion.php");
}

function openSubdireccion(valor,par_subdireccion,par_dispatch)
{
	var id="?id="+valor;
	var par_subdireccion="&par_subdireccion="+par_subdireccion;
	var dispatch="&dispatch="+par_dispatch;	
	$("#divDepartamento").html("");	 
	loadHtmlAjax(true, $("#divSubdireccion"), "combo_subdireccion.php"+id+par_subdireccion+dispatch);
}

function openDepartamento(valor,par_departamento,par_dispatch)
{
	var id="?id="+valor;
	var par_departamento="&par_departamento="+par_departamento;
	var dispatch="&dispatch="+par_dispatch;	
	loadHtmlAjax(true, $("#divDepartamento"), "combo_departamento.php"+id+par_departamento+dispatch);
}

function openPerfil(valor)
{
	var id="?id="+valor;
	//alert (dispatch);
	loadHtmlAjax(true, $("#divOpciones"), "cat_perfil_opciones.php"+id);
}


function openEstado(valor,par_estado,par_dispatch)
{
	var id="?id="+valor;
	var par_estado="&par_estado="+par_estado;
	var dispatch="&dispatch="+par_dispatch;	
	$("#divEstados").html("");	 
	loadHtmlAjax(true, $("#divEstados"), "combo_estados.php"+id+par_estado+dispatch);
}


function openUbicacion(par_dispatch,par_ubicacion)
{	
	//alert ("Entre");
	var dispatch		="dispatch="+par_dispatch;		
	var par_ubicacion	="&par_ubicacion="+par_ubicacion;
	
	loadHtmlAjax(true, $("#divUbicaciones"), "campo_ubicacion.php?"+dispatch+par_ubicacion);
}

function openNewPass()
{
	//var id="?id="+valor;
	//alert ("Entre 1" );
	loadHtmlAjax(true, $("#newpass"), "cambio_password.php");
}

function btnEditLicencia(valor0, valor1)
{		
	var id0="id="+valor0;
	var id1="&id_lic="+valor1;
	var dispatch="&dispatch=save";
	$("#divAltaLic").hide();	
	if (valor1 == 0) $("#divAltaLic").hide(); 
	else loadHtmlAjax(true, $("#divAltaLic"), "cat_licencias_m.php?"+id0+id1+dispatch); 
}

function btnDeleteLicencia(valor0, valor1)
{
	var id0="id="+valor0;
	var id1="&id_lic="+valor1;
	var dispatch="&dispatchLic=delete";
	var url = "process_licencias.php?"+id0+id1+dispatch;        							
	//alert (url);		
				
	var func = function(data){					   			
		var fAceptar = function(){
			$('#dialogMain').dialog("close");
		}
		if(data.error == true){						
			if(data.message != null){							
				jAlert(true,true,data.message,fAceptar);
			}else{
				logout();
			}
		} else {						
		 	if(data.message != null){	
				//alert ("Entre");
				jAlert(true,false,data.message,fAceptar);						
				//$("#divContent").hide();
				loadHtmlAjax(true, $('#divLicencias'), data.html);
			}
		}	
	}	
		
	if (confirm('Deseas BORRAR la Licencia selecccionada... ?'))
	{	
		//alert (url);	
		executeAjax("post", false ,url, "json", func);	     
	}	
}

function btnEditComputo(valor0, valor1)
{		
	var id0="id="+valor0;
	var id1="&id_com="+valor1;
	var dispatch="&dispatchCom=save";
	$("#divAltaCom").hide();	
	//alert("Entre"+id0+id1+dispatch);
	if (valor1 == 0) $("#divAltaCom").hide(); 	
	else loadHtmlAjax(true, $("#divAltaCom"), "cat_empleado_computo_m.php?"+id0+id1+dispatch); 
}

function btnDeleteComputo(valor0, valor1)
{
	var id0="id="+valor0;
	var id1="&id_com="+valor1;
	var dispatch="&dispatchCom=delete";
	var url = "process_empleado_computo.php?"+id0+id1+dispatch;        								
				
	var func = function(data){					   			
		var fAceptar = function(){
			$('#dialogMain').dialog("close");
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
				//$("#divContent").hide();
				loadHtmlAjax(true, $('#divComputo'), data.html);
			}
		}	
	}	
		
	if (confirm('Deseas BORRAR el REGISTRO selecccionado... ?'))
	{	
		executeAjax("post", false ,url, "json", func);	     
	}	
}

function btnEditTelefonia(valor0, valor1)
{		
	var id0="id="+valor0;
	var id1="&id_tel="+valor1;
	var dispatch="&dispatchTel=save";
	$("#divAltaTel").hide();	
	//alert("Entre"+id0+id1+dispatch);
	if (valor1 == 0) $("#divAltaTel").hide(); 	
	else loadHtmlAjax(true, $("#divAltaTel"), "cat_empleado_telefonia_m.php?"+id0+id1+dispatch); 
}

function btnDeleteTelefonia(valor0, valor1)
{
	var id0="id="+valor0;
	var id1="&id_tel="+valor1;
	var dispatch="&dispatchTel=delete";
	var url = "process_empleado_telefonia.php?"+id0+id1+dispatch;        								
				
	var func = function(data){					   			
		var fAceptar = function(){
			$('#dialogMain').dialog("close");
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
				loadHtmlAjax(true, $('#divTelefonia'), data.html);
			}
		}	
	}	
		
	if (confirm('Deseas BORRAR el REGISTRO selecccionado... ?'))
	{	
		//alert("Entre"+url);
		executeAjax("post", false ,url, "json", func);	     
	}	
}

function btnEditFactura(valor0)
{		
	var id0="id="+valor0;
	var dispatch="&dispatch=save";
	$("#divAltaFac").hide();	
	if (valor0 == 0) $("#divAltaFac").hide(); 
	else loadHtmlAjax(true, $("#divAltaFac"), "cat_facturas_cabecera.php?"+id0+dispatch); 
}

function btnDeleteFactura(valor)
{
	var id="id_factura="+valor;
	var anio="&sel_anio_cap="+$("#sel_anio_inicio").val();	
	var dispatch="&dispatch=delete";
	var url = "process_facturas.php?"+id+anio+dispatch;        							
	//alert (url);		
				
	var func = function(data){					   			
		var fAceptar = function(){
			$('#dialogMain').dialog("close");
		}
		if(data.error == true){						
			if(data.message != null){							
				jAlert(true,true,data.message,fAceptar);
			}else{
				logout();
			}
		} else {						
		 	if(data.message != null){	
				//alert ("Entre");
				jAlert(true,false,data.message,fAceptar);						
				$("#divAltaFac").hide(); 
				loadHtmlAjax(true, $('#divDatos'), data.html);
			}
		}	
	}	
		
	if (confirm('Deseas BORRAR la Factura selecccionada... ?'))
	{	
		executeAjax("post", false ,url, "json", func);	     
	}	
}

function completeUrl(){	
	var totchecks = 0;
    var checks = "";
	var dispatch = "";
	
	$('input[name=checks]').each(function(i){
	    if(this.checked){
        	checks = checks + "&check"+totchecks+"="+this.value;
            totchecks++;
        }
    });
	
	dispatch = checks;
	dispatch += dispatch + "&totchecks="+totchecks;		
	alert ("Resul ", dispatch);
}

function validarCampoEnteros(campo,min,max){    
	var er_num = /^([0-9])+$/			
    var flag = false;

    //comprueba campo 
    if(!er_num.test(campo.value)) { 
    	var limpia = "";
        for(var i = 0;i < campo.value.length; i++){
        	if(er_num.test(campo.value.charAt(i))) {
            	limpia = limpia+campo.value.charAt(i);
            }    
        }
        campo.value = limpia;
        return false;
    }else{
    	if(campo.value < min || campo.value > max){
        	alert("El rango debe estar entre "+min+" y "+max);
        	campo.value = "";
        }
    }
    return true;
}

function cambiaStatus(){
    var field = $("#tx_indicador");
    if(field.val() == "1"){
        field.val("0");
        setStatus();
    }else{
        field.val("1");
        setStatus();
    }
}

function setStatus(){
    var field = $("#tx_indicador");
    var divimg = $("#imgstatus");			
    if(field.val() == "1"){
        divimg.html("&nbsp;<img style='cursor:pointer' onclick='cambiaStatus()' border='none' src='images/greenball.png' title='Se encuentra activo click para desactivarlo'>&nbsp;<span style='font-family:verdana; font-size:12px;color: #003366;'>Activo</span>");
    }else{
        divimg.html("&nbsp;<img style='cursor:pointer' onclick='cambiaStatus()' border='none' src='images/redball.png' title='Se encuentra inactivo click para activarlo'>&nbsp;<span style='font-family:verdana; font-size:12px;color: #003366;'>Inactivo</span>");
    }
}

//function Ventana(theURL,w,h) 
//{ 
//	alert ("Entre");
//	var windowprops ="top=40,left=60,toolbar=no,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=" + w + ",height=" + h;
//	winName='_blank';  
//	window.open(theURL,winName,windowprops); 
	
//	window.open( url,"_blank");	
//} 

function Ventana(p1) 
{ 			
	var url = "ventana_empleado.php?id="+p1;
	var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1100, height=700";
	var winName='_blank';  
						
	window.open(url,winName,windowprops); 
} 	
