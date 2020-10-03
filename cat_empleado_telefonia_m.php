<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">	
	
	function fieldsRequiredListaTelefonia(){
	
		var error = true;		
				
		validSelect($("#sel_tipo"), $("#errsel_tipo"));
		validSelect($("#sel_marca"), $("#errsel_marca"));
		validSelect($("#sel_modelo"), $("#errsel_modelo"));
						
		if( !validSelect($("#sel_tipo"), $("#errsel_tipo")) ||
			!validSelect($("#sel_marca"), $("#errsel_marca")) ||
			!validSelect($("#sel_modelo"), $("#errsel_modelo"))) error = false;
        		 
		return error;
	}
	
	$("#btnSaveTel").click(function(){     
	 
		if(fieldsRequiredListaTelefonia()){	
			var url = "process_empleado_telefonia.php?";
         		url += $("#opTelefonia").serialize(); 

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
						loadHtmlAjax(true, $("#divTelefonia"), data.html);
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
	 
	 $("#btnUndoTel").click(function(){  
	 	$("#divAltaTel").hide();
	 }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });
	 
	 $(function(){
     	$('input:text').setMask();
     });
	 
	 ///////////////// DEFINICION DE EVENTOS //////////////////////
	$("#sel_tipo").change(function () {
    	$("#sel_tipo option:selected").each(function () {
			//var id=$(this).val();
			//alert ("Valor"+id);
            loadHtmlAjax(false, $("#sel_marca") , "combo_marca_telefonia.php?id="+$(this).val());
     	});
     });
	 
	$("#sel_marca").change(function () {
     	$("#sel_marca option:selected").each(function () {
		 	var id1 = $("#sel_tipo").val();
		    //var id=$(this).val();
			//alert ("Valor"+id1+id);
            loadHtmlAjax(false, $("#sel_modelo"), "combo_modelo_telefonia.php?id="+$(this).val()+"&id1="+id1);
     	});
     });

	$("#sel_proveedor").change(function () {
     	$("#sel_proveedor option:selected").each(function () {
		 	//var id1 = $("#sel_tipo").val();
		    //var id=$(this).val();
			//alert ("Valor"+id1+id);
            loadHtmlAjax(false, $("#sel_plan"), "combo_plan_telefonia.php?id="+$(this).val());
     	});
     });
	
	$("#sel_plan").change(function () {
    	$("#sel_plan option:selected").each(function () {
			var id1 = $("#sel_proveedor").val();			
			//var id2 = $("#sel_tipo").val();
			var url = "busca_precio_telefonia.php?id="+$("#sel_plan").val()+"&id1="+id1;			  
			var func = function(data){					   			
	 			if(data.pasa == true){							
					$("#cap_precio").val(data.data1);	
				}				
			} 
			//alert ("url"+url);
			executeAjax("post", false ,url, "json", func); 						
       });
    });	 
	  
	$("#cap_numero").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_numero").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });

	$("#cap_serie").focus(function() {
    	$(this).addClass('ui-state-focus');
    });

   	$("#cap_serie").blur(function() {
     	$(this).removeClass('ui-state-focus');
    });
	 
 	$("#cap_siaf").focus(function() {
     	$(this).addClass('ui-state-focus');
    });

    $("#cap_siaf").blur(function() {
    	$(this).removeClass('ui-state-focus');
    });
         	
</script>
<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	//===========================
	//Recibo variables
	//===========================
	$dispatch	= $_GET['dispatchTel'];
	$id			= $_GET['id'];	
	$id_tel		= $_GET['id_tel'];	
	
	//===========================
	//Catalogo de tipo
	//===========================
	$sql = "   SELECT tx_tipo ";
	$sql.= "     FROM tbl_telefonia ";
	$sql.= " GROUP BY tx_tipo ";
	$sql.= " ORDER BY tx_tipo ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoTipo[] = array(			
			'tx_tipo'=>$row["tx_tipo"]
		);
	}	
	
	//===========================	
	//Catalogo de proveedores
	//===========================
	$sql = "   SELECT tx_proveedor ";
	$sql.= "     FROM tbl_telefonia_plan ";
	$sql.= "    WHERE id_telefonia_plan <> 1 ";
	$sql.= " GROUP BY tx_proveedor ";
	$sql.= " ORDER BY tx_proveedor ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoProveedor[] = array(			
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
		
		$sql = "   SELECT id_empleado_telefonia, tx_tipo, tx_marca, tx_modelo, tx_proveedor, tx_plan, fl_precio_mxn, tx_numero, tx_serie, tx_siaf, a.tx_indicador, a.fh_alta, d.tx_nombre AS usuario_alta, a.fh_mod, e.tx_nombre AS usuario_mod ";
		$sql.= "     FROM tbl_empleado_telefonia a, tbl_telefonia b, tbl_telefonia_plan c, tbl_usuario d, tbl_usuario e ";
		$sql.= "    WHERE id_empleado_telefonia	= $id_tel ";		
		$sql.= "      AND a.id_telefonia		= b.id_telefonia ";
		$sql.= "      AND a.id_telefonia_plan	= c.id_telefonia_plan ";
		$sql.= "      AND a.id_usuariomod		= d.id_usuario ";
		$sql.= "      AND a.id_usuarioalta 		= e.id_usuario ";
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoTelefonia[] = array(
				'id_empleado_telefonia'	=>$row["id_empleado_telefonia"],
				'tx_tipo'		=>$row["tx_tipo"],
				'tx_marca'		=>$row["tx_marca"],
				'tx_modelo'		=>$row["tx_modelo"],
				'tx_proveedor'	=>$row["tx_proveedor"],
				'tx_plan'		=>$row["tx_plan"],				
				'fl_precio_mxn'	=>$row["fl_precio_mxn"],	
				'tx_numero'		=>$row["tx_numero"],	
				'tx_serie'		=>$row["tx_serie"],	
				'tx_siaf'		=>$row["tx_siaf"],	
				'tx_indicador'	=>$row["tx_indicador"],
				'fh_alta'		=>$row["fh_alta"],
				'usuario_alta'	=>$row["usuario_alta"],				
				'fh_mod'		=>$row["fh_mod"],
				'usuario_mod'	=>$row["usuario_mod"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoTelefonia); $i++)	{         			 
			while ($elemento = each($TheCatalogoTelefonia[$i]))				
				$ac_empleado_computo=$TheCatalogoTelefonia[$i]['id_empleado_computo'];						  		
				$ac_tipo			=$TheCatalogoTelefonia[$i]['tx_tipo'];	
				$ac_marca			=$TheCatalogoTelefonia[$i]['tx_marca'];		
				$ac_modelo			=$TheCatalogoTelefonia[$i]['tx_modelo'];		
				$ac_proveedor		=$TheCatalogoTelefonia[$i]['tx_proveedor'];		
				$ac_plan			=$TheCatalogoTelefonia[$i]['tx_plan'];		
				$fl_precio_mxn		=$TheCatalogoTelefonia[$i]['fl_precio_mxn'];		
				$tx_numero			=$TheCatalogoTelefonia[$i]['tx_numero'];		
				$tx_serie			=$TheCatalogoTelefonia[$i]['tx_serie'];		
				$tx_siaf			=$TheCatalogoTelefonia[$i]['tx_siaf'];		
				$tx_indicador		=$TheCatalogoTelefonia[$i]['tx_indicador'];
				$fh_alta			=$TheCatalogoTelefonia[$i]['fh_alta'];
				$usuario_alta		=$TheCatalogoTelefonia[$i]['usuario_alta'];
				$fh_mod				=$TheCatalogoTelefonia[$i]['fh_mod'];
				$usuario_mod		=$TheCatalogoTelefonia[$i]['usuario_mod'];
		} 		

		//===========================
		//Catalogo de marca
		//===========================
		$sql = "   SELECT tx_marca ";
		$sql.= "     FROM tbl_telefonia ";
		$sql.= "    WHERE tx_tipo = '$ac_tipo' ";
		$sql.= " GROUP BY tx_marca ";
		$sql.= " ORDER BY tx_marca ";

		//echo " sql ",$sql;
	
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{	
			$TheCatalogoMarca[] = array(			
				'tx_marca'=>$row["tx_marca"]
			);
		}	
				
		//===========================
		//Catalogo de modelo
		//===========================
		$sql = "   SELECT tx_modelo ";
		$sql.= "     FROM tbl_telefonia ";
		$sql.= "    WHERE tx_tipo = '$ac_tipo' ";
		$sql.= "      AND tx_marca = '$ac_marca' ";
		$sql.= " GROUP BY tx_modelo ";
		$sql.= " ORDER BY tx_modelo ";

		//echo " sql ",$sql;
	
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{	
			$TheCatalogoModelo[] = array(			
				'tx_modelo'=>$row["tx_modelo"]
			);
		}	
		
		//===========================	
		//Catalogo de plan
		//===========================
		$sql = "   SELECT tx_plan ";
		$sql.= "     FROM tbl_telefonia_plan ";
		$sql.= "    WHERE tx_proveedor = '$ac_proveedor' ";
		$sql.= " GROUP BY tx_plan ";
		$sql.= " ORDER BY tx_plan ";

		//echo " sql ",$sql;
	
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{	
			$TheCatalogoPlan[] = array(			
				'tx_plan'=>$row["tx_plan"]
			);
		}	
	}		
