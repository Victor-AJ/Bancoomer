<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
session_start();
if 	(isset($_SESSION['sess_user']))
{
	 $id_login = $_SESSION['sess_iduser'];
?>
<script type="text/javascript">

	$('#tabs').tabs();	
	
	//$("#btnDeleteEmp").addClass('ui-state-disabled').attr("disabled","disabled");
	
	function fieldsRequiredLista(){
	
		var error = true;
		
		validText(false, $("#cap_empleado"), $("#errcap_empleado"), 1);
		validText(false, $("#cap_registro"), $("#errcap_registro"), 1);
		validText(false, $("#cap_usuario_red"), $("#errcap_usuario_red"), 1);
		validSelect($("#sel_centro"), $("#errsel_centro"));
		validSelect($("#sel_ubicacion"), $("#errsel_ubicacion"));
				
		if( !validText(false, $("#cap_empleado"), $("#cap_empleado"), 1) ||
			!validText(false, $("#cap_registro"), $("#errcap_registro"), 1)	||
			!validText(false, $("#cap_usuario_red"), $("#errcap_usuario_red"), 1) ||
			!validSelect($("#sel_centro"), $("#errsel_centro")) ||
			!validSelect($("#sel_ubicacion"), $("#errsel_ubicacion"))
			) error = false;	
        		 
		return error;
	}
	
	$("#btnBuscar").click(function(){  	
		
		var tx_busca = $("#tx_busca");
		$("#divBusqueda").html("");	 
		loadHtmlAjax(true, $("#divBusqueda"), "busca_empleados.php?tx_busca="+tx_busca.val());
        
	}).hover(function(){
     	$(this).addClass("ui-state-hover")
    },function(){
        $(this).removeClass("ui-state-hover")
    });

	$("#btnSaveEmp").click(function(){  
	 
		if(fieldsRequiredLista()){	
			var url = "process_empleados.php?";
         		url += $("#opForm1").serialize(); 
							
			//alert (url);		
				
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
						loadHtmlAjax(true, $("#divDatos"), data.html);
					}
				}	
			}	
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
	
	$("#btnNewEmp").click(function(){     
	
		var id="id=0";
		var dispatch="&dispatch=insert";
		$("#divBusqueda").hide();
		loadHtmlAjax(true, $("#divDatos"), "cat_empleado_inventario.php?"+id+dispatch);		
		   
     }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });
	 	 
	$("#btnDeleteEmp").click(function(){   	 	
		
		var id="id="+$("#id").val();
		var dispatch="&dispatch=delete";
		var url = "process_empleados.php?"+id+dispatch;        							
		//alert (url);		
				
		var func = function(data){					   			
			var fAceptar = function(){
				$('#dialogMain').dialog("close");
			};
			if(data.error == true){						
				if(data.message != null){							
					jAlert(true,true,data.message,fAceptar);
				}else{
					logout();
				}
			} else {						
			 	if(data.message != null){							
					jAlert(true,false,data.message,fAceptar);						
					//$("#divContent").hide();
					loadHtmlAjax(true, $('#divContent'), "cat_inventario.php");
				}
			}	
		};	
		
		if (confirm('Deseas BORRAR el Empleado selecccionado... ?'))
 		{							
			executeAjax("post", false ,url, "json", func);	     
		}	
		
	 }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });
	 
	$("#btnExportEmp").click(function(){
    	if(!editing){
        	var url = "excel_empleados.php";
			window.open( url,"_blank");				
        }
   	}).hover(function(){
    	$(this).addClass("ui-state-hover")
    },function(){
        $(this).removeClass("ui-state-hover")
    });
	
	function buscaEmpleado(valor)
	{	
		var id="id="+valor;
		var dispatch="&dispatch=save";
		$("#divBusqueda").hide();	
		if (valor == 0) { $("#divDatos").hide(); }
		else { 
			$("#tx_busca").focus();
			loadHtmlAjax(true, $("#divDatos"), "cat_empleado_inventario.php?"+id+dispatch); 
		}
	}	
	 
	$(function(){
    	$('input:text').setMask();
    });

</script>
<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	
	//Carga la informacion para combo de empleados
	//============================================
	$sql = "   SELECT E.id_empleado, E.tx_empleado ";
	$sql.= "   	 FROM tbl_empleado  E ";
//SEGURIDAD: ACCESO A SUS DIRECCIONES
$sql.="   inner join  tbl_centro_costos c on  c.id_centro_costos = E.id_Centro_costos ";
$sql.="   inner join  TBL_DIRECCION R ON C.ID_DIRECCION= R.ID_DIRECCION ";
$sql.="   inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  

	$sql.= " ORDER BY E.tx_empleado " ; 	
		
	//echo "aaa", $sql;
			
	$result = mysqli_query($mysql, $sql);		
		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoEmpleado[] = array(
			'id_empleado'	=>$row["id_empleado"],
			'tx_empleado'	=>$row["tx_empleado"]
			);
	} 
?>
<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"> 
<div class="ui-widget-header align-center">MODULO DE INVENTARIO</div>	
    <form id="opForm1" action="">
        <table cellspacing="1px" border="0" cellpadding="0" width="100%">
        	<tbody>
				<tr>
              		<td width="8%" class="ui-state-default">Empleado:</td>
              		<td width="30%">
						<select id="sel_empleado" class="textbox" onchange="javascript:buscaEmpleado($('#sel_empleado').val())";>
           				<option value="0">-- S e l e c c i o n e --</option>
           				<?								
						for ($i = 0; $i < count($TheCatalogoEmpleado); $i++)
						{	         			 
							while ($elemento = each($TheCatalogoEmpleado[$i]))					
								$id_empleado=$TheCatalogoEmpleado[$i]['id_empleado'];		
								$tx_empleado=$TheCatalogoEmpleado[$i]['tx_empleado'];			  
								echo "<option value=$id_empleado>$tx_empleado</option>";
						}						 
						?>
						</select>
                    </td>
              		<td width="8%" class="ui-state-default">Empleado a Buscar:</td>
                    <td width="25%"><input name="tx_busca" id="tx_busca" type="text" size="30" title="Nombre del Empleado" value=""/></td>                    						
              		<td width="29%" class="align-center">
                        <a id="btnBuscar" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Buscar" >
                    	Buscar
                    	<span class="ui-icon ui-icon-search"/></a>
                        <a id="btnNewEmp" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Agregar"> 
                        Agregar 
                        <span class="ui-icon ui-icon-plus"/></span></a>
                        <a id="btnSaveEmp" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Guardar">
                        Guardar 
                        <span class="ui-icon ui-icon-disk"/></span></a> 

                        <a id="btnDeleteEmp" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Borrar">
                        Borrar 
                        <span class="ui-icon ui-icon-trash"/></span></a>
                       
                        <a id="btnExportEmp" class="fm-button  ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Exportar">
                        Exportar 
                        <span class="ui-icon ui-icon-suitcase"/></span>
                        </a>                  	
                     </td>              		
           	  	</tr>
            	<tr>
            		<td colspan="5">&nbsp;</td>
       		  	</tr>				
            	<tr>
              		<td colspan="5" valign="top"><div id="divBusqueda" class="divConGrid1"></div></td>
            	</tr>
                <tr>
              		<td colspan="5" valign="top"><div id="divDatos"></div></td>
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