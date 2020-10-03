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
	
	function fieldsRequiredListaAnaquel(){
	
		var error = true;		
		var moneda = $("#sel_moneda").val();
		
		validText(false, $("#cap_proyecto"), $("#errcap_proyecto"), 1);		
		validText(false, $("#cap_monto"), $("#errcap_monto"), 1);		
		validSelect($("#sel_anio_cap"), $("#errsel_anio_cap"));
		validSelect($("#sel_direccion"), $("#errsel_direccion"));
		validSelect($("#sel_glg"), $("#errsel_glg"));
		validSelect($("#sel_departamento"), $("#errsel_departamento"));
		validSelect($("#sel_detalle"), $("#errsel_detalle"));
		validSelect($("#sel_moneda"), $("#errsel_moneda"));
		validSelect($("#sel_prioridad"), $("#errsel_prioridad"));
		validSelect($("#sel_tipo"), $("#errsel_tipo"));
		
		if( !validText(false, $("#cap_proyecto"), $("#errcap_proyecto"), 1) || 
			!validText(false, $("#cap_monto"), $("#errcap_monto"), 1) || 
			!validSelect($("#sel_anio_cap"), $("#errsel_anio_cap")) ||
			!validSelect($("#sel_direccion"), $("#errsel_direccion")) ||
			!validSelect($("#sel_glg"), $("#errsel_glg")) ||
			!validSelect($("#sel_departamento"), $("#errsel_departamento")) ||
			!validSelect($("#sel_detalle"), $("#errsel_detalle")) ||
			!validSelect($("#sel_moneda"), $("#errsel_moneda")) ||
			!validSelect($("#sel_prioridad"), $("#errsel_prioridad")) ||
			!validSelect($("#sel_tipo"), $("#errsel_tipo"))
		) error = false;
        		 
		return error;
	}
	
	$("#btnSaveAna").click(function(){     
	 
		if(fieldsRequiredListaAnaquel()){	
			var url = "process_anaquel.php?";
         		url += $("#formAnaquel").serialize(); 

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
						$("#divAltaAnaquel").hide();
						loadHtmlAjax(true, $("#divDatos"), data.html);
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
	 
	$("#btnUndoAna").click(function(){  
		$("#divAltaAnaquel").hide();
	}).hover(function(){
    	$(this).addClass("ui-state-hover")
    },function(){
       $(this).removeClass("ui-state-hover")
    });
	 
	$(function(){
    	$('input:text').setMask();
    });

	///////////////// DEFINICION DE EVENTOS //////////////////////	 
	$("#sel_direccion").change(function () {
    	$("#sel_direccion option:selected").each(function () {
			var id="id="+$(this).val();		
			$("#sel_departamento").html("");					
            loadHtmlAjax(false, $("#sel_subdireccion"), "combo_subdireccion1.php?"+id);			
       	});
   	});
	
	$("#sel_subdireccion").change(function () {
    	$("#sel_subdireccion option:selected").each(function () {
			var id="id="+$("#sel_direccion").val();	
			var id1="&id1="+$(this).val();	
			//alert (id + " " + id1);
			loadHtmlAjax(false, $("#sel_departamento"), "combo_departamento1.php?"+id+id1);
       	});
   	});
	
	$("#sel_glg").change(function () {
    	$("#sel_glg option:selected").each(function () {
			var id="id="+$(this).val();		
			$("#sel_cuenta").html("");					
            loadHtmlAjax(false, $("#sel_cuenta"), "combo_glg_cuenta.php?"+id);			
       	});
   	});
	
	$("#sel_empleado").change(function () {
    	$("#sel_empleado option:selected").each(function () {
		
			var url = "busca_centro_empleado.php?id="+$("#sel_empleado").val();			  
			var func = function(data){					   			
	 			if(data.pasa == true){							
					$("#cap_direccion").val(data.data1);	
					$("#cap_subdireccion").val(data.data2);	
					$("#cap_departamento").val(data.data3);	
					$("#cap_direccion_id").val(data.data4);	
					$("#cap_subdireccion_id").val(data.data5);	
					$("#cap_departamento_id").val(data.data6);	
				}				
			} 
			executeAjax("post", false ,url, "json", func); 						
        });
     });
	
	
	$("#sel_cuenta").change(function () {
    	$("#sel_cuenta option:selected").each(function () {		
			var url = "combo_tipo_gasto.php?id="+$("#sel_cuenta").val();			  
			var func = function(data){					   			
	 			if(data.pasa == true){							
					$("#cap_tipo_gasto").val(data.data1);	
				}				
			} 
			executeAjax("post", false ,url, "json", func); 						
    	});
    });
	  
	$("#cap_proyecto").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_proyecto").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_descripcion").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_descripcion").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	 
 	$("#cap_monto").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_monto").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	
	
	$("#cap_negocio").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_negocio").blur(function() {
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
	$id_anaquel	= $_GET['id'];	

	# ============================================
	# Catalogo de Anios
	# ============================================
	$sql = "   SELECT tx_anio ";
	$sql.= "   	 FROM tbl_anio ";
	//$sql.= "   	WHERE tx_indicador='1' ";
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
	# Catalogo de MacroIniciativa	
	# ============================================
	$sql = "   SELECT id_macroiniciativa, tx_macroiniciativa ";
	$sql.= "     FROM tbl_macroiniciativa ";
	$sql.= "    WHERE tx_indicador = '1' ";
	$sql.= " ORDER BY id_macroiniciativa ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoMacroiniciativa[] = array(
			'id_macroiniciativa'	=> $row["id_macroiniciativa"],
			'tx_macroiniciativa'	=> $row["tx_macroiniciativa"]
		);
	}
	
	# ============================================
	# Catalogo de Empleados	
	# ============================================	
	$sql = "   SELECT id_empleado, tx_empleado ";
	$sql.= "   	 FROM tbl_empleado  ";
	$sql.= " ORDER BY tx_empleado " ; 	
		
	//echo "aaa", $sql;
			
	$result = mysqli_query($mysql, $sql);		
		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoEmpleado[] = array(
			'id_empleado'	=>$row["id_empleado"],
			'tx_empleado'	=>$row["tx_empleado"]
			);
	} 
	
	# ============================================
	# Catalogo de Moneda
	# ============================================
	$sql = "   SELECT id_moneda, tx_moneda ";
	$sql.= "     FROM tbl_moneda ";
	$sql.= "    WHERE id_moneda = 2 ";
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
	
	# =============================
	# Catalogo de Direcciones
	# =============================	
	$sql = "   SELECT id_direccion, tx_nombre_corto ";
	$sql.= "     FROM tbl_direccion ";
	$sql.= "    WHERE id_entidad = 1 ";
	$sql.= " ORDER BY tx_nombre_corto ";
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoDireccion[] = array(
			'id_direccion'		=>$row["id_direccion"],
			'tx_nombre_corto'	=>$row["tx_nombre_corto"]
		);
	} 	
	
	# =============================
	# Catalogo de GLG
	# =============================	
	$sql = "   SELECT tx_glg, tx_cuenta ";
	$sql.= "     FROM tbl_glg ";
	$sql.= "    WHERE tx_indicador = '1' ";
	$sql.= "  GROUP BY tx_glg ";
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoGLG[] = array(
			'tx_glg'	=> $row["tx_glg"]
		);
	} 
	
	# =============================
	# Catalogo de Prioridad
	# =============================	
	$sql = "   SELECT id_prioridad, tx_prioridad ";
	$sql.= "     FROM tbl_prioridad ";
	$sql.= "    WHERE tx_indicador = '1' ";
	$sql.= " ORDER BY tx_prioridad ";
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoPrioridad[] = array(
			'id_prioridad'	=> $row["id_prioridad"],
			'tx_prioridad'	=> $row["tx_prioridad"]
		);
	} 

	# =============================
	# Catalogo Anaquel Tipo
	# =============================	
	$sql = "   SELECT id_anaquel_tipo, tx_tipo ";
	$sql.= "     FROM tbl_anaquel_tipo ";
	$sql.= "    WHERE tx_indicador = '1' ";
	$sql.= " ORDER BY tx_tipo ";
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoAnaquelTipo[] = array(
			'id_anaquel_tipo'	=> $row["id_anaquel_tipo"],
			'tx_tipo'			=> $row["tx_tipo"]
		);
	} 
	
	if ($dispatch=="insert") {	
		
		$titulo 		= "PARTIDAS - REGISTRO";		
		$tx_indicador	= "1";
	
 	} else if ($dispatch=="save") {
		
		$titulo 	= "PARTIDAS - MODIFICACION";
		$ac_moneda 	= 2;
		
		# ==========================================
		# Carga la informacion para la actualizacion
		# ==========================================		
				
		$sql = "   SELECT id_anaquel, id_empleado, id_macroiniciativa, id_direccion, id_subdireccion, id_departamento, id_glg, id_prioridad, id_anaquel_tipo, tx_anio, tx_proyecto, tx_descripcion, fl_monto_usd, in_consecutivo, tx_notas, a.fh_mod,  b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta ";
		$sql.= "     FROM tbl_anaquel a, tbl_usuario b, tbl_usuario c ";
		$sql.= "    WHERE id_anaquel 		= $id_anaquel ";
		$sql.= "      AND a.id_usuariomod	= b.id_usuario ";
		$sql.= "      AND a.id_usuarioalta 	= c.id_usuario ";
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoAnaquel[] = array(
				'id_anaquel'		=>$row["id_anaquel"],
				'id_empleado'		=>$row["id_empleado"],
				'id_macroiniciativa'=>$row["id_macroiniciativa"],
				'id_direccion'		=>$row["id_direccion"],
				'id_subdireccion'	=>$row["id_subdireccion"],
				'id_departamento'	=>$row["id_departamento"],
				'id_glg'			=>$row["id_glg"],
				'id_prioridad'		=>$row["id_prioridad"],
				'id_anaquel_tipo'	=>$row["id_anaquel_tipo"],
				'tx_anio'			=>$row["tx_anio"],
				'tx_proyecto'		=>$row["tx_proyecto"],				
				'tx_descripcion'	=>$row["tx_descripcion"],				
				'fl_monto_usd'		=>$row["fl_monto_usd"],				
				'in_consecutivo'	=>$row["in_consecutivo"],				
				'tx_notas'			=>$row["tx_notas"],				
				'fh_alta'			=>$row["fh_alta"],
				'usuario_alta'		=>$row["usuario_alta"],				
				'fh_mod'			=>$row["fh_mod"],
				'usuario_mod'		=>$row["usuario_mod"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoAnaquel); $i++)	{         			 
			while ($elemento = each($TheCatalogoAnaquel[$i]))				
				$id_anaquel			=$TheCatalogoAnaquel[$i]['id_anaquel'];						  		
				$ac_empleado		=$TheCatalogoAnaquel[$i]['id_empleado'];	
				$ac_macroiniciativa	=$TheCatalogoAnaquel[$i]['id_macroiniciativa'];	
				$ac_direccion		=$TheCatalogoAnaquel[$i]['id_direccion'];	
				$ac_subdireccion	=$TheCatalogoAnaquel[$i]['id_subdireccion'];	
				$ac_departamento	=$TheCatalogoAnaquel[$i]['id_departamento'];	
				$ac_glg				=$TheCatalogoAnaquel[$i]['id_glg'];	
				$ac_prioridad		=$TheCatalogoAnaquel[$i]['id_prioridad'];					
				$ac_anaquel_tipo	=$TheCatalogoAnaquel[$i]['id_anaquel_tipo'];	
				$ac_anio			=$TheCatalogoAnaquel[$i]['tx_anio'];		
				$tx_proyecto		=$TheCatalogoAnaquel[$i]['tx_proyecto'];		
				$tx_descripcion		=$TheCatalogoAnaquel[$i]['tx_descripcion'];		
				$fl_monto_usd		=$TheCatalogoAnaquel[$i]['fl_monto_usd'];		
				$in_consecutivo		=$TheCatalogoAnaquel[$i]['in_consecutivo'];		
				$tx_notas			=$TheCatalogoAnaquel[$i]['tx_notas'];		
				$fh_alta			=$TheCatalogoAnaquel[$i]['fh_alta'];
				$usuario_alta		=$TheCatalogoAnaquel[$i]['usuario_alta'];
				$fh_mod				=$TheCatalogoAnaquel[$i]['fh_mod'];
				$usuario_mod		=$TheCatalogoAnaquel[$i]['usuario_mod'];
		} 	
		
		# ==============================
		# Catalogo de Direccion
		# ==============================	
		$sql = "   SELECT id_direccion, tx_nombre AS tx_direccion ";
		$sql.= "     FROM tbl_direccion ";
		$sql.= "    WHERE id_direccion = $ac_direccion ";
		
		//echo "sql",$sql;		
			
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoDireccion[] = array(
				'id_direccion'	=> $row["id_direccion"],
				'tx_direccion'	=> $row["tx_direccion"]
			);
		} 	
		
		for ($i=0; $i < count($TheCatalogoDireccion); $i++)	{         			 
			while ($elemento = each($TheCatalogoDireccion[$i]))				
				$id_direccion =$TheCatalogoDireccion[$i]['id_direccion'];						  		
				$tx_direccion =$TheCatalogoDireccion[$i]['tx_direccion'];	
		} 		

		# ==============================
		# Catalogo de Subdireccion
		# ==============================	
		$sql = "   SELECT id_subdireccion, tx_subdireccion ";
		$sql.= "     FROM tbl_subdireccion ";
		$sql.= "    WHERE id_subdireccion = $ac_subdireccion ";
		$sql.= " ORDER BY tx_subdireccion ";
		
		//echo "sql",$sql;		
			
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoSubdireccion[] = array(
				'id_subdireccion'	=> $row["id_subdireccion"],
				'tx_subdireccion'	=> $row["tx_subdireccion"]
			);
		} 	
		
		for ($i=0; $i < count($TheCatalogoSubdireccion); $i++)	{         			 
			while ($elemento = each($TheCatalogoSubdireccion[$i]))				
				$id_subdireccion =$TheCatalogoSubdireccion[$i]['id_subdireccion'];						  		
				$tx_subdireccion =$TheCatalogoSubdireccion[$i]['tx_subdireccion'];	
		} 		
				
		# ==============================
		# Catalogo de Departamentos
		# ==============================	
		$sql = "   SELECT id_departamento, tx_departamento ";
		$sql.= "     FROM tbl_departamento ";
		$sql.= "    WHERE id_departamento = $ac_departamento ";
		
		//echo "sql",$sql;		
			
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoDepartamento[] = array(
				'id_departamento'	=> $row["id_departamento"],
				'tx_departamento'	=> $row["tx_departamento"]
			);
		} 	
		
		for ($i=0; $i < count($TheCatalogoDepartamento); $i++)	{         			 
			while ($elemento = each($TheCatalogoDepartamento[$i]))				
				$id_departamento =$TheCatalogoDepartamento[$i]['id_departamento'];						  		
				$tx_departamento =$TheCatalogoDepartamento[$i]['tx_departamento'];	
		} 	
				
		# =============================
		# Catalogo de GLG
		# =============================	
		$sql = "   SELECT id_glg, tx_glg, tx_cuenta, tx_tipo_gasto ";
		$sql.= "     FROM tbl_glg a, tbl_tipo_gasto b ";
		$sql.= "    WHERE id_glg = $ac_glg ";
			
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoGLG_2[] = array(
				'tx_glg'		=> $row["tx_glg"],
				'tx_tipo_gasto'	=> $row["tx_tipo_gasto"]
			);
		} 	
		
		for ($i=0; $i < count($TheCatalogoGLG_2); $i++)	{         			 
			while ($elemento = each($TheCatalogoGLG_2[$i]))				
				$ac_tx_glg 		= $TheCatalogoGLG_2[$i]['tx_glg'];
				$tx_tipo_gasto 	= $TheCatalogoGLG_2[$i]['tx_tipo_gasto'];						  		
		} 			
	}		
