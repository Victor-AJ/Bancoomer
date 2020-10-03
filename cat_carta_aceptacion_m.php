<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">	
	
	function formatCounterpart(row) {
		return row[0];	
    }
	
	function formatItem(row) {
        return "<strong>"+row[0]+"</strong>";
    }
	
	function fieldsRequiredLista(){
	
		//alert ("Entre");
	
		var error = true;		
		
		validText(false, $("#cap_empresa"), $("#errcap_empresa"), 1);
		validText(false, $("#cap_nombre_glg"), $("#errcap_nombre_glg"), 1);				
						
		if( !validText(false, $("#cap_empresa"), $("#errcap_empresa"), 1) ||
			!validText(false, $("#cap_nombre_glg"), $("#errcap_nombre_glg"), 1) 
			// || !validSelect($("#sel_tipo_gasto"), $("#errsel_tipo_gasto")) 
			) error = false;
        		 
		return error;
	}
	
	$("#btnSave1").click(function(){     
	 
		if(fieldsRequiredLista()){	
			var url = "process_carta_aceptacion.php?";
         		url += $("#opForm1").serialize(); 
							
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
						jAlert(true,false,data.message,fAceptar);
						jQuery("#list1").trigger("reloadGrid");
						$("#divAlta").html("");
            			jQuery("#list1").restoreRow(lastsel).setGridState("visible");    
					}
				}	
				}	
				//alert (url);						
				executeAjax("post", false ,url, "json", func);	
        }else{
          	var fAceptar = function(){
               	$('#dialogMain').dialog("close");
            }
            jAlert(true,true,"Existen campos obligatorios vac&iacute;os",fAceptar);
        }   
     }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });
	 
	 $("#btnUndo1").click(function(){   	 	
		$("#divAlta").html("");
        jQuery("#list1").restoreRow(lastsel).setGridState("visible");    
	 }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });
	 
	 $(function(){
     	$('input:text').setMask();
     });

	$("#cap_empresa").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_empresa").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_nombre_glg").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_nombre_glg").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });

	$("#cap_codigo_glg").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_codigo_glg").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });

	$("#cap_linea_gasto").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_linea_gasto").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_codigo_cuenta").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_codigo_cuenta").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_familia").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_familia").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_orden_compra").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_orden_compra").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_requision_numero").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_requision_numero").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_numero_partida").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_numero_partida").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_pais").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_pais").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });
	
</script>

