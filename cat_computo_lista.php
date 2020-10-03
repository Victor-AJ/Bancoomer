<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
	 <script type="text/javascript">
		//$("#divDatos").hide();
		
		$("#btnNewCom").click(function(){

			var id="id="+$("#id").val();
			var dispatch="&dispatchCom=insert";
			var url="cat_empleado_computo_m.php?"+id+dispatch;   				
			loadHtmlAjax(true, $("#divAltaCom"), url);

		 }).hover(function(){
			$(this).addClass("ui-state-hover")
		 },function(){
			$(this).removeClass("ui-state-hover")
		 });		 
		 
		 $("#verComputo").find("tr").hover(		 
         	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
         );
		 
	</script> 
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();
	$id_login = $_SESSION['sess_iduser'];
	
	// Recibo variables
	// ============================
	$id	= $_GET['id']; 		
		
	$sql = "   SELECT id_empleado_computo, tx_equipo, tx_marca, tx_modelo, tx_ram, tx_serie, tx_siaf, tx_compartido ";
	$sql.= "     FROM tbl_empleado_computo a, tbl_empleado b, tbl_computo c ";
	$sql.= "    WHERE a.id_empleado=$id and a.tx_indicador = '1' ";
	$sql.= "      AND a.id_empleado= b.id_empleado ";
	$sql.= "      AND a.id_computo = c.id_computo ";
	$sql.= " ORDER BY tx_compartido, tx_equipo, tx_marca, tx_marca " ;	
	
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			'id_empleado_computo'	=>$row["id_empleado_computo"],
			'tx_equipo'				=>$row["tx_equipo"],
	  		'tx_marca'				=>$row["tx_marca"],
	  		'tx_modelo'				=>$row["tx_modelo"],
	  		'tx_ram'				=>$row["tx_ram"],
	  		'tx_serie'				=>$row["tx_serie"],
	  		'tx_siaf'				=>$row["tx_siaf"],
	  		'tx_compartido'			=>$row["tx_compartido"]
		);
	} 	
	
	//<BITACORA>
    $myBitacora = new Bitacora();
	$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_EMPLEADO_COMPUTO" , "$id_login" ,  "id_empleado=$id" , ""  ,  "cat_computo_lista.php");
	//<\BITACORA>
	
	$registros=count($TheCatalogo);	
	?>
    <div>
    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
    		<tr id="Act_Buttons">
    			<td class="EditButton ui-widget-content" style="text-align:center">                           
    			<a id="btnNewCom" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Agregar Licencias"> 
    			Agregar
    			<span class="ui-icon ui-icon-plus"/></a>
    			</td>		
			</tr>
        </table>    
    </div>   
	<div id="divListaComputo"> 
	<?
	if ($registros==0) { }
	else {
		echo "<table id='verComputo' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<10; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='Equipo'; 
					echo "<td width='18%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Marca';
					echo "<td width='17%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='Modelo';
					echo "<td width='17%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 4 : $TheField='RAM';
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 5 : $TheField='Serie';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField='SIAF';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 7 : $TheField='Uso';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 8 : $TheField='Editar'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 9 : $TheField='Borrar'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		
		$j=0;		
		
		for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$id_empleado_computo	=$TheCatalogo[$i]['id_empleado_computo'];
				$tx_equipo				=$TheCatalogo[$i]['tx_equipo'];
				$tx_marca				=$TheCatalogo[$i]['tx_marca'];
				$tx_modelo				=$TheCatalogo[$i]['tx_modelo'];
				$tx_ram					=$TheCatalogo[$i]['tx_ram'];
				$tx_serie				=$TheCatalogo[$i]['tx_serie'];
				$tx_siaf				=$TheCatalogo[$i]['tx_siaf'];
				$tx_compartido			=$TheCatalogo[$i]['tx_compartido'];
				
				$j++;				
				
				for ($a=0; $a<10; $a++)
					{
						switch ($a) 
						{   
							case 0: $TheColumn=$j; 
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;						
							case 1: $TheColumn=$tx_equipo;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 2: $TheColumn=$tx_marca;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 3: $TheColumn=$tx_modelo;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 4: $TheColumn=$tx_ram;
									if($TheColumn=="") echo "<td class='align-right' valign='top'>&nbsp;</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";		
							break;
							case 5: $TheColumn=$tx_serie;
									if($TheColumn=="") echo "<td class='align-right' valign='top'>&nbsp;</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";		
							break;
							case 6: $TheColumn=$tx_siaf; 
									if($TheColumn=="") echo "<td class='align-right' valign='top'>&nbsp;</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";		
							break;
							case 7: if ($tx_compartido=="0") $TheColumn="PARTICULAR";
									else if ($tx_compartido=="1") $TheColumn="COMPARTIDO";
									else $TheColumn="&nbsp;";						
									echo "<td class='align-left' valign='top'>$TheColumn</td>";		
							break;
							case 8 : $TheColumn ="<a href='#' style='cursor:pointer' onclick='javascript:btnEditComputo($id, $id_empleado_computo)';><span class='ui-icon ui-icon-pencil' title='Presione para editar ...'></span></a>";				
								echo "<td class='ui-widget-header' align='center'>$TheColumn</td>";
							break;													
							case 9 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnDeleteComputo($id, $id_empleado_computo)';><span class='ui-icon ui-icon-trash' title='Presione para ELIMINAR ...'></span></a>";
								echo "<td class='ui-widget-header' align='center'>$TheColumn</td>";
							break;			
						}							
					}				
					echo "</tr>";					
		}	
		echo "<tr>";								  
		for ($a=0; $a<9; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField=""; 
					echo "<td colspan='10' class='ui-state-highlight align-center'>&nbsp;</td>";					
				break;
			}							
		}	
		echo "</tr>";	
		echo "</table>";
	}
	?>
    </div>
    <div id="divAltaCom"></div>
<?
mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      