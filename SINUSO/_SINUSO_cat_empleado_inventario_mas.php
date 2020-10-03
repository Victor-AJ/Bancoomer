<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">
	
	 
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
			$sql = " SELECT * ";
			$sql.= " FROM tbl_empleado a, tbl_ubicacion b, tbl_estado c, tbl_pais d, tbl_centro_costos e, tbl_departamento f, tbl_subdireccion g, tbl_direccion h ";
			$sql.= " WHERE id_empleado 			= $id ";
			$sql.= "   AND a.id_ubicacion 		= b.id_ubicacion ";
			$sql.= "   AND b.id_estado  		= c.id_estado ";
			$sql.= "   AND b.id_pais   			= c.id_pais ";
			$sql.= "   AND a.id_centro_costos 	= e.id_centro_costos ";
			$sql.= "   AND e.id_departamento 	= f.id_departamento ";
			$sql.= "   AND e.id_subdireccion 	= g.id_subdireccion ";
			$sql.= "   AND h.id_direccion 		= g.id_direccion ";
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
				'tx_direccion'		=>$row["tx_nombre"],		
				'id_ubicacion'		=>$row["id_ubicacion"],
				'tx_pais'			=>$row["tx_pais"],
				'tx_estado'			=>$row["tx_estado"],
				'tx_registro'		=>$row["tx_registro"],
				'tx_usuario_red'	=>$row["tx_usuario_red"],
				'tx_usuario_espacio'=>$row["tx_usuario_espacio"],
				'tx_empleado'		=>$row["tx_empleado"],
				'tx_categoria'		=>$row["tx_categoria"],
				'tx_responsable'	=>$row["tx_responsable"],
				'tx_telefono'		=>$row["tx_telefono"],
				'tx_correo'			=>$row["tx_correo"],
				'tx_notas'			=>$row["tx_notas"],
				'tx_indicador'		=>$row["tx_indicador"]
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
				$tx_responsable		=$TheCatalogoEmpleado[$i]['tx_responsable'];
				$tx_telefono		=$TheCatalogoEmpleado[$i]['tx_telefono'];
				$tx_correo			=$TheCatalogoEmpleado[$i]['tx_correo'];
				$tx_notas			=$TheCatalogoEmpleado[$i]['tx_notas'];
				$tx_indicador		=$TheCatalogoEmpleado[$i]['tx_indicador'];
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

