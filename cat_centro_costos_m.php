<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">
	
	function fieldsRequiredLista(){
	
		var error = true;
		
		validText(false, $("#va_centro_costos"), $("#divError1"), 1);
		validSelect($("#selDireccion"), $("#divError1"));
		validSelect($("#selSubdireccion"), $("#divError1"));
		validSelect($("#selDepartamento"), $("#divError1"));
		
		if( !validText(false, $("#va_centro_costos"), $("#divError1"), 1) || 
			!validSelect($("#selDireccion"), $("#divError1")) || 
			!validSelect($("#selSubdireccion"), $("#divError1")) || 
			!validSelect($("#selDepartamento"), $("#divError1"))) error = false;
        		 
		return error;
	}
	
	////////////////// FUNCIONES DE BOTONES //////////////////////
	
	$("#btnSave1").click(function(){  
	
		//alert ("Entre");
	 
		if(fieldsRequiredLista()){	
			var url = "process_centro_costos.php?";
         		url += $("#opForm1").serialize(); 
				//alert (url);       
		 		//executeAjaxJSON(url, false, null);
		 		//jQuery("#list1").trigger("reloadGrid"); 
				//$("#divAlta").html("");
            	//jQuery("#list1").restoreRow(lastsel).setGridState("visible");                 
				
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

</script>

<?
include("includes/funciones.php");  
$mysql=conexion_db();

# Recibo variables
# ===========================
$dispatch	= $_GET['dispatch'];
$id			= $_GET['id'];
	
	//Carga el combo de direcciones
	//=============================
	$sql = "   SELECT id_direccion, tx_nombre ";
	$sql.= "     FROM tbl_direccion ";
	$sql.= "    WHERE id_entidad = 1 ";
	$sql.= " ORDER BY tx_nombre ";
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoDireccion[] = array(
			'id_direccion'=>$row["id_direccion"],
			'tx_nombre'=>$row["tx_nombre"]
		);
	} 	
	
	# Carga el combo de estado del cr
	# ================================
	$sql = "   SELECT id_cr_estado, tx_cr_estado ";
	$sql.= "     FROM tbl_cr_estado ";
	$sql.= " ORDER BY id_cr_estado ";
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoCrEstado[] = array(
			'id_cr_estado'	=>$row["id_cr_estado"],
			'tx_cr_estado'	=>$row["tx_cr_estado"]
		);
	} 
	
	$titulo = "ALTA";
	$par_direccion = 0;
	$par_subdireccion = 0;
	$par_departamento = 0;
	
 	if ($dispatch=="save") {
	
		//Carga el combo de direcciones
		//=============================
		$sql = "   SELECT id_direccion, id_subdireccion, id_departamento, tx_centro_costos ";
		$sql.= "     FROM tbl_centro_costos ";
		$sql.= "    WHERE id_centro_costos 	= $id  ";
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoCentroCosto[] = array(
				'id_direccion'=>$row["id_direccion"],
				'id_subdireccion'=>$row["id_subdireccion"],
				'id_departamento'=>$row["id_departamento"],
				'tx_centro_costos'=>$row["tx_centro_costos"]
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoCentroCosto); $i++)	{         			 
			while ($elemento = each($TheCatalogoCentroCosto[$i]))					  		
				$id_direccion		=$TheCatalogoCentroCosto[$i]['id_direccion'];		
				$id_subdireccion	=$TheCatalogoCentroCosto[$i]['id_subdireccion'];				
				$id_departamento	=$TheCatalogoCentroCosto[$i]['id_departamento'];
				$tx_centro_costos	=$TheCatalogoCentroCosto[$i]['tx_centro_costos'];				
		} 
		
		$titulo 			= "MODIFICACION";
		$par_direccion 		= $id_direccion;
		$par_subdireccion 	= $id_subdireccion;		
		$par_departamento 	= $id_departamento;		
		$par_centro_costos	= $tx_centro_costos;		
	}	
