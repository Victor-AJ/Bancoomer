<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">	
	
	$("#divFiles").html("");

	function hideFiles(){
        $("#divFiles").toggle("slow");
    }
	
	function fieldsRequiredListaFactura(){
	
		var error = true;		
		var moneda = $("#sel_moneda").val();
		
		if (moneda=="USD" || moneda=="EUR") validText(false, $("#cap_monto_mxn"), $("#errcap_monto_mxn"), 1);
		
		validText(false, $("#cap_factura"), $("#errcap_factura"), 1);		
		validText(false, $("#cap_monto"), $("#errcap_monto"), 1);		
		validText(false, $("#cap_fh_factura"), $("#errcap_fh_factura"), 1);		
		validText(false, $("#cap_fh_inicial"), $("#errcap_fh_inicial"), 1);		
		validText(false, $("#cap_fh_final"), $("#errcap_fh_final"), 1);		
		validText(false, $("#cap_referencia"), $("#errcap_referencia"), 1);			
		validSelect($("#sel_anio_cap"), $("#errsel_anio_cap"));
		validSelect($("#sel_proveedor"), $("#errsel_proveedor"));
		validSelect($("#sel_cuenta"), $("#errsel_cuenta"));
		validSelect($("#sel_moneda"), $("#errsel_moneda"));
		validSelect($("#sel_amortiza"), $("#errsel_amortiza"));
		validSelect($("#sel_estatus"), $("#errsel_estatus"));
		
		if (moneda=="USD" || moneda=="EUR") {		
		
			//alert ("Entre 1"+moneda);		
			if( !validText(false, $("#cap_factura"), $("#errcap_factura"), 1) || 
				!validText(false, $("#cap_monto"), $("#errcap_monto"), 1) || 
				!validText(false, $("#cap_fh_factura"), $("#errcap_fh_factura"), 1) || 
				!validText(false, $("#cap_fh_inicial"), $("#errcap_fh_inicial"), 1) || 
				!validText(false, $("#cap_fh_final"), $("#errcap_fh_final"), 1) || 
				!validText(false, $("#cap_referencia"), $("#errcap_referencia"), 1) || 
				!validSelectCeros($("#cap_monto_mxn"), $("#errcap_monto_mxn")) ||
				!validSelect($("#sel_anio"), $("#errsel_anio")) ||
				!validSelect($("#sel_proveedor"), $("#errsel_proveedor")) ||
				!validSelect($("#sel_cuenta"), $("#errsel_cuenta")) ||
				!validSelect($("#sel_moneda"), $("#errsel_moneda")) ||
				!validSelect($("#sel_amortiza"), $("#errsel_amortiza")) ||
				!validSelect($("#sel_estatus"), $("#errsel_estatus"))
			) error = false;
		} else {
			if( !validText(false, $("#cap_factura"), $("#errcap_factura"), 1) || 
				!validText(false, $("#cap_monto"), $("#errcap_monto"), 1) || 
				!validText(false, $("#cap_fh_factura"), $("#errcap_fh_factura"), 1) || 
				!validText(false, $("#cap_fh_inicial"), $("#errcap_fh_inicial"), 1) || 
				!validText(false, $("#cap_fh_final"), $("#errcap_fh_final"), 1) || 
				!validText(false, $("#cap_referencia"), $("#errcap_referencia"), 1) || 
				!validSelect($("#sel_anio"), $("#errsel_anio")) ||
				!validSelect($("#sel_proveedor"), $("#errsel_proveedor")) ||
				!validSelect($("#sel_cuenta"), $("#errsel_cuenta")) ||
				!validSelect($("#sel_moneda"), $("#errsel_moneda")) ||
				!validSelect($("#sel_amortiza"), $("#errsel_amortiza")) ||
				!validSelect($("#sel_estatus"), $("#errsel_estatus"))
			) error = false;
		
		}	
        		 
		return error;
	}
	
	$("#btnSaveFac").click(function(){     
	 
		if(fieldsRequiredListaFactura()){	
			var url = "process_facturas.php?origen=1&";
         		url += $("#opFacturaCabecera").serialize(); 

			//alert("URL"+url);	
		
			var func = function(data){					   			
				var fAceptar = function(){
					$('#dialogMain').dialog("close");
				}
				//alert (data.error);
				
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
						//loadHtmlAjax(true, $("#divDatos"), data.html);
						loadHtmlAjax(true, $("#divInfPro"), data.html);
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
	 
	 $("#btnUndoFac").click(function(){  
	 	$("#divAltaFac").hide();
	 }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });
	 
	 $(function(){
     	$('input:text').setMask();
     });
	 $("#cap_fh_inicial").datepicker($.datepicker.regional['es']);
	 $("#cap_fh_final").datepicker($.datepicker.regional['es']);
	 $("#cap_fh_factura").datepicker($.datepicker.regional['es']);
	 $("#cap_fh_contable").datepicker($.datepicker.regional['es']);
	 ///////////////// DEFINICION DE EVENTOS //////////////////////
	 $("#sel_proveedor").change(function () {
     	$("#sel_proveedor option:selected").each(function () {        	
        	loadHtmlAjax(false, $("#sel_cuenta") , "combo_cuenta.php?id="+$(this).val());
     	});
     });	
	 
	 $("#sel_moneda").change(function () {
     	$("#sel_moneda option:selected").each(function () {
			
			//var tx_moneda = $("#sel_moneda").val();		
			//if (tx_moneda=="USD") $("#cap_monto_mxn").val()="";
					  
			var url = "busca_moneda.php?id="+$("#sel_moneda").val();			  
			var func = function(data){					   			
	 			if(data.pasa == true){							
					$("#id_moneda").val(data.data1);	
				}				
			} 
			executeAjax("post", false ,url, "json", func); 						
        });
     });
	 
	 
	$("#cap_factura").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_factura").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });
	 
 	$("#cap_monto").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_monto").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });

 	$("#cap_tipo_cambio").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_tipo_cambio").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_fh_factura").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_fh_factura").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });

	$("#cap_fh_inicial").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_fh_inicial").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });

	$("#cap_fh_final").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_fh_final").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_fh_contable").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_fh_contable").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });

	$("#cap_ruta").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_ruta").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_referencia").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_referencia").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
         	
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
	
	$dispatch	= $_GET['dispatch'];
	$id_factura	= $_GET['id'];	

	# ============================================
	# Catalogo de Anios
	# ============================================
	$sql = "   SELECT tx_anio ";
	$sql.= "   	 FROM tbl_anio ";
	$sql.= "   	WHERE tx_indicador='1' ";
	$sql.= " ORDER BY tx_anio DESC " ; 	

	//echo "aaa", $sql;
			
	$result = mysqli_query($mysql, $sql);		
		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoAnios[] = array(
			'tx_anio'	=>$row["tx_anio"]
			);
	} 
	
	# ============================================
	# Catalogo de Proveedores
	# ============================================
	$sql = "   SELECT id_proveedor, tx_proveedor_corto ";
	$sql.= "     FROM tbl_proveedor ";
	$sql.= " ORDER BY tx_proveedor ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoProveedores[] = array(
			'id_proveedor'		=>$row["id_proveedor"],
			'tx_proveedor_corto'=>$row["tx_proveedor_corto"]
		);
	}	
	
	# ============================================
	# Catalogo de Monedas
	# ============================================
	$sql = "   SELECT id_moneda, tx_moneda ";
	$sql.= "     FROM tbl_moneda ";
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
	
	# ============================================
	# Catalogo de Meses
	# ============================================
	$sql = "   SELECT id_mes, tx_mes ";
	$sql.= "     FROM tbl_mes ";
	$sql.= " ORDER BY id_mes ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoMes[] = array(
			'id_mes'=>$row["id_mes"],
			'tx_mes'=>$row["tx_mes"]
		);
	}
	
	# ============================================
	# Catalogo de Estatus
	# ============================================
	$sql = "   SELECT id_factura_estatus, tx_estatus ";
	$sql.= "     FROM tbl_factura_estatus ";
	$sql.= " ORDER BY id_factura_estatus ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoEstatus[] = array(
			'id_factura_estatus'	=>$row["id_factura_estatus"],
			'tx_estatus'			=>$row["tx_estatus"]
		);
	}
	
	if ($dispatch=="insert") {	
		
		$titulo 		= "CABECERA DE FACTURA - ALTA";		
		$tx_indicador	= "1";
	
 	} else if ($dispatch=="save") {
		
		$titulo 		= "MODIFICACION";
		
		# ==========================================
		# Carga la informacion para la actualizacion
		# ==========================================
		
		$sql = "   SELECT id_factura, id_proveedor, id_cuenta, id_mes, id_factura_estatus, id_mes, id_moneda, tx_anio, tx_factura, fh_factura, fh_inicio, fh_final, fh_contable, fl_precio_usd, fl_precio_mxn, fl_precio_eur, fl_tipo_cambio, tx_referencia, tx_ruta, tx_notas, a.fh_mod,  b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta ";
		$sql.= "     FROM tbl_factura a, tbl_usuario b, tbl_usuario c ";
		$sql.= "    WHERE id_factura 		= $id_factura ";
		$sql.= "      AND a.id_usuariomod	= b.id_usuario ";
		$sql.= "      AND a.id_usuarioalta 	= c.id_usuario ";
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoFactura[] = array(
				'id_factura'		=>$row["id_factura"],
				'id_proveedor'		=>$row["id_proveedor"],
				'id_cuenta'			=>$row["id_cuenta"],
				'id_mes'			=>$row["id_mes"],
				'id_factura_estatus'=>$row["id_factura_estatus"],
				'id_moneda'			=>$row["id_moneda"],
				'tx_anio'			=>$row["tx_anio"],
				'tx_factura'		=>$row["tx_factura"],
				'fh_factura'		=>$row["fh_factura"],				
				'fh_inicio'			=>$row["fh_inicio"],				
				'fh_final'			=>$row["fh_final"],				
				'fh_contable'		=>$row["fh_contable"],				
				'fl_precio_usd'		=>$row["fl_precio_usd"],				
				'fl_precio_mxn'		=>$row["fl_precio_mxn"],				
				'fl_precio_eur'		=>$row["fl_precio_eur"],				
				'fl_tipo_cambio'	=>$row["fl_tipo_cambio"],				
				'tx_referencia'		=>$row["tx_referencia"],				
				'tx_ruta'			=>$row["tx_ruta"],				
				'tx_notas'			=>$row["tx_notas"],				
				'fh_alta'			=>$row["fh_alta"],
				'usuario_alta'		=>$row["usuario_alta"],				
				'fh_mod'			=>$row["fh_mod"],
				'usuario_mod'		=>$row["usuario_mod"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoFactura); $i++)	{         			 
			while ($elemento = each($TheCatalogoFactura[$i]))				
				$id_factura			=$TheCatalogoFactura[$i]['id_factura'];						  		
				$ac_proveedor		=$TheCatalogoFactura[$i]['id_proveedor'];	
				$ac_cuenta			=$TheCatalogoFactura[$i]['id_cuenta'];	
				$ac_factura_estatus	=$TheCatalogoFactura[$i]['id_factura_estatus'];	
				$ac_mes				=$TheCatalogoFactura[$i]['id_mes'];	
				$ac_moneda			=$TheCatalogoFactura[$i]['id_moneda'];	
				$ac_anio			=$TheCatalogoFactura[$i]['tx_anio'];		
				$tx_factura			=$TheCatalogoFactura[$i]['tx_factura'];		
				$fh_factura			=$TheCatalogoFactura[$i]['fh_factura'];		
				$fh_inicio			=$TheCatalogoFactura[$i]['fh_inicio'];		
				$fh_final			=$TheCatalogoFactura[$i]['fh_final'];		
				$fh_contable		=$TheCatalogoFactura[$i]['fh_contable'];		
				$fl_precio_usd		=$TheCatalogoFactura[$i]['fl_precio_usd'];		
				$fl_precio_mxn		=$TheCatalogoFactura[$i]['fl_precio_mxn'];		
				$fl_precio_eur		=$TheCatalogoFactura[$i]['fl_precio_eur'];		
				$fl_tipo_cambio		=$TheCatalogoFactura[$i]['fl_tipo_cambio'];	
				$tx_referencia		=$TheCatalogoFactura[$i]['tx_referencia'];			
				$tx_ruta			=$TheCatalogoFactura[$i]['tx_ruta'];		
				$tx_notas			=$TheCatalogoFactura[$i]['tx_notas'];		
				$fh_alta			=$TheCatalogoFactura[$i]['fh_alta'];
				$usuario_alta		=$TheCatalogoFactura[$i]['usuario_alta'];
				$fh_mod				=$TheCatalogoFactura[$i]['fh_mod'];
				$usuario_mod		=$TheCatalogoFactura[$i]['usuario_mod'];
		} 		
		
		if ($ac_moneda==1) 	
		{
			$fl_precio	= $fl_precio_mxn;
			$cap_precio = 0.00;
		} else if ($ac_moneda==2) {
		  	$fl_precio	= $fl_precio_usd;
		  	$cap_precio = $fl_precio_mxn;
		} else if ($ac_moneda==3) {
			$fl_precio	= $fl_precio_eur;
		  	$cap_precio = $fl_precio_mxn;
		}	
		
		//echo "AAAA", $ac_moneda;
		//echo "<br>";

		# ==========================================
		# Catalogo de cuentas
		# ==========================================
		$sql = "   SELECT id_cuenta, tx_cuenta ";
		$sql.= "     FROM tbl_cuenta ";
		$sql.= " 	WHERE id_proveedor = $ac_proveedor ";		
	
		//echo " sql ",$sql;
		
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{	
			$TheCatalogoCuenta[] = array(			
				'id_cuenta'=>$row["id_cuenta"],
				'tx_cuenta'=>$row["tx_cuenta"]
			);
		}		
	}		
