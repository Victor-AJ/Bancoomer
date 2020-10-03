<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
	<script type="text/javascript">			
	 
		$("#verFacturas").find("tr").hover(		 
        	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
        );		 
		
		function btnEditAnaquel(valor0)	{				
			var id0="id="+valor0;
			var dispatch="&dispatch=save";
			$("#divAltaAnaquel").hide();	
			if (valor0 == 0) $("#divAltaAnaquel").hide(); 
			else loadHtmlAjax(true, $("#divAltaAnaquel"), "cat_anaquel_registro.php?"+id0+dispatch); 
		}
		
		function btnDeleteAnaquel(valor) {
			//alert ("Entre");
			var id="id_anaquel="+valor;
			var anio="&sel_anio_cap="+$("#sel_anio").val();	
			var dispatch="&dispatch=delete";
			var url = "process_anaquel.php?"+id+anio+dispatch;        							
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
						//alert ("Entre");
						jAlert(true,false,data.message,fAceptar);						
						$("#divAltaAnaquel").hide(); 
						loadHtmlAjax(true, $('#divDatos'), data.html);
					}
				}	
			}	
				
			if (confirm('Deseas BORRAR el Registro selecccionado... ?'))
			{	
				executeAjax("post", false ,url, "json", func);	     
			}	
		}
		 
	</script> 
