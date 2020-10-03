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
		
		validText(false, $("#cap_glg"), $("#errcap_glg"), 1);
		validText(false, $("#cap_cuenta"), $("#errcap_cuenta"), 1);		
		validSelect($("#sel_tipo_gasto"), $("#errsel_tipo_gasto"));
						
		if( !validText(false, $("#cap_glg"), $("#errcap_glg"), 1) ||
			!validText(false, $("#cap_cuenta"), $("#errcap_cuenta"), 1) ||
			!validSelect($("#sel_tipo_gasto"), $("#errsel_tipo_gasto")) ) error = false;
        		 
		return error;
	}
	
	$("#btnSave1").click(function(){     
	 
		if(fieldsRequiredLista()){	
			var url = "process_glg.php?";
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

</script>

<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	$dispatch	= $_GET['dispatch'];
	$id			= $_GET['id'];	
	
	# ============================================
	# Catalogo de tipo Gasto	
	# ============================================
	$sql = "   SELECT id_tipo_gasto, tx_tipo_gasto ";
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
	}	
	
	if ($dispatch=="insert") {	
		
		$titulo 		= "ALTA";		
		$tx_indicador	= "1";
	
 	} else if ($dispatch=="save") {
		
		$titulo 		= "MODIFICACION";
	
		//Carga la informacion para la actualizacion
		//==========================================
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_glg a, tbl_tipo_gasto ";
		$sql.= "  WHERE id_glg	= $id  ";
				
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoGLG[] = array(
				'ac_glg'		=>$row["id_glg"],
				'ac_tipo_gasto'	=>$row["id_tipo_gasto"],
				'tx_glg'		=>$row["tx_glg"],
				'tx_cuenta'		=>$row["tx_cuenta"],
				'tx_indicador'	=>$row["tx_indicador"],
				'fh_mod'		=>$row["fh_mod"],
				'id_usuariomod'	=>$row["id_usuariomod"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoGLG); $i++)	{         			 
			while ($elemento = each($TheCatalogoGLG[$i]))					  		
				$ac_telefonia	=$TheCatalogoGLG[$i]['ac_glg'];		
				$ac_tipo_gasto	=$TheCatalogoGLG[$i]['ac_tipo_gasto'];		
				$tx_glg			=$TheCatalogoGLG[$i]['tx_glg'];				
				$tx_cuenta		=$TheCatalogoGLG[$i]['tx_cuenta'];				
				$tx_indicador	=$TheCatalogoGLG[$i]['tx_indicador'];
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
                	<td width="20%" class="ui-state-default">GLG:</td>
                  	<td width="60%"><input name="cap_glg" id="cap_glg" type="text" size="100" title="GLG" value="<? echo $tx_glg ?>" /></td>
                  	<td width="20%"><div id="errcap_glg" style="float:left;"></div></td>
            	</tr>
            	<tr>
                  	<td class="ui-state-default">Cuenta:</td>
                  	<td><input name="cap_cuenta" id="cap_cuenta" type="text" size="100" title="Cuenta" value="<? echo $tx_cuenta ?>" /></td>
                  	<td><div id="errcap_cuenta" style="float:left;"></div></td>
                </tr>
                <tr>
                    <td class="ui-state-default">Tipo:</td>
                    <td>
                    <select id="sel_tipo_gasto" name="sel_tipo_gasto">
                    <option value="0" class="">--- S e l e c c i o n e ---</option>
                    <?
					for ($i=0; $i < count($TheCatalogoTipoGasto); $i++) {         			 
						while ($elemento = each($TheCatalogoTipoGasto[$i]))					  		
							$id_tipo_gasto	= $TheCatalogoTipoGasto[$i]['id_tipo_gasto'];		
							$tx_tipo_gasto	= $TheCatalogoTipoGasto[$i]['tx_tipo_gasto'];
							if ($ac_tipo_gasto == $id_tipo_gasto) echo "<option value=$id_tipo_gasto selected='selected'>$tx_tipo_gasto</option>";
							else echo "<option value=$id_tipo_gasto>$tx_tipo_gasto</option>";	
					}
					?>
                    </select>
                    </td>
                    <td><div id="errsel_tipo_gasto" style="float:left;"></div></td>                        
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