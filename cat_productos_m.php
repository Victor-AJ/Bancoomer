<?
//header("Cache-Control: no-cache, must-revalidate"); 
//header("Pragma: no-cache"); 
session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">	
	
	function fieldsRequiredLista()
		{


		var error = true;		
		validText(false, $("#cap_producto"), $("#errcap_producto"), 1);
		validText(false, $("#cap_producto_corto"), $("#errcap_producto_corto"), 1);
		validText(false, $("#cap_descripcion"), $("#errcap_descripcion"), 1);
		validText(false, $("#cap_descripcion_corta"), $("#errcap_descripcion_corta"), 1);
		validText(false, $("#cap_precio"), $("#errcap_precio"), 1);
		validSelect($("#cap_proveedor"), $("#errcap_proveedor"));
		validSelect($("#cap_moneda"), $("#errcap_moneda"));

		//<CUENTASCONT: se agrega CAMPO DE CUENTA  > 
		validSelect($("#cap_cuenta"), $("#errcap_cuenta"));

		if( !validText(false, $("#cap_producto"), $("#errcap_producto"), 1) || !validText(false, $("#cap_producto_corto"), $("#errccap_producto_corto"), 1) ||
			!validText(false, $("#cap_descripcion"), $("errcap_descripcion"), 1) ||!validText(false, $("#cap_descripcion_corta"), $("errcap_descripcion_corta"), 1) ||
			!validText(false, $("#cap_precio"), $("errcap_precio"), 1) ||	!validSelect($("#cap_proveedor"), $("#errcap_proveedor")) ||
			!validSelect($("#cap_moneda"), $("#errcap_moneda"))    ||	!validSelect($("#cap_cuenta"), $("#errcap_cuenta"))   ) 
					error = false;

		return error;
		}
	
	$("#btnSave1").click(function()
								{     
	 							if(fieldsRequiredLista())
		 							{	
									var url = "process_productos.php?";
         							url += $("#opForm1").serialize(); 
									//alert (url);		
									var func = function(data)
										{					   			
											var fAceptar = function()
											{
											$('#dialogMain').dialog("close");
											};
											if(data.error == true)
												{						
												if(data.message != null)
													{							
													jAlert(true,true,data.message,fAceptar);
													}
												else
													{
													logout();
													}
												} 
											else 
												{						
						 						if(data.message != null)
							 						{							
													jAlert(true,false,data.message,fAceptar);
													jQuery("#list1").trigger("reloadGrid");
													$("#divAlta").html("");
		            								jQuery("#list1").restoreRow(lastsel).setGridState("visible");    
													}
												}	

										};	
									//alert (url);						
									executeAjax("post", false ,url, "json", func);	
        							}
        						else
            						{
          								var fAceptar = function()
          								{
               							$('#dialogMain').dialog("close");
            							};
            							jAlert(true,true,"Existen campos obligatorios vac&iacute;os",fAceptar);
        							}   
     							}
							);

	$("#btnSave1").hover(function(){ $(this).addClass("ui-state-hover"); },function(){ $(this).removeClass("ui-state-hover"); });
	 
	 $("#btnUndo1").click(function()
			 						{   	 	
									$("#divAlta").html("");
        							jQuery("#list1").restoreRow(lastsel).setGridState("visible");    
	 								}
						);
	 $("#btnUndo1").hover(function(){$(this).addClass("ui-state-hover");},function(){ $(this).removeClass("ui-state-hover"); });
	 

	 $(function()
			 	{
     			$('input:text').setMask();
     			}
		);

</script>

