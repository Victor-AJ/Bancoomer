<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">

	//jQuery(function($){
	//jQuery(document).ready(function(){
	   //$("#fecha").mask("99/99/9999");
	   //$("#cap_fax").mask("(999) 99-99-99-99");
	   //$("#cap_telefono1").mask("(999) 99-99-99-99");
	   //$("#cap_telefono2").mask("(999) 99-99-99-99");
	   //$("#cap_celular1").mask("04499 99-99-99-99");
	   //$("#cap_celular2").mask("04499 99-99-99-99");
	//});
	
	function fieldsRequiredLista(){
	
		var error = true;
		
		validText(false, $("#cap_proveedor"), $("#errcap_proveedor"), 1);
		validText(false, $("#cap_proveedor_corto"), $("#errcap_proveedor_corto"), 1);
				
		if( !validText(false, $("#cap_proveedor"), $("#errcap_proveedor"), 1)	||
			!validText(false, $("#cap_proveedor_corto"), $("#errcap_proveedor_corto"), 1)	
		) error = false;	
        		 
		return error;
	}
	
	$("#btnSave1").click(function(){     
	 
		if(fieldsRequiredLista()){	
			var url = "process_proveedores.php?";
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
	
	if ($dispatch=="insert") {
	
		$titulo 		= "ALTA";
		$tx_pagina 		= "http://www.";
		$tx_correo1		= "@";	
		$tx_correo2		= "@";
		$tx_indicador	= "1";
	
 	} else if ($dispatch=="save") {
		
		$titulo 		= "MODIFICACION";
	
		//Carga la informacion para la actualizacion
		//==========================================
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_proveedor  ";
		$sql.= "  WHERE id_proveedor	= $id  ";
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoProveedor[] = array(
				'tx_proveedor'		=>$row["tx_proveedor"],
				'tx_proveedor_corto'=>$row["tx_proveedor_corto"],
				'tx_rfc'			=>$row["tx_rfc"],
				'tx_descripcion'	=>$row["tx_descripcion"],
				'tx_contrato'		=>$row["tx_contrato"],
				'tx_extranjero'		=>$row["tx_extranjero"],
				'tx_iva'			=>$row["tx_iva"],
				'tx_gps'			=>$row["tx_gps"],
				'tx_direccion'		=>$row["tx_direccion"],
				'tx_pagina'			=>$row["tx_pagina"],
				'tx_fax'			=>$row["tx_fax"],
				'tx_contacto1'		=>$row["tx_contacto1"],
				'tx_puesto1'		=>$row["tx_puesto1"],
				'tx_telefono1'		=>$row["tx_telefono1"],
				'tx_celular1'		=>$row["tx_celular1"],
				'tx_correo1'		=>$row["tx_correo1"],
				'tx_contacto2'		=>$row["tx_contacto2"],
				'tx_puesto2'		=>$row["tx_puesto2"],
				'tx_telefono2'		=>$row["tx_telefono2"],
				'tx_celular2'		=>$row["tx_celular2"],
				'tx_correo2'		=>$row["tx_correo2"],
				'tx_indicador'		=>$row["tx_indicador"],
				'tx_indicador'		=>$row["tx_indicador"],
				'fh_mod'			=>$row["fh_mod"],
				'id_usuariomod'		=>$row["id_usuariomod"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoProveedor); $i++)	{         			 
			while ($elemento = each($TheCatalogoProveedor[$i]))					  		
				$tx_proveedor		=$TheCatalogoProveedor[$i]['tx_proveedor'];		
				$tx_proveedor_corto	=$TheCatalogoProveedor[$i]['tx_proveedor_corto'];				
				$tx_rfc				=$TheCatalogoProveedor[$i]['tx_rfc'];
				$tx_descripcion		=$TheCatalogoProveedor[$i]['tx_descripcion'];
				$tx_contrato		=$TheCatalogoProveedor[$i]['tx_contrato'];
				$tx_extranjero		=$TheCatalogoProveedor[$i]['tx_extranjero'];
				$tx_iva				=$TheCatalogoProveedor[$i]['tx_iva'];
				$tx_gps				=$TheCatalogoProveedor[$i]['tx_gps'];
				$tx_direccion		=$TheCatalogoProveedor[$i]['tx_direccion'];				
				$tx_pagina			=$TheCatalogoProveedor[$i]['tx_pagina'];
				$tx_fax				=$TheCatalogoProveedor[$i]['tx_fax'];				
				$tx_contacto1		=$TheCatalogoProveedor[$i]['tx_contacto1'];
				$tx_puesto1			=$TheCatalogoProveedor[$i]['tx_puesto1'];
				$tx_telefono1		=$TheCatalogoProveedor[$i]['tx_telefono1'];
				$tx_celular1		=$TheCatalogoProveedor[$i]['tx_celular1'];
				$tx_correo1			=$TheCatalogoProveedor[$i]['tx_correo1'];
				$tx_contacto2		=$TheCatalogoProveedor[$i]['tx_contacto2'];
				$tx_puesto2			=$TheCatalogoProveedor[$i]['tx_puesto2'];
				$tx_telefono2		=$TheCatalogoProveedor[$i]['tx_telefono2'];
				$tx_celular2		=$TheCatalogoProveedor[$i]['tx_celular2'];
				$tx_correo2			=$TheCatalogoProveedor[$i]['tx_correo2'];
				$tx_indicador		=$TheCatalogoProveedor[$i]['tx_indicador'];
		} 	
	}	
	
	if ($tx_extranjero == '1')
	
	
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
                <td colspan="5" class="ui-state-highlight" align="center" style="font-family:Verdana,Arial,sans-serif;font-size: 13px;font-weight:bold;"><? echo $titulo ?></td>  
         	</tr>
         	<tr>                     	
         		<td colspan="5"><? //echo $dispatch." ".$id ?></td>
          	</tr>                
            <tr>
              <td colspan="3" class="ui-state-default fontMedium align-center">DATOS GENERALES</td>
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
            	<td width="20%" class="ui-state-default">Raz&oacute;n Social:</td>
                <td width="60%">
                	<input name="cap_proveedor" id="cap_proveedor" type="text" size="60" title="Nombre del Proveedor" value="<? echo $tx_proveedor ?>" />                </td>
            	<td width="20%"><div id="errcap_proveedor" style="float:left;"></div></td>
           	</tr>
            <tr>
            	<td width="20%" class="ui-state-default">Proveedor:</td>
              <td width="60%"><input name="cap_proveedor_corto" id="cap_proveedor_corto" type="text" size="60" title="Nombre corto del Proveedor" value="<? echo $tx_proveedor_corto ?>" /></td>
                <td width="20%"><div id="errcap_proveedor_corto" style="float:left;"></div></td>                        
            </tr>
            <tr>
              <td class="ui-state-default">RFC:</td>
              <td><input name="cap_rfc" id="cap_rfc" type="text" size="60" title="RFC" value="<? echo $tx_rfc ?>" /></td>
              <td></td>
            </tr>
            <tr>
              <td class="ui-state-default">Descripci&oacute;n del Servicio:</td>
              <td><input name="cap_descripcion" id="cap_descripcion" type="text" size="60" title="Descripci&oacute;n del Servicio" value="<? echo $tx_descripcion ?>" /></td>
              <td></td>
            </tr>
            <tr>
              <td class="ui-state-default">N&uacute;mero de Contrato:</td>
              <td><input name="cap_contrato" id="cap_contrato" type="text" size="60" title="N&uacute;mero de Contrato" value="<? echo $tx_contrato ?>" /></td>
              <td></td>
            </tr>
            <tr>
            	<td class="ui-state-default">Proveedor Extranjero:</td>
              	<td>
              	<select id="sel_extranjero" name="sel_extranjero">
                	<?
					if ($tx_extranjero == '1')
					{							
					?> 
                   		<option value="1" selected="selected">SI</option>              	                 
                    	<option value="0">NO</option>                       
                    <?
					} else {
					?>                                       
                    	<option value="0" selected="selected">NO</option>
                       	<option value="1">SI</option>              	  
                    <?
					}
					?>                
              	</select>                </td>
              	<td></td>
            </tr>
            <tr>
            	<td class="ui-state-default">Genera IVA:</td>
              	<td>
              	<select id="sel_iva" name="sel_iva">
                	<?
					if ($tx_iva == '1')
					{							
					?>     
                       	<option value="1" selected="selected">SI</option>   
                      	<option value="0">NO</option>                    
                    <?
					} else {
					?>                                       
                    	<option value="0" selected="selected">NO</option>
                       	<option value="1">SI</option>              	  
                    <?
					}
					?>                
              	</select>
                </td>
              	<td></td>
            </tr>
            <tr>
              	<td class="ui-state-default">N&uacute;mero de GPS:</td>
              	<td>
              		<input name="cap_gps" id="cap_gps" type="text" size="60" title="N&uacute;mero GPS" value="<? echo $tx_gps ?>" />
                </td>
              	<td></td>
            </tr>
            <tr>
            	<td class="ui-state-default">Direcci&oacute;n:</td>
                <td>
                	<input name="cap_direccion" id="cap_direccion" type="text" size="60" title="Direcci&oacute;n" value="<? echo $tx_direccion ?>" />
                </td>
                <td></td>
            </tr>
            <tr>
            	<td class="ui-state-default">Pagina WEB:</td>
                <td>
                	<input name="cap_pagina" id="cap_pagina" type="text" size="60" title="Pagina Web" value="<? echo $tx_pagina ?>" />
                </td>
                <td></td>
          	</tr>
           	<tr>
            	<td class="ui-state-default">Fax:</td>
                <td>
                	<input name="cap_fax" id="cap_fax" type="text" size="60" title="Fax" value="<? echo $tx_fax ?>" />
                </td>
              	<td></td>
           	</tr>
            <tr>
                <td colspan="3" class="ui-state-default fontMedium align-center">DATOS CONTACTO 1</td>
           	</tr>
            <tr>
            	<td class="ui-state-default">Nombre:</td>
                <td>
                	<input name="cap_contacto1" id="cap_contacto1" type="text" size="60" title="Nombre del Contacto" value="<? echo $tx_contacto1 ?>" /></td>
                <td></td>
           	</tr>
            <tr>
            	<td class="ui-state-default">Puesto:</td>
                <td>
                	<input name="cap_puesto1" id="cap_puesto1" type="text" size="60" title="Puesto" value="<? echo $tx_puesto1 ?>" /></td>
                <td></td>
           	</tr>
            <tr>
            	<td class="ui-state-default">Tel&eacute;fono:</td>
                <td>
                	<input name="cap_telefono1" id="cap_telefono1" type="text" size="60" title="Tel&eacute;fono" value="<? echo $tx_telefono1 ?>" onkeyup="validarCampoEnteros(this,0,9999999999999);" /></td>
                <td></td>
            </tr>
           	<tr>
            	<td class="ui-state-default">Celular:</td>
                <td><input name="cap_celular1" id="cap_celular1" type="text" size="60" title="Celular" value="<? echo $tx_celular1 ?>" onkeyup="validarCampoEnteros(this,0,9999999999999);"/></td>
                <td></td>
           	</tr>
           	<tr>
            	<td class="ui-state-default">Correo</td>
                <td>
                	<input name="cap_correo1" id="cap_correo1" type="text" size="60" title="Correo" value="<? echo $tx_correo1 ?>" /></td>
                <td></td>
           	</tr>
            <tr>
            	<td colspan="3" class="ui-state-default fontMedium align-center">DATOS CONTACTO 2</td>
           	</tr>
            <tr>
            	<td class="ui-state-default">Nombre:</td>
                <td>
                	<input name="cap_contacto2" id="cap_contacto2" type="text" size="60" title="Nombre de Contacto" value="<? echo $tx_contacto2 ?>" /></td>
                <td></td>
            </tr>
            <tr>
               	<td class="ui-state-default">Puesto:</td>
                <td>
                	<input name="cap_puesto2" id="cap_puesto2" type="text" size="60" title="Puesto" value="<? echo $tx_puesto2 ?>" /></td>
                <td></td>
           	</tr>
            <tr>
            	<td class="ui-state-default">Telefono:</td>
                <td>
                	<input name="cap_telefono2" id="cap_telefono2" type="text" size="60" title="Tel&eacute;fono" value="<? echo $tx_telefono2 ?>" onkeyup="validarCampoEnteros(this,0,9999999999999);" /></td>
                <td></td>
           	</tr>
            <tr>
            	<td class="ui-state-default">Celular:</td>
                <td><input name="cap_celular2" id="cap_celular2" type="text" size="60" title="Celular" value="<? echo $tx_celular2 ?>" onkeyup="validarCampoEnteros(this,0,9999999999999);" /></td>
                <td></td>
           	</tr>
           	<tr>
            	<td class="ui-state-default">Correo:</td>
                <td>
                	<input name="cap_correo2" id="cap_correo2" type="text" size="60" title="Ubicaci&oacute;n" value="<? echo $tx_correo2 ?>" /></td>
                <td></td>
          	</tr>
            <tr>
              	<td colspan="3" class="ui-state-default">&nbsp;</td>
           	</tr>
            <tr>                     	
              	<td colspan="5">&nbsp;</td>
            </tr>
            <tr id="Act_Buttons">
            	<td class="EditButton ui-widget-content" colspan="5" style="text-align:center">                            
                	<a id="btnSave1" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left ui-pg-div" href="javascript:void(0)" style="font-size:smaller;">
                    Guardar
                    <span class="ui-icon ui-icon-disk"/></span></a>
                    <a id="btnUndo1" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Cancelar
                    <span class="ui-icon ui-icon-cancel"/></span></a>               	</td>
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