?>
<br>
<div>
    <form id="opTelefonia" action="">
    	<input id="id" name="id" type="hidden" value="<? echo $id ?>" />
		<input id="id_tel" name="id_tel" type="hidden" value="<? echo $id_tel ?>" />
    	<input id="dispatchTel" name="dispatchTel" type="hidden" value="<? echo $dispatch ?>" />        
	  	<table cellspacing="1px" border="0" cellpadding="0" width="100%">   		
   			<tr>
                <td colspan="5" class="ui-state-highlight" align="center" style="font-family:Verdana,Arial,sans-serif;font-size: 12px;font-weight:bold;"><? echo $titulo ?></td>  
         	</tr>         	                          
            <tr>
            	<td width="20%" class="ui-state-default">Tipo:</td>
       	    	<td width="60%"><select id="sel_tipo" name="sel_tipo">
                  	<option value="0" class="">--- S e l e c c i o n e ---</option>
                  	<?
					for ($i=0; $i < count($TheCatalogoTipo); $i++)	{         			 
						while ($elemento = each($TheCatalogoTipo[$i]))					  		
							$tx_tipo	=$TheCatalogoTipo[$i]['tx_tipo'];	
							if ($ac_tipo == $tx_tipo ) echo "<option value=$tx_tipo selected='selected'>$tx_tipo</option>";
							else echo "<option value='$tx_tipo'>$tx_tipo</option>";	
					}
					?>
                	</select>              </td>
           	  <td width="20%"><div id="errsel_tipo" style="float:left;"></div></td>                        
            </tr>
            <tr>
            	<td class="ui-state-default">Marca:</td>
                <td><select id="sel_marca" name="sel_marca">
                  <option value="0" class="">--- S e l e c c i o n e ---</option>
                  <?
						for ($i=0; $i < count($TheCatalogoMarca); $i++)	{         			 
						while ($elemento = each($TheCatalogoMarca[$i]))					  		
							$tx_marca	=$TheCatalogoMarca[$i]['tx_marca'];	
							if ($ac_marca == $tx_marca ) echo "<option value='$tx_marca' selected='selected'>$tx_marca</option>";
							else echo "<option value='$tx_marca'>$tx_marca</option>";	
						}
					?>
                </select></td>
           	  <td width="20%"><div id="errsel_marca" style="float:left;"></div></td>   
            </tr>
            <tr>
            	<td class="ui-state-default">Modelo:</td>
                <td>
                	<select id="sel_modelo" name="sel_modelo">
                  	<option value="0" class="">--- S e l e c c i o n e ---</option>
                 	<?
						for ($i=0; $i < count($TheCatalogoModelo); $i++)	{         			 
						while ($elemento = each($TheCatalogoModelo[$i]))					  		
							$tx_modelo	=$TheCatalogoModelo[$i]['tx_modelo'];	
							if ($ac_modelo == $tx_modelo ) echo "<option value='$tx_modelo' selected='selected'>$tx_modelo</option>";
							else echo "<option value='$tx_modelo'>$tx_modelo</option>";	
						}
					?>
                	</select>                </td>
                <td width="20%"><div id="errsel_modelo" style="float:left;"></div></td>   
          	</tr>
           	<tr>
            	<td class="ui-state-default">N&uacute;mero:</td>
                <td><input name="cap_numero" id="cap_numero" type="text" size="30" title="N&uacute;mero" value="<? echo $tx_numero ?>" /></td>
                <td width="20%"><div id="errcap_ram" style="float:left;"></div></td>   
           	</tr>
            
            <tr>
              	<td class="ui-state-default">Proveedor:</td>
              	<td>
              		<select id="sel_proveedor" name="sel_proveedor">
                	<option value="0" class="">--- S e l e c c i o n e ---</option>
                	<?
						for ($i=0; $i < count($TheCatalogoProveedor); $i++)	{         			 
						while ($elemento = each($TheCatalogoProveedor[$i]))					  		
							$tx_proveedor	=$TheCatalogoProveedor[$i]['tx_proveedor'];	
							if ($ac_proveedor == $tx_proveedor ) echo "<option value=$tx_proveedor selected='selected'>$tx_proveedor</option>";
							else echo "<option value=$tx_proveedor>$tx_proveedor</option>";	
						}
					?>
              		</select>
                </td>
              	<td>&nbsp;</td>
            </tr>
            <tr>
          		<td class="ui-state-default">Plan:</td>
              	<td>
                <select id="sel_plan" name="sel_plan">
                <option value="0" class="">--- S e l e c c i o n e ---</option>
                <?
					for ($i=0; $i < count($TheCatalogoPlan); $i++)	{         			 
						while ($elemento = each($TheCatalogoPlan[$i]))					  		
							$tx_plan	=$TheCatalogoPlan[$i]['tx_plan'];	
							if ($ac_plan == $tx_plan ) echo "<option value='$tx_plan' selected='selected'>$tx_plan</option>";
							else echo "<option value='$tx_plan'>$tx_plan</option>";	
					}
				?>
              	</select>                </td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td class="ui-state-default">Precion en MXN</td>
              <td><input name="cap_precio" id="cap_precio" type="text" size="30" title="Serie" value="<? echo $fl_precio_mxn ?>" disabled="disabled"/></td>
              <td>&nbsp;</td>
            </tr>
            <tr>
            	<td class="ui-state-default">Serie:</td>
       	  		<td><input name="cap_serie" id="cap_serie" type="text" size="30" title="Serie" value="<? echo $tx_serie ?>"/></td>
                <td width="20%"><div id="errsel_licencia" style="float:left;"></div></td>   
           	</tr>
            <tr>
            	<td class="ui-state-default">SIAF:</td>
           	  	<td><input name="cap_siaf" id="cap_siaf" type="text" size="30" title="SIAF" value="<? echo $tx_siaf ?>"/></td>
       	  	  	<td width="20%"><div id="errcap_siaf" style="float:left;"></div></td>   
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
                	<a id="btnSaveTel" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Guardar
                    <span class="ui-icon ui-icon-disk"/></a>
                    <a id="btnUndoTel" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Cancelar
                    <span class="ui-icon ui-icon-cancel"/></a>                </td>
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