<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	//Recibo variables
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

	//	<CUENTASCONT: se agrega LISTADO  DE CUENTA  > 
	//Catalogo de cuentas contables
	//===========================

	$sql = " select id as id_cuenta, concat(tx_valor ,' : ' , tx_observaciones ) as tx_cuenta from tbl45_catalogo_global ";
	$sql.= " where substr(tx_clave,1,15)=  'CUENTA_CONTABLE'   and tx_indicador=1 ";
	
	//echo " sql ",$sql;
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoCuenta[] = array( 'id_cuenta'=>$row["id_cuenta"], 'tx_cuenta'=>$row["tx_cuenta"] );
	}
	
	
	//Catalogo de Monedas
	//===========================
	$sql = "   SELECT id_moneda, tx_moneda ";
	$sql.= "     FROM tbl_moneda where tx_indicador=1 ";
	$sql.= " ORDER BY tx_moneda ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoMoneda[] = array(
			'id_moneda'=>$row["id_moneda"],
			'tx_moneda'=>$row["tx_moneda"]
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
		$sql.= "   FROM tbl_producto  ";
		$sql.= "  WHERE id_producto	= $id  ";
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
			//<CUENTASCONT: se agrega CAMPO DE CUENTA  > 
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoProducto[] = array(
				'ac_proveedor'			=>$row["id_proveedor"],
				'ac_moneda'				=>$row["id_moneda"],
				'tx_producto'			=>$row["tx_producto"],
				'tx_producto_corto' 	=>$row["tx_producto_corto"],
				'tx_descripcion'		=>$row["tx_descripcion"],
				'tx_descripcion_corta'	=>$row["tx_descripcion_corta"],
				'fl_precio'				=>$row["fl_precio"],	
				'fl_precio_mxn'			=>$row["fl_precio_mxn"],				
				'fl_precio_eur'			=>$row["fl_precio_eur"],
				'in_licencia'			=>$row["in_licencia"],				
				'tx_indicador'			=>$row["tx_indicador"],
				'fh_mod'				=>$row["fh_mod"],
				'id_usuariomod'			=>$row["id_usuariomod"],
				'ac_cuenta_contable'			=>$row["id_cuenta_contable"]
							
			);
		} 
		//<CUENTASCONT: se agrega CAMPO DE CUENTA  > 
		for ($i=0; $i < count($TheCatalogoProducto); $i++)	{         			 
			while ($elemento = each($TheCatalogoProducto[$i]))					  		
				$ac_proveedor		=$TheCatalogoProducto[$i]['ac_proveedor'];		
				$ac_moneda			=$TheCatalogoProducto[$i]['ac_moneda'];		
				$tx_producto		=$TheCatalogoProducto[$i]['tx_producto'];		
				$tx_producto_corto	=$TheCatalogoProducto[$i]['tx_producto_corto'];
				$tx_descripcion		=$TheCatalogoProducto[$i]['tx_descripcion'];				
				$tx_descripcion_corta=$TheCatalogoProducto[$i]['tx_descripcion_corta'];
				$fl_precio			=$TheCatalogoProducto[$i]['fl_precio'];								
				$fl_precio_mxn		=$TheCatalogoProducto[$i]['fl_precio_mxn'];
				$fl_precio_eur		=$TheCatalogoProducto[$i]['fl_precio_eur'];									
				$in_licencia		=$TheCatalogoProducto[$i]['in_licencia'];								
				$tx_indicador		=$TheCatalogoProducto[$i]['tx_indicador'];
				$ac_cuenta_contable=$TheCatalogoProducto[$i]['ac_cuenta_contable'];		
		} 	
		
		if ($fl_precio==0 && $fl_precio_eur==0) 
			$in_precio = $fl_precio_mxn;
		if ($fl_precio_mxn==0 && $fl_precio_eur==0) 
			$in_precio = $fl_precio;
		if ($fl_precio==0 && $fl_precio_mxn==0)
			$in_precio = $fl_precio_eur; 		
	
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
            	<td class="ui-state-default">Producto:</td>
                <td>
                	<input name="cap_producto" id="cap_producto" type="text" size="60" title="Producto" value="<? echo $tx_producto ?>" /></td>
                <td width="20%"><div id="errcap_producto" style="float:left;"></div></td>   
            </tr>
            <tr>
            	<td class="ui-state-default">Producto (Nombre Corto):</td>
                <td>
                	<input name="cap_producto_corto" id="cap_producto_corto" type="text" size="60" title="Producto (Nombre Corto)" value="<? echo $tx_producto_corto ?>" /></td>
                <td width="20%"><div id="errcap_producto_corto" style="float:left;"></div></td>   
          	</tr>
           	
           	 <tr>
            	<td class="ui-state-default">Cuenta contable:</td>
       	    	<td>
                	<select id="cap_cuenta" name="cap_cuenta">
                	<option value="0" class="">--- S e l e c c i o n e ---</option>
                	<?
						for ($i=0; $i < count($TheCatalogoCuenta); $i++)	{         			 
						while ($elemento = each($TheCatalogoCuenta[$i]))					  		
							$id_cuenta	=$TheCatalogoCuenta[$i]['id_cuenta'];		
							$tx_cuenta	=$TheCatalogoCuenta[$i]['tx_cuenta'];	
							if ($id_cuenta == $ac_cuenta_contable ) 
								echo "<option value=$id_cuenta selected='selected'>$tx_cuenta</option>";
							else 
								echo "<option value=$id_cuenta>$tx_cuenta</option>";	
						}
					?>
              		</select>                </td>
                <td width="20%"><div id="errcap_cuenta" style="float:left;"></div></td>   
           	</tr>
            
           	
           	
           	<tr>
            	<td class="ui-state-default">Descripci&oacute;n:</td>
                <td>
                	<input name="cap_descripcion" id="cap_descripcion" type="text" size="60" title="Descripci&oacute;n" value="<? echo $tx_descripcion ?>" /></td>
                <td width="20%"><div id="errcap_descripcion" style="float:left;"></div></td>   
           	</tr>
            
            
            
            
            
            <tr>
            	<td class="ui-state-default">Descripci&oacute;n Corta:</td>
                <td><input name="cap_descripcion_corta" id="cap_descripcion_corta" type="text" size="60" title="Descripci&oacute;n Corta" value="<? echo $tx_descripcion_corta ?>" /></td>
                <td width="20%"><div id="errcap_descripcion_corta" style="float:left;"></div></td>   
           	</tr>
            <tr>
            	<td class="ui-state-default">Precio:</td>
              	<td>
                	<input id="cap_precio" name="cap_precio" type="text" class="textbox" size="60" alt="decimal-us" value="<? echo $in_precio ?>" title="Precio"/>
				</td>
                <td width="20%"><div id="errcap_precio" style="float:left;"></div></td>   
           	</tr>
             <tr>
            	<td class="ui-state-default">Moneda:</td>
       	    	<td>
                	<select id="cap_moneda" name="cap_moneda">
                	<option value="0" class="">--- S e l e c c i o n e ---</option>
                	<?
						for ($i=0; $i < count($TheCatalogoMoneda); $i++)	{         			 
						while ($elemento = each($TheCatalogoMoneda[$i]))					  		
							$id_moneda	=$TheCatalogoMoneda[$i]['id_moneda'];		
							$tx_moneda	=$TheCatalogoMoneda[$i]['tx_moneda'];	
							if ($id_moneda == $ac_moneda ) echo "<option value=$id_moneda selected='selected'>$tx_moneda</option>";
							else echo "<option value=$id_moneda>$tx_moneda</option>";	
						}
					?>
              		</select>                </td>
                <td width="20%"><div id="errcap_moneda" style="float:left;"></div></td>   
           	</tr>
            <tr>
            	<td class="ui-state-default">Licencia:</td>
              	<td>
                	<select id="sel_licencia" name="sel_licencia">
                    <? 						
						if ($in_licencia == 1) {
							echo "<option value='1' selected='selected'>SI</option>";
							echo "<option value='0'>NO</option>";
						} else {  
							echo "<option value='0' selected='selected'>NO</option>";
							echo "<option value='1'>SI</option>";
						}						
					?>
                    </select>
				</td>
                <td><div id="errsel_licencia" style="float:left;"></div></td>   
           	</tr>
            <tr>                     	
              	<td colspan="5">&nbsp;</td>
            </tr>
            <tr id="Act_Buttons">
            	<td class="EditButton ui-widget-content" colspan="5" style="text-align:center">                            
                	<a id="btnSave1" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Guardar
                    <span class="ui-icon ui-icon-disk"></span></a>
                    <a id="btnUndo1" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Cancelar
                    <span class="ui-icon ui-icon-cancel"></span></a>
                </td>
           	</tr>
           	<tr>
           		<td colspan="5" class="ui-state-highlight">&nbsp;</td>                      
            </tr>
        </tbody>    
     	</table>     
   	</form>
    </div>   
</div>  
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  
