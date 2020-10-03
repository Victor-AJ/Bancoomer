<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION["sess_user"]))
{
?>
<script type="text/javascript">

	
	function fieldsRequiredLista()
	{	
		var error = true;		
		validSelect($("#sel_usuario"), $("#errsel_usuario"));
				
		if( !validSelect($("#sel_usuario"), $("#errsel_usuario"))
		) error = false;	
        		 
		return error;
	}	

	$("#btnResetar").click(function()
	{  	 
		if(fieldsRequiredLista())
		{	
			var url = "process_reseteo.php?";
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
						//loadHtmlAjax(true, $("#divDatos"), data.html);
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
	
	$("#btnNewEmp").click(function()
	{     
		var id="id=0";
		var dispatch="&dispatch=insert";
		$("#divBusqueda").hide();
		loadHtmlAjax(true, $("#divDatos"), "cat_empleado_inventario.php?"+id+dispatch);		
		   
     }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });

</script>
<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	# Carga de usuarios
	# ============================================
	$sql = "   SELECT id_usuario, tx_nombre ";
	$sql.= "   	 FROM tbl_usuario  ";
	$sql.= " ORDER BY tx_nombre " ; 	
		
	//echo "aaa", $sql;
			
	$result = mysqli_query($mysql, $sql);		
		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			"id_usuario"=>$row["id_usuario"],
			"tx_nombre"	=>$row["tx_nombre"]
			);
	} 
?>
<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"> 
<div class="ui-widget-header align-center">MODULO DE USUARIO</div>	
    <form id="opForm1" action="">
        <table cellspacing="1px" border="0" cellpadding="0" width="100%">
        	<tbody>
				<tr>
              		<td width="8%" class="ui-state-default">Usuario:</td>
              		<td width="30%">
                    <select id="sel_usuario" name="sel_usuario">
                    <option value="0">-- S e l e c c i o n e --</option>
                    <?								
						for ($i = 0; $i < count($TheCatalogo); $i++)
						{	         			 
							while ($elemento = each($TheCatalogo[$i]))					
								$id_usuario	=$TheCatalogo[$i]["id_usuario"];		
								$tx_nombre	=$TheCatalogo[$i]["tx_nombre"];			  
								echo "<option value=$id_usuario>$tx_nombre</option>";
						}						 
						?>
                    </select>
                    </td>
              		<td width="4%">&nbsp;</td>
                	<td width="29%"><span class="align-center">
                  	<a id="btnResetar" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Buscar">Resetear Contrase&ntilde;a<span class="ui-icon ui-icon-search"/></a></span></td>                    						
	  				<td width="29%" class="align-center"><div id="errsel_usuario" style="float:left;"></div></td>              		
       	  	  </tr>            		            	
          </tbody>
        </table>
  </form>   
  </div> 
<?
	mysqli_close($mysql);	
} 
else 
{
	echo "Sessi&oacute;n Invalida";
}	
?>  