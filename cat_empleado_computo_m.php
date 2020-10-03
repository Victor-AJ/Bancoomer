<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">	

	function formatCounterpart(row) {
		return row[0];	
    }
	
	function formatItem(row) {
		//alert ("Entre");
        return "<strong>"+row[0]+"</strong>";
    }
		
	function fieldsRequiredListaComputo(){
	
		var error = true;		
				
		validSelect($("#sel_equipo"), $("#errsel_equipo"));
		validSelect($("#sel_marca"), $("#errsel_marca"));
		validSelect($("#sel_modelo"), $("#errsel_modelo"));
						
		if( !validSelect($("#sel_equipo"), $("#errsel_equipo")) ||
			!validSelect($("#sel_marca"), $("#errsel_marca")) ||
			!validSelect($("#sel_modelo"), $("#errsel_modelo"))) error = false;
        		 
		return error;
	}
	
	$("#btnSave2").click(function(){     
	 
		if(fieldsRequiredListaComputo()){	
			var url = "process_empleado_computo.php?";
         		url += $("#opComputo").serialize(); 

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
						loadHtmlAjax(true, $("#divComputo"), data.html);
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
	 
	 $("#btnUndo2").click(function(){  
	 	$("#divAltaCom").hide();
	 }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });
	 	 
	 ///////////////// DEFINICION DE EVENTOS //////////////////////
	 $("#sel_equipo").change(function () {
            $("#sel_equipo option:selected").each(function () {
			    //var id=$(this).val();
				//alert ("Valor"+id);
                loadHtmlAjax(false, $("#sel_marca") , "combo_marca.php?id="+$(this).val());
     	});
     });
	 
	$("#sel_marca").change(function () {
    	$("#sel_marca option:selected").each(function () {
			var id1 = $("#sel_equipo").val();
		    //var id=$(this).val();
			//alert ("Valor"+id1);
            loadHtmlAjax(false, $("#sel_modelo"), "combo_modelo.php?id="+$(this).val()+"&id1="+id1);	
     	});
    });	
	
	$("#cap_ram").autocomplete("process_empleado_computo.php?dispatch=find&campo=ram",{
    	minChars: 1,
        max: 5,
        width:300,
       	autoFill: true,
        selectFirst: false,
        scrollHeight: 220,
        cacheLength: 1,
        formatItem: formatItem
    }).result(function(e, item) {
        $("#cap_ram").val(item[0]);
   	});
		
	//$("#cap_ram").focus(function() {
    //	$(this).addClass('ui-state-focus');
    //});

   	//$("#cap_ram").blur(function() {
    // 	$(this).removeClass('ui-state-focus');
    //});
	  
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
	
	//Recibo variables
	//===========================
	$dispatch	= $_GET['dispatchCom'];
	$id			= $_GET['id'];	
	$id_com		= $_GET['id_com'];	
	
	//Catalogo de equipo
	//===========================
	$sql = "   SELECT tx_equipo ";
	$sql.= "     FROM tbl_computo ";
	$sql.= " GROUP BY tx_equipo ";
	$sql.= " ORDER BY tx_equipo ";

	//echo " sql ",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoEquipo[] = array(			
			'tx_equipo'=>$row["tx_equipo"]
		);
	}	
	
	if ($dispatch=="insert") {	
		
		$titulo 			= "ALTA";		
		$tx_indicador		= "1";	
		$tx_compartido1  	= "checked='checked'";
		$tx_compartido2  	= "";
	
 	} else if ($dispatch=="save") {
		
		$titulo 		= "MODIFICACION";
	
		//Carga la informacion para la actualizacion
		//==========================================
		
		$sql = "   SELECT id_empleado_computo, tx_serie, tx_siaf, tx_equipo, tx_marca, tx_modelo, tx_ram, tx_compartido, a.tx_indicador, a.fh_alta, c.tx_nombre AS usuario_alta, a.fh_mod, d.tx_nombre AS usuario_mod ";
		$sql.= "     FROM tbl_empleado_computo a, tbl_computo b, tbl_usuario c, tbl_usuario d ";
		$sql.= "    WHERE id_empleado_computo= $id_com ";		
		$sql.= "      AND a.id_computo		= b.id_computo ";
		$sql.= "      AND a.id_usuariomod	= c.id_usuario ";
		$sql.= "      AND a.id_usuarioalta 	= d.id_usuario ";
		
		//echo "aaa", $sql;
			
		$result = mysqli_query($mysql, $sql);		
		
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{	
			$TheCatalogoComputo[] = array(
				'id_proveedor'	=>$row["id_empleado_computo"],
				'tx_serie'		=>$row["tx_serie"],
				'tx_siaf'		=>$row["tx_siaf"],
				'ac_equipo'		=>$row["tx_equipo"],
				'ac_marca'		=>$row["tx_marca"],
				'ac_modelo'		=>$row["tx_modelo"],
				'tx_ram'		=>$row["tx_ram"],				
				'tx_compartido'	=>$row["tx_compartido"],
				'tx_indicador'	=>$row["tx_indicador"],
				'fh_alta'		=>$row["fh_alta"],
				'usuario_alta'	=>$row["usuario_alta"],				
				'fh_mod'		=>$row["fh_mod"],
				'usuario_mod'	=>$row["usuario_mod"]				
			);
		} 
		
		for ($i=0; $i < count($TheCatalogoComputo); $i++)	{         			 
			while ($elemento = each($TheCatalogoComputo[$i]))				
				$ac_empleado_computo=$TheCatalogoComputo[$i]['id_empleado_computo'];						  		
				$tx_serie			=$TheCatalogoComputo[$i]['tx_serie'];	
				$tx_siaf			=$TheCatalogoComputo[$i]['tx_siaf'];	
				$ac_equipo			=$TheCatalogoComputo[$i]['ac_equipo'];	
				$ac_marca			=$TheCatalogoComputo[$i]['ac_marca'];		
				$ac_modelo			=$TheCatalogoComputo[$i]['ac_modelo'];		
				$tx_ram				=$TheCatalogoComputo[$i]['tx_ram'];		
				$tx_compartido		=$TheCatalogoComputo[$i]['tx_compartido'];
				$tx_indicador		=$TheCatalogoComputo[$i]['tx_indicador'];
				$fh_alta			=$TheCatalogoComputo[$i]['fh_alta'];
				$usuario_alta		=$TheCatalogoComputo[$i]['usuario_alta'];
				$fh_mod				=$TheCatalogoComputo[$i]['fh_mod'];
				$usuario_mod		=$TheCatalogoComputo[$i]['usuario_mod'];
		} 		

		//Catalogo de marcas
		//===========================
		$sql = "   SELECT tx_marca ";
		$sql.= "     FROM tbl_computo ";
		$sql.= " 	WHERE tx_equipo = '$ac_equipo' ";		
		$sql.= " GROUP BY tx_marca ";
	
		//echo " sql ",$sql;
		
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{	
			$TheCatalogoMarca[] = array(			
				'tx_marca'=>$row["tx_marca"]
			);
		}	
				
		//Catalogo de modelos
		//===========================
		$sql = "   SELECT tx_modelo ";
		$sql.= "     FROM tbl_computo ";
		$sql.= " 	WHERE tx_equipo = '$ac_equipo' ";		
		$sql.= " 	  AND tx_marca  = '$ac_marca' ";		
		$sql.= " GROUP BY tx_modelo ";
	
		//echo " sql ",$sql;
		
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{	
			$TheCatalogoModelo[] = array(			
				'tx_modelo'=>$row["tx_modelo"]
			);
		}	
				
		if ($tx_compartido=="0") {
			$tx_compartido1  	= "checked='checked'";
			$tx_compartido2  	= "";
		} else {
			$tx_compartido1  	= "";
			$tx_compartido2  	= "checked='checked'";
		}
	}		