?>
<br>
<div>
    <form id="opFacturaCabecera" action="">
    	<input id="id_factura" name="id_factura" type="hidden" value="<? echo $id_factura ?>" />
    	<input id="dispatch" name="dispatch" type="hidden" value="<? echo $dispatch ?>" />        
	  	<table cellspacing="1px" border="0" cellpadding="0" width="100%">   		
   			<tr>
                <td colspan="5" class="ui-state-highlight" align="center" style="font-family:Verdana,Arial,sans-serif;font-size: 12px;font-weight:bold;"><? echo $titulo ?></td>  
         	</tr>         	                          
            <tr>
              	<td>&nbsp;</td>
              	<td>&nbsp;</td>
              	<td class="ui-amarillo" align="right" style="font-family:Verdana,Arial,sans-serif;font-size:11px;font-weight:bold;"><em>Control Econ&oacute;mico</em></td>
            </tr>
            <tr>
            	<td width="20%" class="ui-state-default">A&ntilde;o:</td>
              	<td width="60%">
                	<select id="sel_anio_cap" name="sel_anio_cap">
                  		<option value="0" class="">--- S e l e c c i o n e ---</option>
                  		<?
							for ($i=0; $i < count($TheCatalogoAnios); $i++)	{         			 
							while ($elemento = each($TheCatalogoAnios[$i]))					  		
								$tx_anio	=$TheCatalogoAnios[$i]['tx_anio'];	
								if ($ac_anio == $tx_anio ) echo "<option value='$tx_anio' selected='selected'>$tx_anio</option>";
								else echo "<option value='$tx_anio'>$tx_anio</option>";	
							}
						?>
                    </select>                </td>
           	  	<td width="20%"><div id="errsel_anio_cap" style="float:left;"></div></td>                        
            </tr>
            <tr>
            	<td class="ui-state-default">Proveedor:</td>
                <td>
                	<select id="sel_proveedor" name="sel_proveedor">
                  		<option value="0" class="">--- S e l e c c i o n e ---</option>
                  		<?
							for ($i=0; $i < count($TheCatalogoProveedores); $i++)	{         			 
							while ($elemento = each($TheCatalogoProveedores[$i]))					  		
								$id_proveedor		=$TheCatalogoProveedores[$i]['id_proveedor'];		
								$tx_proveedor_corto	=$TheCatalogoProveedores[$i]['tx_proveedor_corto'];	
								if ($id_proveedor == $ac_proveedor ) echo "<option value=$id_proveedor selected='selected'>$tx_proveedor_corto</option>";
								else echo "<option value=$id_proveedor>$tx_proveedor_corto</option>";	
							}
						?>
                	</select>                </td>
              	<td><div id="errsel_proveedor" style="float:left;"></div></td>   
            </tr>
            <tr>
            	<td class="ui-state-default">Cuenta:</td>
                <td>
                	<select id="sel_cuenta" name="sel_cuenta">
                  		<option value="0" class="">--- S e l e c c i o n e ---</option>
                 		<?
							for ($i=0; $i < count($TheCatalogoCuenta); $i++)	{         			 
							while ($elemento = each($TheCatalogoCuenta[$i]))					  		
								$id_cuenta	=$TheCatalogoCuenta[$i]['id_cuenta'];	
								$tx_cuenta	=$TheCatalogoCuenta[$i]['tx_cuenta'];	
								if ($ac_cuenta == $id_cuenta ) echo "<option value=$id_cuenta selected='selected'>$tx_cuenta</option>";
								else echo "<option value=$id_cuenta>$tx_cuenta</option>";	
							}
						?>
                	</select>                </td>
                <td><div id="errsel_cuenta" style="float:left;"></div></td>   
          	</tr>
           	<tr>
            	<td class="ui-state-default">N&uacute;mero Factura:</td>
                <td><input name="cap_factura" id="cap_factura" type="text" size="30" title="Factura" value="<? echo $tx_factura ?>"/></td>
                <td><div id="errcap_factura" style="float:left;"></div></td>   
           	</tr>
            
            <tr>
            	<td class="ui-state-default">Monto:</td>
       	  		<td><input name="cap_monto" id="cap_monto" type="text" size="30" alt="signed-decimal-us" title="Monto" value="<? echo $fl_precio ?>"/></td>
   	  		  <td><div id="errcap_monto" style="float:left;"></div></td>   
           	</tr>
            <tr>
           		<td class="ui-state-default">Moneda:</td>
              	<td>
                	<select id="sel_moneda" name="sel_moneda">
                  	<option value="0" class="">--- S e l e c c i o n e ---</option>
                  	<?php						
						for ($i=0; $i < count($TheCatalogoMoneda); $i++)	{         			 
							while ($elemento = each($TheCatalogoMoneda[$i]))					  		
								$id_moneda	=$TheCatalogoMoneda[$i]['id_moneda'];		
								$tx_moneda	=$TheCatalogoMoneda[$i]['tx_moneda'];	
								if ($id_moneda == $ac_moneda ) echo "<option value='$tx_moneda' selected='selected'>$tx_moneda</option>";
								else echo "<option value='$tx_moneda'>$tx_moneda</option>";	
						}
					?>
                	</select>
                    <input name="id_moneda" id="id_moneda" type="hidden" size="10" value="<? echo $ac_moneda ?>"/>              	</td>
       	  		<td><div id="errsel_moneda" style="float:left;"></div></td>
            </tr>            
            <tr>
            	<td class="ui-amarillo">Monto en MXN</td>
              	<td>
		  	  <?php
					//echo "ac_moneda", $ac_moneda;
					//echo "<br>";
					
					if ($ac_moneda==1)		$tx_disabled = "disabled"; 
					elseif ($ac_moneda==2)	$tx_disabled = "";
					elseif ($ac_moneda==3)	$tx_disabled = "";
					
					//echo "tx_disabled", $tx_disabled;
					//echo "<br>";
				?>
		  	  <input name="cap_monto_mxn" id="cap_monto_mxn" type="text" size="30" alt="signed-decimal-us" title="Monto en MXN"  <?php echo $tx_disabled ?> value="<?php echo $cap_precio ?>"/></td>					
           	  <td><div id="errcap_monto_mxn" style="float:left;"></div></td>
            </tr>
            <tr>
              	<td class="ui-state-default">Tipo de Cambio:</td>
              	<td><input name='cap_tipo_cambio' id='cap_tipo_cambio' type='text' size='30' alt='decimal' title='Tipo de Cambio' value="<?php echo $fl_tipo_cambio ?>" disabled="disabled"/></td>
           	  	<td><div id="errcap_tipo_cambio" style="float:left;"></td>
            </tr>
            <tr>
              	<td class="ui-state-default">Fecha de la Factura:</td>
              	<td><input name="cap_fh_factura" id="cap_fh_factura" type="text" size="30" title="Fecha Factura" value="<?php echo cambiaf_a_normal($fh_factura) ?>" pattern="dd/MM/yyyy"/></td>
           	  <td><div id="errcap_fh_factura" style="float:left;"></div></td>  
            </tr>
            <tr>
            	<td class="ui-state-default">Fecha Inicial:</td>
           	  	<td><input name="cap_fh_inicial" id="cap_fh_inicial" type="text" size="30" title="Fecha Inicial" value="<?php echo cambiaf_a_normal($fh_inicio) ?>" pattern="dd/MM/yyyy"/></td>
       	  	  	<td><div id="errcap_fh_inicial" style="float:left;"></div></td>   
           	</tr>
            <tr>
            	<td class="ui-state-default">Fecha Final:</td>
           	  	<td>
                	<input name="cap_fh_final" id="cap_fh_final" type="text" size="30" title="Fecha Final" value="<?php echo cambiaf_a_normal($fh_final) ?>" pattern="dd/MM/yyyy" />                </td>
       	  	  	<td width="20%"><div id="errcap_fh_final" style="float:left;"></div></td>   
           	</tr>
            <tr>
           	  	<td class="ui-amarillo">Fecha Contable:</td>
              	<td><input name="cap_fh_contable" id="cap_fh_contable" type="text" size="30" title="Fecha Contable" value="<?php echo cambiaf_a_normal($fh_contable) ?>" pattern="dd/MM/yyyy" /></td>
           	  <td>&nbsp;</td>
            </tr>
            <tr>
            	<td class="ui-state-default">Mes de Pago:</td>
           	  	<td>
                	<select id="sel_amortiza" name="sel_amortiza">
                  	<option value="0" class="">--- S e l e c c i o n e ---</option>
                  	<?php
						for ($i=0; $i < count($TheCatalogoMes); $i++)	{         			 
							while ($elemento = each($TheCatalogoMes[$i]))					  		
								$id_mes	=$TheCatalogoMes[$i]['id_mes'];	
								$tx_mes	=$TheCatalogoMes[$i]['tx_mes'];	
								if ($ac_mes == $id_mes ) echo "<option value=$id_mes selected='selected'>$tx_mes</option>";
								else echo "<option value=$id_mes>$tx_mes</option>";	
						}
					?>
                	</select>                </td>
       	  	  	<td><div id="errsel_amortiza" style="float:left;"></div></td>   
           	</tr> 
            <tr>
           	  	<td class="ui-state-default">Estatus</td>
              	<td>
                <select id="sel_estatus" name="sel_estatus">
                <option value="0" class="">--- S e l e c c i o n e ---</option>
                <?						
				for ($i=0; $i < count($TheCatalogoEstatus); $i++)	{         			 
					while ($elemento = each($TheCatalogoEstatus[$i]))					  		
						$id_factura_estatus	=$TheCatalogoEstatus[$i]['id_factura_estatus'];		
						$tx_estatus			=$TheCatalogoEstatus[$i]['tx_estatus'];	
						if ($id_factura_estatus == $ac_factura_estatus ) echo "<option value=$id_factura_estatus selected='selected'>$tx_estatus</option>";
						else echo "<option value=$id_factura_estatus>$tx_estatus</option>";	
				}
				?>
                </select>                </td>
           	  	<td><div id="errsel_estatus" style="float:left;"></div></td>
          	</tr>
            <tr>
              	<td class="ui-amarillo">N&uacute;mero de Documento GPS:</td>
              	<td>
              		<input name="cap_referencia" id="cap_referencia" type="text" size="30" title="Referencia" value="<? echo $tx_referencia ?>"/>                </td>
              	<td><div id="errcap_referencia" style="float:left;"></div></td>
            </tr>
            <tr>
            	<td class="ui-state-default">Archivo PDF:</td>
           	  	<td>
                	<input name="cap_ruta" id="cap_ruta" type="text" size="30" title="Ruta" value="<? echo $tx_ruta ?>"/>                </td>
       	  	  	<td></td>   
           	</tr>  
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
                	<a id="btnSaveFac" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Guardar
                    <span class="ui-icon ui-icon-disk"/></a>
                    <a id="btnUndoFac" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Cancelar
                    <span class="ui-icon ui-icon-cancel"/></a>                </td>
           	</tr>
           	<tr>
           		<td colspan="5" class="ui-state-highlight">&nbsp;</td>                      
            </tr>
          <!--  <tr>
            	<td colspan="5"><div class="fm-button ui-state-default fontMedium fm-button-icon-right" onclick="hideFiles()">Archivos adjuntos<span class="ui-icon ui-icon-circle-triangle-s" style="cursor:pointer;text-align:right" /></div>                </td>
            </tr>   -->     
    	</table>     
  </form>
</div>    
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  