?>
<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;">    
    <div id="divError1"></div>     
    <form id="opForm1" action="">
    	<input id="id" name="id" type="hidden" value="<? echo $id ?>" />
    	<input id="dispatch" name="dispatch" type="hidden" value="<? echo $dispatch ?>" />
        <input id="par_direccion" name="par_direccion" type="hidden" value="<? echo $par_direccion ?>" />
    	<input id="par_subdireccion" name="par_subdireccion" type="hidden" value="<? echo $par_subdireccion ?>" />
    	<input id="par_departamento" name="par_departamento" type="hidden" value="<? echo $par_departamento ?>" />
  <table cellspacing="2px" border="0" cellpadding="0" width="100%">
        	<tbody>
            	<tr>
                	<td colspan="5" class="ui-state-default fontMedium align-center"><? echo $titulo ?></td>                      
                </tr>
                <tr>                     	
                	<td colspan="5"><? //echo $dispatch." ".$id ?></td>
                </tr>
                <tr>
                	<td class="ui-state-default">Centro de Costos:</td>
                  	<td>
                    	<input name="va_centro_costos" id="va_centro_costos" type="text" size="10" title="Centro de Costos" value="<? echo $par_centro_costos ?>"/>                    </td>
                  	<td></td>
                </tr>
                <tr>
                	<td width="20%" class="ui-state-default">Dirección (CR Consolidador):</td>
                    <td width="60%">
                    <select id="selDireccion" name="selDireccion" onchange="openSubdireccion($('#selDireccion').val(),$('#par_subdireccion').val(),$('#dispatch').val())";>
                    <option value="0" class="">--- S e l e c c i o n e ---</option>
                    <?
                    	for ($i=0; $i < count($TheCatalogoDireccion); $i++)	{         			 
							while ($elemento = each($TheCatalogoDireccion[$i]))					  		
								$id_direccion	=$TheCatalogoDireccion[$i]['id_direccion'];		
								$tx_nombre		=$TheCatalogoDireccion[$i]['tx_nombre'];										
								if ($par_direccion == $id_direccion ) echo "<option value=$id_direccion selected='selected'>$tx_nombre</option>";	
								else echo "<option value=$id_direccion>$tx_nombre</option>";	
						}
					?>
                    </select>
                    </td>
                    <td width="10%"></td>
                </tr>
                <tr>
                 	<td width="20%" class="ui-state-default">Subdirección (CR Descripción):</td>
                    <td width="60%" id="divSubdireccion">
                    	<script type="text/javascript">
							var valor 				= $('#par_direccion').val();
							var par_subdireccion 	= $('#par_subdireccion').val();
							var par_dispatch 		= $('#dispatch').val();
							openSubdireccion(valor,par_subdireccion,par_dispatch);                        
                        </script>
                        <!-- <select id="selSubdireccion" name="selSubdireccion"; disabled="disabled">
                            <option value="0" class="">--- S e l e c c i o n e ---</option>
                        </select> -->                    </td>
                    <td width="10%"></td>                        
                </tr>
                <tr>
               	  	<td width="20%" class="ui-state-default">Departamento (CR Departamento):</td>
                   	<td width="60%" id="divDepartamento">
                    	<script type="text/javascript">	
							var valor 				= $('#par_subdireccion').val();
							var par_departamento 	= $('#par_departamento').val();
							var par_dispatch 		= $('#dispatch').val();					
							openDepartamento(valor,par_departamento,par_dispatch);                          
                        </script>
                    	<!-- <select id="selDepartamento" name="selDepartamento"; disabled="disabled">
                      		<option value="0" class="">--- S e l e c c i o n e ---</option>
                    	</select> -->
                    </td>
                    <td width="10%"></td>
                </tr>                
                <tr>
               		<td class="ui-state-default">Estado:</td>
                 	<td>
                    	<select id="selCrEstado" name="selCrEstado">
                      	<?
                    		for ($i=0; $i < count($TheCatalogoCrEstado); $i++)	{         			 
								while ($elemento = each($TheCatalogoCrEstado[$i]))					  		
									$id_cr_estado	=$TheCatalogoCrEstado[$i]['id_cr_estado'];		
									$tx_cr_estado	=$TheCatalogoCrEstado[$i]['tx_cr_estado'];										
									if ($tx_cr_estado == $id_direccion ) echo "<option value=$id_cr_estado selected='selected'>$tx_cr_estado</option>";	
									else echo "<option value=$id_cr_estado>$tx_cr_estado</option>";	
						}
						?>
                    	</select>
                   	</td>
                  	<td>&nbsp;</td>
                </tr>
                <tr>                     	
                	<td colspan="5">&nbsp;</td>
                </tr>
                <tr id="Act_Buttons">
                	<td class="EditButton ui-widget-content" colspan="5" style="text-align:center">                            
                    	<a id="btnSave1" class="fm-button ui-state-default ui-corner-all fm-button-icon-left ui-pg-div" href="javascript:void(0)" style="font-size:smaller;">
                        Guardar
                        <span class="ui-icon ui-icon-disk"/></a>
                        <a id="btnUndo1" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                        Cancelar
                        <span class="ui-icon ui-icon-cancel"/></a>
                    </td>
              	</tr>
                <tr>
                	<td colspan="5" class="ui-state-default fontMedium align-center">&nbsp;</td>                      
                </tr>
           	</tbody>    
     	</table>     
   	</form>
	<?
	mysqli_close($mysql);			
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>