?>
<br>
<div>
    <form id="opComputo" action="">
    	<input id="id" name="id" type="hidden" value="<? echo $id ?>" />
		<input id="id_com" name="id_com" type="hidden" value="<? echo $id_com ?>" />
    	<input id="dispatchCom" name="dispatchCom" type="hidden" value="<? echo $dispatch ?>" />        
	  	<table cellspacing="1px" border="0" cellpadding="0" width="100%">   		
   			<tr>
                <td colspan="5" class="ui-state-highlight" align="center" style="font-family:Verdana,Arial,sans-serif;font-size: 12px;font-weight:bold;"><? echo $titulo ?></td>  
         	</tr>         	                          
            <tr>
            	<td width="20%" class="ui-state-default">Equipo:</td>
           	  	<td width="60%">
                <select id="sel_equipo" name="sel_equipo">
                	<option value="0" class="">--- S e l e c c i o n e ---</option>
                  	<?
						for ($i=0; $i < count($TheCatalogoEquipo); $i++)	{         			 
						while ($elemento = each($TheCatalogoEquipo[$i]))					  		
							$tx_equipo	=$TheCatalogoEquipo[$i]['tx_equipo'];	
							if ($ac_equipo == $tx_equipo ) echo "<option value=$tx_equipo selected='selected'>$tx_equipo</option>";
							else echo "<option value='$tx_equipo'>$tx_equipo</option>";	
						}
					?>
                </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           	    <input type="radio" name="cap_compartido" id="cap_compartido" value="0"  <? echo $tx_compartido1 ?> /> Particular 
                <input type="radio" name="cap_compartido" id="cap_compartido" value="1" <? echo $tx_compartido2 ?> /> Compartido 
           	    </td>
           	  	<td width="20%"><div id="errsel_equipo" style="float:left;"></div></td>                        
            </tr>
            <tr>
            	<td class="ui-state-default">Marca:</td>
                <td>
                	<select id="sel_marca" name="sel_marca">
                  	<option value="0" class="">--- S e l e c c i o n e ---</option>
                  	<?
						for ($i=0; $i < count($TheCatalogoMarca); $i++)	{         			 
						while ($elemento = each($TheCatalogoMarca[$i]))					  		
							$tx_marca	=$TheCatalogoMarca[$i]['tx_marca'];	
							if ($ac_marca == $tx_marca ) echo "<option value='$tx_marca' selected='selected'>$tx_marca</option>";
							else echo "<option value='$tx_marca'>$tx_marca</option>";	
						}
					?>
                	</select>
                </td>
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
                	</select>
                </td>
                <td width="20%"><div id="errsel_modelo" style="float:left;"></div></td>   
          	</tr>
           	<tr>
            	<td class="ui-state-default">RAM:</td>
                <td><input name="cap_ram" id="cap_ram" type="text" size="30" title="RAM" value="<? echo $tx_ram ?>"/></td>
                <td></td>   
           	</tr>
            
            <tr>
            	<td class="ui-state-default">Serie:</td>
       	  		<td>
          			<input name="cap_serie" id="cap_serie" type="text" size="30" title="Serie" value="<? echo $tx_serie ?>"/></td>
                <td width="20%"><div id="errsel_licencia" style="float:left;"></div></td>   
           	</tr>
            <tr>
            	<td class="ui-state-default">SIAF:</td>
           	  	<td>
                	<input name="cap_siaf" id="cap_siaf" type="text" size="30" title="SIAF" value="<? echo $tx_siaf ?>"/>       	  	    </td>
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
                	<a id="btnSave2" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Guardar
                    <span class="ui-icon ui-icon-disk"/></a>
                    <a id="btnUndo2" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                    Cancelar
                    <span class="ui-icon ui-icon-cancel"/></a>
             	</td>
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