/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function verifySaveCatalog(controller, form, dispatch, catalogo, container){
    dispatch += catalogo;
    var id = $('#id');
    if(id.val() == undefined){
        return;
    }else{
        dispatch = dispatch+"&";  
        dispatch += form.serialize();
        if(validEmptyFields(catalogo, form)){
            alert(dispatch);
            loadPageJSON(controller, false,dispatch,container, false)
            $("#add").val("false");
            $("#labelAdd").html("Actualizando elemento");
        }
    }
}

function validEmptyFields(catalogo, form){
    if(catalogo == 'alertas' || catalogo == 'areas' || catalogo == 'estadosriesgo' || catalogo == 'estadosop' 
        || catalogo == 'grupos' || catalogo == 'monedas' || catalogo == 'razones'){
        validUniqueField($('#txtname'), $('#errtxtname'), 'catalogProcessSophie.do', form, 'dispatch=validUniqueField&')
    }
    if(catalogo == 'grupos' || catalogo == 'monedas'){
        validUniqueField($('#txtcode'), $('#errtxtcode'), 'catalogProcessSophie.do', form,'dispatch=validUniqueField&catalogo='+catalogo+'&')
    }
    if(catalogo == 'zonas'){
        validZone($('#txtname'), $('#errtxtname'), 'catalogProcessSophie.do', form,'dispatch=validUniqueField&catalogo='+catalogo+'&')
    }
    if($("#error").val() == "true"){
        return false;
    }
    return true;
}

function deleteCatalog(controller, dispatch, container){
    var totchecks = 0;
    var checks = "";
    if($("#borrar").val()=="false"){
        $("input:checkbox").show();
        $("#borrar").val("true");
    }else{
        $('input[name=checks]').each(function(i){
            if(this.checked){
                checks = checks + "&check="+this.value;
                totchecks++;
            }
        });
        if(totchecks != 0){
            dispatch += checks;
            if( window.confirm("¿Desea borrar los elementos?") ){
                $("#divDatos").html("");
                loadPageJSON(controller, false,dispatch,container, true);
            }            
        }
        $("input:checkbox").hide();
        $("#borrar").val("false");
    }
    return true;
}

function validZone(field, fielderror, controller, form, values){
    var fielderror2 = $("#erridarea");
    var fieldarea = $("#idarea");
    if( parseInt($("#idarea").val()) == 0){
        fieldarea.addClass("error");
        fielderror2.text("Es necesario seleccionar un \u00e1rea");
        fielderror2.addClass("errors");
        $("#error").val("true");
        return false;
    }else {
        fieldarea.removeClass("error");
        fielderror2.text("");
        fielderror2.removeClass("errors");
        $("#error").val("false");
        validUniqueField(field, fielderror, controller, form, values);
    }
    return true;
}

