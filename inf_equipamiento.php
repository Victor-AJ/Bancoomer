<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
session_start();
if 	(isset($_SESSION['sess_user']))
{
	$id_login = $_SESSION['sess_iduser'];
	
?>
<script type="text/javascript">	

	$("#btnBuscar").click(function(){  
		
		loadHtmlAjax(true, $("#divTabs"), "inf_equipamiento_tabs.php"); 					
        
	}).hover(function(){
     	$(this).addClass("ui-state-hover")
    },function(){
        $(this).removeClass("ui-state-hover")
    });
	
	$("#btnLimpiar").click(function(){     
	
		$("#sel_direccion").val("--- S e l e c c i o n e ---");
		$("#sel_subdireccion").html("");
		$("#sel_subdireccion").val("--- S e l e c c i o n e ---");
		$("#sel_departamento").html("");	
		$("#divTabs").html("");	
		   
     }).hover(function(){
     	$(this).addClass("ui-state-hover")
     },function(){
        $(this).removeClass("ui-state-hover")
     });
		
	$("#sel_direccion").change(function () {
    	$("#sel_direccion option:selected").each(function () {
			var id="id="+$(this).val();	
			$("#divTabs").html("");	
			$("#sel_departamento").html("");
			//alert("id: "+id);
            loadHtmlAjax(false, $("#sel_subdireccion"), "combo_subdireccion1.php?"+id);			
       	});
   	});
	
	$("#sel_subdireccion").change(function () {
    	$("#sel_subdireccion option:selected").each(function () {
			var id="id="+$("#sel_direccion").val();	
			var id1="&id1="+$(this).val();	
			$("#divTabs").html("");	
			//alert (id + " " + id1);
			loadHtmlAjax(false, $("#sel_departamento"), "combo_departamento1.php?"+id+id1);
			//loadHtmlAjax(true, $("#divTabs"), "inf_equipamiento_tabs.php"); 					
       	});
   	});
	
	$("#sel_departamento").change(function () {
    	$("#sel_departamento option:selected").each(function () {
			$("#divTabs").html("");	
			//loadHtmlAjax(true, $("#divTabs"), "inf_equipamiento_tabs.php"); 					
       	});
   	});
	
	$("#sel_equipo").change(function () {
    	$("#sel_equipo option:selected").each(function () {
			$("#divTabs").html("");	
       	});
   	});
	
	
</script>
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	
	
	$mysql=conexion_db();		
	
	# Carga el combo de direcciones
	# =============================
	
	$sql = "   SELECT D.id_direccion, D.tx_nombre_corto ";
	$sql.= "     FROM tbl_direccion D";
	$sql.= "    WHERE D.id_entidad 	= 1 ";
	$sql.= " 	  AND D.tx_agrupacion	='1' and D.tx_indicador='1'";
	$sql.= " AND D.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	$sql.= " ORDER BY tx_nombre ";
	
	//echo "OROGINAL: <br>".$sql;
	
	$sql = "   SELECT D.id_direccion, D.tx_nombre_corto ";
	$sql.= "     FROM tbl_direccion D";
	$sql.= "    WHERE D.id_entidad 	= 1 ";
	//$sql.= " 	  AND D.tx_agrupacion	='1' and D.tx_indicador='1'";
	$sql.= " 	  and D.tx_indicador='1'";
	$sql.= " AND D.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	$sql.= " ORDER BY tx_nombre ";
	//echo "<br><br>MODIFICADO: <br>".$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoDireccion[] = array(
			'id_direccion'		=>$row["id_direccion"],
			'tx_nombre_corto'	=>$row["tx_nombre_corto"]
		);
	} 
		
?>
<form id="opInfomesEqui" action=""> 	
	<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"> 
    	<div class="ui-widget-header align-center">RESUMEN EJECUTIVO - EQUIPAMIENTO</div>	
    	<table cellspacing="1px" border="0" cellpadding="0" width="100%">  
    		<tr>
   				<td width="10%" class="ui-state-default">Direcci&oacute;n Corporativa:</td>
       		  	<td width="30%">
                    <select id="sel_direccion" name="sel_direccion">
                    <option value="0" class="">--- S e l e c c i o n e ---</option>
                    <?
                    	for ($i=0; $i < count($TheCatalogoDireccion); $i++)	{         			 
                        	while ($elemento = each($TheCatalogoDireccion[$i]))					  		
                            	$id_direccion	=$TheCatalogoDireccion[$i]['id_direccion'];		
                            	$tx_nombre_corto=$TheCatalogoDireccion[$i]['tx_nombre_corto'];
                            	echo "<option value=$id_direccion>$tx_nombre_corto</option>";	
                    	}
                	?>
                	</select>
                </td>    
   		  	  	<td width="10%" class="ui-state-default">Equipaqmiento:</td>
      			<td width="30%">
                    <select id="sel_equipo" name="sel_equipo">
                        <option value="1">COMPUTO</option>
                        <option value="2">TELEFONIA</option>
                    </select>
                </td> 
                <td width="20%" style="text-align:center">
                	<a id="btnBuscar" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Buscar" >
                    Buscar
               		<span class="ui-icon ui-icon-search"/></span>
                    </a>
                    <a id="btnLimpiar" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Limpiar">
                    Limpiar 
                    <span class="ui-icon ui-icon-trash"/></span>
                    </a>
              	</td>            		
  		  	</tr>
            <tr>
    			<td class="ui-state-default">Direcciones:</td>
    		  	<td>
                	<select id="sel_subdireccion" name="sel_subdireccion">
                		<option value="0">--- S e l e c c i o n e ---</option>
              		</select>                </td>
    		  	<td class="ui-state-default">Empleados:</td>
    		  	<td>
                	<select id="sel_status" name="sel_status">
                  		<option value="1">ACTIVOS</option>
                  		<option value="0">INACTIVOS</option>
                  		<option value="2">AMBOS</option>
                	</select>
                </td>
                <td >&nbsp;</td>
  			</tr>
        	<tr>
    			<td class="ui-state-default">Departamento:</td>
    		  	<td>
                	<select id="sel_departamento" name="sel_departamento">
                		<option value="0">--- S e l e c c i o n e ---</option>
              		</select>                </td>
    		  	<td>&nbsp;</td>
    		  	<td>&nbsp;</td>
                <td>&nbsp;</td>
  		  	</tr>
        	<tr>
        		<td colspan="5">&nbsp;</td>
        	</tr>    
    		<tr>
        		<td colspan="5"><div id="divTabs"></div></td>
      		</tr>           
   		</table>        
  </div>
</form> 
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  