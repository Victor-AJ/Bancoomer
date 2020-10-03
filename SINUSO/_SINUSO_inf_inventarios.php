<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">
	
	$(function() {   
		$('#tabs').tabs();		
				
		var accion=$("#dispatch").val();
		
		if(accion=="insert") 
		{
			$("#divMasDatos").show().fadIn('slow');				
		} else {
			var id="id="+$("#id").val();			
			//var dispatch="&dispatch=delete";
			var url1="cat_licencias_lista.php?"+id;   
			var url2="cat_computo_lista.php?"+id;   
			var url3="cat_telefonia_lista.php?"+id;   
			
			$("#divMasDatos").hide();		
			loadHtmlAjax(true, $('#divLicencias'), url1);				
			loadHtmlAjax(true, $('#divComputo'), url2);
			loadHtmlAjax(true, $('#divTelefonia'), url3);
		}
	});	
	
	function formatCounterpart(row) {
		return row[0];	
    }
	
	function formatItem(row) {
        return "<strong>"+row[0]+"</strong>";
    }
	
	$("#btnMasInf").click(function(){
		//$("#divDivBoton").removeClass("ui-icon ui-icon-circle-plus");		
        //$("#divDivBoton").html("<a id='btnMenosInf' href='javascript:void(0)' title='Presione para cerrar' class='ui-icon ui-icon-circle-minus'></a>");
		$("#divMasDatos").show().fadIn('slow');
     });
	 
 	$("#btnMenosInf").click(function(){  		 		 
		$("#divMasDatos").hide();		 
	 });

	 ///////////////// DEFINICION DE EVENTOS //////////////////////
     $("#sel_centro").change(function () {
     	$("#sel_centro option:selected").each(function () {
		
			var url = "busca_centro.php?id="+$("#sel_centro").val();			  
			var func = function(data){					   			
	 			if(data.pasa == true){							
					$("#cap_direccion").val(data.data1);	
					$("#cap_subdireccion").val(data.data2);	
					$("#cap_departamento").val(data.data3);	
				}				
			} 
			executeAjax("post", false ,url, "json", func); 						
        });
     });
	 
	 $("#sel_ubicacion").change(function () {
     	$("#sel_ubicacion option:selected").each(function () {
		
			var url = "busca_ubicacion.php?id="+$("#sel_ubicacion").val();			  
			var func = function(data){					   			
	 			if(data.pasa == true){							
					$("#cap_estado").val(data.data1);	
					$("#cap_pais").val(data.data2);	
				}				
			} 
			executeAjax("post", false ,url, "json", func); 						
        });
     });
	 
	$("#cap_empleado").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_empleado").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_registro").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_registro").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_usuario_red").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_usuario_red").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_usuario_espacio").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_usuario_espacio").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });

	$("#cap_direccion").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_direccion").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_telefono").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_telefono").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_subdireccion").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_subdireccion").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_departamento").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_departamento").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_correo").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_correo").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_categoria").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_categoria").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	
	
	$("#cap_responsable").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_responsable").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	
	
	$("#cap_tipologia").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_tipologia").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	

	$("#cap_funcion").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_funcion").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	
	
	$("#cap_notas").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_notas").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
	
	$("#cap_n3").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_n3").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	

	$("#cap_n4").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_n4").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	

	$("#cap_n5").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_n5").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	

	$("#cap_n6").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_n6").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	

	$("#cap_n7").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_n7").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	

	$("#cap_n8").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_n8").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });	

	$("#cap_n9").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

    $("#cap_n9").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });		
	
	$("#cap_categoria").autocomplete("process_empleados.php?dispatch=find&campo=categoria",{
    	minChars: 2,
        max: 10,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#cap_categoria").val(item[0]);
   	});
	
	$("#cap_responsable").autocomplete("process_empleados.php?dispatch=find&campo=responsable",{
    	minChars: 2,
        max: 10,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#cap_responsable").val(item[0]);
   	});
	
	$("#cap_tipologia").autocomplete("process_empleados.php?dispatch=find&campo=tipologia",{
    	minChars: 2,
        max: 10,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#cap_tipologia").val(item[0]);
   	});
	
	$("#cap_funcion").autocomplete("process_empleados.php?dispatch=find&campo=funcion",{
    	minChars: 2,
        max: 10,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#cap_funcion").val(item[0]);
   	});
	 
