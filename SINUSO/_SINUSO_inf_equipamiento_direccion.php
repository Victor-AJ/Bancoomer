<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	include("includes/funciones.php");  
	$mysql=conexion_db();	
	
	$fecha_hoy = date("j-m-Y");
	
	$par_direccion 	= $_GET["par_direccion"];		
	?>
    
	<script type="text/javascript">	
	
		$("#tabla_equipamiento").find("tr").hover(		 
        	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
        );		
		
		function btnInformesDirFac(v1)
		{
			var p1= parseInt(v1); 			
			var id0="par_direccion="+p1;
			var id1="&par_anio="+$("#par_anio").val();
			//alert ("Entre "+id0+id1);
	
			$("#divFacturacionDirN1").html("");
			$("#divFacturacionDirN2").html("");
	
			loadHtmlAjax(true, $("#divFacturacionDirN1"), "inf_facturacion_dir_sub.php?"+id0+id1); 
		}	
	
	</script> 
    
	<?
	
	if ($par_direccion==0) 
	{					
		$sql = "   SELECT id_direccion, tx_nombre ";
		$sql.= "     FROM tbl_direccion ";
		$sql.= " ORDER BY tx_nombre ";
		
	} else  {		

		$sql = "   SELECT id_direccion, tx_nombre ";
		$sql.= "     FROM tbl_direccion ";
		$sql.= "    WHERE id_direccion = $par_direccion ";
		$sql.= " ORDER BY tx_nombre ";
		
	}
	
	//echo "sql ", $sql;
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeDireccionEqui[] = array(
			'id_direccion'	=>$row["id_direccion"],
			'tx_nombre'		=>$row["tx_nombre"]
		);
	} 	
	
	$registros=count($TheInformeDireccionEqui);	
	
	if ($registros==0) {	
		echo "<table align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<br/>";
		echo "<tr>";
		echo "<td class='align-center'><em><b>Sin Informaci&oacute;n ...</b></em></td>";
		echo "</tr>";	
		echo "<br/>";		
		echo "</table>";	 
	} else {
		echo "<div class='ui-widget-header align-center'>EQUIPAMIENTO POR DIRECCION</div>";	
		echo "<table id='tabla_equipamiento' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		echo "<td width='3%' rowspan='2' class='ui-state-highlight align-center'>#</td>";							 
		echo "<td width='47%' rowspan='2' class='ui-state-highlight align-center'>DIRECCION</td>";		
		echo "<td width='24%' colspan='2' class='ui-state-highlight align-center'>TELEFONIA</td>";	
		echo "<td width='24%' colspan='2' class='ui-state-highlight align-center'>COMPUTO</td>";						 
		echo "<td width='3%' rowspan='2' class='ui-state-highlight align-center'>Gr&aacute;fica</td>";						 
		echo "</tr>";
		echo "<tr>";
		echo "<td width='12%' class='ui-state-highlight align-center'>Unidades</td>";	
		echo "<td width='12%' class='ui-state-highlight align-center'>Monto</td>";						 
		echo "<td width='12%' class='ui-state-highlight align-center'>Unidades</td>";	
		echo "<td width='12%' class='ui-state-highlight align-center'>Monto</td>";						 
		echo "</tr>";
				
		$c=0;		
		$fl_total_precio_usd=0;		
		$fl_total_precio_usd_1=0;			
		$fl_total_precio_usd_2=0;			
		$fl_total_precio_usd_3=0;	
		
		for ($i=0; $i < count($TheInformeDireccionEqui); $i++)	{ 	        			 
			while ($elemento = each($TheInformeDireccionEqui[$i]))				
				$id_direccion	=$TheInformeDireccionEqui[$i]['id_direccion'];	  		
				$tx_nombre		=$TheInformeDireccionEqui[$i]['tx_nombre'];	  		
				
				$fl_total_precio_usd=$fl_total_precio_usd+$total_precio_usd;
				
				$c++;
				
				# ===============================					
				# Busco Equipamiento de TELEFONIA
				# ===============================
				
				$sql1 = " 	SELECT COUNT( * ) AS in_equipamiento, SUM( fl_precio_usd ) + SUM( fl_precio_mxn *13 ) AS total_precio_usd ";
				$sql1.= " 	  FROM tbl_telefonia a, tbl_empleado_telefonia b, tbl_empleado c, tbl_centro_costos d, tbl_direccion e, tbl_telefonia_plan f ";
				$sql1.= "    WHERE a.id_telefonia 		= b.id_telefonia ";
				$sql1.= "      AND b.id_empleado 		= c.id_empleado ";
				$sql1.= "      AND c.id_centro_costos 	= d.id_centro_costos ";
				$sql1.= "      AND d.id_direccion 		= e.id_direccion ";
				$sql1.= "      AND b.id_telefonia_plan 	= f.id_telefonia_plan ";
				$sql1.= "      AND d.id_direccion 		= $id_direccion ";
				$sql1.= " GROUP BY d.id_direccion ";
				
				//echo "sql",$sql1;	
				
				$result1 = mysqli_query($mysql, $sql1);	
				while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
				{	
					$TheInformeDireccionEqui_1[] = array(						
						'in_equipamiento_1'		=>$row1["in_equipamiento"],
						'total_precio_usd_1'	=>$row1["total_precio_usd"]
					);
				} 
				
				$num_result1=mysqli_num_rows($result1);	
				
				for ($j=0; $j < count($TheInformeDireccionEqui_1); $j++)	{ 	        			 
					while ($elemento = each($TheInformeDireccionEqui_1[$j]))	
						$in_equipamiento_1	=$TheInformeDireccionEqui_1[$j]['in_equipamiento_1'];	
						$total_precio_usd_1	=$TheInformeDireccionEqui_1[$j]['total_precio_usd_1'];	
				}
				
				//echo "aaa", $in_equipamiento_1;
				
				if ($num_result1==0)
				{	
					$in_equipamiento_1=0;		
					$total_precio_usd_1=0;
				}			
			
				$fl_total_precio_usd_1	=$fl_total_precio_usd_1+$total_precio_usd_1;
												
				# =============================					
				# Busco Equipamiento de COMPUTO
				# =============================				
				
				$sql2 = "   SELECT COUNT(*) as in_equipamiento, SUM(a.fl_precio_usd) + SUM(a.fl_precio_mxn * 13) AS total_precio_usd ";
			 	$sql2.= "     FROM tbl_computo a, tbl_empleado_computo b, tbl_empleado c, tbl_centro_costos d, tbl_direccion e ";
				$sql2.= "    WHERE a.id_computo 		= b.id_computo ";
				$sql2.= "      AND b.id_empleado 		= c.id_empleado ";
				$sql2.= "      AND c.id_centro_costos 	= d.id_centro_costos ";
				$sql2.= "      AND d.id_direccion 		= e.id_direccion ";
				$sql2.= "      AND e.id_direccion		= $id_direccion ";
				$sql2.= " GROUP BY e.id_direccion ";
				
				//echo "<br>";
				//echo "sql 2 ",$sql2;	
								
				$result2 = mysqli_query($mysql, $sql2);						
					
				while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
				{	
					$TheInformeDireccionEqui_2[] = array(
						'in_equipamiento_2'		=>$row2["in_equipamiento"],
						'total_precio_usd_2'	=>$row2["total_precio_usd"]
					);
				} 
				
				$num_result2=mysqli_num_rows($result2);	
				
				for ($k=0; $k < count($TheInformeDireccionEqui_2); $k++)	{ 	        			 
					while ($elemento = each($TheInformeDireccionEqui_2[$k]))	
						$in_equipamiento_2	=$TheInformeDireccionEqui_2[$k]['in_equipamiento_2'];				
						$total_precio_usd_2	=$TheInformeDireccionEqui_2[$k]['total_precio_usd_2'];	
				}		
				
				if ($num_result2==0)
				{		
					$in_equipamiento_2=0;		
					$total_precio_usd_2=0;
				}			
			
				$fl_total_precio_usd_2	=$fl_total_precio_usd_2+$total_precio_usd_2;
				
				# ============================	
							
				echo "<tr>";
				for ($a=0; $a<7; $a++)
					{
						switch ($a) 
						{   
							case 0: $TheColumn=$c; 									
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;						
							case 1: $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesDirFac($id_direccion)' title='Presione para ver el detalle de la $tx_nombre ...'>$tx_nombre</a>";
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;																																		
							case 2: if ($in_equipamiento_1==0) $TheColumn="-";
									else $TheColumn=number_format($in_equipamiento_1,0); 
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;																									
							case 3: if ($total_precio_usd_1==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_1,0); 
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;																									
							case 4: if ($in_equipamiento_2==0) $TheColumn="-";
									else $TheColumn=number_format($in_equipamiento_2,0); 
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;																		
							case 5: if ($total_precio_usd_2==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_2,0); 
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;
							case 6 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnEditFactura($id_direccion)'><span class='ui-icon ui-icon-signal' title='Presione para ver Gr&aacute;fica ...'></span></a>";				
								echo "<td class='ui-widget-header' align='center' valign='top'>$TheColumn</td>";
							break;								
						}							
					}				
				echo "</tr>";					
			}	
		echo "<tr>";								  
		for ($a=0; $a<7; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='TOTAL';
					echo "<td colspan='2' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
				break;				
				case 1 : $TheField=number_format($fl_total_precio_usd_1,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;				
				case 2 : $TheField=number_format($fl_total_precio_usd_2,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;				
				case 3 : $TheField=number_format($fl_total_precio_usd_3,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;			
				case 4 : $TheField=number_format($fl_total_precio_usd,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;		
				case 5: 									
					echo "<td class='ui-state-highlight align-right'>&nbsp;</td>";
				break;			
			}							
		}	
		echo "</tr>";	
		echo "<td rowspan='2' class='ui-state-highlight align-center'>#</td>";							 
		echo "<td rowspan='2' class='ui-state-highlight align-center'>DIRECCION</td>";	
		echo "<td class='ui-state-highlight align-center'>Unidades</td>";	
		echo "<td class='ui-state-highlight align-center'>Monto</td>";						 
		echo "<td class='ui-state-highlight align-center'>Unidades</td>";	
		echo "<td class='ui-state-highlight align-center'>Monto</td>";						 
		echo "<td rowspan='2' class='ui-state-highlight align-center'>Gr&aacute;fica</td>";						 
		echo "</tr>";
		echo "<tr>";			
		echo "<td colspan='2' class='ui-state-highlight align-center'>TELEFONIA</td>";	
		echo "<td colspan='2' class='ui-state-highlight align-center'>COMPUTO</td>";						 
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan='11' align='left'>";	
        echo "<ul type='square'> ";
        echo "<li>Monto calculado en base a la derrama.</li> ";
        echo "<li>Monto en USD (D&oacute;lare Americano).</li>";
        echo "<li>Actualizado al $fecha_hoy.</li>";
        echo "</ul>";      
        echo "</td>";
		echo "</tr>";		
	echo "</table>";
	?>   
    <div id="divFacturacionDirN1"></div>
    <div id="divFacturacionDirN2"></div>
    <?
	}
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      