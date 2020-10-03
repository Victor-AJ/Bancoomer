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
		
		validText(false, $("#cap_proveedor"), $("#errcap_proveedor"), 1);
		validText(false, $("#cap_plan"), $("#errcap_plan"), 1);
						
		if( !validText(false, $("#cap_proveedor"), $("#errcap_proveedor"), 1) || 
			!validText(false, $("#cap_plan"), $("#errcap_plan"), 1) )error = false;
        		 
		return error;
	}
	
	$("#btnSave1").click(function(){     
	 
		if(fieldsRequiredLista()){	
			var url = "process_telefonia_planes.php?";
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
	 
	$("#cap_proveedor").autocomplete("process_telefonia_planes.php?dispatch=find&campo=proveedor",{
    	minChars: 1,
        max: 5,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#cap_proveedor").val(item[0]);
   	});
	
	$("#cap_plan").autocomplete("process_telefonia_planes.php?dispatch=find&campo=plan",{
    	minChars: 1,
        max: 5,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#cap_plan").val(item[0]);
   	});

</script>

<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	//Resivo variables
	//===========================
	$dispatch	= $_GET['dispatch'];
	$id			= $_GET['id'];		
	
	if ($dispatch=="insert") {	
		
		$titulo 		= "ALTA";		
		$tx_indicador	= "1";
	
 	} else if ($dispatch=="save") {
		
		$titulo 		= "MODIFICACION";
	
		//Carga la informacion para la actualizacion
		//==========================================
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_telefonia_plan  ";
		$sql.= "  WHERE id_telefonia_plan	= $id  ";
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoTelefonia[] = array(
				'ac_telefonia_plan'	=>$row["id_telefonia_plan"],
				'tx_proveedor'		=>$row["tx_proveedor"],
				'tx_plan'			=>$row["tx_plan"],	
				'fl_precio_mxn'		=>$row["fl_precio_mxn"],	
				'tx_descripcion'	=>$row["tx_descripcion"],	
				'tx_indicador'		=>$row["tx_indicador"],
				'fh_mod'			=>$row["fh_mod"],
				'id_usuariomod'		=>$row["id_usuariomod"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoTelefonia); $i++)	{         			 
			while ($elemento = each($TheCatalogoTelefonia[$i]))					  		
				$ac_telefonia_plan	=$TheCatalogoTelefonia[$i]['ac_telefonia_plan'];		
				$tx_proveedor		=$TheCatalogoTelefonia[$i]['tx_proveedor'];		
				$tx_plan			=$TheCatalogoTelefonia[$i]['tx_plan'];				
				$fl_precio_mxn		=$TheCatalogoTelefonia[$i]['fl_precio_mxn'];
				$tx_descripcion		=$TheCatalogoTelefonia[$i]['tx_descripcion'];
				$tx_indicador		=$TheCatalogoTelefonia[$i]['tx_indicador'];
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
                	<script>setStatus();</script>                </td>
                <td></td>
           	</tr>
          	
            <tr>
            	<td width="20%" class="ui-state-default">Proveedor:</td>
              	<td width="60%"><input name="cap_proveedor" id="cap_proveedor" type="text" size="40" title="Proveedor" value="<? echo $tx_proveedor ?>" /></td>
              	<td width="20%"><div id="errcap_proveedor" style="float:left;"></div></td>                        
            </tr>
            <tr>
            	<td class="ui-state-default">Plan:</td>
              <td><input name="cap_plan" id="cap_plan" type="text" size="40" title="Plan" value="<? echo $tx_plan ?>" /></td>
                <td width="20%"><div id="errcap_plan" style="float:left;"></div></td>   
            </tr>
            <tr>
              <td class="ui-state-default">Precio:</td>
              <td><input name="cap_precio" id="cap_precio" type="text" size="40" title="Precio en MXN" alt="decimal-us" value="<? echo $fl_precio_mxn ?>" /></td>
              <td><div id="errcap_precio" style="float:left;"></div></td>
            </tr>
            <tr>
              <td class="ui-state-default">Descripci&oacute;n:</td>
              <td><input name="cap_descripcion" id="cap_descripcion" type="text" size="80" title="Descripci&oacute;n" value="<? echo $tx_descripcion ?>" /></td>
              <td>&nbsp;</td>
            </tr>
                        
            <tr>
              	<td colspan="3" class="ui-state-default">&nbsp;</td>
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