</script>

<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	//Resivo variables
	//===========================
	$dispatch	= $_GET['dispatch'];
	$id			= $_GET['id'];	
	$registro	= $_GET['tx_registro'];	
	$accion  	= $_GET['accion'];	
	
	//echo "accion",$accion;
	//echo "<br>";
	//echo "dispatch",$dispatch;
	//echo "<br>";
	//echo "id",$id;
	
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
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_empleado  ";
		//$sql.= "  WHERE id_empleado	= $id  ";
		
		if ($accion=="nuevo") 
		{
			$sql = " SELECT * ";
			$sql.= " FROM tbl_empleado a, tbl_ubicacion b, tbl_estado c, tbl_pais d, tbl_centro_costos e, tbl_departamento f, tbl_subdireccion g, tbl_direccion h ";
			$sql.= " WHERE a.tx_registro 		= '$registro' ";
			$sql.= "   AND a.id_ubicacion 		= b.id_ubicacion ";
			$sql.= "   AND b.id_estado  		= c.id_estado ";
			$sql.= "   AND b.id_pais   			= c.id_pais ";
			$sql.= "   AND a.id_centro_costos 	= e.id_centro_costos ";
			$sql.= "   AND e.id_departamento 	= f.id_departamento ";
			$sql.= "   AND e.id_subdireccion 	= g.id_subdireccion ";
			$sql.= "   AND h.id_direccion 		= g.id_direccion ";		
		} else {
			$sql = " SELECT a.*, f.tx_departamento, g.tx_subdireccion, d.tx_pais, c.tx_estado, h.tx_nombre AS tx_direccion,i.tx_nombre AS usuario_alta, i.tx_nombre AS usuario_mod, a.fh_alta AS fh_alta_empleado, j.tx_nombre AS usuario_alta, a.fh_mod AS fh_mod_empleado ";
			$sql.= " FROM tbl_empleado a, tbl_ubicacion b, tbl_estado c, tbl_pais d, tbl_centro_costos e, tbl_departamento f, tbl_subdireccion g, tbl_direccion h, tbl_usuario i, tbl_usuario j ";
			$sql.= " WHERE id_empleado 			= $id ";
			$sql.= "   AND a.id_ubicacion 		= b.id_ubicacion ";
			$sql.= "   AND b.id_estado  		= c.id_estado ";
			$sql.= "   AND b.id_pais   			= c.id_pais ";
			$sql.= "   AND a.id_centro_costos 	= e.id_centro_costos ";
			$sql.= "   AND e.id_departamento 	= f.id_departamento ";
			$sql.= "   AND e.id_subdireccion 	= g.id_subdireccion ";
			$sql.= "   AND h.id_direccion 		= g.id_direccion ";
			$sql.= "   AND a.id_usuariomod 		= i.id_usuario ";
			$sql.= "   AND a.id_usuarioalta 	= j.id_usuario ";
		}	
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoEmpleado[] = array(
				'id_empleado'		=>$row["id_empleado"],
				'id_centro_costos'	=>$row["id_centro_costos"],
				'tx_departamento'	=>$row["tx_departamento"],
				'tx_subdireccion'	=>$row["tx_subdireccion"],
				'tx_direccion'		=>$row["tx_direccion"],		
				'id_ubicacion'		=>$row["id_ubicacion"],
				'tx_pais'			=>$row["tx_pais"],
				'tx_estado'			=>$row["tx_estado"],
				'tx_registro'		=>$row["tx_registro"],
				'tx_usuario_red'	=>$row["tx_usuario_red"],
				'tx_usuario_espacio'=>$row["tx_usuario_espacio"],
				'tx_empleado'		=>$row["tx_empleado"],
				'tx_categoria'		=>$row["tx_categoria"],
				'tx_tipologia'		=>$row["tx_tipologia"],
				'tx_funcion'		=>$row["tx_funcion"],
				'tx_responsable'	=>$row["tx_responsable"],
				'tx_telefono'		=>$row["tx_telefono"],
				'tx_correo'			=>$row["tx_correo"],
				'tx_notas'			=>$row["tx_notas"],
				'tx_n3'				=>$row["tx_n3"],
				'tx_n4'				=>$row["tx_n4"],
				'tx_n5'				=>$row["tx_n5"],
				'tx_n6'				=>$row["tx_n6"],
				'tx_n7'				=>$row["tx_n7"],
				'tx_n8'				=>$row["tx_n8"],
				'tx_n9'				=>$row["tx_n9"],
				'tx_indicador'		=>$row["tx_indicador"],
				'fh_alta'			=>$row["fh_alta_empleado"],
				'usuario_alta'		=>$row["usuario_alta"],
				'fh_mod'			=>$row["fh_mod_empleado"],
				'usuario_mod'		=>$row["usuario_mod"]
			);
		} 
			
		for ($i=0; $i < count($TheCatalogoEmpleado); $i++)	{         			 
			while ($elemento = each($TheCatalogoEmpleado[$i]))					  		
				$id_empleado		=$TheCatalogoEmpleado[$i]['id_empleado'];		
				$id_centro_costos	=$TheCatalogoEmpleado[$i]['id_centro_costos'];		
				$tx_departamento	=$TheCatalogoEmpleado[$i]['tx_departamento'];
				$tx_subdireccion	=$TheCatalogoEmpleado[$i]['tx_subdireccion'];
				$tx_direccion		=$TheCatalogoEmpleado[$i]['tx_direccion'];				
				$tx_pais			=$TheCatalogoEmpleado[$i]['tx_pais'];
				$id_ubicacion		=$TheCatalogoEmpleado[$i]['id_ubicacion'];
				$tx_pais			=$TheCatalogoEmpleado[$i]['tx_pais'];
				$tx_estado			=$TheCatalogoEmpleado[$i]['tx_estado'];
				$tx_registro		=$TheCatalogoEmpleado[$i]['tx_registro'];				
				$tx_usuario_red		=$TheCatalogoEmpleado[$i]['tx_usuario_red'];
				$tx_usuario_espacio	=$TheCatalogoEmpleado[$i]['tx_usuario_espacio'];				
				$tx_empleado		=$TheCatalogoEmpleado[$i]['tx_empleado'];				
				$tx_categoria		=$TheCatalogoEmpleado[$i]['tx_categoria'];
				$tx_tipologia		=$TheCatalogoEmpleado[$i]['tx_tipologia'];
				$tx_funcion			=$TheCatalogoEmpleado[$i]['tx_funcion'];
				$tx_responsable		=$TheCatalogoEmpleado[$i]['tx_responsable'];
				$tx_telefono		=$TheCatalogoEmpleado[$i]['tx_telefono'];
				$tx_correo			=$TheCatalogoEmpleado[$i]['tx_correo'];
				$tx_notas			=$TheCatalogoEmpleado[$i]['tx_notas'];
				$tx_n3				=$TheCatalogoEmpleado[$i]['tx_n3'];
				$tx_n4				=$TheCatalogoEmpleado[$i]['tx_n4'];
				$tx_n5				=$TheCatalogoEmpleado[$i]['tx_n5'];
				$tx_n6				=$TheCatalogoEmpleado[$i]['tx_n6'];
				$tx_n7				=$TheCatalogoEmpleado[$i]['tx_n7'];
				$tx_n8				=$TheCatalogoEmpleado[$i]['tx_n8'];
				$tx_n9				=$TheCatalogoEmpleado[$i]['tx_n9'];
				$tx_indicador		=$TheCatalogoEmpleado[$i]['tx_indicador'];
				$fh_alta			=$TheCatalogoEmpleado[$i]['fh_alta'];
				$usuario_alta		=$TheCatalogoEmpleado[$i]['usuario_alta'];				
				$fh_mod				=$TheCatalogoEmpleado[$i]['fh_mod'];
				$usuario_mod		=$TheCatalogoEmpleado[$i]['usuario_mod'];
		} 
	}	
	
	//Catalogo de Centro de Costos
	//============================
	
	$sql = "   SELECT id_centro_costos, tx_centro_costos ";
	$sql.= "     FROM tbl_centro_costos ";
	$sql.= " ORDER BY tx_centro_costos ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoCentro[] = array(
			'id_centro_costos'=>$row["id_centro_costos"],
	  		'tx_centro_costos'=>$row["tx_centro_costos"]
		);
	}

	//Catalogo de Ubicaciones
	//=======================
	
	$sql = "   SELECT id_ubicacion, tx_ubicacion ";
	$sql.= "     FROM tbl_ubicacion ";
	$sql.= " ORDER BY tx_ubicacion ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoUbicacion[] = array(
			'id_ubicacion'=>$row["id_ubicacion"],
	  		'tx_ubicacion'=>$row["tx_ubicacion"]
		);
	}
	
	if ($accion=="nuevo") 
	{
		$id=$id_empleado;
	}	