<!-- <div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"> -->
    <form id="opForm1" action="">
   	  	<input id="id" name="id" type="text" value="<? echo $id ?>" />
    	<input id="dispatch" name="dispatch" type="text" value="<? echo $dispatch ?>" />
        <table cellspacing="1px" border="0" cellpadding="0" width="100%">
        	<tbody>            	
            	<tr>
              		<td colspan="6" class="ui-state-highlight align-center" style="font-family:Verdana,Arial,sans-serif;font-size: 12px;font-weight:bold;"><? echo $titulo ?></td>
            	</tr>
            	<tr>
              		<td colspan="6" class="ui-state-default fontMedium align-center">DATOS GENERALES</td>
            	</tr>
            	<tr>
              		<td width="10%" class="ui-state-default">Indicador:</td>
              		<td width="30%"><input id="tx_indicador" name="tx_indicador" type="hidden" value="<? echo $tx_indicador ?>" tabindex="7" />
              			<div id="imgstatus"></div>
                		<script>setStatus();</script>                        </td>
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
                        </select>                    </td>
       		      	<td><div id="errsel_centro" style="float:left;"></div></td>
              		<td class="ui-state-default">Usuario e-espacio:</td>
              		<td><input name="cap_usuario_espacio" id="cap_usuario_espacio" type="text" size="30" title="Usuario de acceso a la Red" value="<? echo $tx_usuario_espacio ?>" /></td>
              		<td><div></div></td>
            	</tr>       
                <tr>
              		<td class="ui-state-default">Direcci&oacute;n:</td>
              		<td><input name="cap_direccion" id="cap_direccion" type="text" size="60" title="Direcci&oacute;n" value="<? echo $tx_direccion ?>"  disabled="disabled"/></td>
              		<td><div></div></td>
              		<td class="ui-state-default">Tel&eacute;fono/Extensi&oacute;n:</td>
              		<td><input name="cap_telefono" id="cap_telefono" type="text" size="30" title="Tel&eacute;fono" value="<? echo $tx_telefono ?>" /></td>
           		  <td><div></div></td>
            	</tr>     
                <tr>
              		<td class="ui-state-default">Subdirecci&oacute;n:</td>
              		<td><input name="cap_subdireccion" id="cap_subdireccion" type="text" size="60" title="Subdirecci&oacute;n" value="<? echo $tx_subdireccion ?>"  disabled="disabled"/></td>
              		<td><div></div></td>
              		<td class="ui-state-default">Correo:</td>
              		<td><input name="cap_correo" id="cap_correo" type="text" size="40" title="Usuario de acceso a la Red" value="<? echo $tx_correo ?>" /></td>
              		<td><div></div></td>
            	</tr>  
                <tr>
              		<td class="ui-state-default">Departamento:</td>
           		    <td><input name="cap_departamento" id="cap_departamento" type="text" size="60" title="Area" value="<? echo $tx_departamento ?>" disabled="disabled"/></td>
           		  	<td><div></div></td>
              		<td rowspan="5" class="ui-state-default" valign="top">Notas:</td>
              		<td rowspan="5" valign="top"><textarea id="cap_notas" name="cap_notas" cols="40" rows="6" title="Notas"><? echo $tx_notas ?></textarea></td>
              		<td><div></div></td>
            	</tr>  
                <tr>
              		<td class="ui-state-default">Categor&iacute;a:</td>
              		<td><input name="cap_categoria" id="cap_categoria" type="text" size="60" title="Categor&iacute;a" value="<? echo $tx_categoria ?>" /></td>
              		<td><div></div></td>
              		<td><div></div></td>
            	</tr>
                <tr>
              		<td class="ui-state-default">Responsable:</td>
              		<td><input name="cap_responsable" id="cap_responsable" type="text" size="60" title="Nombre del Empleado" value="<? echo $tx_responsable ?>" /></td>
              		<td><div></div></td>
              		<td><div></div></td>
            	</tr>
                 <tr>
              		<td class="ui-state-default">Ubicaci&oacute;n:</td>
              		<td>
                    	<select id="sel_ubicacion" name="sel_ubicacion">
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
                    	</select>                   </td>
           		   <td><div></div></td>
              		<td><div></div></td>
            	</tr>
                <tr>
              		<td class="ui-state-default">Estado:</td>
              		<td><input name="cap_estado" id="cap_estado" type="text" size="60" title="Estado" value="<? echo $tx_estado ?>" disabled="disabled" /></td>
              		<td><div></div></td>
              		<td><div></div></td>
            	</tr>    
                <tr>
              		<td class="ui-state-default">Pa&iacute;s:</td>
              		<td><input name="cap_pais" id="cap_pais" type="text" size="60" title="Pa&iacute;s" value="<? echo $tx_pais ?>" disabled="disabled"/></td>
              		<td><div></div></td>
              		<td class="EditButton ui-widget-content" style="text-align:left">                           
    					<a id="btnMasInf" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Ver mas informaci&oacute;n"> 
    					Informaci&oacute;n
    					<span class="ui-icon ui-icon-plus"/></a>
    				</td>
            	</tr>  
                <tr>
              		<td colspan="6"><div id="divMasInf"></div></td>
            	</tr>
            	<tr>
              		<td colspan="6" class="ui-state-default">&nbsp;</td>
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
                                    <li><a href="#tabs-4">Aplicaciones</a></li>
                            	</ul>
                            	<div id="tabs-1">
                                	<div id="divLicencias" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>
                            	</div>    
                            	<div id="tabs-2">
                                	<div id="divComputo" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>
                            	</div>
                            	<div id="tabs-3"><div id="divGrid3"></div></div>
                            	<div id="tabs-4"><div id="divGrid3"></div></div>
                        	</div>
                    <!-- -------------- -->
                    </td>
                </tr>
                <?
				}
				?>
          </tbody>
        </table>
   </form>
  <!--  </div>    -->
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  