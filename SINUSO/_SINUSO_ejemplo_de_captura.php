<%--
    Document   : detailoper
    Created on : 14/05/2009, 5:16:21 PM
    Author     : Ing. Raúl García Balmori
    Email      : raul.garciab@bbva.bancomer.com
    Teléfono   : 55 5621-9723
 */
--%>

<%@ page contentType="text/html" pageEncoding="ISO-8859-1" session="false"%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="fmt" uri="http://java.sun.com/jsp/jstl/fmt" %>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<fmt:setLocale value="es-MX" scope="request"/>

<%
            response.setHeader("Cache-Control", "no-store, no-cache");
            response.setHeader("Pragma", "no-cache");
            response.setDateHeader("Expires", 0);
%>

<jsp:useBean id="beanOp" scope="request" class="mx.com.bancomer.bbva.orion.ibatis.beans.sophie.views.ViewOpp"/>
<br>
<script type="text/javascript">
    function hideFiles(){
        $('#divFiles').toggle('slow');
    }

    function formatCounterpart(row) {
        return "<strong>"+row[1]+"</strong> ("+ row[0] +")";
    }

    function formatItem(row) {
        return "<strong>"+row[1]+"</strong> ("+ row[0] +")";
    }

    function fieldsReq(){
        var error = false;
        validSelect($("#selpromotor"), $("#errpromotor"));
        validSelect($("#selmoneda"), $("#errmoneda"));
        validSelect($("#selseguimiento"), $("#errseguimiento"));
        validSelect($("#selarea"), $("#errarea"));
        validSelect($("#selzona"), $("#errzona"));
        validText(false, $("#fcierre"), $("#errfcierre"), 1);
        validText(false, $("#descrip"), $("#errdescrip"), 1);
        validNumeric($("#plazo"), $("#errplazo"));
        validNumeric($("#monto"), $("#errmonto"));
        validNumeric($("#franq"), $("#errfranq"));
        if(
            !validSelect($("#selpromotor"), $("#errpromotor")) ||
            !validSelect($("#selmoneda"), $("#errmoneda")) ||
            !validSelect($("#selseguimiento"), $("#errseguimiento")) ||
            !validSelect($("#selarea"), $("#errarea")) ||
            !validSelect($("#selzona"), $("#errzona"))  ||
            !validText(false, $("#fcierre"), $("#errfcierre"), 1) ||
            !validText(false, $("#descrip"), $("#errdescrip"), 1) ||
            !validNumeric($("#plazo"), $("#errplazo")) ||
            !validNumeric($("#monto"), $("#errmonto")) ||
            !validNumeric($("#franq"), $("#errfranq"))) {
            error = true;
        }
        switch($("#selestado").val()){
            case "2":   //PIPELINE
                validText(false, $("#contraparte"), $("#errcontraparte"), 1);
                if(error ||
                    !validText(false, $("#contraparte"), $("#errcontraparte"), 1)){
                    error = true;
                }

                $("#errproducto").removeClass("ui-state-error ui-state-error-text");
                $("#errproducto").html("");

                $("#erroperacion").removeClass("ui-state-error ui-state-error-text");
                $("#erroperacion").html("");
                $("#errfcierreop").removeClass("ui-state-error ui-state-error-text");
                $("#errfcierreop").html("");

                $("#errrazon").removeClass("ui-state-error ui-state-error-text");
                $("#errrazon").html("");
                $("#errfperdida").removeClass("ui-state-error ui-state-error-text");
                $("#errfperdida").html("");
                $("#errdescperdida").removeClass("ui-state-error ui-state-error-text");
                $("#errdescperdida").html("");

                $("#divCierre").hide();
                $("#divPerdida").hide();
                break;
            case "3":   //CERRADA
                validText(false, $("#contraparte"), $("#errcontraparte"), 1);
                validText(false, $("#idproducto"), $("#errproducto"), 1);
                validText(false, $("#operacion"), $("#erroperacion"), 1);
                validText(false, $("#fcierreop"), $("#errfcierreop"), 1);
                if(error ||
                    !validText(false, $("#contraparte"), $("#errcontraparte"), 1) ||
                    !validText(false, $("#idproducto"), $("#errproducto"), 1) ||
                    !validText(false, $("#operacion"), $("#erroperacion"), 1) ||
                    !validText(false, $("#fcierreop"), $("#errfcierreop"), 1)){
                    error = true;
                }

                $("#errrazon").removeClass("ui-state-error ui-state-error-text");
                $("#errrazon").html("");
                $("#errfperdida").removeClass("ui-state-error ui-state-error-text");
                $("#errfperdida").html("");
                $("#errdescperdida").removeClass("ui-state-error ui-state-error-text");
                $("#errdescperdida").html("");

                $("#divCierre").show().fadeIn('slow');
                $("#divPerdida").hide();
                break;
            case "4":   //PERDIDA
                validSelect($("#selrazon"), $("#errrazon"));
                validText(false, $("#descperdida"), $("#errdescperdida"), 1);
                validText(false, $("#fperdida"), $("#errfperdida"), 1);
                if(error ||
                    !validSelect($("#selrazon"), $("#errrazon")) ||
                    !validText(false, $("#descperdida"), $("#errdescperdida"), 1) ||
                    !validText(false, $("#fperdida"), $("#errfperdida"), 1)){
                    error = true;
                }
                $("#errproducto").removeClass("ui-state-error ui-state-error-text");
                $("#errproducto").html("");

                $("#erroperacion").removeClass("ui-state-error ui-state-error-text");
                $("#erroperacion").html("");
                $("#errfcierreop").removeClass("ui-state-error ui-state-error-text");
                $("#errfcierreop").html("");

                $("#divCierre").hide();
                $("#divPerdida").show().fadeIn('slow');
                break;
        }
        return !error;
    }

    $(document).ready(function() {
        function fieldsReq(){
            var error = false;
            validSelect($("#selpromotor"), $("#errpromotor"));
            validSelect($("#selmoneda"), $("#errmoneda"));
            validSelect($("#selseguimiento"), $("#errseguimiento"));
            validSelect($("#selarea"), $("#errarea"));
            validSelect($("#selzona"), $("#errzona"));
            validText(false, $("#fcierre"), $("#errfcierre"), 1);
            validText(false, $("#descrip"), $("#errdescrip"), 1);
            validNumeric($("#plazo"), $("#errplazo"));
            validNumeric($("#monto"), $("#errmonto"));
            validNumeric($("#franq"), $("#errfranq"));
            if(
                !validSelect($("#selpromotor"), $("#errpromotor")) ||
                !validSelect($("#selmoneda"), $("#errmoneda")) ||
                !validSelect($("#selseguimiento"), $("#errseguimiento")) ||
                !validSelect($("#selarea"), $("#errarea")) ||
                !validSelect($("#selzona"), $("#errzona"))  ||
                !validText(false, $("#fcierre"), $("#errfcierre"), 1) ||
                !validText(false, $("#descrip"), $("#errdescrip"), 1) ||
                !validNumeric($("#plazo"), $("#errplazo")) ||
                !validNumeric($("#monto"), $("#errmonto")) ||
                !validNumeric($("#franq"), $("#errfranq"))) {
                error = true;
            }
            switch($("#selestado").val()){
                case "2":   //PIPELINE
                    validText(false, $("#contraparte"), $("#errcontraparte"), 1);
                    if(error ||
                        !validText(false, $("#contraparte"), $("#errcontraparte"), 1)){
                        error = true;
                    }

                    $("#errproducto").removeClass("ui-state-error ui-state-error-text");
                    $("#errproducto").html("");

                    $("#erroperacion").removeClass("ui-state-error ui-state-error-text");
                    $("#erroperacion").html("");
                    $("#errfcierreop").removeClass("ui-state-error ui-state-error-text");
                    $("#errfcierreop").html("");

                    $("#errrazon").removeClass("ui-state-error ui-state-error-text");
                    $("#errrazon").html("");
                    $("#errfperdida").removeClass("ui-state-error ui-state-error-text");
                    $("#errfperdida").html("");
                    $("#errdescperdida").removeClass("ui-state-error ui-state-error-text");
                    $("#errdescperdida").html("");

                    $("#divCierre").hide();
                    $("#divPerdida").hide();
                    break;
                case "3":   //CERRADA
                    validText(false, $("#contraparte"), $("#errcontraparte"), 1);
                    validText(false, $("#idproducto"), $("#errproducto"), 1);
                    validText(false, $("#operacion"), $("#erroperacion"), 1);
                    validText(false, $("#fcierreop"), $("#errfcierreop"), 1);
                    if(error ||
                        !validText(false, $("#contraparte"), $("#errcontraparte"), 1) ||
                        !validText(false, $("#idproducto"), $("#errproducto"), 1) ||
                        !validText(false, $("#operacion"), $("#erroperacion"), 1) ||
                        !validText(false, $("#fcierreop"), $("#errfcierreop"), 1)){
                        error = true;
                    }

                    $("#errrazon").removeClass("ui-state-error ui-state-error-text");
                    $("#errrazon").html("");
                    $("#errfperdida").removeClass("ui-state-error ui-state-error-text");
                    $("#errfperdida").html("");
                    $("#errdescperdida").removeClass("ui-state-error ui-state-error-text");
                    $("#errdescperdida").html("");

                    $("#divCierre").show().fadeIn('slow');
                    $("#divPerdida").hide();
                    break;
                case "4":   //PERDIDA
                    validSelect($("#selrazon"), $("#errrazon"));
                    validText(false, $("#descperdida"), $("#errdescperdida"), 1);
                    validText(false, $("#fperdida"), $("#errfperdida"), 1);
                    if(error ||
                        !validSelect($("#selrazon"), $("#errrazon")) ||
                        !validText(false, $("#descperdida"), $("#errdescperdida"), 1) ||
                        !validText(false, $("#fperdida"), $("#errfperdida"), 1)){
                        error = true;
                    }
                    $("#errproducto").removeClass("ui-state-error ui-state-error-text");
                    $("#errproducto").html("");

                    $("#erroperacion").removeClass("ui-state-error ui-state-error-text");
                    $("#erroperacion").html("");
                    $("#errfcierreop").removeClass("ui-state-error ui-state-error-text");
                    $("#errfcierreop").html("");

                    $("#divCierre").hide();
                    $("#divPerdida").show().fadeIn('slow');
                    break;
            }
            return !error;
        }
        //////////////////// PLUGINS /////////////////////////////
        $(function(){
            $('input:text').setMask();
        });
        
        $("#slider").slider({
            value: ${beanOp.probability},
            min: 0,
            max: 95,
            step: 5,
            slide: function(event, ui) {
                $("#probabilidad").val(ui.value);
            }
        });
        
        $('#fcierre').datepicker({
            dateFormat: 'mm/yy',
            numberOfMonths: 3,
            showButtonPanel: true,
            minDate: 0
        });
        $('#fcierreop').datepicker({
            dateFormat: 'dd/mm/yy',
            numberOfMonths: 1,
            showButtonPanel: true,
            minDate: -6
        });
        $('#fperdida').datepicker({
            dateFormat: 'dd/mm/yy',
            numberOfMonths: 1,
            showButtonPanel: true,
            minDate: -6
        });
        
        $("#fcierre").datepicker($.datepicker.regional['es']);
        $("#fcierreop").datepicker($.datepicker.regional['es']);
        $("#fperdida").datepicker($.datepicker.regional['es']);

        $("#producto").autocomplete("catalogSophie.do?dispatch=findProduct",{
            minChars: 3,
            max: 5,
            width:300,
            autoFill: true,
            selectFirst: false,
            scrollHeight: 220,
            cacheLength: 1,
            formatItem: formatItem
        }).result(function(e, item) {
            $("#idproducto").val("");
            $("#idgpoprod").val("");
            $("#gpoprod").val("");
            $("#idproducto").val(item[1]);
            $("#idgpoprod").val(item[2]);
            $("#gpoprod").val(item[3]);
        });

        $("#contraparte").autocomplete("catalogSophie.do?dispatch=findCounterpart",{
            minChars: 5,
            max: 5,
            width:300,
            autoFill: true,
            selectFirst: false,
            scrollHeight: 220,
            cacheLength: 1,
            formatItem: formatCounterpart
        }).result(function(e, item) {
            $("#idcontraparte").val("");
            $("#idgrupo").val("");
            $("#grupo").val("");
            $("#idcontraparte").val(item[1]);
            $("#idgrupo").val(item[2]);
            $("#grupo").val(item[3]);
        });

        var urlfiles1 = 'fileProcess.do?dispatch=upload=sophie='+${beanOp.idopportunity}+'='+$("#userid").val();
        $('#uploadify').uploadify({
            'uploader': 'js/upload/uploadify.swf',
            'script': urlfiles1,
            'folder': 'files',
            'cancelImg': 'images/cancel.png',
            'queueID': 'fileQueue',
            'buttonText': 'Adjuntar',
            'method': 'POST',
            'scriptData': {'ejemplo':'esta es la prueba'},
            'auto': true,
            'multi': true,
            onProgress: function(event, queueID, fileObj, data){
                $("#divLoading").show();
            },
            onCancel: function(event, queueID, fileObj, data){
                $("#divLoading").hide();
            },
            onAllComplete: function(event, queueID, fileObj, response, data){
                $("#divLoading").hide();
                jQuery("#filelist").trigger("reloadGrid");
            },
            onError: function(event, queueID, fileObj, errorObj){
                $("#divLoading").hide();
                var fAceptar = function(){
                    $('#dialogMain').dialog("close");
                }
                jAlert(true, true, errorObj.type+" Error:"+errorObj.info, fAceptar);
            }
        });

        ///////////////// DEFINICION DE EVENTOS //////////////////////
        $("#selpromotor").change(function () {
            $("#selpromotor option:selected").each(function () {
                $("#selzona").html("<option value='0'>--- S e l e c c i o n e ---</option>");
                loadHtmlAjax(false, $("#selarea") , "catalogSophie.do?dispatch=catArea&idpromotor="+$(this).val());
            });
        });

        $("#selarea").change(function () {
            if(validSelect($(this), $("#errarea"))){
                $("#selarea option:selected").each(function () {
                    loadHtmlAjax(false, $("#selzona") , "catalogSophie.do?dispatch=catZona&idarea="+$(this).val());
                });
            }
        });

        $("#selzona").change(function () {
            validSelect($(this), $("#errzona"));
        });

        $("#selmoneda").change(function () {
            validSelect($(this), $("#errmoneda"));
        });
        
        $("#selrazon").change(function () {
            validSelect($(this), $("#errrazon"));
        });
        
        $("#selseguimiento").change(function () {
            validSelect($(this), $("#errseguimiento"));
        });

        $("#fcierre").change(function () {
            fieldsReq();
        });

        $("#fcierreop").change(function () {
            fieldsReq();
        });

        $("#fperdida").change(function () {
            fieldsReq();
        });

        $("input:text").focus(function() {
            $(this).addClass('ui-state-focus');
        });

        $("input:text").blur(function() {
            $(this).removeClass('ui-state-focus');
        });

        $("#descrip").blur(function () {
            validText(false, $(this), $("#errdescrip"), 1);
        });

        $("#plazo").blur(function () {
            //validNumeric($(this), $("#errplazo"));
        });

        $("#monto").blur(function () {
            validNumeric($(this), $("#errmonto"));
        });

        $("#franq").blur(function () {
            validNumeric($(this), $("#errfranq"));
        });

        $("#contraparte").blur(function () {
            validText(false, $(this), $("#errcontraparte"), 1);
        });

        $("#operacion").blur(function () {
            validText(false, $(this), $("#erroperacion"), 1);
        });

        $("#fcierreop").blur(function () {
            validText(false, $(this), $("#errfcierreop"), 1);
        });

        $("#fperdida").blur(function () {
            validText(false, $(this), $("#errfperdida"), 1);
        });

        $("#descperdida").blur(function () {
            validText(false, $(this), $("#errdescperdida"), 1);
        });

        /////////////////// INICIALIZACION DE DATOS  /////////////////
        $("#probabilidad").val($("#slider").slider("value"));

        ////////////////// FUNCIONES DE BOTONES //////////////////////
        $("#btnSave1").click(function(){
            if(editing1){
                var url = "operationSophie.do?dispatch=save&";
                url += $("#opForm2").serialize();
                url += ("&id="+$("#id").val());
                url += ("&gpoprod="+$("#gpoprod").val());
                url += ("&checkcompartida="+$("#compartida:checkbox:checked").val());
                url += ("&idarea2="+$("#selcompartida").val());
                url += ("&probabilidad="+$("#probabilidad").val());
                url += ("&grupo="+$("#grupo").val());
                if(fieldsReq()){
                    executeAjaxJSON(url, false, null);
                    editing1 = false;
                    adding1 = false;
                    edicionPipeline(editing1);
                    jQuery("#filelist").trigger("reloadGrid");
                    jQuery("#list1").trigger("reloadGrid");
                    $("#detail1").html("");
                    jQuery("#list1").restoreRow(lastsel1).setGridState("visible");
                }else{
                    var fAceptar = function(){
                        $('#dialogMain').dialog("close");
                    }
                    jAlert(true,true,"Existen campos obligatorios vac&iacute;os",fAceptar);
                }
            }
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        });

        $("#btnUndo1").click(function(){
            if(editing1){
                editing1 = false;
                adding1 = false;
                edicionPipeline(editing1);
            }
            $("#detail1").html("");
            jQuery("#list1").restoreRow(lastsel1).setGridState("visible");
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        });

        ////////////////////// GRID ARCHIVOS ///////////////////////////////
        jQuery("#filelist").jqGrid({
            caption:"ARCHIVOS ADJUNTOS",
            mtype: "POST",
            url:'operationSophie.do?dispatch=viewfiles&idop=${beanOp.idopportunity}',
            datatype: "xml",
            colNames:['ID Archivo','Fecha adjuntado','Nombre archivo','Usuario que adjuntó archivo'],
            colModel:[
                {name:'id',index:'idfile',width:100, align:"left",editable:false, editoptions:{readonly:true,size:10},searchoptions:{sopt:['eq','ne','lt','le','gt','ge','in','ni']} },
                {name:'fecha',index:'date_added',width:150, align:"center",editable:false, editoptions:{readonly:true,size:50},searchoptions:{dataInit:function(el){$(el).datepicker({dateFormat:'dd/mm/yy'});}} },
                {name:'archivo',index:'name',width:200, align:"left",editable:false, editoptions:{readonly:true,size:100},searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']} },
                {name:'usuario',index:'user_added',width:200, align:"left",editable:false, editoptions:{readonly:true,size:100},searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']} }
            ],
            pager: '#pagerfiles', // Nombre del paginador
            altRows: true, // Activa la visualizacion de zebra en las filas
            imgpath: "/css/ui-personal/images",
            rowNum:10,  // numero de filas por pagina
            rowList:[10,20,30],  // opciones de filas por pagina
            width: $("#gview_list1").width(),    // Ancho automatico para columnas
            height:100,
            shrinkToFit :false,
            sortable: true,
            gridview: true,     // Mejora rendimiento para mostrar datos. Algunas funciones no estan disponibles
            rownumbers: true,   // Muestra los numeros de linea en el grid
            viewrecords: true,  //
            viewsortcols: [true,'vertical',true], // Muestra las columnas que pueden ser ordenadas dinamicamente
            sortname: 'idfile', // primer columna de ordenacion
            sortorder: "desc",       // tipo de ordenacion inicial
            loadError : function(xhr,st,err) {
                var fAceptar = function(){
                    $('#dialogMain').dialog("close");
                }
                jAlert(true,true,"Error cargando grid Type: "+st+"; Response: "+ xhr.status + " "+xhr.statusText,fAceptar);
            }
        }).navGrid('#pagerfiles',
        {add:false,edit:false,view:false,del:false,search:false, refresh:false}, //options
        {}, // edit options
        {}, // add options
        {}, // del options
        {}, // search options
        {} // view options
    ).navButtonAdd('#pagerfiles',{
            id:"btnSearchFile",
            caption:"Buscar",
            title:"Buscar archivo",
            buttonicon :'ui-icon-search',
            position:"first",
            onClickButton:function(){
                jQuery("#filelist").searchGrid({closeOnEscape:true,multipleSearch:true});
            }
        }).navButtonAdd('#pagerfiles',{
            id:"btnDownload",
            caption:"Descargar",
            title:"Descargar archivo seleccionado",
            buttonicon :'ui-icon-arrowreturnthick-1-s',
            position:"first",
            onClickButton:function(){
                alert("Descargando");
            }
        }).navButtonAdd('#pagerfiles',{
            id:"btnRefreshFiles",
            caption:"Actualizar",
            title:"Actualiza los datos mostrados en la tabla",
            buttonicon :'ui-icon-refresh',
            position:"last",
            onClickButton:function(){
                jQuery("#filelist").trigger("reloadGrid");
            }
        });
        
        $("#btnSearchFile").addClass("border-button");
        $("#btnDownload").addClass("border-button");
        $('#divFiles').toggle('slow');
        fieldsReq();
        enableArea();
    });
</script>

<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;">
    <div class="ui-widget-header align-center ">DETALLE DE OPORTUNIDAD DE NEGOCIO</div>
    <div class="ui-jqdialog-content ui-widget-content">
        <form id="opForm2" action="">
            <table cellspacing="2px" border="0" cellpadding="0">
                <tbody>
                    <tr>
                        <td colspan="8" class="ui-state-default fontMedium">Datos Generales</td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="id">Id:</label></td>
                        <td width="20%">
                            <input id="id" name="id" type="text" value="<c:if test="${beanOp.idopportunity != null}"><jsp:getProperty name="beanOp" property="idopportunity"/></c:if>"
                                   size="10" maxlength="10" style="text-align:right;font-weight:bold;"
                                   title="Identificador de la oportunidad" class="ui-state-default" disabled="disabled"/>                        </td>
                        <td width="10%"><div id="errid" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="estado">Estado:</label></td>
                        <td width="20%">
                            <select id="selestado" name="selestado" onchange="fieldsReq();">
                                <option value="2" selected>PIPELINE</option>
                                <option value="3" >CERRADA</option>
                                <option value="4" >PERDIDA</option>
                            </select>                        </td>
                        <td width="10%"><div id="errestado" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="fcierre">Fecha estimada cierre:</label></td>
                        <td width="20%">
                            <input id="fcierre" name="fcierre" type="text" value="<fmt:formatDate value="${beanOp.date_estimated_close}" pattern="MM/yyyy" />" size="10" maxlength="10"
                                   title="Fecha estimada de cierre de operaci&oacute;n" readonly/>                        </td>
                        <td width="10%"><div id="errfcierre" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="promotor">Promotor:</label></td>
                        <td width="20%">
                            <select id="selpromotor" name="selpromotor">
                                <c:forEach var="beanPromotor" items="${listapromotores}">
                                    <jsp:useBean id="beanPromotor" scope="page" class="mx.com.bancomer.bbva.orion.ibatis.beans.sophie.views.ViewPromoters"/>
                                    <option title="<jsp:getProperty name="beanPromotor" property="username"/>"
                                            value="<jsp:getProperty name="beanPromotor" property="idpromoter"/>"
                                            <%if (beanOp.getIdpromoter() != null) {
                if (beanOp.getIdpromoter().equals(beanPromotor.getIdpromoter())) {%>selected<%}
            }%> >
                                        <jsp:getProperty name="beanPromotor" property="username"/>                                    </option>
                                </c:forEach>
                            </select>                        </td>
                        <td width="10%"><div id="errpromotor" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="producto">Producto:</label></td>
                        <td width="20%">
                            <input id="idproducto" name="idproducto" type="hidden" value="<jsp:getProperty name="beanOp" property="product"/>" maxlength="30"/>
                            <input id="producto" name="producto" type="text" value="<jsp:getProperty name="beanOp" property="product_desc"/>" size="30" maxlength="30"
                                   title="Producto asociado a la oportunidad"/>                        </td>
                        <td width="10%"><div id="errproducto" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="probabilidad">Probabilidad:</label></td>
                        <td width="30%">
                            <table style="width:100%">
                                <tr>
                                    <td width="70%"><div id="slider"></div></td>
                                    <td width="5%">
                                        <input id="probabilidad" name="probabilidad" type="text" size="1" style="text-align:center;" maxlength="3"
                                               value="<jsp:getProperty name="beanOp" property="probability"/>"
                                               title="Porcentaje de probabilidad de cierre" class="ui-state-default" disabled="disabled"/>                                    </td>
                                    <td>%</td>
                                </tr>
                            </table>                        </td>
                        <td width="10%"><div id="errprobabilidad" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="gpoprod">Grupo productos:</label></td>
                        <td width="20%">
                            <input id="idgpoprod" name="idgpoprod" type="hidden" value="<jsp:getProperty name="beanOp" property="product_group"/>" maxlength="30"/>
                            <input id="gpoprod" name="gpoprod" type="text" value="<jsp:getProperty name="beanOp" property="prod_grp_desc"/>" size="30" maxlength="30"
                                   title="Grupo de productos" class="ui-state-default" disabled="disabled"/>                        </td>
                        <td width="10%"><div id="errgpoprod" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="seguimiento">Seguimiento:</label></td>
                        <td width="20%">
                            <select id="selseguimiento" name="selseguimiento">
                                <option value="0">--- S e l e c c i o n e ---</option>
                                <c:forEach var="beanTrack" items="${listaseguimientos}">
                                    <jsp:useBean id="beanTrack" scope="page" class="mx.com.bancomer.bbva.orion.ibatis.beans.sophie.Track"/>
                                    <option title="<jsp:getProperty name="beanTrack" property="name"/>"
                                            value="<jsp:getProperty name="beanTrack" property="idtrack"/>"
                                            <%if (beanOp.getIdtrack() != null) {
                if (beanOp.getIdtrack().equals(beanTrack.getIdtrack())) {%>selected<%}
            }%> >
                                        <jsp:getProperty name="beanTrack" property="name"/>                                    </option>
                                </c:forEach>
                            </select>                        </td>
                        <td width="10%"><div id="errseguimiento" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="descrip">Descripci&oacute;n operaci&oacute;n:</label></td>
                        <td width="20%">
                            <textarea id="descrip" name="descrip" cols="45" rows="5" title="Descripci&oacute;n de la operaci&oacute;n"><jsp:getProperty name="beanOp" property="description"/></textarea>                        </td>
                        <td width="10%"><div id="errdescrip" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="comentario">Comentario seguimiento:</label></td>
                        <td width="20%">
                            <textarea id="comentario" name="comentario" cols="45" rows="5" title="Comentario del seguimiento del clientes"><jsp:getProperty name="beanOp" property="comment_monitoring"/></textarea>                        </td>
                        <td width="10%"><div id="errcomentario" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="plazo">Plazo:</label></td>
                        <td width="20%">
                            <table>
                                <tr>
                                    <td>
                                        <input id="plazo" name="plazo" type="text" value="<jsp:getProperty name="beanOp" property="term"/>" size="4" maxlength="10"
                                               title="Plazo"/>                                    </td>
                                    <td  class="ui-state-default">
                                        <input id="tipoplazo" name="tipoplazo" type="radio" value="d">D&iacute;as
                                        <input id="tipoplazo" name="tipoplazo" type="radio" value="m">Meses
                                        <input id="tipoplazo" name="tipoplazo" type="radio" value="y" checked>A&ntilde;os                                    </td>
                                </tr>
                            </table>                        </td>
                        <td width="10%"><div id="errplazo" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="contraparte">Contraparte:</label></td>
                        <td width="20%">
                            <input id="idcontraparte" name="idcontraparte" type="hidden" value="<jsp:getProperty name="beanOp" property="ctrp_code"/>" />
                            <input id="contraparte" name="contraparte" type="text" value="<jsp:getProperty name="beanOp" property="ctrp_desc"/>" size="50" maxlength="100"
                                   title="Nombre del contraparte"/>                        </td>
                        <td width="10%"><div id="errcontraparte" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="moneda">Moneda:</label></td>
                        <td width="20%"><select id="selmoneda" name="selmoneda">
                          <option value="0" class="">--- S e l e c c i o n e ---</option>
                          <c:forEach var="beanCurrency" items="${listamonedas}">
                            <jsp:useBean id="beanCurrency" scope="page" class="mx.com.bancomer.bbva.orion.ibatis.beans.sophie.Currency"/>
                            
                            <option title="<jsp:getProperty name="beanCurrency" property="name"/>"
                                            value="<jsp:getProperty name="beanCurrency" property="idcurrency"/>"
                                            <%if (beanOp.getIdcurrency() != null) {
                if (beanOp.getIdcurrency().equals(beanCurrency.getIdcurrency())) {%>selected<%}
            }%> >
                            <jsp:getProperty name="beanCurrency" property="code"/>                        
                              &nbsp;-&nbsp;
                              <jsp:getProperty name="beanCurrency" property="name"/>                                                    </option>
                          </c:forEach>
                        </select></td>
                        <td width="10%"><div id="errmoneda" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="grupo">Grupo Empresarial:</label></td>
                        <td width="20%">
                            <input id="idgrupo" name="idgrupo" type="hidden" value="<jsp:getProperty name="beanOp" property="group_code"/>" />
                            <input id="grupo" name="grupo" type="text" value="<jsp:getProperty name="beanOp" property="group_desc"/>" size="50" maxlength="100"
                                   title="Grupo empresarial al que pertence la contraparte" class="ui-state-default" disabled="disabled"/>                        </td>
                        <td width="10%"><div id="errgrupo" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="monto">Monto:</label></td>
                        <td width="20%">
                            <input id="monto" style="text-align:right" name="monto" type="text" value="<fmt:formatNumber type="currency" value="${beanOp.amount}" currencySymbol=""/>" size="30" maxlength="30"
                                   alt="decimal-us" title="Monto de la operaci&oacute;n"/>                        </td>
                        <td width="10%"><div id="errmonto" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="compartida">Compartida:</label></td>
                        <td width="20%">
                            <input id="compartida" name="compartida" type="checkbox" onclick="enableArea()"
                                   <c:if test="${beanOp.shared == 'S'}" scope="page" var="comp">checked</c:if>
                                   title="Indicador de operaci&oacute;n compartida entre &aacute;rea" />                        </td>
                        <td width="10%"><div id="errcompartida" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="franq">Franquicia:</label></td>
                        <td width="20%">
                            <input id="franq" style="text-align:right" name="franq" type="text" value="<fmt:formatNumber type="currency" value="${beanOp.amount_franq}" currencySymbol=""/>" size="30" maxlength="30"
                                   alt="decimal-us" title="Monto de franquicia"/>                        </td>
                        <td width="10%"><div id="errfranq" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="acompartida">&Aacute;rea compartida:</label></td>
                        <td width="20%"><select id="selcompartida" name="selcompartida" disabled="disabled">
                          <option value="0">--- S e l e c c i o n e ---</option>
                          <c:forEach var="beanCompartida" items="${listacompartidas}">
                            <jsp:useBean id="beanCompartida" scope="page" class="mx.com.bancomer.bbva.orion.ibatis.beans.sophie.Area"/>
                            
                            <option title="<jsp:getProperty name="beanCompartida" property="name"/>"
                                            value="<jsp:getProperty name="beanCompartida" property="idarea"/>"
                                            <%if (beanOp.getIdarea2() != null) {
                if (beanOp.getIdarea2().equals(beanCompartida.getIdarea())) {%>selected<%}
            }%> >
                            <jsp:getProperty name="beanCompartida" property="name"/>                                                    </option>
                          </c:forEach>
                        </select></td>
                        <td width="10%"><div id="erracompartida" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="edoriesgo">Estado riesgo:</label></td>
                        <td width="20%"><select id="selriesgo" name="selriesgo">
                          <option value="0">--- S e l e c c i o n e ---</option>
                          <c:forEach var="beanRiesgo" items="${listaestados}">
                            <jsp:useBean id="beanRiesgo" scope="page" class="mx.com.bancomer.bbva.orion.ibatis.beans.sophie.StatusRisk"/>
                            
                            <option title="<jsp:getProperty name="beanRiesgo" property="name"/>"
                                            value="<jsp:getProperty name="beanRiesgo" property="idstatus_risk"/>"
                                            <%if (beanOp.getIdstatus_risk() != null) {
                if (beanOp.getIdstatus_risk().equals(beanRiesgo.getIdstatus_risk())) {%>selected<%}
            }%> >
                            <jsp:getProperty name="beanRiesgo" property="name"/>                                                    </option>
                          </c:forEach>
                        </select></td>
                      <td width="10%"><div id="errriesgo" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="area">&Aacute;rea:</label></td>
                        <td width="20%">
                            <select id="selarea" name="selarea">
                                <c:forEach var="beanArea" items="${listaareas}">
                                    <jsp:useBean id="beanArea" scope="page" class="mx.com.bancomer.bbva.orion.ibatis.beans.sophie.Area"/>
                                    <option title="<jsp:getProperty name="beanArea" property="name"/>"
                                            value="<jsp:getProperty name="beanArea" property="idarea"/>"
                                            <%if (beanOp.getIdarea() != null) {
                if (beanOp.getIdarea().equals(beanArea.getIdarea())) {%>selected<%}
            }%> >
                                        <jsp:getProperty name="beanArea" property="name"/>                                    </option>
                                </c:forEach>
                            </select>                        </td>
                        <td width="10%"><div id="errarea" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="alerta">Alerta riesgo:</label></td>
                        <td width="20%"><select id="selalerta" name="selalerta">
                          <option value="0">--- S e l e c c i o n e ---</option>
                          <c:forEach var="beanAlert" items="${listaalertas}">
                            <jsp:useBean id="beanAlert" scope="page" class="mx.com.bancomer.bbva.orion.ibatis.beans.sophie.Alert"/>
                            
                            <option title="<jsp:getProperty name="beanAlert" property="name"/>"
                                            value="<jsp:getProperty name="beanAlert" property="idalert"/>"
                                            <%if (beanOp.getIdalert() != null) {
                if (beanOp.getIdalert().equals(beanAlert.getIdalert())) {%>selected<%}
            }%> >
                            <jsp:getProperty name="beanAlert" property="name"/>                                                    </option>
                          </c:forEach>
                        </select></td>
                        <td width="10%"><div id="erralerta" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="zona">Zona:</label></td>
                        <td width="20%">
                            <select id="selzona" name="selzona">
                                <c:forEach var="beanZona" items="${listazonas}">
                                    <jsp:useBean id="beanZona" scope="page" class="mx.com.bancomer.bbva.orion.ibatis.beans.sophie.Zone"/>
                                    <option title="<jsp:getProperty name="beanZona" property="name"/>"
                                            value="<jsp:getProperty name="beanZona" property="idzone"/>"
                                            <%if (beanOp.getIdzone() != null) {
                if (beanOp.getIdzone().equals(beanZona.getIdzone())) {%>selected<%}
            }%> >
                                        <jsp:getProperty name="beanZona" property="name"/>                                    </option>
                                </c:forEach>
                            </select>                        </td>
                        <td width="10%"><div id="errzona" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="selnivel">Visibilidad:</label></td>
                        <td width="20%">
                            <select id="selnivel" name="selnivel">
                                <option value="PERSONAL" <%if (beanOp.getLevel() != null) {
                if (beanOp.getLevel().equals("PERSONAL")) {%>selected<%}
            }%> >PERSONAL</option>
                                <option value="AREA" <%if (beanOp.getLevel() != null) {
                if (beanOp.getLevel().equals("AREA")) {%>selected<%}
            }%> >&Aacute;REA</option>
                            </select>                        </td>
                        <td width="10%"><div id="errnivel" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="soporte">Soporte requerido</label></td>
                        <td width="20%">
                            <textarea id="soporte" name="soporte" cols="45" rows="5" title="Descripci&oacute;n de soporte requerido"><jsp:getProperty name="beanOp" property="support_required"/></textarea>                        </td>
                        <td width="10%"><div id="errsoporte" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="selproyecto">Proyecto:</label></td>
                        <td width="20%">
                            <select id="selproyecto" name="selproyecto">
                                <option value="0">--- S e l e c c i o n e ---</option>
                                <c:forEach var="beanProject" items="${listaproyectos}">
                                    <jsp:useBean id="beanProject" scope="page" class="mx.com.bancomer.bbva.orion.ibatis.beans.sophie.Project"/>
                                    <option title="<jsp:getProperty name="beanProject" property="name"/>"
                                            value="<jsp:getProperty name="beanProject" property="idproject"/>"
                                            <%if (beanOp.getIdproject() != null) {
                if (beanOp.getIdproject().equals(beanProject.getIdproject())) {%>selected<%}
            }%> >
                                        <jsp:getProperty name="beanProject" property="name"/>                                    </option>
                                </c:forEach>
                            </select>                        </td>
                        <td width="10%"><div id="errproyecto" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" class="ui-state-default"><label for="falta">Fecha Alta:</label></td>
                        <td width="20%">
                            <input id="falta" name="falta" type="text" value="<fmt:formatDate value="${beanOp.date_added}" pattern="dd/MM/yyyy HH:mm" />" size="15" maxlength="15"
                                   title="Fecha de alta de la oportunidad" class="ui-state-default" disabled="disabled"/>                        </td>
                        <td width="10%"><div id="errfalta" style="float:left;"></div></td>
                        <td width="10%" class="ui-state-default"><label for="usralta">Usuario Alta:</label></td>
                        <td width="20%">
                            <input id="usralta" name="usralta" type="text" value="<jsp:getProperty name="beanOp" property="user_added"/>" size="50" maxlength="50"
                                   title="Nombre del usuario que dio el alta de la oportunidad" class="ui-state-default" disabled="disabled"/>                        </td>
                        <td width="10%"><div id="errusralta" style="float:left;"></div></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div id="divCierre" class="ui-helper-hidden">
                                <table>
                                    <tr>
                                        <td colspan="6"><div class="ui-widget-header align-center ">DATOS DE CIERRE DE LA OPERACI&Oacute;N</div></td>
                                    </tr>
                                    <tr>
                                        <td width="10%" class="ui-state-default"><label for="operacion">Operaci&oacute;n:</label></td>
                                        <td width="20%">
                                            <input id="operacion" name="operacion" type="text" value="" size="30" maxlength="30"
                                                   title="N&uacute;mero de operaci&oacute;n"/>                                        </td>
                                        <td width="10%"><div id="erroperacion" style="float:left;"></div></td>
                                        <td width="10%" class="ui-state-default"><label for="fcierreop">Fecha de cierre:</label></td>
                                        <td width="20%">
                                            <input id="fcierreop" name="fcierreop" type="text" value="<fmt:formatDate value="${beanOp.date_close}" pattern="dd/MM/yyyy" />" size="10" maxlength="10"
                                                   title="Fecha de cierre de la operaci&oacute;n" readonly/>                                        </td>
                                        <td width="10%"><div id="errfcierreop" style="float:left;"></div></td>
                                    </tr>
                                    <tr><td colspan="6"><hr></td></tr>
                                </table>
                            </div>
                            <div id="divPerdida" class="ui-helper-hidden">
                                <table>
                                    <tr>
                                        <td colspan="6"><div class="ui-widget-header align-center ">DATOS DE P&Eacute;RDIDA DE OPERACI&Oacute;N</div></td>
                                    </tr>
                                    <tr>
                                        <td width="10%" class="ui-state-default"><label for="razon">Raz&oacute;n:</label></td>
                                        <td width="20%"><select id="selrazon" name="selrazon">
                                          <option value="0">--- S e l e c c i o n e ---</option>
                                          <c:forEach var="beanReason" items="${listarazones}">
                                            <jsp:useBean id="beanReason" scope="page" class="mx.com.bancomer.bbva.orion.ibatis.beans.sophie.Reason"/>
                                            
                                            <option title="<jsp:getProperty name="beanReason" property="name"/>"
                                                            value="<jsp:getProperty name="beanReason" property="idreason"/>" >
                                            <jsp:getProperty name="beanReason" property="name"/>                                                                                    </option>
                                          </c:forEach>
                                        </select></td>
                                        <td width="10%"><div id="errrazon" style="float:left;"></div></td>
                                        <td width="10%" class="ui-state-default"><label for="fperdida">Fecha de p&eacute;rdida:</label></td>
                                        <td width="20%">
                                            <input id="fperdida" name="fperdida" type="text" value="<fmt:formatDate value="${beanOp.date_lose}" pattern="dd/MM/yyyy" />" size="10" maxlength="10"
                                                   title="Fecha de p&eacute;rdida de la operaci&oacute;n" readonly/>                                        </td>
                                        <td width="10%"><div id="errfperdida" style="float:left;"></div></td>
                                    </tr>
                                    <tr>
                                        <td width="10%" class="ui-state-default"><label for="descperdida">Descripci&oacute;n p&eacute;rdida:</label></td>
                                        <td width="20%">
                                            <textarea id="descperdida" name="descperdida" cols="45" rows="5" title="Descripci&oacute;n p&eacute;rdida"></textarea>                                        </td>
                                        <td width="10%"><div id="errdescperdida" style="float:left;"></div></td>
                                    </tr>
                                    <tr><td colspan="6"><hr></td></tr>
                                </table>
                            </div>                        </td>
                    </tr>
                    <tr id="Act_Buttons">
                        <td colspan="2"></td>
                        <td class="EditButton ui-widget-content" colspan="4" style="text-align:right">
                            <c:if test="${requestScope.write == true}">
                                <a id="btnSave1" class="fm-button ui-state-default ui-corner-all fm-button-icon-left ui-pg-div" href="javascript:void(0)" style="font-size:smaller;">
                                    Guardar
                                    <span class="ui-icon ui-icon-disk"/>                                </a>                            </c:if>
                            <a id="btnUndo1" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                                Cancelar
                                <span class="ui-icon ui-icon-cancel"/>                            </a>                        </td>
                    </tr>
                    <tr>
                        <td colspan="6"><div class="fm-button ui-state-default fontMedium fm-button-icon-right" onclick="hideFiles()">Archivos adjuntos<span class="ui-icon ui-icon-circle-triangle-s" style="cursor:pointer;text-align:right" /></div></td>
                    </tr>
                </tbody>
            </table>
      </form>
        <table>
            <tr>
                <td colspan="6">
                    <div id="divFiles">
                        <form name="form" action="" enctype="multipart/form-data" method="post">
                            <table width="100%">
                                <c:if test="${requestScope.write == true}">
                                    <tr>
                                        <td width="10%" class="ui-state-default"><label for="archivo">Archivo:</label></td>
                                        <td width="30%">
                                            <input id="uploadify" name="uploadify" type="file" title="Nombre del archivo a subir como anexo a la oportunidad"/> &nbsp;<p><a href="javascript:jQuery('#uploadify').uploadifyClearQueue()">Cancelar todos los env&iacute;os</a>
                                        </td>
                                        <td width="40%">
                                            <div id="fileQueue"></div>                                            
                                        </td>
                                        <td width="20%"><div id="errarchivo" style="float:left;"></div></td>
                                    </tr>
                                </c:if>
                            </table>
                        </form>    
                        <table id="filelist" class="scroll" cellpadding="0" cellspacing="0">
                            <tr><td style="border:1px black solid"></td></tr>
                        </table>
                        <div id="pagerfiles" class="scroll" style="text-align:center;"></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