<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	$dispatch	= $_GET['dispatch'];
	$id			= $_GET['id'];	
	
	# ============================================
	# Catalogo de tipo Gasto	
	# ============================================
	/* $sql = "   SELECT id_tipo_gasto, tx_tipo_gasto ";
	$sql.= "     FROM tbl_tipo_gasto ";
	$sql.= "    WHERE tx_indicador = '1' ";
	$sql.= " ORDER BY tx_tipo_gasto ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoTipoGasto[] = array(
			'id_tipo_gasto'	=> $row["id_tipo_gasto"],
			'tx_tipo_gasto'	=> $row["tx_tipo_gasto"]
		);
	}	*/
	
	if ($dispatch=="insert") {	
		
		$titulo 		= "ALTA";		
		$tx_indicador	= "1";
	
 	} else if ($dispatch=="save") {
		
		$titulo 		= "MODIFICACION";
	
		# Carga la informacion para la actualizacion
		# ==========================================
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_carta_aceptacion ";
		$sql.= "  WHERE id_carta_aceptacion	= $id  ";
				
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoCarta[] = array(
				'ac_carta_aceptacion'	=>$row["id_carta_aceptacion"],
				'tx_empresa'			=>$row["tx_empresa"],
				'tx_nombre_glg'			=>$row["tx_nombre_glg"],
				'tx_codigo_glg'			=>$row["tx_codigo_glg"],
				'tx_linea_gasto'		=>$row["tx_linea_gasto"],
				'tx_codigo_cuenta'		=>$row["tx_codigo_cuenta"],
				'tx_codigo_glg'			=>$row["tx_codigo_glg"],
				'tx_familia'			=>$row["tx_familia"],
				'tx_orden_compra'		=>$row["tx_orden_compra"],
				'tx_requision_numero'	=>$row["tx_requision_numero"],
				'tx_numero_partida'		=>$row["tx_numero_partida"],
				'tx_pais'				=>$row["tx_pais"],
				'tx_indicador'			=>$row["tx_indicador"],
				'fh_mod'				=>$row["fh_mod"],
				'id_usuariomod'			=>$row["id_usuariomod"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoCarta); $i++)	{         			 
			while ($elemento = each($TheCatalogoCarta[$i]))					  		
				$ac_carta_aceptacion	=$TheCatalogoCarta[$i]['ac_carta_aceptacion'];		
				$tx_empresa				=$TheCatalogoCarta[$i]['tx_empresa'];		
				$tx_nombre_glg			=$TheCatalogoCarta[$i]['tx_nombre_glg'];				
				$tx_codigo_glg			=$TheCatalogoCarta[$i]['tx_codigo_glg'];
				$tx_linea_gasto			=$TheCatalogoCarta[$i]['tx_linea_gasto'];
				$tx_codigo_cuenta		=$TheCatalogoCarta[$i]['tx_codigo_cuenta'];
				$tx_codigo_glg			=$TheCatalogoCarta[$i]['tx_codigo_glg'];
				$tx_familia				=$TheCatalogoCarta[$i]['tx_familia'];
				$tx_orden_compra		=$TheCatalogoCarta[$i]['tx_orden_compra'];
				$tx_requision_numero	=$TheCatalogoCarta[$i]['tx_requision_numero'];
				$tx_numero_partida		=$TheCatalogoCarta[$i]['tx_numero_partida'];
				$tx_pais				=$TheCatalogoCarta[$i]['tx_pais'];								
				$tx_indicador			=$TheCatalogoCarta[$i]['tx_indicador'];
		} 	
	}	