<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	# ============================
	# Recibo variables
	# ============================
	$id					= $_GET['id']; 	
	$tx_busca_proveedor	= $_GET['id1']; 
	$tx_busca_factura	= $_GET['id2']; 
	$sel_busca_estatus	= $_GET['id3']; 
	
	//echo "busca_estado",$tx_busca_estado;
	//echo "<br>";

	$banio 		= $id.'%';		
	$bproveedor	= '%'.$tx_busca_proveedor.'%';		
	$bfactura	= '%'.$tx_busca_factura.'%';		
	
	$sql = "   SELECT id_anaquel, tx_anio, c.tx_nombre AS tx_direccion, tx_subdireccion, tx_departamento, tx_macroiniciativa, tx_glg, tx_proyecto, tx_descripcion, fl_monto_usd, tx_prioridad, tx_tipo, in_consecutivo, tx_notas ";
	$sql.= "     FROM tbl_anaquel a, tbl_macroiniciativa b, tbl_direccion c, tbl_departamento d, tbl_glg e, tbl_prioridad f, tbl_anaquel_tipo g, tbl_subdireccion h ";
	$sql.= "    WHERE a.id_macroiniciativa	= b.id_macroiniciativa ";
	$sql.= "      AND a.id_direccion 		= c.id_direccion ";
	$sql.= "      AND a.id_subdireccion 	= h.id_subdireccion ";
	$sql.= "      AND a.id_departamento 	= d.id_departamento ";
	$sql.= "      AND a.id_glg 				= e.id_glg ";
	$sql.= "      AND a.id_prioridad 		= f.id_prioridad ";
	$sql.= "      AND a.id_anaquel_tipo 	= g.id_anaquel_tipo ";
	$sql.= " ORDER BY tx_anio, tx_direccion, tx_departamento ";   	
	
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			'id_anaquel'		=>$row["id_anaquel"],
			'tx_anio'			=>$row["tx_anio"],
	  		'tx_direccion'		=>$row["tx_direccion"],
	  		'tx_subdireccion'	=>$row["tx_subdireccion"],
	  		'tx_departamento'	=>$row["tx_departamento"],
	  		'tx_macroiniciativa'=>$row["tx_macroiniciativa"],
	  		'tx_glg_corto'		=>$row["tx_glg_corto"],
			'tx_proyecto'		=>$row["tx_proyecto"],
	  		'tx_descripcion'	=>$row["tx_descripcion"],
	  		'fl_monto_usd'		=>$row["fl_monto_usd"],
	  		'tx_prioridad'		=>$row["tx_prioridad"],
	  		'tx_tipo'			=>$row["tx_tipo"],
	  		'in_consecutivo'	=>$row["in_consecutivo"],
	  		'tx_notas'			=>$row["tx_notas"]
		);
	} 	
	
	$registros=count($TheCatalogo);	
	
	?>
    <br>
 	<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:98%;"> 
    <fieldset>
    <legend class="ui-state-default"><em>RESULTADO DE LA B&Uacute;SQUEDA ...</em></legend>
    <br>
    <?
	if ($registros==0) { 
		echo "<table align='center' width='100%'>";
		echo "<br>";
		echo "<tr>";
		echo "<td class='align-center'><em><b>Sin Informaci&oacute;n Encontrada ...</b></em></td>";
		echo "</tr>";	
		echo "<br>";		
		echo "</table>";
	} else {
		echo "<table id='verFacturas' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<16; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					echo "<td width='2%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='A&ntilde;o'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Direcci&oacute;n';
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='Subdirecci&oacute;n';
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 4 : $TheField='Solicitante';
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 5 : $TheField='Macro Iniciativa';
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField='GLG';
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 7 : $TheField='Proyecto';
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 8 : $TheField='Detalle';
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 9 : $TheField='Estimado USD'; 
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 10 : $TheField='Prioridad Negocio'; 
					echo "<td width='5%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 11 : $TheField='Prioridad Proyecto'; 
					echo "<td width='4%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 12 : $TheField='Corporativo / Local'; 
					echo "<td width='5%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 13 : $TheField='Notas'; 
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 14 : $TheField='Editar'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 15 : $TheField='Borrar'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		
		$m=0;		
		$in_total_licencias=0;	
		$fl_total_precio_usd=0;	
		$fl_total_precio_mxn=0;	
		
		for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$id_anaquel			=$TheCatalogo[$i]['id_anaquel'];
				$tx_anio			=$TheCatalogo[$i]['tx_anio'];
				$tx_direccion		=$TheCatalogo[$i]['tx_direccion'];
				$tx_subdireccion	=$TheCatalogo[$i]['tx_subdireccion'];
				$tx_departamento	=$TheCatalogo[$i]['tx_departamento'];
				$tx_macroiniciativa	=$TheCatalogo[$i]['tx_macroiniciativa'];
				$tx_glg_corto		=$TheCatalogo[$i]['tx_glg_corto'];
				$tx_proyecto		=$TheCatalogo[$i]['tx_proyecto'];
				$tx_descripcion		=$TheCatalogo[$i]['tx_descripcion'];
				$fl_monto_usd		=$TheCatalogo[$i]['fl_monto_usd'];
				$tx_prioridad		=$TheCatalogo[$i]['tx_prioridad'];	  		
				$tx_tipo			=$TheCatalogo[$i]['tx_tipo'];
				$in_consecutivo		=$TheCatalogo[$i]['in_consecutivo'];
				$tx_notas			=$TheCatalogo[$i]['tx_notas'];
				
				$m++;				

				$fl_total_monto_usd=$fl_total_precio_usd+$fl_monto_usd;				
				
				//==========================							  						
				
				for ($a=0; $a<16; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$m; 
								echo "<td class='align-center'>$TheColumn</td>";
						break;						
						case 1: $TheColumn=$tx_anio;								
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 2: $TheColumn=$tx_direccion;
								echo "<td class='align-left'>$TheColumn</td>";
						break;
						case 3: $TheColumn=$tx_subdireccion; 																
								echo "<td class='align-left'>$TheColumn</td>";
						break;
						case 4: $TheColumn=$tx_departamento; 																
								echo "<td class='align-left'>$TheColumn</td>";
						break;
						case 5: $TheColumn=$tx_macroiniciativa;
								echo "<td class='align-left'>$TheColumn</td>";
						break;
						case 6: $TheColumn=$tx_glg_corto;
								echo "<td class='align-left'>$TheColumn</td>";
						break;
						case 7: $TheColumn=$tx_proyecto; 
								echo "<td class='align-left'>$TheColumn</td>";
						break;
						case 8: $TheColumn=$tx_descripcion; 
								echo "<td class='align-left'>$TheColumn</td>";
						break;
						case 9: $TheColumn=number_format($fl_monto_usd,0); 
								echo "<td class='align-right'>$TheColumn</td>";
						break;
						case 10: $TheColumn=$tx_prioridad;								
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 11: $TheColumn=$in_consecutivo;
								 echo "<td class='align-center'>$TheColumn</td>";
						break;						
						case 12: $TheColumn=$tx_tipo;
								 echo "<td class='align-center'>$TheColumn</td>";
						break;						
						case 13: $TheColumn=$tx_notas; 
								 echo "<td class='align-left'>$TheColumn</td>";							
						break;
						case 14 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnEditAnaquel($id_anaquel)'><span class='ui-icon ui-icon-pencil' title='Presione para EDITAR ...'></span></a>";				
								echo "<td class='ui-widget-header align-center' valign='top'>$TheColumn</td>";
						break;																					
						case 15 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnDeleteAnaquel($id_anaquel)'><span class='ui-icon ui-icon-trash' title='Presione para ELIMINAR ...'></span></a>";
								echo "<td class='ui-widget-header align-center' valign='top'>$TheColumn</td>";
						break;			
					}							
				}				
				echo "</tr>";		
				}								
				echo "<tr>";								  
				for ($a=0; $a<3; $a++)
				{
					switch ($a) 
					{   
						case 0 : $TheField="Totales";
							echo "<td colspan='9' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
						break;
						case 1 : $TheField=number_format($fl_total_monto_usd,0); 
							echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
						break;
						case 2 : $TheField="";
							echo "<td colspan='7' class='ui-state-highlight'>&nbsp;</td>";					
						break;
					}							
				}	
				echo "</tr>";	
				echo "<tr>";
				for ($a=0; $a<16; $a++)
				{
					switch ($a) 
					{   
						case 0 : $TheField='#';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";							 
						break;
						case 1 : $TheField='A&ntilde;o'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 2 : $TheField='Direcci&oacute;n';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";	
						break;
						case 3 : $TheField='Subdirecci&oacute;n';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";	
						break;
						case 4 : $TheField='Solicitante';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
						break;
						case 5 : $TheField='Macro Iniciativa';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
						break;
						case 6 : $TheField='GLG';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
						break;
						case 7 : $TheField='Proyecto';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
						break;
						case 8 : $TheField='Detalle';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
						break;
						case 9 : $TheField='Estimado USD'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 10 : $TheField='Prioridad Negocio'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 11 : $TheField='Prioridad Proyecto'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 12 : $TheField='Corporativo / Local'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 13 : $TheField='Notas'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 14 : $TheField='Editar'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 15 : $TheField='Borrar'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
					}							
				}	
				echo "</tr>";	
			echo "</table>";
		}
	?>
</fieldset>   
<div> 
<?
mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      