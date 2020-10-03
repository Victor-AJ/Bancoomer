<?

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">	
	
	function fieldsRequiredListaFacturaDet(){
	
		var error = true;		
		
		validSelect($("#sel_producto"), $("#errsel_producto"));
		if( !validSelect($("#sel_producto"), $("#errsel_producto"))) error = false;
        		 
		return error;
	}
	
	$("#btnSaveFacDet").click(function(){     
	 
		if(fieldsRequiredListaFacturaDet()){			
			
			var id1="id_factura="+$("#id_factura").val();	
			var id2="&id_cuenta="+$("#id_cuenta").val();	
			var id6="&fl_precio_usd_cabecera="+$("#fl_precio_usd_cabecera").val();
			var id7="&fl_precio_mxn_cabecera="+$("#fl_precio_mxn_cabecera").val();				
			var id8="&fl_precio_eur_cabecera="+$("#fl_precio_eur_cabecera").val();				
		
			var url = "process_facturas_detalle.php?"+id1+id2+id6+id7+id8+"&";
         		url += $("#opFacturaDetalleMantenimiento").serialize(); 

			//alert("URL"+url);	
		
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
						$("#divAltaFac").hide();
						loadHtmlAjax(true, $("#divAltaFac"), data.html);
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
	 
	 $("#btnUndoFacDet").click(function(){  
	 	$("#divCapturaDetalle").hide();
	 }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });
	 
	 $(function(){
     	$('input:text').setMask();
     });
	 
	 ///////////////// DEFINICION DE EVENTOS //////////////////////
	
	$("#sel_producto").change(function () {
    	$("#sel_producto option:selected").each(function () {		
			var url = "busca_producto.php?id="+$("#sel_producto").val();			  
			var func = function(data){					   			
	 			if(data.pasa == true){							
					$("#cap_precio").val(data.data2);	
					$("#cap_moneda").val(data.data3);	
					$("#cap_moneda1").val(data.data3);	
				}				
			} 
			executeAjax("post", false ,url, "json", func); 						
        });
    });	 	  
	 
 	$("#cap_precio").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_precio").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });

	//	CAMBIO CUENTA CONTABLE
    /*
 	$("#cap_concepto_contable").focus(function() {
     	$(this).addClass('ui-state-focus');
    });
	
	
    $("#cap_concepto_contable").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
    
	*/

	     	
	$("#cap_notas").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_notas").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
			
</script>
<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	# ============================================
	# Recibo variables
	# ============================================
	$id_empleado			= $_GET['id'];	
	$id_factura_detalle		= $_GET['id_factura_detalle'];	
	$id_factura				= $_GET['id_factura'];	
	$id_cuenta				= $_GET['id_cuenta'];	
	$tx_cuenta				= $_GET['tx_cuenta'];	
	$id_proveedor			= $_GET['id_proveedor'];
	$tx_proveedor_corto		= $_GET['tx_proveedor_corto'];	
	$dispatch				= $_GET['dispatch'];
	$fl_precio_usd_cabecera	= $_GET['fl_precio_usd_cabecera'];
	$fl_precio_mxn_cabecera	= $_GET['fl_precio_mxn_cabecera'];
	$fl_precio_eur_cabecera	= $_GET['fl_precio_eur_cabecera'];

	if ($fl_precio_mxn_cabecera <> 0 ) 		$ac_moneda=1;
	else if ($fl_precio_usd_cabecera <> 0 ) $ac_moneda=2;
	else if ($fl_precio_eur_cabecera <> 0 ) $ac_moneda=3;
	
	//echo "<br>";
	//echo "ac_moneda",$ac_moneda;
	
	# ============================================
	# Catalogo de Monedas
	# ============================================
	$sql = "   SELECT id_moneda, tx_moneda ";
	$sql.= "     FROM tbl_moneda ";
	
	$sql.= "     where tx_indicador='1' ";
	$sql.= " ORDER BY id_moneda ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoMoneda[] = array(
			'id_moneda'=>$row["id_moneda"],
			'tx_moneda'=>$row["tx_moneda"]
		);
	}
		
	if ($dispatch=="save") 
	{
		$titulo 		= "MODIFICACION";	
		
		# ==========================================
		# Carga la informacion para la actualizacion
		# ==========================================	
		
		//CAMBIO CUENTAS CONTABLES, se quita concepto contable de query
		$sql = "   SELECT a.id_factura, a.id_empleado, a.id_producto, fl_precio_usd, fl_precio_mxn, fl_precio_eur, tx_notas, tx_centro_costos, a.fh_mod, c.tx_nombre AS usuario_mod, a.fh_alta, d.tx_nombre AS usuario_alta ";
		$sql.= "     FROM tbl_factura_detalle a, tbl_centro_costos b, tbl_usuario c, tbl_usuario d ";
		$sql.= "    WHERE id_factura_detalle= $id_factura_detalle ";
		$sql.= "      AND a.id_centro_costos= b.id_centro_costos ";
		$sql.= "      AND a.id_usuariomod	= c.id_usuario ";
		$sql.= "      AND a.id_usuarioalta 	= d.id_usuario ";
		
		//echo "aaa", $sql;
		
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoFacturaDetalle[] = array(
				'id_factura'			=>$row["id_factura"],
				'id_empleado'			=>$row["id_empleado"],
				'id_producto'			=>$row["id_producto"],
				'fl_precio_usd'			=>$row["fl_precio_usd"],				
				'fl_precio_mxn'			=>$row["fl_precio_mxn"],				
				'fl_precio_eur'			=>$row["fl_precio_eur"],
				//	CAMBIO CUENTAS CONTABLES, se quita concepto contable de query				
				//'tx_concepto_contable'	=>$row["tx_concepto_contable"],				
				'tx_notas'				=>$row["tx_notas"],				
				'tx_centro_costos'		=>$row["tx_centro_costos"],				
				'fh_alta'				=>$row["fh_alta"],
				'usuario_alta'			=>$row["usuario_alta"],				
				'fh_mod'				=>$row["fh_mod"],
				'usuario_mod'			=>$row["usuario_mod"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoFacturaDetalle); $i++)	{         			 
			while ($elemento = each($TheCatalogoFacturaDetalle[$i]))				
				$ac_factura				=$TheCatalogoFacturaDetalle[$i]['id_factura'];						  		
				$id_empleado			=$TheCatalogoFacturaDetalle[$i]['id_empleado'];	
				$ac_producto			=$TheCatalogoFacturaDetalle[$i]['id_producto'];		
				$fl_precio_usd			=$TheCatalogoFacturaDetalle[$i]['fl_precio_usd'];		
				$fl_precio_mxn			=$TheCatalogoFacturaDetalle[$i]['fl_precio_mxn'];		
				$fl_precio_eur			=$TheCatalogoFacturaDetalle[$i]['fl_precio_eur'];
				//CAMBIO CUENTAS CONTABLES, se quita concepto contable de query		
				//$tx_concepto_contable	=$TheCatalogoFacturaDetalle[$i]['tx_concepto_contable'];		
				$tx_notas				=$TheCatalogoFacturaDetalle[$i]['tx_notas'];	
				$tx_centro_costos		=$TheCatalogoFacturaDetalle[$i]['tx_centro_costos'];						
				$fh_alta				=$TheCatalogoFacturaDetalle[$i]['fh_alta'];
				$usuario_alta			=$TheCatalogoFacturaDetalle[$i]['usuario_alta'];
				$fh_mod					=$TheCatalogoFacturaDetalle[$i]['fh_mod'];
				$usuario_mod			=$TheCatalogoFacturaDetalle[$i]['usuario_mod'];
		} 
		
		if ($fl_precio_mxn <> 0.00) 
		{
			$fl_precio=$fl_precio_mxn;
			$ac_moneda=1;
		} else if ($fl_precio_usd <> 0.00)  {
		 	$fl_precio=$fl_precio_usd;
			$ac_moneda=2;
		} else if ($fl_precio_eur <> 0.00)  {
		 	$fl_precio=$fl_precio_eur;
			$ac_moneda=3;
		} 
				
	} else if ($dispatch=="insert") {	
		
		$titulo 		= "ALTA";		
		$tx_indicador	= "1";
		
		$sql = "   SELECT a.id_empleado, a.id_centro_costos, tx_centro_costos ";
		$sql.= "     FROM tbl_empleado a, tbl_centro_costos b ";
		$sql.= "    WHERE a.id_empleado		= $id_empleado ";
		$sql.= "      AND a.id_centro_costos= b.id_centro_costos ";
		
		//echo "aaa", $sql;
		
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoEmpleado[] = array(
				'id_empleado'		=>$row["id_empleado"],
				'id_centro_costos'	=>$row["id_centro_costos"],
				'tx_centro_costos'	=>$row["tx_centro_costos"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoEmpleado); $i++)	{         			 
			while ($elemento = each($TheCatalogoEmpleado[$i]))				
				$id_empleado		=$TheCatalogoEmpleado[$i]['id_empleado'];	
				$id_centro_costos	=$TheCatalogoEmpleado[$i]['id_centro_costos'];		
				$tx_centro_costos	=$TheCatalogoEmpleado[$i]['tx_centro_costos'];						
		} 
	} 
	
	//============================================
	//Catalogo de Productos
	//============================================
	$sql = "   SELECT a.id_producto, tx_producto ";
	$sql.= "     FROM tbl_licencia a, tbl_producto b ";
	$sql.= "    WHERE id_empleado 	= $id_empleado ";
	$sql.= "      AND id_cuenta		= $id_cuenta ";
	$sql.= "      AND a.id_producto = b.id_producto and b.tx_indicador='1' and  a.tx_indicador <> '0' ";
	
	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoProducto[] = array(
			'id_producto'	=>$row["id_producto"],
			'tx_producto'	=>$row["tx_producto"]
		);
	}		