?>
<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"> 
    <div class="ui-jqdialog-content ui-widget-content">
    <form id="opForm1" action="">
    	<input id="id" name="id" type="hidden" value="<? echo $id ?>" />
    	<input id="dispatch" name="dispatch" type="hidden" value="<? echo $dispatch ?>" />        
	  	<table cellspacing="2px" border="0" cellpadding="0" width="100%">
   			<tbody>
   				<tr>
                	<td colspan="5" class="ui-state-highlight" align="center" style="font-family:Verdana,Arial,sans-serif;font-size: 13px;font-weight:bold;"> <? echo $titulo ?></td>  
         		</tr>         	              
            	<tr>
              	<td colspan="3" class="ui-state-default fontMedium align-center">&nbsp;</td>
            	</tr>
            	<tr>
            		<td class="ui-state-default">Indicador:</td>
                	<td> 
                		<input id="tx_indicador" name="tx_indicador" type="hidden" value="<? echo $tx_indicador ?>">
                		<div id="imgstatus"></div>
                		<script>setStatus();</script>                    </td>
                	<td></td>
           		</tr>          	
            	<tr>
                	<td width="30%" class="ui-state-default">Empresa / Sociedad receptora del servicio:</td>
                  	<td width="50%">
                    	<input name="cap_empresa" id="cap_empresa" type="text" size="100" title="Empresa" value="<? echo $tx_empresa ?>" />
                    </td>
                  	<td width="20%"><div id="errcap_empresa" style="float:left;"></div></td>
            	</tr>
            	<tr>
                  	<td class="ui-state-default">Nombre de la GLG:</td>
                  	<td>
                    	<input name="cap_nombre_glg" id="cap_nombre_glg" type="text" size="100" title="Nombre GLG" value="<? echo $tx_nombre_glg ?>" />
                    </td>
                  	<td><div id="errcap_nombre_glg" style="float:left;"></div></td>
                </tr>
                <tr>
                  	<td class="ui-state-default">Codigo GLG:</td>
                  	<td>
                    	<input name="cap_codigo_glg" id="cap_codigo_glg" type="text" size="100" title="Codigo GLG" value="<? echo $tx_codigo_glg ?>" />
                    </td>
                  	<td>&nbsp;</td>
                </tr>
                <tr>
                  	<td class="ui-state-default">L&iacute;nea de Gasto asignada:</td>
                  	<td>
                    	<input name="cap_linea_gasto" id="cap_linea_gasto" type="text" size="100" title="L&iacute;nea de Gasto asignada" value="<? echo $tx_linea_gasto ?>" />
                    </td>
                  	<td>&nbsp;</td>
                </tr>
                <tr>
                  	<td class="ui-state-default">Codigo Cuenta:</td>
                  	<td>
                    	<input name="cap_codigo_cuenta" id="cap_codigo_cuenta" type="text" size="100" title="Codigo Cuenta" value="<? echo $tx_codigo_cuenta ?>" />
                    </td>
                  	<td>&nbsp;</td>
                </tr>
                <tr>
                  	<td class="ui-state-default">Familia (2):</td>
                  	<td>
                    	<input name="cap_familia" id="cap_familia" type="text" size="100" title="Familia (2)" value="<? echo $tx_familia ?>" />
                    </td>
                  	<td>&nbsp;</td>
                </tr>
                <tr>
                  	<td class="ui-state-default">Orden de compra n&uacute;mero</td>
                  	<td>
                    	<input name="cap_orden_compra" id="cap_orden_compra" type="text" size="100" title="Orden de compra n&uacute;mero" value="<? echo $tx_orden_compra ?>" />
                    </td>
                  	<td>&nbsp;</td>
                </tr>
                <tr>
                  	<td class="ui-state-default">Requisici&oacute;n n&uacute;mero:</td>
                  	<td>
                    	<input name="cap_requision_numero" id="cap_requision_numero" type="text" size="100" title="Requisici&oacute;n n&uacute;mero" value="<? echo $tx_requision_numero ?>" />
                    </td>
                  	<td>&nbsp;</td>
                </tr>
                <tr>
                  	<td class="ui-state-default">N&uacute;mero Partida / Proyecto:</td>
                  	<td>
                  		<input name="cap_numero_partida" id="cap_numero_partida" type="text" size="100" title="N&uacute;mero Partida / Proyecto" value="<? echo $tx_numero_partida ?>" />
                    </td>
                  	<td>&nbsp;</td>
                </tr>
                <tr>
                    <td class="ui-state-default">Estado del pa&iacute;s en donde se proporcion&oacute; el servicio:</td>
                    <td>
                    	<input name="cap_pais" id="cap_pais" type="text" size="100" title="Estado del pa&iacute;s en donde se proporcion&oacute; el servicio" value="<? echo $tx_pais ?>" />
                    </td>
                    <td>&nbsp;</td>                        
                </tr>       
                <tr>
                    <td colspan="3" class="ui-state-default">&nbsp;</td>
                </tr>            
                <tr id="Act_Buttons">
                    <td class="EditButton ui-widget-content" colspan="5" style="text-align:center">                            
                        <a id="btnSave1" class="fm-button ui-state-default ui-corner-all fm-button-icon-left ui-pg-div" href="javascript:void(0)" style="font-size:smaller;">
                        Guardar
                        <span class="ui-icon ui-icon-disk"></span></a>
                        <a id="btnUndo1" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                        Cancelar
                        <span class="ui-icon ui-icon-cancel"></span></a>                   	</td>
                </tr>
                <tr>
                    <td colspan="5" class="ui-state-highlight">&nbsp;</td>                      
                </tr>
        	</tbody>    
     	</table>     
   	</form>
    </div>    
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  