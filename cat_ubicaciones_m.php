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
		
		validSelect($("#selPais"), $("#divError1"));
		validSelect($("#selEstado"), $("#divError1"));
		validText(false, $("#va_ubicacion"), $("#divError1"), 1);
		
		//alert("Entre");		
		
		if( !validSelect($("#selPais"), $("#divError1")) || 
			!validSelect($("#selEstado"), $("#divError1")) ||
			!validText(false, $("#va_ubicacion"), $("#divError1"), 1)		
			) error = false;	
        		 
		return error;
	}
	
	$("#btnSave1").click(function(){               
	 
		if(fieldsRequiredLista()){	
			var url = "process_ubicaciones.php?";
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

</script>

<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	//Resivo variables
	//===========================
	$dispatch	= $_GET['dispatch'];
	$id			= $_GET['id'];
	
	//Carga el combo de paises
	//=============================
	$sql = "   SELECT id_pais, tx_pais ";
	$sql.= "     FROM tbl_pais ";
	$sql.= " ORDER BY tx_pais ";
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoPais[] = array(
			'id_pais'=>$row["id_pais"],
			'tx_pais'=>$row["tx_pais"]
		);
	} 	
	
	//Carga el combo de estados
	//=============================
	$sql = "   SELECT id_estado, tx_estado ";
	$sql.= "     FROM tbl_estado ";
	$sql.= " ORDER BY tx_estado ";
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoEstado[] = array(
			'id_estado'=>$row["id_estado"],
			'tx_estado'=>$row["tx_estado"]
		);
	} 	
	
	$titulo = "ALTA";
	$par_indicador	= "1";
	
 	if ($dispatch=="save") {
	
		//Carga el combo de ubicaciones
		//=============================
		$sql = " SELECT id_ubicacion, id_pais, id_estado, tx_indicador ";
		$sql.= "   FROM tbl_ubicacion  ";
		$sql.= "  WHERE id_ubicacion	= $id  ";
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoUbicacion[] = array(
				'id_ubicacion'	=>$row["id_ubicacion"],
				'id_pais'		=>$row["id_pais"],
				'id_estado'		=>$row["id_estado"],
				'tx_indicador'	=>$row["tx_indicador"]
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoUbicacion); $i++)	{         			 
			while ($elemento = each($TheCatalogoUbicacion[$i]))					  		
				$id_ubicacion	=$TheCatalogoUbicacion[$i]['id_ubicacion'];		
				$id_pais		=$TheCatalogoUbicacion[$i]['id_pais'];
				$id_estado		=$TheCatalogoUbicacion[$i]['id_estado'];				
				$tx_indicador	=$TheCatalogoUbicacion[$i]['tx_indicador'];
		} 
		
		$titulo 		= "MODIFICACION";
		$par_ubicacion 	= $id_ubicacion;
		$par_estado 	= $id_estado;		
		$par_pais 		= $id_pais;		
		$par_indicador	= $tx_indicador;
	}	
?>
<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;">    
    <div id="divError1"></div>     
    <div class="ui-jqdialog-content ui-widget-content">
    <form id="opForm1" action="">
    	<input id="id" name="id" type="hidden" value="<? echo $id ?>" />
    	<input id="dispatch" name="dispatch" type="hidden" value="<? echo $dispatch ?>" />
        <input id="par_ubicacion" name="par_ubicacion" type="hidden" value="<? echo $par_ubicacion ?>" />
        <input id="par_estado" name="par_estado" type="hidden" value="<? echo $par_estado ?>" />
      	<input id="par_pais" name="par_pais" type="hidden" value="<? echo $par_pais ?>" />
	  	<table cellspacing="2px" border="0" cellpadding="0" width="100%">
   		<tbody>
   		  <tr>
       		<td colspan="5" class="ui-state-default fontMedium align-center"><? echo $titulo ?></td>                      
            	</tr>
                <tr>                     	
                	<td colspan="5"><? //echo $dispatch." ".$id ?></td>
                </tr>                
                <tr>
                  <td class="ui-state-default">Indicador:</td>
                  <td> 
                  	<input id="tx_indicador" name="tx_indicador" type="hidden" value="<? echo $par_indicador ?>">
                		<div id="imgstatus"></div>
                		<script>setStatus();</script>
                  </td>
                  <td></td>
                </tr>
          <tr>
                	<td width="20%" class="ui-state-default">Pa&iacute;s:</td>
                    <td width="60%"><select id="selPais" name="selPais"  onchange="openEstado($('#selPais').val(),$('#par_estado').val(),$('#dispatch').val())";>
                    	<option value="0" class="">--- S e l e c c i o n e ---</option>
                      	<?
                      		for ($i=0; $i < count($TheCatalogoPais); $i++)	{         			 
								while ($elemento = each($TheCatalogoPais[$i]))					  		
									$id_pais	=$TheCatalogoPais[$i]['id_pais'];		
									$tx_pais	=$TheCatalogoPais[$i]['tx_pais'];										
									if ($par_pais == $id_pais ) echo "<option value=$id_pais selected='selected'>$tx_pais</option>";	
									else echo "<option value=$id_pais>$tx_pais</option>";	
							}
						?>
                    	</select>
                    </td>
            <td width="10%"></td>
                </tr>
                <tr>
                 	<td width="20%" class="ui-state-default">Estado:</td>
                    <td width="60%" id="divEstados">
               	  		<script type="text/javascript">
							var valor 			= $('#par_pais').val();
							var par_estado 		= $('#par_estado').val();
							var par_dispatch 	= $('#dispatch').val();
							if (par_dispatch=="save") openEstado(valor,par_estado,par_dispatch);                        
                        </script>                    </td>
                    <td width="10%"></td>                        
                </tr>
                <tr>
                	<td class="ui-state-default">Ubicaci&oacute;n:</td>
                 	<td width="60%" id="divUbicaciones">
                    	<script type="text/javascript">
							var par_dispatch 	= $('#dispatch').val();
							var par_ubicacion 	= $('#par_ubicacion').val();
							if (par_dispatch=="save") openUbicacion(par_dispatch, par_ubicacion);                        
                        </script>                    </td>
                  	<td></td>
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
    </div>    
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  