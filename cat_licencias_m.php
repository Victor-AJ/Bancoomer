<?

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">	
	
	function fieldsRequiredListaLicencias(){
	
		var error = true;		
				
		validSelect($("#sel_proveedor"), $("#errsel_proveedor"));
		validSelect($("#sel_cuenta"), $("#errsel_cuenta"));
		validSelect($("#sel_producto"), $("#errsel_producto"));
						
		if( !validSelect($("#sel_proveedor"), $("#errsel_proveedor")) ||
			!validSelect($("#sel_cuenta"), $("#errsel_cuenta")) ||
			!validSelect($("#sel_producto"), $("#errsel_producto"))) error = false;
        		 
		return error;
	}
	
	$("#btnSave1").click(function(){     
	 
		if(fieldsRequiredListaLicencias()){	
			var url = "process_licencias.php?";
         		url += $("#opLicencias").serialize(); 
		
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
						loadHtmlAjax(true, $("#divLicencias"), data.html);
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
	 	$("#divAltaLic").hide();
	 }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });
	 
	 $(function(){
     	$('input:text').setMask();
     });
	 
	 ///////////////// DEFINICION DE EVENTOS //////////////////////
	 $("#sel_proveedor").change(function () {
            $("#sel_proveedor option:selected").each(function () {
                loadHtmlAjax(false, $("#sel_producto") , "combo_producto.php?id="+$(this).val());
                loadHtmlAjax(false, $("#sel_cuenta") , "combo_cuenta.php?id="+$(this).val());
				$("#cap_descripcion").val("");	
				$("#cap_precio").val(0.00);	
				$("#cap_moneda").val("");
				$("#cap_moneda1").val("");					
     	});
     });
	 
	 $("#sel_producto").change(function () {
     	$("#sel_producto option:selected").each(function () {
		
			var url = "busca_producto.php?id="+$("#sel_producto").val();			  
			var func = function(data){					   			
	 			if(data.pasa == true){							
					$("#cap_descripcion").val(data.data1);	
					$("#cap_precio").val(data.data2);	
					$("#cap_moneda").val(data.data3);	
					$("#cap_moneda1").val(data.data3);	
				}				
			} 
			executeAjax("post", false ,url, "json", func); 						
        });
     });	 
	 
	$("#cap_reasignacion").autocomplete("process_empleados.php?dispatch=find&campo=responsable",{
    	minChars: 2,
        max: 10,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#cap_reasignacion").val(item[0]);
   	});
	 
	$("#sel_proveedor").change(function () {
    	validSelect($(this), $("#errsel_proveedor"));
    });

	$("#sel_producto").change(function () {
    	validSelect($(this), $("#errsel_producto"));
    });
	 
	$("#sel_cuenta").change(function () {
    	validSelect($(this), $("#errsel_cuenta"));
    });
	 
	$("#cap_precio").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_precio").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });

    //CAMBIO CUENTAS CONTABLES
	/*  
	$("#cap_concepto_contable").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

     $("#cap_concepto_contable").blur(function() {
     	$(this).removeClass('ui-state-focus');
     });

	 */
	 
 	 $("#cap_sid_terminal").focus(function() {
     	$(this).addClass('ui-state-focus');
     });

     $("#cap_sid_terminal").blur(function() {
     	$(this).removeClass('ui-state-focus');
     });
	 
	 $("#cap_login").focus(function() {
     	$(this).addClass('ui-state-focus');
     });

     $("#cap_login").blur(function() {
     	$(this).removeClass('ui-state-focus');
     });
	 
	 $("#cap_serial_number").focus(function() {
     	$(this).addClass('ui-state-focus');
     });

     $("#cap_serial_number").blur(function() {
     	$(this).removeClass('ui-state-focus');
     });
	 
	 $("#cap_reasignacion").focus(function() {
     	$(this).addClass('ui-state-focus');
     });

     $("#cap_reasignacion").blur(function() {
     	$(this).removeClass('ui-state-focus');
     });
	 
         	
</script>

