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
		
		validText(false, $("#cap_tipo"), $("#errcap_tipo"), 1);
		validText(false, $("#cap_marca"), $("#errcap_marca"), 1);
		validText(false, $("#cap_modelo"), $("#errcap_modelo"), 1);
						
		if( !validText(false, $("#cap_tipo"), $("#errcap_tipo"), 1) || 
			!validText(false, $("#cap_marca"), $("#errcap_marca"), 1) ||
			!validText(false, $("#cap_modelo"), $("#errcap_modelo"), 1) )error = false;
        		 
		return error;
	}
	
	$("#btnSave1").click(function(){     
	 
		if(fieldsRequiredLista()){	
			var url = "process_telefonia.php?";
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
	 
	$("#cap_tipo").autocomplete("process_telefonia.php?dispatch=find&campo=tipo",{
    	minChars: 1,
        max: 5,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#cap_tipo").val(item[0]);
   	});
	
	$("#cap_marca").autocomplete("process_telefonia.php?dispatch=find&campo=marca",{
    	minChars: 1,
        max: 5,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#cap_marca").val(item[0]);
   	});
	
	$("#cap_modelo").autocomplete("process_telefonia.php?dispatch=find&campo=modelo",{
    	minChars: 1,
        max: 5,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#cap_modelo").val(item[0]);
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
		$sql.= "   FROM tbl_telefonia  ";
		$sql.= "  WHERE id_telefonia	= $id  ";
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoTelefonia[] = array(
				'ac_telefonia'		=>$row["id_telefonia"],
				'tx_tipo'			=>$row["tx_tipo"],
				'tx_marca'			=>$row["tx_marca"],	
				'tx_modelo'			=>$row["tx_modelo"],	
				'tx_indicador'		=>$row["tx_indicador"],
				'fh_mod'			=>$row["fh_mod"],
				'id_usuariomod'		=>$row["id_usuariomod"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoTelefonia); $i++)	{         			 
			while ($elemento = each($TheCatalogoTelefonia[$i]))					  		
				$ac_telefonia	=$TheCatalogoTelefonia[$i]['ac_telefonia'];		
				$tx_tipo		=$TheCatalogoTelefonia[$i]['tx_tipo'];		
				$tx_marca		=$TheCatalogoTelefonia[$i]['tx_marca'];				
				$tx_modelo		=$TheCatalogoTelefonia[$i]['tx_modelo'];
				$tx_indicador	=$TheCatalogoTelefonia[$i]['tx_indicador'];
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
       			<!-- <td colspan="5" class="ui-state-default fontMedium align-center"><? //echo $titulo ?></td>  -->
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
                	<script>setStatus();</script>
                </td>
                <td></td>
           	</tr>
          	
            <tr>
            	<td width="20%" class="ui-state-default">Tipo:</td>
              	<td width="60%"><input name="cap_tipo" id="cap_tipo" type="text" size="60" title="Equipo" value="<? echo $tx_tipo ?>" /></td>
              	<td width="20%"><div id="errcap_tipo" style="float:left;"></div></td>                        
            </tr>
            <tr>
            	<td class="ui-state-default">Marca:</td>
                <td><input name="cap_marca" id="cap_marca" type="text" size="60" title="Marca" value="<? echo $tx_marca ?>" /></td>
                <td width="20%"><div id="errcap_marca" style="float:left;"></div></td>   
            </tr>
            <tr>
              	<td class="ui-state-default">Modelo:</td>
              	<td><input name="cap_modelo" id="cap_modelo" type="text" size="60" title="Modelo" value="<? echo $tx_modelo ?>" /></td>
              	<td width="20%"><div id="errcap_modelo" style="float:left;"></div></td>
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