<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{	
?>
	<script type="text/javascript">	
	
        $("#sel_proveedor").change(function () {
            $("#sel_proveedor option:selected").each(function () {
				var id="id="+$(this).val();	
				var id1="id1="+$(this).val();	
				var id2="&id2="+$("#sel_anio").val();	
				var origen="&origen=proveedor"	
				$("#divInfPro").html("");
				$("#divInfDet").html("");
				
                loadHtmlAjax(false, $("#sel_cuenta"), "combo_cuenta.php?"+id);
				loadHtmlAjax(true, $("#divInfPro"), "inf_facturacion_proveedor_lista.php?"+id1+id2+origen);						
            });
         });
		 
		$("#sel_cuenta").change(function () {
	 		$("#sel_cuenta option:selected").each(function () {				
				var id="id="+$(this).val();	
				var id1="&id1="+$("#sel_proveedor").val();			
				var id2="&id2="+$("#sel_anio").val();	
				if (id==0) $("#divInfPro").html("");
				else loadHtmlAjax(true, $("#divInfPro"), "inf_facturacion_proveedor_lista.php?"+id+id1+id2);				
     		});
     	});
    
    </script>
    
	<?
	include("includes/funciones.php");  
	$mysql=conexion_db();	

	$par_anio	= $_GET['par_anio'];	
	
	# ====================================
	# Catalogo de PROVEEDORES
	# ====================================
	$sql = "   SELECT id_proveedor, tx_proveedor_corto ";
	$sql.= "     FROM tbl_proveedor ";
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
	<div class='ui-widget-header align-center'>FACTURACION POR PROVEEDOR</div>
 		<table cellspacing="1px" border="0" cellpadding="0" width="100%">        	
			<tr>
            	<td width="10%" class="ui-state-default align-left">Proveedor:</td>
  			  	<td width="20%">
               	<select id="sel_proveedor" name="sel_proveedor">
                  	<option value="0" class="">--- S e l e c c i o n e ---</option>
                  	<?
						for ($i=0; $i < count($TheCatalogoProveedores); $i++)	{         			 
						while ($elemento = each($TheCatalogoProveedores[$i]))					  		
							$id_proveedor		=$TheCatalogoProveedores[$i]['id_proveedor'];		
							$tx_proveedor_corto	=$TheCatalogoProveedores[$i]['tx_proveedor_corto'];								
							echo "<option value=$id_proveedor>$tx_proveedor_corto</option>";	
					}
					?>
                	</select>                
                </td>
                <td width="10%" class="ui-state-default">Cuenta:</td>
              	<td width="20%">
                <select id="sel_cuenta" name="sel_cuenta">
                	<option value="0" class="">--- S e l e c c i o n e ---</option>
                  	<?
						for ($i=0; $i < count($TheCatalogoCuentas); $i++)	{         			 
						while ($elemento = each($TheCatalogoCuentas[$i]))					  		
							$id_cuenta	=$TheCatalogoCuentas[$i]['id_cuenta'];		
							$tx_cuenta	=$TheCatalogoCuentas[$i]['tx_cuenta'];						
							echo "<option value=$id_cuenta>$tx_cuenta</option>";	
						}
					?>
                </select>
                </td>    
           	  	<td width="40%">&nbsp;</td>
         	</tr> 
            <tr>
          		<td colspan="5">&nbsp;</td>
       		</tr>	           	
            <tr>
            	<td colspan="5" valign="top"><div id="divInfPro"></div></td>                    
           	</tr>
            <tr>
          		<td colspan="5" valign="top"><div id="divInfDet"></div></td>
           	</tr>
        </table>	
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  