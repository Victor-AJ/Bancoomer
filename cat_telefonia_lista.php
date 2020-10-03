<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
	 <script type="text/javascript">
		//$("#divDatos").hide();
		
		$("#btnNewTel").click(function(){

			var id="id="+$("#id").val();
			var dispatch="&dispatchTel=insert";
			var url="cat_empleado_telefonia_m.php?"+id+dispatch;   				
			loadHtmlAjax(true, $("#divAltaTel"), url);

		 }).hover(function(){
			$(this).addClass("ui-state-hover")
		 },function(){
			$(this).removeClass("ui-state-hover")
		 });		 
		 
		 $("#verTelefonia").find("tr").hover(		 
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
	
	$sql = "  SELECT id_empleado_telefonia, tx_tipo, tx_marca, tx_modelo, tx_numero, tx_proveedor, tx_plan, fl_precio_mxn, tx_serie, tx_siaf ";
	$sql.= "    FROM tbl_empleado_telefonia a, tbl_empleado b, tbl_telefonia c, tbl_telefonia_plan d ";
	$sql.= "   WHERE a.id_empleado	=$id  and a.tx_indicador = '1' ";
	$sql.= "	 AND a.id_empleado 	= b.id_empleado ";
	$sql.= "     AND a.id_telefonia = c.id_telefonia ";
	$sql.= " 	 AND a.id_telefonia_plan = d.id_telefonia_plan ";
	$sql.= "ORDER BY tx_tipo, tx_marca, tx_modelo ";
	
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			'id_empleado_telefonia'	=>$row["id_empleado_telefonia"],
			'tx_tipo'				=>$row["tx_tipo"],
			'tx_equipo'				=>$row["tx_equipo"],
	  		'tx_marca'				=>$row["tx_marca"],
	  		'tx_modelo'				=>$row["tx_modelo"],
	  		'tx_numero'				=>$row["tx_numero"],
	  		'tx_proveedor'			=>$row["tx_proveedor"],
	  		'tx_plan'				=>$row["tx_plan"],
	  		'fl_precio_mxn'			=>$row["fl_precio_mxn"],
	  		'tx_serie'				=>$row["tx_serie"],
	  		'tx_siaf'				=>$row["tx_siaf"]
		);
	} 	
	
	//<BITACORA>
    $myBitacora = new Bitacora();
	$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_EMPLEADO_TELEFONIA" , "$id_login" ,  "id_empleado=$id" , ""  ,  "cat_telefonia_lista.php");
	//<\BITACORA>
	
	$registros=count($TheCatalogo);	
	?>
    <div>
    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
    		<tr id="Act_Buttons">
    			<td class="EditButton ui-widget-content" style="text-align:center">                           
    			<a id="btnNewTel" class="fm-button ui-state-default ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Agregar Licencias"> 
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
		echo "<table id='verTelefonia' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<12; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					echo "<td width='2%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='Tipo'; 
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Marca';
					echo "<td width='10%' width='10%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='Modelo';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 4 : $TheField='N&uacute;mero';
					echo "<td width='8%' class='ui-state-highlight align-center'>$TheField</td>";						 			
				break;
				case 5 : $TheField='Proveedor';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField='Plan';
					echo "<td width='12%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 7 : $TheField='Precio';
					echo "<td width='8%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 8 : $TheField='Serie';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 9 : $TheField='SIAF';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 10 : $TheField='Editar'; 
					echo "<td width='5%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 11 : $TheField='Borrar'; 
					echo "<td width='5%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		
		$j=0;	
		$fl_total_precio_mxn=0;		
		
		for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$id_empleado_telefonia	=$TheCatalogo[$i]['id_empleado_telefonia'];
				$tx_tipo				=$TheCatalogo[$i]['tx_tipo'];
				$tx_marca				=$TheCatalogo[$i]['tx_marca'];
				$tx_modelo				=$TheCatalogo[$i]['tx_modelo'];
				$tx_numero				=$TheCatalogo[$i]['tx_numero'];
				$tx_proveedor			=$TheCatalogo[$i]['tx_proveedor'];
				$tx_plan				=$TheCatalogo[$i]['tx_plan'];
				$fl_precio_mxn			=$TheCatalogo[$i]['fl_precio_mxn'];
				$tx_serie				=$TheCatalogo[$i]['tx_serie'];
				$tx_siaf				=$TheCatalogo[$i]['tx_siaf'];
				
				$j++;	
				
				$fl_total_precio_mxn=$fl_total_precio_mxn+$fl_precio_mxn;		
				
				for ($a=0; $a<12; $a++)
					{
						switch ($a) 
						{   
							case 0: $TheColumn=$j; 
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;						
							case 1: $TheColumn=$tx_tipo;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 2: $TheColumn=$tx_marca;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 3: $TheColumn=$tx_modelo;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 4: $TheColumn=$tx_numero;
									if($TheColumn=="") echo "<td class='align-right' valign='top'>&nbsp;</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";											
							break;
							case 5: $TheColumn=$tx_proveedor;
									if($TheColumn=="") echo "<td class='align-left' valign='top'>&nbsp;</td>";						
									else echo "<td class='align-left' valign='top'>$TheColumn</td>";		
							break;
							case 6: $TheColumn=$tx_plan;
									if($TheColumn=="") echo "<td class='align-left' valign='top'>&nbsp;</td>";						
									else echo "<td class='align-left' valign='top'>$TheColumn</td>";											
							break;
							case 7: $TheColumn=$fl_precio_mxn;
									if($TheColumn=="") echo "<td class='align-right' valign='top'>&nbsp;</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";											
							break;
							case 8: $TheColumn=$tx_serie;
									if($TheColumn=="") echo "<td class='align-right' valign='top'>&nbsp;</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";		
							break;
							case 9: $TheColumn=$tx_siaf; 
									if($TheColumn=="") echo "<td class='align-right' valign='top'>&nbsp;</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";		
							break;
							case 10 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnEditTelefonia($id, $id_empleado_telefonia)';><span class='ui-icon ui-icon-pencil' title='Presione para EDITAR ...'></span></a>";				
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;													
							case 11 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnDeleteTelefonia($id, $id_empleado_telefonia)';><span class='ui-icon ui-icon-trash' title='Presione para ELIMINAR ...'></span></a>";
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;			
						}							
					}				
					echo "</tr>";					
		}	
		echo "<tr>";								  
		for ($a=0; $a<12; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField=""; 
					echo "<td colspan='7' class='ui-state-highlight align-right'>Total&nbsp;&nbsp;</td>";					
				break;
				case 8 : $TheField=number_format($fl_total_precio_mxn,2);  
					echo "<td class='ui-state-highlight align-center align-right'>$TheField</td>";					
				break;
				case 9 : $TheField=""; 
					echo "<td colspan='4' class='ui-state-highlight align-center'>&nbsp;</td>";					
				break;
			}							
		}	
		echo "</tr>";	
		echo "</table>";
	}
	?>
    </div>
    <div id="divAltaTel"></div>
<?
mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      