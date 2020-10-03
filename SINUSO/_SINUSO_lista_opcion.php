<?	
include("includes/funciones.php");  
$mysql=conexion_db();

$id	= $_GET["id"];
	
$sql = "   SELECT id_opcion, tx_nombre ";
$sql.= "     FROM tbl_opcion ";
$sql.= " ORDER BY tx_nombre ";
	
$result = mysqli_query($mysql, $sql);	
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{	
	$TheCatalogo[] = array(
		'id_opcion'=>$row["id_opcion"],
	  	'tx_nombre'=>$row["tx_nombre"]
	);
} 	


												
							echo "<table cellspacing='0' border='0' cellpadding='0' width='100%'>";
							echo "	<tr>";
							echo "		<td class='ui-state-default align-center'>Opciones</td>";
							echo "		<td class='ui-state-default align-center'>Consulta</td>";
							echo "		<td class='ui-state-default align-center'>Insertar</td>";
							echo "		<td class='ui-state-default align-center'>Actualizar</td>";
							echo "		<td class='ui-state-default align-center'>Borrar</td>";
							echo "	</tr>";
								
								for ($i=0; $i < count($TheCatalogo); $i++)
								{         			 
									while ($elemento = each($TheCatalogo[$i]))	
										echo "<tr>";			
											$id_opcion=$TheCatalogo[$i]['id_opcion'];		
											$tx_nombre=$TheCatalogo[$i]['tx_nombre'];						
											echo "<td>";			
											echo "<input name='checks_op' type='checkbox' value={$TheCatalogo[$i]['id_opcion']}>$tx_nombre";				
											echo "</td>";
											echo "<td class='align-center'>";			
											echo "<input name='checks_op' type='checkbox' value={$TheCatalogo[$i]['id_opcion']}>";				
											echo "</td>";
											echo "<td class='align-center'>";			
											echo "<input name='checks_op' type='checkbox' value={$TheCatalogo[$i]['id_opcion']}>";				
											echo "</td>";
											echo "<td class='align-center'>";			
											echo "<input name='checks_op' type='checkbox' value={$TheCatalogo[$i]['id_opcion']}>";				
											echo "</td>";
											echo "<td class='align-center'>";			
											echo "<input name='checks_op' type='checkbox' value={$TheCatalogo[$i]['id_opcion']}>";				
											echo "</td>";
										echo "</tr>";
								}	
								echo "</table>";  
                        	?>
                       	  </div>
                    </td>
                    <td width="10%"></td>                        
                </tr>
                <tr>
               	  	<td width="10%" class="ui-state-default">Entidad:</td>
                   	<td width="20%">
                    	<select id="selentidad" name="selentidad" onchange="openDireccion($('#selentidad').val())";>
                        	<option value="0" class="">--- S e l e c c i o n e ---</option>
                        	<?
                          	for ($i=0; $i < count($TheCatalogoEntidad); $i++)
							{         			 
								while ($elemento = each($TheCatalogoEntidad[$i]))					  		
									$id_entidad=$TheCatalogoEntidad[$i]['id_entidad'];		
									$tx_nombre=$TheCatalogoEntidad[$i]['tx_nombre'];	
									echo "<option value=$id_entidad>$tx_nombre</option>";	
							}
							?>
                      	</select>
                    </td>
                    <td width="10%"></td>
                </tr>
                <tr>
                	<td width="10%" class="ui-state-default">Direcciones:</td>
                    <td width="40%" valign="top"><div id="divDireccion" style="float:left;"></div></td>
                    <td width="10%"></td>
                </tr>
                <tr>                     	
                	<td colspan="5">&nbsp;</td>
                </tr>
                <tr id="Act_Buttons">
                	<td class="EditButton ui-widget-content" colspan="8" style="text-align:center">                            
                    	<a id="btnSave" class="fm-button ui-state-default ui-corner-all fm-button-icon-left ui-pg-div" href="javascript:void(0)" style="font-size:smaller;">
                        Guardar
                        <span class="ui-icon ui-icon-disk"/></a>
                        <a id="btnUndo2" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;">
                        Cancelar
                        <span class="ui-icon ui-icon-cancel"/></a>
                   	</td>
              	</tr>
           	</tbody>    
     	</table>     
