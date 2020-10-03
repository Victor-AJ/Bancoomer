<script type="text/javascript">

	$("#divEspacios").html("");	 
	
	//function fieldsRequired(){
	
	//	var error = false;
		
	//	validSelect($("#selperfil"), $("#divError"));
	//	validSelect($("#selentidad"), $("#divError"));
				
	//	if( 
    //    	!validSelect($("#selperfil"), $("#divError")) ||
    //        !validSelect($("#selentidad"), $("#divError"))){
    //        error = true;
    //    }
	//	 return !error;
	//}
	
	////////////////// FUNCIONES DE BOTONES //////////////////////
/*    $("#btnSave").click(function(){
    	//if(editing2){
			//alert ("Entre");
			//var url_a =	completeUrl();
			
        	var url = "process_perfil_config?dispatch=insert&";
                url += $("#opForm").serialize();
                //url += ("&id="+$("#id").val());
                //url += ("&gpoprod="+$("#gpoprod").val());
                //url += ("&checkcompartida="+$("#compartida:checkbox:checked").val());
                //url += ("&idarea2="+$("#selcompartida").val());
                //url += ("&probabilidad="+$("#probabilidad").val());
                //url += ("&grupo="+$("#grupo").val());
				//alert("Entre"+url);
			
          	if(fieldsRequired()){
				//alert("Entre"+url_a);
				alert("Entre"+url);
            //    executeAjaxJSON(url, false, null);
            //        editing2 = false;
            //        adding2 = false;
            //        edicionOportunidad(editing2);
            //        jQuery("#filelist2").trigger("reloadGrid");
            //        jQuery("#list1").trigger("reloadGrid");
            //        jQuery("#list2").trigger("reloadGrid");
            //        $("#detail2").html("");
            //        jQuery("#list2").restoreRow(lastsel2).setGridState("visible");
            }else{
            	var fAceptar = function(){
                	$('#dialogMain').dialog("close");
                }
                jAlert(true,true,"Existen campos obligatorios vac&iacute;os",fAceptar);
            }
       // }
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        }); */

</script>

<?
include("includes/funciones.php");  
$mysql=conexion_db();

//Carga el combo de perfiles
//===========================
$sql = "   SELECT id_perfil, tx_nombre ";
$sql.= "     FROM tbl_perfil ";
$sql.= " ORDER BY tx_nombre ";
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
{	
	$TheCatalogoPerfil[] = array(
		'id_perfil'=>$row["id_perfil"],
	  	'tx_nombre'=>$row["tx_nombre"]
	);
} 	

//Carga el combo de entidades
//===========================
$sql = "   SELECT id_entidad, tx_nombre ";
$sql.= "     FROM tbl_entidad ";
$sql.= " ORDER BY tx_nombre ";
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
{	
	$TheCatalogoEntidad[] = array(
   		'id_entidad'=>$row["id_entidad"],
  		'tx_nombre'=>$row["tx_nombre"]
	);
} 
?>
<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;">
    <div class="ui-widget-header align-center ">CONFIGURACION DE PERFILES</div>
    <div class="ui-jqdialog-content ui-widget-content">
    <form id="opForm" action="">
    	<table cellspacing="2px" border="0" cellpadding="0" width="100%">
        	<tbody>
            	
                <tr>
                	<td width="10%" class="ui-state-default">Perfil:</td>
                    <td width="60%">
                    <select id="selperfil" name="selperfil" onchange="openPerfil($('#selperfil').val())";>                            
                    	<option value="0" class="">--- S e l e c c i o n e ---</option>                            
                        <?
                        	for ($i=0; $i < count($TheCatalogoPerfil); $i++)	{         			 
								while ($elemento = each($TheCatalogoPerfil[$i]))					  		
									$id_perfil=$TheCatalogoPerfil[$i]['id_perfil'];		
									$tx_nombre=$TheCatalogoPerfil[$i]['tx_nombre'];	
									echo "<option value=$id_perfil>$tx_nombre</option>";	
							}
						?>
                    </select>                    </td>
                    <td width="10%"></td>
                </tr>
                <tr>                 	
                    <td width="100%" colspan="3" valign="top" id="divOpciones"></td>
                    
                </tr>
           	</tbody>    
     	</table>     
   	</form>
    </div>    
<?
	mysqli_close($mysql);	
?>
  