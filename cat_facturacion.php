<?php
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">

	$('#tabs').tabs();		
	
	function formatCounterpart(row) {
		return row[0];	
    }
	
	function formatItem(row) {
        return "<strong>"+row[0]+"</strong>";
    }
	
	// Carga al inicial	
	var id="id="+$("#sel_anio_inicio").val();		
	$("#tx_busca_proveedor").focus();
	loadHtmlAjax(true, $("#divDatos"), "cat_facturas_lista.php?"+id); 
	
	$("#btnBuscar").click(function(){  	
	
		var id="id="+$("#sel_anio_inicio").val();			
		var id1="&id1="+$("#tx_busca_proveedor").val();			
		var id2="&id2="+$("#tx_busca_factura").val();			
		var id3="&id3="+$("#sel_busca_estatus").val();	
		var id4="&id4="+$("#tx_regs").val();	
		
		var dispatch="&dispatch=save";		
		//alert ("Proveedor"+id1);		
		$("#divAltaFac").hide();	
		if (id==0) { $("#divDatos").hide(); }
		else { 
			$("#tx_busca_proveedor").focus();
			loadHtmlAjax(true, $("#divDatos"), "cat_facturas_lista.php?"+id+id1+id2+id3+id4+dispatch); 
		}		
        
	}).hover(function(){
     	$(this).addClass("ui-state-hover")
    },function(){
        $(this).removeClass("ui-state-hover")
    });
	
	$("#btnNewRefre").click(function(){  	
	
		var id="id="+$("#sel_anio_inicio").val();			
		var id1="&id1="+$("#tx_busca_proveedor").val();			
		var id2="&id2="+$("#tx_busca_factura").val();			
		var id4="&id4="+$("#tx_regs").val();
		var dispatch="&dispatch=save";		
		//alert ("Proveedor"+id1);		
		$("#divAltaFac").hide();	
		if (id==0) { $("#divDatos").hide(); }
		else { 
			$("#tx_busca_proveedor").focus();
			loadHtmlAjax(true, $("#divDatos"), "cat_facturas_lista.php?"+id+id1+id2+id4+dispatch); 
		}		
        
	}).hover(function(){
     	$(this).addClass("ui-state-hover")
    },function(){
        $(this).removeClass("ui-state-hover")
    });
	
	$("#btnNewFac").click(function(){     	
	
		var id0="id=0";
		var dispatch="&dispatch=insert";
		$("#divAltaFac").hide();
		loadHtmlAjax(true, $("#divAltaFac"), "cat_facturas_cabecera.php?"+id0+dispatch); 
		   
     }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });
	 
	 $("#btnLimpiar").click(function(){     
	
		$("#tx_busca_proveedor").val("");
		$("#tx_busca_factura").val("");
		$("#sel_busca_estatus").val("");
		$("#tx_regs").val("20");
		   
     }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });
	 
	 
	$("#btnFacturacion").click(function(){   
		
		var id="id="+$("#sel_anio_inicio").val();		
	   	var url = "excel_facturacion.php?"+id;			
		//alert("url"+url);
		window.open( url,"_blank");		
		
	}).hover(function(){
		$(this).addClass("ui-state-hover")
	},function(){
		$(this).removeClass("ui-state-hover")
	});
	 
	 
	  ///////////////// DEFINICION DE EVENTOS //////////////////////
	 $("#sel_anio_inicio").change(function () {
	 	$("#sel_anio_inicio option:selected").each(function () {				
			var id="id="+$(this).val();	
			var id1="&id1="+$("#tx_busca_proveedor").val();			
			var id2="&id2="+$("#tx_busca_factura").val();			
			var id4="&id4="+$("#tx_regs").val();
			//alert ("Valor"+id);
			var dispatch="&dispatch=save";
			$("#divAltaFac").hide();	
			if (id==0) { $("#divDatos").hide(); }
			else { 
				$("#tx_busca_proveedor").focus();
				loadHtmlAjax(true, $("#divDatos"), "cat_facturas_lista.php?"+id+id1+id2+id4+dispatch); 
			}
     	});
     });
	 
	$("#tx_busca_proveedor").focus(function() {
    	$(this).addClass('ui-state-focus');
    });
	
	$("#tx_busca_proveedor").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	
	
	$("#tx_busca_factura").focus(function() {
    	$(this).addClass('ui-state-focus');
    });
	
	$("#tx_busca_factura").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	
	$("#tx_regs").focus(function() {
    	$(this).addClass('ui-state-focus');
    });
	
	$("#tx_regs").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	
	
	$("#tx_busca_proveedor").autocomplete("process_proveedores.php?dispatch=find&campo=proveedor",{
    	minChars: 2,
        max: 10,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#tx_busca_proveedor").val(item[0]);
   	});
	
	$("#tx_busca_factura").autocomplete("process_facturas.php?dispatch=find&campo=factura",{
    	minChars: 2,
        max: 10,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#tx_busca_factura").val(item[0]);
   	});	