?>
<br>
<div>
    <form id="formAnaquel" action="">
    	<input id="id_anaquel" name="id_anaquel" type="hidden" value="<? echo $id_anaquel ?>" />
    	<input id="dispatch" name="dispatch" type="hidden" value="<? echo $dispatch ?>" />        
	  	<table cellspacing="1px" border="0" cellpadding="0" width="100%">   		
   			<tr>
                <td colspan="5" class="ui-state-highlight" align="center" style="font-family:Verdana,Arial,sans-serif;font-size: 12px;font-weight:bold;"><? echo $titulo ?></td>  
         	</tr>         	                          
            <tr>
            	<td width="20%" class="ui-state-default">A&ntilde;o:</td>
              	<td width="60%">
                <select id="sel_anio_cap" name="sel_anio_cap">
                <!-- <option value="0" class="">--- S e l e c c i o n e ---</option> -->
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
              	<td class="ui-state-default">Macro Iniciativa</td>
              	<td>
                <select id="sel_macroiniciativa" name="sel_macroiniciativa">
                <option value="0" class="">--- S e l e c c i o n e ---</option>
                <?
                for ($i=0; $i < count($TheCatalogoMacroiniciativa); $i++)	{         			 
                   	while ($elemento = each($TheCatalogoMacroiniciativa[$i]))					  		
                       	$id_macroiniciativa	= $TheCatalogoMacroiniciativa[$i]['id_macroiniciativa'];		
                      	$tx_macroiniciativa	= $TheCatalogoMacroiniciativa[$i]['tx_macroiniciativa'];
						if ($ac_macroiniciativa == $id_macroiniciativa ) echo "<option value=$id_macroiniciativa selected='selected'>$tx_macroiniciativa</option>";
                       	else echo "<option value=$id_macroiniciativa>$tx_macroiniciativa</option>";	
                }
                ?>
                </select>                </td>
           	  <td>&nbsp;</td>
            </tr>
            <tr>
            	<td class="ui-state-default">Persona que Solicita:</td>
              	<td>                
              	<select name="sel_empleado" id="sel_empleado">
              	<option value="0">--- S e l e c c i o n e ---</option>
              	<?								
				for ($i = 0; $i < count($TheCatalogoEmpleado); $i++)
				{	         			 
					while ($elemento = each($TheCatalogoEmpleado[$i]))					
						$id_empleado=$TheCatalogoEmpleado[$i]['id_empleado'];		
						$tx_empleado=$TheCatalogoEmpleado[$i]['tx_empleado'];							
						if ($ac_empleado == $id_empleado) echo "<option value=$id_empleado selected='selected'>$tx_empleado</option>";
						else echo "<option value=$id_empleado>$tx_empleado</option>";
				}						 
				?>
              	</select>                </td>
              	<td>&nbsp;</td>
            </tr>
            <tr>
            	<td class="ui-state-default">Direcci&oacute;n:</td>
          		<td>
                	<input name="cap_direccion" id="cap_direccion" type="text" size="60" title="Direcci&oacute;n" value="<? echo $tx_direccion ?>"  disabled="disabled"/>
                	<input name="cap_direccion_id" id="cap_direccion_id" type="hidden" value="<? echo $id_direccion ?>"/></td>
              	<td><div id="errsel_direccion" style="float:left;"></div></td>   
            </tr>
            <tr>
              	<td class="ui-state-default">Subdirecci&oacute;n:</td>
              	<td>
                	<input name="cap_subdireccion" id="cap_subdireccion" type="text" size="60" title="Subdirecci&oacute;n" value="<? echo $tx_subdireccion ?>"  disabled="disabled"/>
                	<input name="cap_subdireccion_id" id="cap_subdireccion_id" type="hidden" value="<? echo $id_subdireccion ?>"/></td>
              	<td>&nbsp;</td>
            </tr>
            <tr>
            	<td class="ui-state-default">Area Solicitante:</td>
                <td>
                	<input name="cap_departamento" id="cap_departamento" type="text" size="60" title="Area" value="<? echo $tx_departamento ?>" disabled="disabled"/>
                  	<input name="cap_departamento_id" id="cap_departamento_id" type="hidden" value="<? echo $id_departamento ?>"/>                </td>
                <td><div id="errsel_departamento" style="float:left;"></div></td>   
          	</tr>
           	<tr>
            	<td class="ui-state-default">GLG:</td>
              	<td>                            
                <select id="sel_glg" name="sel_glg">
                <option value="0" class="">--- S e l e c c i o n e ---</option>
                <?
				for ($i=0; $i < count($TheCatalogoGLG); $i++)	{         			 
					while ($elemento = each($TheCatalogoGLG[$i]))
						$id_glg		= $TheCatalogoGLG[$i]['id_glg'];					  		
						$tx_glg		= $TheCatalogoGLG[$i]['tx_glg'];	
						if ($ac_tx_glg == $tx_glg ) echo "<option value='$tx_glg' selected='selected'>$tx_glg</option>";
						else echo "<option value='$tx_glg'>$tx_glg</option>";	
				}
				?>
                </select></td>
                <td><div id="errsel_glg" style="float:left;"></div></td>   
           	</tr>
            <tr>
            	<td class="ui-state-default">Cuenta:</td>
              	<td>
              	<select id="sel_cuenta" name="sel_cuenta">
              	<option value="0" class="">--- S e l e c c i o n e ---</option>              				
              	</select>                </td>
              	<td>&nbsp;</td>
            </tr>
            <tr>
              <td class="ui-state-default">Tipo</td>
              <td><input name="cap_tipo_gasto" id="cap_tipo_gasto" type="text" size="60" title="Tipo" value="<? echo $tx_tipo_gasto ?>" disabled="disabled"/></td>
              <td>&nbsp;</td>
            </tr>
            <tr>
           		<td class="ui-state-default">Proyecto:</td>
              	<td><input name="cap_proyecto" id="cap_proyecto" type="text" size="100" title="Proyecto" value="<? echo $tx_proyecto ?>"/></td>
           	  	<td><div id="errcap_proyecto" style="float:left;"></div></td>
            </tr>
            <tr>
              	<td class="ui-state-default">Detalle:</td>
              	<td><textarea id="cap_descripcion" name="cap_descripcion" cols="100" rows="6" title="Detalle del Proyecto"><? echo $tx_descripcion ?></textarea></td>
           	  	<td><div id="errcap_descripcion" style="float:left;"></td>
            </tr>
            <tr>
              	<td class="ui-state-default">Monto Estimado:</td>
              	<td><input name="cap_monto" id="cap_monto" type="text" size="20" alt="signed-decimal-us" title="Monto" value="<? echo $fl_monto_usd ?>"/></td>
           	  	<td><div id="errcap_monto" style="float:left;"></div></td>  
            </tr>
            <tr>
            	<td class="ui-state-default">Moneda:</td>
           	  	<td>
                <select id="sel_moneda" name="sel_moneda">
                <!-- <option value="0" class="">--- S e l e c c i o n e ---</option> -->
                <?						
				for ($i=0; $i < count($TheCatalogoMoneda); $i++) {         			 
					while ($elemento = each($TheCatalogoMoneda[$i]))					  		
						$id_moneda	=$TheCatalogoMoneda[$i]['id_moneda'];		
						$tx_moneda	=$TheCatalogoMoneda[$i]['tx_moneda'];	
						if ($ac_moneda == $id_moneda) echo "<option value='$tx_moneda' selected='selected'>$tx_moneda</option>";
						else echo "<option value='$tx_moneda'>$tx_moneda</option>";	
				}
				?>
                </select>                </td>
       	  	  	<td><div id="errsel_moneda" style="float:left;"></div></td>   
           	</tr>
            <tr>
            	<td class="ui-state-default">Prioridad Proyecto:</td>
           	  	<td><input name="cap_negocio" id="cap_negocio" type="text" size="20" alt="integer" title="Prioridad Negocio" value="<? echo $in_consecutivo ?>"/></td>
       	  	  	<td width="20%"><div id="errcap_fh_final" style="float:left;"></div></td>   
           	</tr>
            <tr>
            	<td class="ui-state-default">Prioridad Negocio:</td>
           	  	<td><select id="sel_prioridad" name="sel_prioridad">
                  <option value="0" class="">--- S e l e c c i o n e ---</option>
                  <?
				for ($i=0; $i < count($TheCatalogoPrioridad); $i++)	{         			 
					while ($elemento = each($TheCatalogoPrioridad[$i]))					  		
						$id_prioridad	= $TheCatalogoPrioridad[$i]['id_prioridad'];	
						$tx_prioridad	= $TheCatalogoPrioridad[$i]['tx_prioridad'];	
						if ($ac_prioridad == $id_prioridad ) echo "<option value=$id_prioridad selected='selected'>$tx_prioridad</option>";
						else echo "<option value=$id_prioridad>$tx_prioridad</option>";	
				}
				?>
                </select></td>
       	  	  	<td><div id="errsel_prioridad" style="float:left;"></div></td>   
           	</tr> 
            <tr>
           	  	<td class="ui-state-default">Corporativo/Local:</td>
              	<td><select id="sel_tipo" name="sel_tipo">
                  <option value="0" class="">--- S e l e c c i o n e ---</option>
                  <?						
				for ($i=0; $i < count($TheCatalogoAnaquelTipo); $i++)	{         			 
					while ($elemento = each($TheCatalogoAnaquelTipo[$i]))					  		
						$id_anaquel_tipo	= $TheCatalogoAnaquelTipo[$i]['id_anaquel_tipo'];		
						$tx_tipo			= $TheCatalogoAnaquelTipo[$i]['tx_tipo'];	
						if ($ac_anaquel_tipo == $id_anaquel_tipo ) echo "<option value=$id_anaquel_tipo selected='selected'>$tx_tipo</option>";
						else echo "<option value=$id_anaquel_tipo>$tx_tipo</option>";	
				}
				?>
                </select></td>
           	  	<td><div id="errsel_tipo" style="float:left;"></div></td>
          	</tr>  
            <tr>
            	<td class="ui-state-default" valign="top">Notas:</td>
           	  	<td>
                	<textarea id="cap_notas" name="cap_notas" cols="100" rows="4" title="Notas"><? echo $tx_notas ?></textarea></td>
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
                	<a id="btnSaveAna" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Guardar
                    <span class="ui-icon ui-icon-disk"></span></a>
                    <a id="btnUndoAna" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Cancelar
                    <span class="ui-icon ui-icon-cancel"></span></a>               	</td>
           	</tr>
           	<tr>
           		<td colspan="5" class="ui-state-highlight">&nbsp;</td>                      
            </tr>
    	</table>     
  </form>
</div>    
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  