?>
<br>
    <form id="opFacturaDetalleMantenimiento" action="">
	  	<input id="id_centro_costos" name="id_centro_costos" type="hidden" value="<? echo $id_centro_costos ?>" />
    	<input id="id_empleado" name="id_empleado" type="hidden" value="<? echo $id_empleado ?>" /> 
        <input id="id_factura_detalle" name="id_factura_detalle" type="hidden" value="<? echo $id_factura_detalle ?>" /> 
       	<input id="dispatch" name="dispatch" type="hidden" value="<? echo $dispatch ?>" /> 
        <table cellspacing="1px" border="0" cellpadding="0" width="100%">   		
   			<tr>
                <td colspan="5" class="ui-state-highlight" align="center" style="font-family:Verdana,Arial,sans-serif;font-size: 12px;font-weight:bold;"><? echo $titulo ?></td>  
         	</tr>         	                          
            <tr>
            	<td width="20%" class="ui-state-default">Centro de Costos:</td>
              	<td width="60%"><? echo "$tx_centro_costos" ?></td>
           	  	<td width="20%">&nbsp;</td>                        
            </tr>
            <tr>
              	<td class="ui-state-default">Proveedor:</td>
              	<td><? echo "$tx_proveedor_corto" ?></td>
              	<td>&nbsp;</td>
            </tr>
            <tr>
            	<td class="ui-state-default">Cuenta:</td>
  				<td><? echo "$tx_cuenta" ?></td>
              	<td></td>   
            </tr>
            <tr>
            	<td class="ui-state-default">Producto:</td>
                <td>
                	<select id="sel_producto" name="sel_producto">
                  	<option value="0" class="">--- S e l e c c i o n e ---</option>
                  	<?
						for ($i=0; $i < count($TheCatalogoProducto); $i++)	{         			 
							while ($elemento 	= each($TheCatalogoProducto[$i]))					  		
								$id_producto	= $TheCatalogoProducto[$i]['id_producto'];	
								$tx_producto	= $TheCatalogoProducto[$i]['tx_producto'];	
								if ($ac_producto == $id_producto ) echo "<option value=$id_producto selected='selected'>$tx_producto</option>";
								else echo "<option value=$id_producto>$tx_producto</option>";	
						}
					?>
                	</select>                </td>
                <td><div id="errsel_producto" style="float:left;"></div></td>   
          	</tr>
           	<tr>
            	<td class="ui-state-default">Precio:</td>
                <td><input id="cap_monto" name="cap_monto" type="text" size="20" alt="signed-decimal-us" title="Precio" value="<? echo $fl_precio ?>"/>                  &nbsp;&nbsp;
                    <select id="sel_moneda" name="sel_moneda">
                    <?						
						for ($i=0; $i < count($TheCatalogoMoneda); $i++)	{         			 
							while ($elemento = each($TheCatalogoMoneda[$i]))					  		
								$id_moneda = $TheCatalogoMoneda[$i]['id_moneda'];		
								$tx_moneda = $TheCatalogoMoneda[$i]['tx_moneda'];	
								if ($id_moneda == $ac_moneda ) echo "<option value='$tx_moneda' selected='selected'>$tx_moneda</option>";
								else echo "<option value='$tx_moneda'>$tx_moneda</option>";	
						}
					?>
                  	</select>               	</td>
              	<td></td>   
           	</tr>                          
           	
           	<!--   CAMBIO CUENTA CONTABLE  -->
           	<!-- 
            <tr>
              <td class="ui-state-default" valign="top">Concepto Contable:</td>
              <td><input name="cap_concepto_contable" id="cap_concepto_contable" type="text" size="20"  title="Concepto Contable" value="<? echo $tx_concepto_contable ?>"/></td>
              <td></td>
            </tr>
             -->
            
            <tr>
            	<td class="ui-state-default" valign="top">Notas:</td>
           	  	<td>
                	<textarea id="cap_notas" name="cap_notas" cols="50" rows="5" title="Notas"><? echo $tx_notas ?></textarea>                </td>
       	  	  	<td></td>   
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
             <?
			 	}
			 ?>   
            <tr id="Act_Buttons_Lic">
            	<td class="EditButton ui-widget-content" colspan="5" style="text-align:center">                            
                	<a id="btnSaveFacDet" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Guardar
                    <span class="ui-icon ui-icon-disk"/></a>
                    <a id="btnUndoFacDet" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Cancelar
                    <span class="ui-icon ui-icon-cancel"/></a>                </td>
           	</tr>
           	<tr>
           		<td colspan="5" class="ui-state-highlight">&nbsp;</td>                      
            </tr>        
    	</table>     
</form>
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  