<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	//Resivo variables
	//===========================
	$dispatch	= $_GET['dispatch'];
	$id			= $_GET['id'];	
	$id_lic		= $_GET['id_lic'];	
	
	//Catalogo de poveedores
	//===========================
	$sql = "   SELECT id_proveedor, tx_proveedor ";
	$sql.= "     FROM tbl_proveedor ";

	$sql.= " ORDER BY tx_proveedor ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoProveedores[] = array(
			'id_proveedor'=>$row["id_proveedor"],
			'tx_proveedor'=>$row["tx_proveedor"]
		);
	}
	
	//Catalogo de Monedas
	//===========================
	$sql = "   SELECT id_moneda, tx_moneda ";
	$sql.= "     FROM tbl_moneda ";

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

		//CAMBIO CUENTAS CONTABLES se quita tx_concepto_contable de query
		$sql = " SELECT id_proveedor, a.id_producto, id_cuenta, in_licencia, b.tx_descripcion, b.fl_precio, b.fl_precio_mxn, tx_moneda,  tx_login, tx_sid_terminal,  tx_serial_number, a.tx_indicador, a.fh_alta, d.tx_nombre AS usuario_mod, a.fh_mod, e.tx_nombre AS usuario_alta ";
		$sql.= "   FROM tbl_licencia a, tbl_producto b, tbl_moneda c, tbl_usuario d, tbl_usuario e ";
		$sql.= "  WHERE id_licencia		= $id_lic  ";
		$sql.= "    AND a.id_producto 	= b.id_producto ";
		$sql.= "    AND b.id_moneda 	= c.id_moneda ";
		$sql.= "    AND a.id_usuariomod = d.id_usuario ";
		$sql.= "    AND a.id_usuarioalta = e.id_usuario ";
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoLicencia[] = array(
				'id_proveedor'			=>$row["id_proveedor"],
				'id_producto'			=>$row["id_producto"],
				'id_cuenta'				=>$row["id_cuenta"],
				'in_licencia'			=>$row["in_licencia"],
				'tx_descripcion'		=>$row["tx_descripcion"],
				'fl_precio_usd'			=>$row["fl_precio"],
				'fl_precio_mxn'			=>$row["fl_precio_mxn"],
				'tx_moneda'				=>$row["tx_moneda"],
				//CAMBIO CUENTAS CONTABLES 	
				//'tx_concepto_contable'	=>$row["tx_concepto_contable"],				
				'tx_login'				=>$row["tx_login"],				
				'tx_sid_terminal'		=>$row["tx_sid_terminal"],				
				'tx_serial_number'		=>$row["tx_serial_number"],				
				'tx_indicador'			=>$row["tx_indicador"],
				'fh_alta'				=>$row["fh_alta"],
				'usuario_alta'			=>$row["usuario_alta"],				
				'fh_mod'				=>$row["fh_mod"],
				'usuario_mod'			=>$row["usuario_mod"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoLicencia); $i++)	{         			 
			while ($elemento = each($TheCatalogoLicencia[$i]))				
				$ac_proveedor			=$TheCatalogoLicencia[$i]['id_proveedor'];						  		
				$ac_producto			=$TheCatalogoLicencia[$i]['id_producto'];	
				$ac_cuenta				=$TheCatalogoLicencia[$i]['id_cuenta'];	
				$in_licencia			=$TheCatalogoLicencia[$i]['in_licencia'];	
				$fl_precio_usd			=$TheCatalogoLicencia[$i]['fl_precio_usd'];		
				$fl_precio_mxn			=$TheCatalogoLicencia[$i]['fl_precio_mxn'];		
				$tx_moneda				=$TheCatalogoLicencia[$i]['tx_moneda'];		
				$tx_descripcion			=$TheCatalogoLicencia[$i]['tx_descripcion'];
				//CAMBIO CUENTAS CONTABLES 							
				//$tx_concepto_contable	=$TheCatalogoLicencia[$i]['tx_concepto_contable'];		
				$tx_login				=$TheCatalogoLicencia[$i]['tx_login'];
				$tx_sid_terminal		=$TheCatalogoLicencia[$i]['tx_sid_terminal'];				
				$tx_serial_number		=$TheCatalogoLicencia[$i]['tx_serial_number'];
				$tx_serial_number		=$TheCatalogoLicencia[$i]['tx_serial_number'];
				$tx_indicador			=$TheCatalogoLicencia[$i]['tx_indicador'];
				$fh_alta				=$TheCatalogoLicencia[$i]['fh_alta'];
				$usuario_alta			=$TheCatalogoLicencia[$i]['usuario_alta'];
				$fh_mod					=$TheCatalogoLicencia[$i]['fh_mod'];
				$usuario_mod			=$TheCatalogoLicencia[$i]['usuario_mod'];
		} 
		
		if ($tx_moneda=="USD")
		{
			$fl_precio=$fl_precio;
		} else if ($tx_moneda=="MXN") {
			$fl_precio=$fl_precio_mxn;
		}
		
		//echo "<br>";
		//echo "cuenta",$ac_cuenta;
		//echo "<br>";

		//Catalogo de productos
		//==================================
	
		$sql = "   SELECT id_producto, tx_producto ";
		$sql.= "     FROM tbl_producto ";
		$sql.= "    WHERE id_proveedor = $ac_proveedor   ";
		$sql.= " ORDER BY tx_producto ";
	
		//echo "aaa", $sql;
	
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoProductos[] = array(
				'id_producto'	=>$row["id_producto"],
				'tx_producto'	=>$row["tx_producto"]
			);
		} 		
			
		//Catalogo de cuentas
		//==================================
	
		$sql = "   SELECT id_cuenta, tx_cuenta ";
		$sql.= "     FROM tbl_cuenta ";
		$sql.= "    WHERE id_proveedor = $ac_proveedor  ";
		$sql.= " ORDER BY tx_cuenta ";
	
		//echo "aaa", $sql;
	
		$result1 = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result1,MYSQLI_ASSOC))
		{	
			$TheCatalogoCuentas[] = array(
				'id_cuenta'	=>$row["id_cuenta"],
				'tx_cuenta'	=>$row["tx_cuenta"]
			);
		} 
	}		
