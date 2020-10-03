<?php

session_start();
if 	(isset($_SESSION["sess_user"]))
{	
	include("includes/funciones.php");  
	$mysql=conexion_db();	

	//$par_anio= $_GET['par_anio'];
	
?>
	<!-- <input id="par_anio" name="par_anio" type="hidden" value="<? //echo $par_anio ?>" /> -->
    	
	<script type="text/javascript">
                
        $(function() {   
            $("#tabs").tabs();						
        });	
	
		// ==============================================================================
		// Carga primer TAB
		// ==============================================================================
		var id="par_anio="+$("#sel_anio_d").val();		
		var id1="&par_agrupacion=1";		
		var id2="&par_moneda=USD";		
		var id3="&par_estatus=0";
		var id4="&par_cuenta=0";							
		//alert ("Entre"+id1);	
        var url1="inf_facturacion_direccion.php?"+id+id1+id2+id3+id4;   		
        loadHtmlAjax(true, $('#divDireccionFac'), url1);				
		
		// ==============================================================================
		// Carga segundo TAB
		// ==============================================================================		
		var id0="par_anio="+$("#sel_anio_p").val();	
		var id1="&par_proveedor="+$("#sel_proveedor_p").val();
		var id2="&par_cuenta="+$("#sel_cuenta_p").val();
		var id3="&par_estatus="+$("#sel_estatus_p").val();
		var origen="&origen=1";	
		$("#divInfPro").html("");
		$("#divInfDet").html("");
		var url2="inf_facturacion_proveedor_lista.php?"+id0+id1+id2+id3+origen;   
		loadHtmlAjax(true, $("#divInfPro"), url2);	

		$("#btnLimpiar").click(function(){    	
			
			//alert("Entre"); 

			$("#divDireccionFac").html("");			
			$("#divDetalleFac").html("");			
			$("#divDetalleFacDir").html("");
			   
		}).hover(function(){
			$(this).addClass("ui-state-hover")
		},function(){
			$(this).removeClass("ui-state-hover")
		});		
		
		// ================================================================================ 
		// FUNCIONALIDAD POR DIRECCION
		// ================================================================================
		
		$("#sel_anio_d").change(function () {
			$("#sel_anio_d option:selected").each(function () {				
														var id="par_anio="+$(this).val();
														var id1="&par_agrupacion="+$("#sel_agrupacion_d").val();
														var id2="&par_moneda="+$("#sel_moneda_d").val();
														var id3="&par_estatus="+$("#sel_estatus_d").val();
														var id4="&par_cuenta="+$("#sel_cta_contable").val();
														$("#divTabsFac").html("");	
														$("#divFacturacionDirN1").html("");
														$("#divFacturacionDirN2").html("");
														$("#divDetalleFac").html("");
														$("#divDetalleFacDir").html("");					
				loadHtmlAjax(true, $("#divDireccionFac"), "inf_facturacion_direccion.php?"+id+id1+id2+id3+id4); 
			});
    	});	
	
		$("#sel_agrupacion_d").change(function () {
			$("#sel_agrupacion_d option:selected").each(function () {
														var id="par_anio="+$("#sel_anio_d").val();
														var id1="&par_agrupacion="+$("#sel_agrupacion_d").val();
														var id2="&par_moneda="+$("#sel_moneda_d").val();
														var id3="&par_estatus="+$("#sel_estatus_d").val();
														var id4="&par_cuenta="+$("#sel_cta_contable").val();
														$("#divTabsFac").html("");	
														$("#divFacturacionDirN1").html("");
														$("#divFacturacionDirN2").html("");
														$("#divDetalleFac").html("");
														$("#divDetalleFacDir").html("");	
				loadHtmlAjax(true, $("#divDireccionFac"), "inf_facturacion_direccion.php?"+id+id1+id2+id3+id4); 
			});
		});
		
		$("#sel_moneda_d").change(function () {
					$("#sel_moneda_d option:selected").each(function () {
														var id="par_anio="+$("#sel_anio_d").val();
														var id1="&par_agrupacion="+$("#sel_agrupacion_d").val();
														var id2="&par_moneda="+$("#sel_moneda_d").val();
														var id3="&par_estatus="+$("#sel_estatus_d").val();
														var id4="&par_cuenta="+$("#sel_cta_contable").val();
														$("#divTabsFac").html("");	
														$("#divFacturacionDirN1").html("");
														$("#divFacturacionDirN2").html("");
														$("#divDetalleFac").html("");
														$("#divDetalleFacDir").html("");
				loadHtmlAjax(true, $("#divDireccionFac"), "inf_facturacion_direccion.php?"+id+id1+id2+id3+id4); 
			});
		});
		
		$("#sel_estatus_d").change(function () {
				$("#sel_estatus_d option:selected").each(function () {
															var id="par_anio="+$("#sel_anio_d").val();
															var id1="&par_agrupacion="+$("#sel_agrupacion_d").val();
															var id2="&par_moneda="+$("#sel_moneda_d").val();
															var id3="&par_estatus="+$("#sel_estatus_d").val();
															var id4="&par_cuenta="+$("#sel_cta_contable").val();
															$("#divTabsFac").html("");	
															$("#divFacturacionDirN1").html("");
															$("#divFacturacionDirN2").html("");
															$("#divDetalleFac").html("");
															$("#divDetalleFacDir").html("");
				loadHtmlAjax(true, $("#divDireccionFac"), "inf_facturacion_direccion.php?"+id+id1+id2+id3+id4); 
			});
		});

		$("#sel_cta_contable").change(function () {
				$("#sel_cta_contable option:selected").each(function () {
															var id="par_anio="+$("#sel_anio_d").val();
															var id1="&par_agrupacion="+$("#sel_agrupacion_d").val();
															var id2="&par_moneda="+$("#sel_moneda_d").val();
															var id3="&par_estatus="+$("#sel_estatus_d").val();
															var id4="&par_cuenta="+$("#sel_cta_contable").val();
															$("#divTabsFac").html("");	
															$("#divFacturacionDirN1").html("");
															$("#divFacturacionDirN2").html("");
															$("#divDetalleFac").html("");
															$("#divDetalleFacDir").html("");
				loadHtmlAjax(true, $("#divDireccionFac"), "inf_facturacion_direccion.php?"+id+id1+id2+id3+id4); 
			});
		});
		
		

		
		// ================================================================================ 
		// FUNCIONALIDAD POR PROVEEDOR
		// ================================================================================
		
		$("#sel_anio_p").change(function () {
			$("#sel_anio_p option:selected").each(function () {				
				var id0="par_anio="+$("#sel_anio_p").val();	
				var id1="&par_proveedor="+$("#sel_proveedor_p").val();
				var id2="&par_cuenta="+$("#sel_cuenta_p").val();
				var id3="&par_estatus="+$("#sel_estatus_p").val();
				var origen="&origen=1";	
				$("#divInfPro").html("");
				$("#divInfDet").html("");
				loadHtmlAjax(true, $("#divInfPro"), "inf_facturacion_proveedor_lista.php?"+id0+id1+id2+id3+origen);	
			});
    	});	
		
		$("#sel_proveedor_p").change(function () {
            $("#sel_proveedor_p option:selected").each(function () {
				var id0="par_anio="+$("#sel_anio_p").val();	
				var id1="&par_proveedor="+$("#sel_proveedor_p").val();
				var id2="&par_cuenta="+$("#sel_cuenta_p").val();
				var id3="&par_estatus="+$("#sel_estatus_p").val();
				var origen="&origen=1";
				var id="id="+$("#sel_proveedor_p").val();
				
				$("#divInfPro").html("");
				$("#divInfDet").html("");
				
                loadHtmlAjax(false, $("#sel_cuenta_p"), "combo_cuenta.php?"+id);
				loadHtmlAjax(true, $("#divInfPro"), "inf_facturacion_proveedor_lista.php?"+id0+id1+id2+id3+origen);	
            });
         });
		 
		$("#sel_cuenta_p").change(function () {
	 		$("#sel_cuenta_p option:selected").each(function () {				
				var id0="par_anio="+$("#sel_anio_p").val();	
				var id1="&par_proveedor="+$("#sel_proveedor_p").val();
				var id2="&par_cuenta="+$("#sel_cuenta_p").val();
				var id3="&par_estatus="+$("#sel_estatus_p").val();
				var origen="&origen=1";	
				$("#divInfPro").html("");
				$("#divInfDet").html("");
				loadHtmlAjax(true, $("#divInfPro"), "inf_facturacion_proveedor_lista.php?"+id0+id1+id2+id3+origen);			
     		});
     	});
		
		$("#sel_estatus_p").change(function () {
			$("#sel_estatus_p option:selected").each(function () {
				var id0="par_anio="+$("#sel_anio_p").val();	
				var id1="&par_proveedor="+$("#sel_proveedor_p").val();
				var id2="&par_cuenta="+$("#sel_cuenta_p").val();
				var id3="&par_estatus="+$("#sel_estatus_p").val();
				var origen="&origen=1";	
				$("#divInfPro").html("");
				$("#divInfDet").html("");
				loadHtmlAjax(true, $("#divInfPro"), "inf_facturacion_proveedor_lista.php?"+id0+id1+id2+id3+origen);							
			});
		});

    </script>
    
    <?
	
    # =======================================
	# Carga el combo de anio
	# =======================================
	$sql = "   SELECT tx_anio ";
	$sql.= "     FROM tbl_anio ";
	$sql.= "    WHERE tx_indicador = 1 ";
	$sql.= " ORDER BY tx_anio DESC ";
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoAnio[] = array(
			'tx_anio' =>$row["tx_anio"]
		);
	} 
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoAnio1[] = array(
			'tx_anio' =>$row["tx_anio"]
		);
	} 
	
	
	# =======================================
	# Carga el combo de estado de la factura
	# =======================================
	$sql = "   SELECT id_factura_estatus, tx_estatus ";
	$sql.= "     FROM tbl_factura_estatus where tx_indicador='1' ";
	$sql.= " ORDER BY id_factura_estatus ";
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoEstatus[] = array(
			'id_factura_estatus'	=>$row["id_factura_estatus"],
			'tx_estatus'			=>$row["tx_estatus"]
		);
	} 
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoEstatus1[] = array(
			'id_factura_estatus'	=>$row["id_factura_estatus"],
			'tx_estatus'			=>$row["tx_estatus"]
		);
	} 
	
	
	# =======================================
	# Carga el combo de cuenta contable 
	# =======================================
	$sql = " select id as id_cuenta, concat(tx_valor ,' : ' , tx_observaciones ) as tx_cuenta from tbl45_catalogo_global ";
	$sql.= " where substr(tx_clave,1,15)=  'CUENTA_CONTABLE'  and tx_indicador=1";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoContable[] = array(
			'id'	=>$row["id_cuenta"],
			'tx_cuenta'			=>$row["tx_cuenta"]
		);
	} 
	
	
	
	# ====================================
	# Catalogo de PROVEEDORES
	# ====================================
	$sql = "   SELECT id_proveedor, tx_proveedor_corto ";
	$sql.= "     FROM tbl_proveedor where tx_indicador='1' ";
	$sql.= " ORDER BY tx_proveedor_corto ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoProveedores[] = array(
			'id_proveedor'			=>$row["id_proveedor"],
			'tx_proveedor_corto'	=>$row["tx_proveedor_corto"]
		);
	}	
	
	?>   	
	<table cellspacing="1px" border="0" cellpadding="0" width="100%">         
   		<tr>
        	<td colspan="2"> 
            	<div id="tabs">
                	<ul>
        				<li><a href="#tabs-1">Por Direcci&oacute;n Corporativa</a></li>
                      	<li><a href="#tabs-2">Por Proveedor</a></li>                     						
                    </ul>
                    <div id="tabs-1">
                    	<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"> 
                    	<div class="ui-widget-header align-center">FACTURACION POR DIRECCION</div>	                    	
                        <fieldset>
            			<legend class="ui-state-default"><b><em>CONFIGURACION ...</em></b></legend>
                        	<br/>
               	  			<table cellspacing="1px" border="0" cellpadding="0" width="100%">         
           		  				<tr>
                                	<td width="8%" class="ui-state-default">A&ntilde;o:</td>
                                	<td width="10%">
                                	<select id="sel_anio_d" name="sel_anio_d">              
                                	<?
                                	for ($i=0; $i < count($TheCatalogoAnio); $i++)	{         			 
                                    						  		
                                        	$tx_anio		  	= $TheCatalogoAnio[$i]['tx_anio'];										
                                        	echo "<option value = $tx_anio>$tx_anio</option>";	
                                	}
                                	?>
                                	</select>
                                	</td>    
                                	<td width="8%" class="ui-state-default">Areas de Negocio:</td>
                                	<td width="10%">
                                	<select id="sel_agrupacion_d" name="sel_agrupacion_d">
                                    	<option value=1>WB&amp;AM</option>
                                    	<option value=0>BANCOMER</option>
                                    	<option value=2>TODAS</option>
                                	</select>
                                	</td>     
                                	<td width="8%" class="ui-state-default">Moneda:</td>
                                	<td width="10%">
                                	<select id="sel_moneda_d" name="sel_moneda_d">
                                    	<option value="USD">USD</option>
                                    	<option value="MXN">MXN</option>
                                    	<!-- <option value="EUR">EUR</option> -->
                                	</select>
                                	</td>              		
                                	<td width="10%" class="ui-state-default">Estatus de Facturaci&oacute;n:</td>
                                	<td width="10%">
                                	<select id="sel_estatus_d" name="sel_estatus_d">
                                    	<option value=0>TODAS</option>            
                                    	<?
                                    	for ($i=0; $i < count($TheCatalogoEstatus); $i++)
										{         			 
                                        					  		
                                            	$id_factura_estatus	= $TheCatalogoEstatus[$i]["id_factura_estatus"];		
                                            	$tx_estatus			= $TheCatalogoEstatus[$i]["tx_estatus"];										
                                            	echo "<option value	= $id_factura_estatus>$tx_estatus</option>";	
                                    	}
                                    	?>
                                	</select>
                                	</td>    
                                	<td width="8%" class="ui-state-default">Cuenta Contable:</td>
                                	<td width="10%">
                                	<select id="sel_cta_contable" name="sel_cta_contable">
                                	<option value=0>TODAS</option>            
                                    	<?
                                    	for ($i=0; $i < count($TheCatalogoContable); $i++)
										{         			 
                                            	$id	= $TheCatalogoContable[$i]["id"];		
                                            	$tx_cuenta			= $TheCatalogoContable[$i]["tx_cuenta"];										
                                            	echo "<option value=$id >$tx_cuenta</option>";	
                                    	}
                                    	?>
                                	</select>
                                	
                                	
                                	</td>       
                                	          		
                                   	<td width="10%" align="center">
                                    	<a id="btnLimpiar" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Limpiar">
                    					Limpiar 
                    					<span class="ui-icon ui-icon-trash"></span></a>
                                     </td>
                            	</tr>                         
                        	</table> 
                            <br/> 
                            </fieldset>                                 
                     	</div> 
                    	<div id="divDireccionFac" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>
                        <div id="divDetalleFac" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>
                        <div id="divDetalleFacDir" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>                   
                    </div>
                    <div id="tabs-2">
                    	<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"> 
                    	<div class="ui-widget-header align-center">FACTURACION POR PROVEEDOR</div>	                    	
                        <fieldset>
            			<legend class="ui-state-default"><b><em>CONFIGURACION ...</em></b></legend>
                        	<br/>
               	  			<table cellspacing="1px" border="0" cellpadding="0" width="100%">         
           		  				<tr>
                                	<td width="8%" class="ui-state-default">A&ntilde;o:</td>
                                	<td width="8%">
                                    <select id="sel_anio_p" name="sel_anio_p">              
                                	<?
                                	for ($i=0; $i < count($TheCatalogoAnio1); $i++)	{         			 
                                    	while ($elemento = each($TheCatalogoAnio1[$i]))					  		
                                        	$tx_anio		  	= $TheCatalogoAnio1[$i]['tx_anio'];										
                                        	echo "<option value = $tx_anio>$tx_anio</option>";	
                                	}
                                	?>
                                	</select>                                    </td>    
                               	  	<td width="8%" class="ui-state-default">Proveedor:</td>
                                	<td width="30%">
                                    <select id="sel_proveedor_p" name="sel_proveedor_p">
                                    	<option value="0" class="">--- S e l e c c i o n e ---</option>
                                      	<?
											for ($i=0; $i < count($TheCatalogoProveedores); $i++)	{         			 
											while ($elemento = each($TheCatalogoProveedores[$i]))					  		
												$id_proveedor		=$TheCatalogoProveedores[$i]['id_proveedor'];		
												$tx_proveedor_corto	=$TheCatalogoProveedores[$i]['tx_proveedor_corto'];								
												echo "<option value=$id_proveedor>$tx_proveedor_corto</option>";	
										}
										?>
                                    </select></td>     
                                	<td width="8%" class="ui-state-default">Cuenta:</td>
                                	<td width="15%">
                                    <select id="sel_cuenta_p" name="sel_cuenta_p">
                                    	<option value="0" class="">--- S e l e c c i o n e ---</option>
                                      	<?
										for ($i=0; $i < count($TheCatalogoCuentas); $i++)	{         			 
											while ($elemento = each($TheCatalogoCuentas[$i]))					  		
												$id_cuenta	= $TheCatalogoCuentas[$i]['id_cuenta'];		
												$tx_cuenta	= $TheCatalogoCuentas[$i]['tx_cuenta'];						
												echo "<option value=$id_cuenta>$tx_cuenta</option>";	
										}
										?>
                                    </select>                                    </td>              		
                                	<td width="10%" class="ui-state-default">Estatus de Facturaci&oacute;n:</td>
                                	<td width="9%">
                                	<select id="sel_estatus_p" name="sel_estatus_p">
                                    	<option value=0>TODAS</option>            
                                    	<?
                                    	for ($i=0; $i < count($TheCatalogoEstatus1); $i++)	{         			 
                                        	while ($elemento = each($TheCatalogoEstatus1[$i]))					  		
                                            	$id_factura_estatus	= $TheCatalogoEstatus1[$i]['id_factura_estatus'];		
                                            	$tx_estatus			= $TheCatalogoEstatus1[$i]['tx_estatus'];										
                                            	echo "<option value	= $id_factura_estatus>$tx_estatus</option>";	
                                    	}
                                    	?>
                                	</select>
                                    </td>              		
                            	</tr>                         
                        	</table> 
               				<br/> 
                            </fieldset>                      	
                        <div id="divInfPro" class="divConGrid1" style="font-size:11px;width:100%;"></div>
                        <div id="divInfDet" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>   
                        <div id="divAltaFac"class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>                     
                	</div>
            	</div>
            </div>                   
        	</td>
    	</tr>           
	</table>      
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  