<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">	
	
	function fieldsRequiredLista(){
	
		//alert ("Entre");
	
		var error = true;		
		
		validText(false, $("#cap_cuenta"), $("#errcap_cuenta"), 1);
		validText(false, $("#cap_descripcion"), $("#errcap_descripcion"), 1);
		validSelect($("#cap_proveedor"), $("#errcap_proveedor"));
						
		if( !validText(false, $("#cap_cuenta"), $("#errcap_cuenta"), 1) || 
			!validText(false, $("#cap_descripcion"), $("#errccap_descripcion"), 1) ||
			!validSelect($("#cap_proveedor"), $("#errcap_proveedor"))) error = true;
        		 
		return error;
	}
	
	$("#btnSave1").click(function(){     
	 
		if(fieldsRequiredLista()){	
			var url = "process_cuentas.php?";
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
	
	//Resivo variables
	//===========================
	$dispatch	= $_GET['dispatch'];
	$id			= $_GET['id'];	
	
	//Catalogo de poveedores
	//===========================
	$sql = "   SELECT id_proveedor, tx_proveedor ";
	$sql.= "     FROM tbl_proveedor ";
	$sql.= " ORDER BY tx_proveedor ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			'id_proveedor'=>$row["id_proveedor"],
			'tx_proveedor'=>$row["tx_proveedor"]
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
		$sql.= "   FROM tbl_cuenta  ";
		$sql.= "  WHERE id_cuenta	= $id  ";
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoCuenta[] = array(
				'ac_proveedor'			=>$row["id_proveedor"],
				'tx_cuenta'				=>$row["tx_cuenta"],
				'tx_descripcion'		=>$row["tx_descripcion"],	
				'tx_indicador'			=>$row["tx_indicador"],
				'fh_mod'				=>$row["fh_mod"],
				'id_usuariomod'			=>$row["id_usuariomod"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoCuenta); $i++)	{         			 
			while ($elemento = each($TheCatalogoCuenta[$i]))					  		
				$ac_proveedor		=$TheCatalogoCuenta[$i]['ac_proveedor'];		
				$tx_cuenta			=$TheCatalogoCuenta[$i]['tx_cuenta'];		
				$tx_descripcion		=$TheCatalogoCuenta[$i]['tx_descripcion'];				
				$tx_indicador		=$TheCatalogoCuenta[$i]['tx_indicador'];
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
                	<script>setStatus();</script>                </td>
                <td></td>
           	</tr>
          	
            <tr>
            	<td width="20%" class="ui-state-default">Proveedor:</td>
              	<td width="60%">
              	<select id="cap_proveedor" name="cap_proveedor">
                	<option value="0" class="">--- S e l e c c i o n e ---</option>
                	<?
					for ($i=0; $i < count($TheCatalogo); $i++)	{         			 
					while ($elemento = each($TheCatalogo[$i]))					  		
						$id_proveedor	=$TheCatalogo[$i]['id_proveedor'];		
						$tx_proveedor	=$TheCatalogo[$i]['tx_proveedor'];	
						if ($id_proveedor == $ac_proveedor ) echo "<option value=$id_proveedor selected='selected'>$tx_proveedor</option>";
						else echo "<option value=$id_proveedor>$tx_proveedor</option>";	
					}
					?>
              	</select>
                </td>
                <td width="20%"><div id="errcap_proveedor" style="float:left;"></div></td>                        
            </tr>
            <tr>
            	<td class="ui-state-default">Cuenta:</td>
                <td>
                	<input name="cap_cuenta" id="cap_cuenta" type="text" size="60" title="Cuenta" value="<? echo $tx_cuenta ?>" /></td>
                <td width="20%"><div id="errcap_cuenta" style="float:left;"></div></td>   
            </tr>
            <tr>
            	<td class="ui-state-default">Descripci&oacute;n:</td>
          		<td>
                	<input name="cap_descripcion" id="cap_descripcion" type="text" size="60" title="Descripci&oacute;n" value="<? echo $tx_descripcion ?>" /></td>
                <td width="20%"><div id="errcap_descripcion" style="float:left;"></div></td>   
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
                    <span class="ui-icon ui-icon-cancel"/></a>               </td>
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