?>
<br>
<div>
    <form id="opLicencias" action="">
    	<input id="id" name="id" type="hidden" value="<? echo $id ?>" />
		<input id="id_lic" name="id_lic" type="hidden" value="<? echo $id_lic ?>" />
    	<input id="dispatchLic" name="dispatchLic" type="hidden" value="<? echo $dispatch ?>" />        
	  	<table cellspacing="1px" border="0" cellpadding="0" width="100%">
   		<tbody>
   			<tr>
                <td colspan="5" class="ui-state-highlight" align="center" style="font-family:Verdana,Arial,sans-serif;font-size: 12px;font-weight:bold;"><? echo $titulo ?></td>  
         	</tr>         	                          
            <tr>
            	<td width="20%" class="ui-state-default">Proveedor:</td>
              	<td width="60%">
                	<select id="sel_proveedor" name="sel_proveedor">
                  	<option value="0" class="">--- S e l e c c i o n e ---</option>
                  	<?
						for ($i=0; $i < count($TheCatalogoProveedores); $i++)	{         			 
						while ($elemento = each($TheCatalogoProveedores[$i]))					  		
							$id_proveedor	=$TheCatalogoProveedores[$i]['id_proveedor'];		
							$tx_proveedor	=$TheCatalogoProveedores[$i]['tx_proveedor'];	
							if ($id_proveedor == $ac_proveedor ) echo "<option value=$id_proveedor selected='selected'>$tx_proveedor</option>";
							else echo "<option value=$id_proveedor>$tx_proveedor</option>";	
					}
					?>
                	</select>                </td>
              	<td width="20%"><div id="errsel_proveedor" style="float:left;"></div></td>                        
            </tr>
            <tr>
            	<td class="ui-state-default">Cuenta:</td>
                <td><select id="sel_cuenta" name="sel_cuenta">
                    <option value="0" class="">--- S e l e c c i o n e ---</option>
                    <?
						for ($i=0; $i < count($TheCatalogoCuentas); $i++)	{         			 
						while ($elemento = each($TheCatalogoCuentas[$i]))					  		
							$id_cuenta	=$TheCatalogoCuentas[$i]['id_cuenta'];		
							$tx_cuenta	=$TheCatalogoCuentas[$i]['tx_cuenta'];
							if ($id_cuenta == $ac_cuenta ) echo "<option value=$id_cuenta selected='selected'>$tx_cuenta</option>";
							else echo "<option value=$id_cuenta>$tx_cuenta</option>";	
						}
						?>
                  </select></td>
              <td width="20%"><div id="errsel_cuenta" style="float:left;"></div></td>   
            </tr>
            <tr>
            	<td class="ui-state-default">Producto:</td>
                <td><select id="sel_producto" name="sel_producto">
                  <option value="0" class="">--- S e l e c c i o n e ---</option>
                  <?
						for ($i=0; $i < count($TheCatalogoProductos); $i++)	{         			 
						while ($elemento = each($TheCatalogoProductos[$i]))					  		
							$id_producto	=$TheCatalogoProductos[$i]['id_producto'];		
							$tx_producto	=$TheCatalogoProductos[$i]['tx_producto'];	
							if ($id_producto == $ac_producto ) echo "<option value=$id_producto selected='selected'>$tx_producto</option>";
							else echo "<option value=$id_producto>$tx_producto</option>";	
						}
					?>
                </select></td>
                <td width="20%"><div id="errsel_producto" style="float:left;"></div></td>   
          	</tr>
           	<tr>
            	<td class="ui-state-default">Descripci&oacute;n:</td>
                <td>
                	<input name="cap_descripcion" id="cap_descripcion" type="text" size="80" title="Descripci&oacute;n" value="<? echo $tx_descripcion ?>" disabled="disabled"/></td>
                <td width="20%"><div id="errcap_descripcion" style="float:left;"></div></td>   
           	</tr>            
            <tr>
            	<td class="ui-state-default">Precio:</td>
              	<td><input id="cap_precio" name="cap_precio" type="text" class="textbox" size="30" alt="decimal-us" value="<? echo $fl_precio ?>" title="Precio"/></td>
              <td width="20%"><div id="errcap_precio" style="float:left;"></div></td>   
           	</tr>
            <tr>
              	<td class="ui-state-default">Moneda:</td>
               	<td><input id="cap_moneda1" name="cap_moneda1" type="text" class="textbox" size="30" value="<? echo $tx_moneda ?>" title="Moneda" disabled="disabled"/>
               	  <input id="cap_moneda" name="cap_moneda" type="hidden" class="textbox" size="20" value="<? echo $tx_moneda ?>" title="Moneda" />                </td>
            	<td>&nbsp;</td>
            </tr>
            <!--  CAMBIO CUENTAS CONTABLES -->
            <!-- 
            <tr>
               <td class="ui-state-default">Concepto Contable:</td>
               <td><input id="cap_concepto_contable" name="cap_concepto_contable" type="text" class="textbox" size="30" value="<? echo $tx_concepto_contable ?>" title="Concepto Contable"/></td>            <td>&nbsp;</td>
             </tr>
              -->
              
             <tr>
               <td class="ui-state-default">Login:</td>
               <td><input id="cap_login" name="cap_login" type="text" class="textbox" size="30" value="<? echo $tx_login ?>" title="Login"/></td>
               <td>&nbsp;</td>
             </tr>
             <tr>
               <td class="ui-state-default">SID Terminal:</td>
               <td><input id="cap_sid_terminal" name="cap_sid_terminal" type="text" class="textbox" size="30" value="<? echo $tx_sid_terminal ?>" title="SID Terminal"/></td>
               <td>&nbsp;</td>
             </tr>
             <tr>
               <td class="ui-state-default">Serial Number:</td>
               <td><input id="cap_serial_number" name="cap_serial_number" type="text" class="textbox" size="30" value="<? echo $tx_serial_number ?>" title="Serial Number"/></td>
               <td>&nbsp;</td>
             </tr>
             <? 
			   if ($dispatch=="insert") { }
               else {
			 ?>  
                 <tr>
                   <td class="ui-state-default">Fecha modifica:</td>
                   <td><? echo $fh_mod ?></td>
                   <td>&nbsp;</td>
                 </tr>
                 <tr>
                   <td class="ui-state-default">Usuario modifica:</td>
                   <td><? echo $usuario_mod ?></td>
                   <td>&nbsp;</td>
                 </tr>
                 <tr>
                   <td class="ui-state-default">Fecha alta:</td>
                   <td><? echo $fh_alta ?></td>
                   <td>&nbsp;</td>
                 </tr>
                 <tr>
                   <td class="ui-state-default">Usuario alta:</td>
                   <td><? echo $usuario_alta ?></td>
                   <td>&nbsp;</td>
                 </tr>
                   <tr>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                 </tr>
                 <tr>
                   <td class="ui-state-default"><em>Reasignaci&oacute;n de Licencia a:</em></td>
                   <td><input name="cap_reasignacion" id="cap_reasignacion" type="text" size="50" title="Nombre del Empleado para la Reasignaci&oacute;n de la Licencia" value="" /></td>
                   <td>&nbsp;</td>
                 </tr>
                
             <?
			 	}
			 ?>   
            <tr id="Act_Buttons_Lic">
            	<td class="EditButton ui-widget-content" colspan="5" style="text-align:center">                            
                	<a id="btnSave1" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Guardar
                    <span class="ui-icon ui-icon-disk"></span></a>
                    <a id="btnUndo1" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Cancelar
                    <span class="ui-icon ui-icon-cancel"></span></a>               	</td>
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