</script>
<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	# ============================================
	# Carga la informacion para combo de anios
	# ============================================
	$sql = "   SELECT tx_anio ";
	$sql.= "   	 FROM tbl_anio ";
	$sql.= "   	WHERE tx_indicador='1' ";
	$sql.= " ORDER BY tx_anio DESC " ; 	
		
	//echo "aaa", $sql;
			
	$result = mysqli_query($mysql, $sql);		
		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoAnios[] = array(
			'tx_anio'	=>$row["tx_anio"]
			);
	} 
	
	# =======================================
	# Carga el combo de estado de la factura
	# =======================================
	$sql = "   SELECT id_factura_estatus, tx_estatus ";
	$sql.= "     FROM tbl_factura_estatus ";
	$sql.= "      WHERE TX_INDICADOR='1' ";
	$sql.= " ORDER BY id_factura_estatus ";
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoEstatus[] = array(
			'id_factura_estatus'	=>$row["id_factura_estatus"],
			'tx_estatus'			=>$row["tx_estatus"]
		);
	} 

?>
<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"> 
<div class="ui-widget-header align-center ">MODULO DE FACTURACION</div>	
    <form id="opForm1" action="">
        <table cellspacing="1px" border="0" cellpadding="0" width="100%">        	
			<tr>
            	<td width="5%" class="ui-state-default align-left">A&ntilde;o:</td>
	  			<td width="7%">
                <select id="sel_anio_inicio" name="sel_anio_inicio">
                <!--<option value="0">-- S e l e c c i o n e --</option> -->
                <?								
					for ($i = 0; $i < count($TheCatalogoAnios); $i++)
					{	         			 
						while ($elemento = each($TheCatalogoAnios[$i]))														
							$tx_anio=$TheCatalogoAnios[$i]['tx_anio'];			  
							echo "<option value='$tx_anio'>$tx_anio</option>";
					}						 
				?>
                </select>
                </td>
              	<td width="5%" class="ui-state-default">Proveedor:</td>
              	<td width="18%">
					<input name="tx_busca_proveedor" id="tx_busca_proveedor" type="text" size="30" title="Proveedor" value="">
                </td>    
   		  	  	<td width="5%" class="ui-state-default">Factura:</td>
              	<td width="14%">
                	<input name="tx_busca_factura" id="tx_busca_factura" type="text" size="20" title="N&uacute;mero de Factura" value="">
                </td>    
 				<td width="5%" class="ui-state-default">Estatus:</td>
              	<td width="11%">
                <select id="sel_busca_estatus" name="sel_busca_estatus">
                    <option value="0" class=""></option>
                    <?
                        for ($i=0; $i < count($TheCatalogoEstatus); $i++)	{         			 
                            while ($elemento = each($TheCatalogoEstatus[$i]))					  		
                                $id_factura_estatus	=$TheCatalogoEstatus[$i]['id_factura_estatus'];		
                                $tx_estatus			=$TheCatalogoEstatus[$i]['tx_estatus'];										
                                echo "<option value=$id_factura_estatus>$tx_estatus</option>";	
                        }
                    ?>
                </select>
                </td> 
   		  	  	<td width="5%"class="ui-state-default">Registros:</td>
              	<td >
                	<input name="tx_regs" id="tx_regs" type="text" size="2" title="N&uacute;mero de registros" value="20" maxlength="3">
                </td>    
   	  	  		<td width="30%" class="align-center">    
                	<a id="btnBuscar" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Buscar" >
                    Buscar
               		<span class="ui-icon ui-icon-search"></span></a>
                    <a id="btnLimpiar" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Limpiar">
                    Limpiar 
                    <span class="ui-icon ui-icon-trash"></span></a>
                	<a id="btnNewFac" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Agregar">
                    Agregar
                    <span class="ui-icon ui-icon-plus"></span></a>
                    <a id="btnNewRefre" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Refrescar">
                    Refrescar
                    <span class="ui-icon ui-icon-refresh"></span></a>
                    <a id="btnFacturacion" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Exportar">
                    Exportar
                    <span class="ui-icon ui-icon-extlink"></span></a>
                </td>              		
          	</tr> 
            <tr>
            	<td colspan="11">&nbsp;</td>
       		</tr>	           	
            <tr>
            	<td colspan="11" valign="top"><div id="divDatos" class="divConGrid1"></div></td>                    
           	</tr>
            <tr>
          		<td colspan="11" valign="top"><div id="divAltaFac"></div></td>
           	</tr>
        </table>
  </form>   
</div> 
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  