?>
<form id="opForm1" action="">
   	  	<input id="id" name="id" type="hidden" value="<? echo $id ?>" />
    	<input id="dispatch" name="dispatch" type="hidden" value="<? echo $dispatch ?>" />
        <table cellspacing="1px" border="0" cellpadding="0" width="100%">         
        	<tr>
            	<td colspan="6" class="ui-state-default fontMedium align-center">DATOS GENERALES</td>
            </tr>
            <tr>
              	<td width="10%" class="ui-state-default">Indicador:</td>
              	<td width="30%"><input id="tx_indicador" name="tx_indicador" type="hidden" value="<? echo $tx_indicador ?>" tabindex="7" />
                    <div id="imgstatus"></div>
                    <script>setStatus();</script>
                </td>
              	<td width="10%"></td>
              	<td width="10%" class="ui-state-default">Registro:</td>
              	<td width="28%"><input name="cap_registro" id="cap_registro" type="text" size="30" title="Registro del Empleado" value="<? echo $tx_registro ?>" /></td>
              	<td width="12%"><div id="errcap_registro" style="float:left;"></div></td>
            </tr>
            <tr>
              	<td class="ui-state-default">Nombre:</td>
              	<td><input name="cap_empleado" id="cap_empleado" type="text" size="60" title="Nombre del Empleado" value="<? echo $tx_empleado ?>" /></td>
              	<td><div id="errcap_empleado" style="float:left;"></div></td>
              	<td class="ui-state-default">Usuario Red:</td>
              	<td><input name="cap_usuario_red" id="cap_usuario_red" type="text" size="30" title="Usuario de acceso a la Red" value="<? echo $tx_usuario_red ?>" /></td>
              	<td><div id="errcap_usuario_red" style="float:left;"></div></td>
            </tr>
            <tr>
              	<td class="ui-state-default">Centro Responsable:</td>
              	<td>
                	<select id="sel_centro" name="sel_centro";>
                  	<option value="0" class="">--- S e l e c c i o n e ---</option>
                  	<?              
                    	for ($i=0; $i < count($TheCatalogoCentro); $i++)	{         			 
                        	while ($elemento 		= each($TheCatalogoCentro[$i]))					  		
                            	$par_centro			=$TheCatalogoCentro[$i]['id_centro_costos'];		
                                $tx_centro_costos	=$TheCatalogoCentro[$i]['tx_centro_costos'];	
                                if ($par_centro == $id_centro_costos ) echo "<option value=$id_centro_costos selected='selected'>$tx_centro_costos</option>";
                                else echo "<option value=$par_centro>$tx_centro_costos</option>";		
                        }
                    ?>
                	</select>
              	</td>
              	<td><div id="errsel_centro" style="float:left;"></div></td>
              	<td class="ui-state-default">Usuario e-espacio:</td>
              	<td><input name="cap_usuario_espacio" id="cap_usuario_espacio" type="text" size="30" title="Usuario de acceso a la Red" value="<? echo $tx_usuario_espacio ?>" /></td>
              	<td></td>
            </tr>
            <tr>
              	<td class="ui-state-default">Direcci&oacute;n:</td>
              	<td><input name="cap_direccion" id="cap_direccion" type="text" size="60" title="Direcci&oacute;n" value="<? echo $tx_direccion ?>"  disabled="disabled"/></td>
              	<td></td>
              	<td class="ui-state-default">Tel&eacute;fono/Extensi&oacute;n:</td>
              	<td><input name="cap_telefono" id="cap_telefono" type="text" size="30" title="Tel&eacute;fono" value="<? echo $tx_telefono ?>" /></td>
              	<td></td>
            </tr>
            <tr>
              	<td class="ui-state-default">Subdirecci&oacute;n:</td>
              	<td><input name="cap_subdireccion" id="cap_subdireccion" type="text" size="60" title="Subdirecci&oacute;n" value="<? echo $tx_subdireccion ?>"  disabled="disabled"/></td>
              	<td></td>
              	<td class="ui-state-default">Correo:</td>
              	<td><input name="cap_correo" id="cap_correo" type="text" size="40" title="Usuario de acceso a la Red" value="<? echo $tx_correo ?>" /></td>
              	<td></td>
            </tr>
            <tr>
              	<td class="ui-state-default">Departamento:</td>
              	<td><input name="cap_departamento" id="cap_departamento" type="text" size="60" title="Area" value="<? echo $tx_departamento ?>" disabled="disabled"/></td>
              	<td></td>
              	<td class="ui-state-default" valign="top">Ubicaci&oacute;n:</td>
                <td valign="top"><select id="sel_ubicacion" name="sel_ubicacion">
                <option value='0' class=''>--- S e l e c c i o n e ---</option>
                <?                            
                	for ($i=0; $i < count($TheCatalogoUbicacion); $i++)	{         			 
                    	while ($elemento = each($TheCatalogoUbicacion[$i]))					  		
                        	$par_ubicacion	=$TheCatalogoUbicacion[$i]['id_ubicacion'];		
                            $tx_ubicacion	=$TheCatalogoUbicacion[$i]['tx_ubicacion'];	
                            if ($par_ubicacion == $id_ubicacion ) echo "<option value=$id_ubicacion selected='selected'>$tx_ubicacion</option>";
                            else echo "<option value=$par_ubicacion>$tx_ubicacion</option>";		
                    }
               	?>
              	</select>
                </td>
              	<td><div id="errsel_ubicacion" style="float:left;"></div></td>
            </tr>
            <tr>
              	<td class="ui-state-default">Categor&iacute;a:</td>
             	 <td><input name="cap_categoria" id="cap_categoria" type="text" size="60" title="Categor&iacute;a" value="<? echo $tx_categoria ?>" /></td>
              	<td></td>
              	<td class="ui-state-default" valign="top">Estado:</td>
              	<td><input name="cap_estado" id="cap_estado" type="text" size="30" title="Estado" value="<? echo $tx_estado ?>" disabled="disabled" /></td>
            </tr>
            <tr>
              <td class="ui-state-default">Tipologia:</td>
              <td><input name="cap_tipologia" id="cap_tipologia" type="text" size="60" title="Tipologia" value="<? echo $tx_tipologia ?>" /></td>
              <td></td>
              <td class="ui-state-default" valign="top">Pa&iacute;s:</td>
              <td><input name="cap_pais" id="cap_pais" type="text" size="30" title="Pa&iacute;s" value="<? echo $tx_pais ?>" disabled="disabled"/></td>
            </tr>
            <tr>
              <td class="ui-state-default">Funciones:</td>
              <td><input name="cap_funcion" id="cap_funcion" type="text" size="60" title="Funciones" value="<? echo $tx_funcion ?>" /></td>
              <td></td>
              <td rowspan="2" valign="top" class="ui-state-default">Notas:</td>
              <td rowspan="2" valign="top"><textarea id="cap_notas" name="cap_notas" cols="40" rows="3" title="Notas"><? echo $tx_notas ?></textarea></td>
            </tr>
            <tr>
              <td class="ui-state-default">Responsable:</td>
              <td><input name="cap_responsable" id="cap_responsable" type="text" size="60" title="Nombre del Empleado" value="<? echo $tx_responsable ?>" /></td>
              <td></td>
            </tr>
            <tr>
              <td colspan="6"><div id="divMasDatos"  class="ui-helper-hidden">
                  <table cellspacing="1px" border="0" cellpadding="0" width="100%">
                  	<tr>
                    	<td width="10%" class="ui-state-default">Nivel 3:</td>
                        <td width="30%"><input name="cap_n3" id="cap_n3" type="text" size="60" title="Nivel 3" value="<? echo $tx_n3 ?>" /></td>
                        <td width="10%">&nbsp;</td>
                        <td width="10%">&nbsp;</td>
                        <td width="28%">&nbsp;</td>
                        <td width="12%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="ui-state-default">Nivel 4:</td>
                        <td><input name="cap_n4" id="cap_n4" type="text" size="60" title="Nivel 4" value="<? echo $tx_n4 ?>" /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="ui-state-default">Nivel 5:</td>
                        <td><input name="cap_n5" id="cap_n5" type="text" size="60" title="Nivel 5" value="<? echo $tx_n5 ?>" /></td>
                        <td>&nbsp;</td>
                        <td colspan="3"class="ui-state-default fontMedium align-center">MANTENIMIENTO AL CATALOGO</td>
                    </tr>
                    <tr>
                        <td class="ui-state-default">Nivel 6:</td>
                        <td><input name="cap_n6" id="cap_n6" type="text" size="60" title="Nivel 6" value="<? echo $tx_n6 ?>" /></td>
                        <td></td>
                        <td class="ui-state-default">Fecha Modifica:</td>
                        <td><? echo $fh_mod ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="ui-state-default">Nivel 7:</td>
                        <td><input name="cap_n7" id="cap_n7" type="text" size="60" title="Nivel 7" value="<? echo $tx_n7 ?>" /></td>
                        <td></td>
                        <td class="ui-state-default">Usuario Modifica:</td>
                        <td><? echo $usuario_mod ?></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td class="ui-state-default">Nivel 8:</td>
                        <td><input name="cap_n8" id="cap_n8" type="text" size="60" title="Nivel 8" value="<? echo $tx_n8 ?>" /></td>
                        <td></td>
                        <td class="ui-state-default">Fecha Alta:</td>
                        <td><? echo $fh_alta ?></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td class="ui-state-default">Nivel 9:</td>
                        <td><input name="cap_n9" id="cap_n9" type="text" size="60" title="Nivel 9" value="<? echo $tx_n9 ?>" /></td>
                        <td></td>
                        <td class="ui-state-default">Usuario Alta:</td>
                        <td><? echo $usuario_alta ?></td>
                        <td></td>
                      </tr>
                  </table>
              </div>
              </td>
            </tr>
            <tr>
            <?  if ($dispatch=="insert") 
				{    
					echo "<td colspan='6' class='ui-state-default'>&nbsp;</td>";                 
				} else {
					echo "<td colspan='3' class='ui-state-default' align='right'>"; 				               
           	  		echo "<a id='btnMasInf' href='javascript:void(0)' title='Presione para ver mas Informaci&oacute;n' class='ui-icon ui-icon-circle-plus'></a>";								                   	echo "</td>";
               		echo "<td colspan='3' class='ui-state-default' align='left'>";                                  
               		echo "<a id='btnMenosInf' href='javascript:void(0)' title='Presione para cerrar' class='ui-icon ui-icon-circle-minus'></a>";    
					echo "</td>";
				}
			?>                    
            </tr>
            <?
			if ($dispatch=="insert") {}
			else {
			?>
            <tr>
            	<td colspan="6">
              		<!-- Inicia Tabs -->
                  	<div id="tabs">
                    	<ul>
                      		<li><a href="#tabs-1">Licencias</a></li>
                      		<li><a href="#tabs-2">Computo</a></li>
                      		<li><a href="#tabs-3">Telefon&iacute;a</a></li>                     
                    	</ul>
                    	<div id="tabs-1">
                    		<div id="divLicencias" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>
                    	</div>
                    	<div id="tabs-2">
                    		<div id="divComputo" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>
                    	</div>
                    	<div id="tabs-3">
                    		<div id="divTelefonia" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>
                    	</div>
                  </div>
                <!--  -------------- -->
              	</td>
            </tr>
            <?
			}
			?>
    </table>        
</form>
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  