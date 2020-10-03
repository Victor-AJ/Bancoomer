<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION["sess_user"]))
{
	
	$id_login = $_SESSION['sess_iduser'];
?>
	<script type="text/javascript">			
		 
		$("#tablaEquipamiento").find("tr").hover(		 
        	function() { $(this).addClass("ui-state-hover"); },
     		function() { $(this).removeClass("ui-state-hover"); }
        );		 
		
		$("#btnExportar").click(function()
		{   
			var id0="par_direccion="+$("#sel_direccion").val();	
			var id1="&par_subdireccion="+$("#sel_subdireccion").val();	
			var id2="&par_departamento="+$("#sel_departamento").val();	
			var id3="&par_equipo="+$("#sel_equipo").val();	
			var id4="&par_status="+$("#sel_status").val();			
			var id5="&par_equipo_com="+$("#sel_equipo_com").val();	
			var id6="&par_marca="+$("#sel_marca").val();	
			var id7="&par_modelo="+$("#sel_modelo").val();			
			
			var url ="excel_equipamiento_eficiencia.php?"+id0+id1+id2+id3+id4+id5+id6+id7;
			//alert("url"+url);
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1100, height=700";
			var winName='_blank';  						
			window.open(url,winName,windowprops); 
			
		}).hover(function(){
			$(this).addClass("ui-state-hover")
		},function(){
			$(this).removeClass("ui-state-hover")
		});

		function Ventana(valor) 
		{ 			
			var url = "ventana_empleado.php?id="+valor;
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1200, height=700";
			winName='_blank';  
			window.open(url,winName,windowprops); 
		} 
		 
	</script> 
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();		

	$par_direccion 		= $_GET["par_direccion"];			
	$par_subdireccion 	= $_GET["par_subdireccion"];				
	$par_departamento 	= $_GET["par_departamento"];				
	$par_equipo 		= $_GET["par_equipo"];				
	$par_status 		= $_GET["par_status"];			
	$par_equipo_com		= $_GET["par_equipo_com"];			
	$par_marca 			= $_GET["par_marca"];			
	$par_modelo 		= $_GET["par_modelo"];				
	
	$tipo_cambio = 13;	
	
	//echo " par_direccion ", $par_direccion;			
	//echo " <br> ";			
	//echo " par_subdireccion ", $par_subdireccion;				
	//echo " <br> ";			
	//echo " par_departamento ", $par_departamento;				
	//echo " <br> ";			
	//echo " par_equipo ", $par_equipo;				
	//echo " <br> ";			
	//echo " par_status ", $par_status;		
	
	# ===========================================================================
	# Busca Empleados 
	# ===========================================================================
	
	if ($par_subdireccion==0) {
	
		$sql = "   SELECT a.id_empleado, tx_registro, tx_empleado, a.tx_indicador, tx_nombre_corto, d.id_subdireccion, tx_subdireccion, e.id_departamento, e.tx_departamento, tx_notas ";
		$sql.= " 	 FROM tbl_empleado a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e ";
		$sql.= "    WHERE a.id_centro_costos  	= b.id_centro_costos ";
		$sql.= "	  AND b.id_direccion 		= $par_direccion and b.tx_indicador='1' ";
		$sql.= "	  AND b.id_direccion 		= c.id_direccion and c.tx_indicador='1'  ";
		$sql.= "  AND c.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
		$sql.= "	  AND b.id_subdireccion 	= d.id_subdireccion and d.tx_indicador='1' ";
		$sql.= "	  AND b.id_departamento 	= e.id_departamento and e.tx_indicador='1'  ";
		if ($par_status=="2"){}
		else $sql.= "	  AND a.tx_indicador 		= $par_status ";
		$sql.= " ORDER BY tx_subdireccion, e.tx_departamento, tx_empleado ";
		
	} else {	
		
		if ($par_departamento==0) {
		
			$sql = "   SELECT a.id_empleado, tx_registro, tx_empleado, a.tx_indicador, tx_nombre_corto, d.id_subdireccion, tx_subdireccion, e.id_departamento, e.tx_departamento, tx_notas ";
			$sql.= " 	 FROM tbl_empleado a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e ";
			$sql.= "    WHERE a.id_centro_costos  	= b.id_centro_costos and  b.tx_indicador='1' ";
			$sql.= "	  AND b.id_direccion 		= $par_direccion ";
			$sql.= "	  AND b.id_direccion 		= c.id_direccion and c.tx_indicador='1'  ";
			$sql.= "  AND c.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	
			$sql.= "	  AND b.id_subdireccion 	= $par_subdireccion ";
			$sql.= "	  AND b.id_subdireccion 	= d.id_subdireccion and d.tx_indicador='1'";
			$sql.= "	  AND b.id_departamento 	= e.id_departamento and e.tx_indicador='1'";
			if ($par_status=="2"){}
			else $sql.= "	  AND a.tx_indicador 		= $par_status ";
			$sql.= " ORDER BY e.tx_departamento, tx_empleado ";
			
		} else {
		
			$sql = "   SELECT a.id_empleado, tx_registro, tx_empleado, a.tx_indicador, tx_nombre_corto, d.id_subdireccion, tx_subdireccion, e.id_departamento, e.tx_departamento, tx_notas ";
			$sql.= " 	 FROM tbl_empleado a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e ";
			$sql.= "    WHERE a.id_centro_costos  	= b.id_centro_costos  and  b.tx_indicador='1'";
			$sql.= "	  AND b.id_direccion 		= $par_direccion ";
			$sql.= "	  AND b.id_direccion 		= c.id_direccion and c.tx_indicador='1' ";
			$sql.= "  AND c.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	
				
			$sql.= "	  AND b.id_subdireccion 	= $par_subdireccion ";
			$sql.= "	  AND b.id_subdireccion 	= d.id_subdireccion  and d.tx_indicador='1'";
			$sql.= "	  AND b.id_departamento 	= e.id_departamento and e.tx_indicador='1'";
			$sql.= "	  AND e.id_departamento 	= $par_departamento ";
			if ($par_status=="2"){}
			else $sql.= "	  AND a.tx_indicador 		= $par_status ";
			$sql.= " ORDER BY e.tx_departamento, tx_empleado ";
		
		}
	}	
	//echo "aaa",$sql;	
	
	$c=0;	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoEmpleados[] = array(			
	  		'id_empleado'		=>$row["id_empleado"],
			'tx_registro'		=>$row["tx_registro"],
			'tx_empleado'		=>$row["tx_empleado"],
			'tx_indicador'		=>$row["tx_indicador"],
			'tx_nombre_corto'	=>$row["tx_nombre_corto"],
			'id_subdireccion'	=>$row["id_subdireccion"],
			'tx_subdireccion'	=>$row["tx_subdireccion"],
			'id_departamento'	=>$row["id_departamento"],
			'tx_departamento'	=>$row["tx_departamento"],
			'tx_notas'			=>$row["tx_notas"]
		);
	} 
	$registros=count($TheCatalogoEmpleados);
	
	# ==============================================================
	# Busco COMPUTO 
	# ==============================================================	
	
	if ($par_equipo_com==0) {	
		$sql1 = "   SELECT tx_equipo ";
		$sql1.= "     FROM tbl_computo ";
		$sql1.= " GROUP BY tx_equipo ";
		$sql1.= " ORDER BY id_computo ";
	} else 	{
		$sql1 = "   SELECT tx_equipo ";
		$sql1.= "     FROM tbl_computo ";
		$sql1.= "    WHERE tx_equipo = '$par_equipo_com' ";
		$sql1.= " GROUP BY tx_equipo ";
		$sql1.= " ORDER BY id_computo ";
	}
	
	//echo " sql1 ",$sql1;
	
	$result1 = mysqli_query($mysql, $sql1);	
	while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
	{	
		$TheCatalogoComputo[] = array(			
	  		"tx_equipo"		=>$row1["tx_equipo"]
		);
	} 
	
	$reg_computo=count($TheCatalogoComputo);	
	$cabecera=$reg_computo+4;
	$titulos=$reg_computo*2;
	$corte_1=$titulos+6;
	$corte_2=$titulos+5;
	
	switch ($reg_computo) 
	{   
		case 0 : $TheWidthCom	='10%'; $TheWidthDet='5%'; break;
		case 1 : $TheWidthCom	='10%'; $TheWidthDet='5%'; break;  
		case 2 : $TheWidthCom	='10%'; $TheWidthDet='5%'; break; 
		case 3 : $TheWidthCom	='10%'; $TheWidthDet='5%'; break; 
		case 4 : $TheWidthCom	='10%'; $TheWidthDet='5%'; break; 
		case 5 : $TheWidthCom	='8%'; 	$TheWidthDet='4%'; break; 
		case 6 : $TheWidthCom	='8%'; 	$TheWidthDet='4%'; break; 
		case 7 : $TheWidthCom	='6%'; 	$TheWidthDet='3%'; break; 
		default : $TheWidthCom	='6%'; 	$TheWidthDet='3%'; break; 
	}	
	?>	
	<div align="right">
	<a id="btnExportar" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Exportar">
    Exportar
    <span class="ui-icon ui-icon-extlink"></span></a>
	</div>
	<?php	
	# ==============================================================
	# Inicia Reporte
	# ==============================================================	
	if ($registros==0)
	{ 		
		echo "<table id='tablaEquipamiento' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		echo "<td class='align-center'>Sin informaci&oacute;n ...</td>";		
		echo "</tr>";					
		echo "</table>";		
	} 
	else
	{			
		echo "<table id='tablaEquipamiento' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		
		# ==============================================================
		# Pinta Cabecera
		# ==============================================================	
		echo "<tr>";							
			for ($a=0; $a<4; $a++)
			{
				switch ($a) {   
					case 0 : $TheWidth="2%"; $TheField="#"; break;
					case 1 : $TheWidth="2%"; $TheField="-"; break;					
					case 2 : $TheWidth="6%"; $TheField="Registro"; break;					
					case 3 : $TheWidth="40%"; $TheField="Nombre"; break;					
			}		
			echo "<td width='$TheWidth' rowspan='3' class='ui-state-highlight align-center'>$TheField</td>";
		}						
		echo "<td colspan='$titulos' class='ui-state-highlight align-center'>COMPUTO</td>";
		echo "<td rowspan='2' colspan='2' class='ui-state-highlight align-center'>TOTALES</td>";				
		echo "</tr>";	
			
		# ==============================================================
		# Pinta Cabecera de Computo
		# ==============================================================	
		echo "<tr>";	
		for ($a=0; $a < count($TheCatalogoComputo); $a++) { 	        			 
			while ($elemento = each($TheCatalogoComputo[$a]))					  		
				$tx_equipo	=$TheCatalogoComputo[$a]['tx_equipo'];
				echo "<td width='$TheWidthCom' colspan='2' class='ui-state-highlight align-center'>$tx_equipo</td>";	
		}					
		echo "</tr>";					
			
		echo "<tr>";
		for ($a=0; $a < count($TheCatalogoComputo); $a++) { 					        			 
			echo "<td width='$TheWidthComDet' class='ui-state-highlight align-center'>Unidades</td>";	
			echo "<td width='$TheWidthComDet' class='ui-state-highlight align-center'>Costo</td>";				
		}	
		echo "<td width='$TheWidthComDet' class='ui-state-highlight align-center'>Unidades</td>";	
		echo "<td width='$TheWidthComDet' class='ui-state-highlight align-center'>Costo</td>";				
		echo "</tr>";	
		
		for ($i=0; $i < count($TheCatalogoEmpleados); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogoEmpleados[$i]))					  		
				$id_empleado	=$TheCatalogoEmpleados[$i]['id_empleado'];		
				$tx_registro	=$TheCatalogoEmpleados[$i]['tx_registro'];		
				$tx_empleado	=$TheCatalogoEmpleados[$i]['tx_empleado'];		
				$tx_indicador	=$TheCatalogoEmpleados[$i]['tx_indicador'];	
				$tx_nombre_corto=$TheCatalogoEmpleados[$i]['tx_nombre_corto'];						
				$id_subdireccion=$TheCatalogoEmpleados[$i]['id_subdireccion'];		
				$tx_subdireccion=$TheCatalogoEmpleados[$i]['tx_subdireccion'];	
				$id_departamento=$TheCatalogoEmpleados[$i]['id_departamento'];		
				$tx_departamento=$TheCatalogoEmpleados[$i]['tx_departamento'];	
				$tx_notas		=$TheCatalogoEmpleados[$i]['tx_notas'];	
				
				$c++;
				
				if ($i==0)	{
					echo "<tr>";							
					echo "<td class='ui-state-default' colspan='$corte_1'><em>$tx_subdireccion</em></td>";						
					echo "</tr>";	
					echo "<tr>";							
					echo "<td class='ui-state-default align-center'>-</td>";	
					echo "<td class='ui-state-default' colspan='$corte_2'><em>$tx_departamento</em></td>";						
					echo "</tr>";	
				}
				
				# =============================================
				# Corte 1 - Subdireccion
				# =============================================				
				if ($i>0)
				{															
					if ($id_subdireccion_tmp==$id_subdireccion) { }					
					else {
						echo "<tr>";		
						echo "<td class='ui-state-default' colspan='$corte_1'><em>$tx_subdireccion</em></td>";					
						echo "</tr>";	
						$id_subdireccion_tmp=$id_subdireccion;
					}
				} else {				
					$id_subdireccion_tmp=$id_subdireccion;
				}	
				
				# =============================================
				# Corte 2 - Departamento
				# =============================================				
				if ($i>0)
				{															
					if ($id_departamento_tmp==$id_departamento) { }					
					else {
						echo "<tr>";	
						echo "<td class='ui-state-default align-center'>-</td>";							
						echo "<td class='ui-state-default' colspan='$corte_2'><em>$tx_departamento</em></td>";						
						echo "</tr>";	
						$id_departamento_tmp=$id_departamento;
					}
				} else {				
					$id_departamento_tmp=$id_departamento;
				}				
				
			# =============================================
			# Pinta el Detalle
			# =============================================				
			echo "<tr>";	
			$in_unidades_renglon	=0;
			$fl_precio_usd_renglon	=0;
							
			for ($b=0; $b<4; $b++)
		 	{
				switch ($b) 
				{   
					case 0: $TheColumn=$c; 
							echo "<td class='align-center' valign='top'>$TheColumn</td>";
					break;						
					case 1:	if ($tx_indicador=="0") $TheColumn="<img src='images/redball.png' alt='$tx_notas'/>"; 
							else $TheColumn="<img src='images/greenball.png'/>";
							echo "<td class='align-center' valign='top'>$TheColumn</td>";
					break;
					case 2:	$TheColumn=$tx_registro;	
							echo "<td class='align-center' valign='top'>$TheColumn</td>";
					break;
					case 3:	$TheColumn="<a href='#' onclick='javascript:Ventana($id_empleado)' title='Presione para ver el Detalle de $tx_empleado ...'>$tx_empleado</a>";	
							echo "<td class='align-left' valign='top'>$TheColumn</td>";
					break;		
				}								
			}	
						
			for ($a=0; $a < count($TheCatalogoComputo); $a++) { 	        			 
				while ($elemento = each($TheCatalogoComputo[$a]))	
					$tx_equipo	="";				  		
					$tx_equipo	=$TheCatalogoComputo[$a]['tx_equipo'];		
										
					$sql2 = "   SELECT count( * ) AS in_computo, SUM( c.fl_precio_usd ) + SUM( c.fl_precio_mxn / $tipo_cambio ) AS total_precio_usd ";
					$sql2.= "     FROM tbl_empleado a, tbl_empleado_computo b, tbl_computo c ";
					$sql2.= "    WHERE a.id_empleado	= $id_empleado ";
					$sql2.= "      AND a.id_empleado 	= b.id_empleado and b.tx_indicador='1' ";
					$sql2.= "      AND b.id_computo 	= c.id_computo and c.tx_indicador='1' ";
					$sql2.= "      AND tx_equipo 		= '$tx_equipo' ";
					$sql2.= " GROUP BY tx_equipo ";
		
					//echo " sql2 ",$sql2;
			
					$result2 = mysqli_query($mysql, $sql2);	
					while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
					{	
						$TheCatalogoComputoDetalle[] = array(			
							'in_computo'		=>$row2["in_computo"],
							'total_precio_usd'	=>$row2["total_precio_usd"]
						);
					} 

					$reg_computo_detalle = mysqli_num_rows($result2);					
					
					if ($reg_computo_detalle==0)
					{						
						$TheColumn="-";
						echo "<td class='align-center' valign='top'>$TheColumn</td>";
						echo "<td class='align-right' valign='top'>$TheColumn</td>";										
					} else  {
						for ($a1=0; $a1 < count($TheCatalogoComputoDetalle); $a1++) { 	        			 
							while ($elemento = each($TheCatalogoComputoDetalle[$a1]))
								$in_computo			=$TheCatalogoComputoDetalle[$a1]['in_computo'];
								$total_precio_usd	=$TheCatalogoComputoDetalle[$a1]['total_precio_usd'];
						}	
						
						if ($in_computo==NULL) $in_computo="-";
						else { 
							$in_unidades_renglon 	= $in_unidades_renglon + $in_computo;
							$in_computo			= number_format($in_computo,0);
						}	
															
						if ($total_precio_usd==NULL) $total_precio_usd="-";						
						else { 
							$fl_precio_usd_renglon=$fl_precio_usd_renglon+$total_precio_usd;						
							$total_precio_usd=number_format($total_precio_usd,0);
						}	
						
						echo "<td class='align-center' valign='top'>$in_computo</td>";
						echo "<td class='align-right' valign='top'>$total_precio_usd</td>";	
					}						
				}	
								
				if ($in_unidades_renglon==NULL) $in_unidades_renglon="-";
				else $in_unidades_renglon=number_format($in_unidades_renglon,0);
				
				if ($fl_precio_usd_renglon==NULL) $fl_precio_usd_renglon=number_format($fl_precio_usd_renglon,0);
				else $fl_precio_usd_renglon=number_format($fl_precio_usd_renglon,0);
				
				echo "<td class='ui-state-default align-center' valign='top'>$in_unidades_renglon</td>";
				echo "<td class='ui-state-default align-right' valign='top'>$fl_precio_usd_renglon</td>";									
			echo "</tr>";	
			}	
			
			# ==============================================================
			# Pinta Cabecera de Totales
			# ==============================================================	
			echo "<tr>";
			echo "<td colspan='4' class='ui-state-highlight align-center'>TOTALES</td>";				
			for ($a=0; $a < count($TheCatalogoComputo); $a++) { 					        			 
				$tx_equipo	="";				  		
				$tx_equipo	=$TheCatalogoComputo[$a]['tx_equipo'];		
							
				# ===========================================================================
				# Busca Empleados 
				# ===========================================================================
				
				if ($par_subdireccion==0) {
				
					$sql = "   SELECT count( * ) AS in_computo_total, SUM( g.fl_precio_usd ) + SUM( g.fl_precio_mxn / $tipo_cambio ) AS total_precio_usd ";
					$sql.= " 	 FROM tbl_empleado a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e, tbl_empleado_computo f, tbl_computo g ";
					$sql.= "    WHERE a.id_centro_costos  	= b.id_centro_costos and b.tx_indicador='1'";
					$sql.= "	  AND b.id_direccion 		= $par_direccion ";
					$sql.= "	  AND b.id_direccion 		= c.id_direccion  and c.tx_indicador='1' ";
					$sql.= "	  AND b.id_subdireccion 	= d.id_subdireccion  and d.tx_indicador='1' ";
					$sql.= "  AND c.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
					
							
					$sql.= "	  AND b.id_departamento 	= e.id_departamento  and e.tx_indicador='1' ";
					$sql.= "	  AND a.id_empleado 		= f.id_empleado  and f.tx_indicador='1' ";
					$sql.= "      AND f.id_computo 			= g.id_computo  and g.tx_indicador='1' ";
					$sql.= "      AND g.tx_equipo 			= '$tx_equipo' ";
					if ($par_status=="2"){}
					else $sql.= "	  AND a.tx_indicador 	= $par_status ";
					$sql.= " ORDER BY c.id_direccion  ";
					
				} else {	
					
					if ($par_departamento==0) {
					
						$sql = "   SELECT count( * ) AS in_computo_total, SUM( g.fl_precio_usd ) + SUM( g.fl_precio_mxn / $tipo_cambio ) AS total_precio_usd ";
						$sql.= " 	 FROM tbl_empleado a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e, tbl_empleado_computo f, tbl_computo g ";
						$sql.= "    WHERE a.id_centro_costos  	= b.id_centro_costos and b.tx_indicador='1' ";
						$sql.= "	 AND b.id_direccion 		= $par_direccion ";
						$sql.= "	 AND b.id_direccion 		= c.id_direccion  and c.tx_indicador='1' ";
						$sql.= "  	 AND c.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where  DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
				
						$sql.= "	  AND b.id_subdireccion 	= $par_subdireccion ";
						$sql.= "	  AND b.id_subdireccion 	= d.id_subdireccion  and d.tx_indicador='1' ";
						$sql.= "	  AND b.id_departamento 	= e.id_departamento  and e.tx_indicador='1' ";
						$sql.= "	  AND a.id_empleado 		= f.id_empleado  and f.tx_indicador='1' ";
						$sql.= "      AND f.id_computo 			= g.id_computo  and g.tx_indicador='1' ";
						$sql.= "      AND g.tx_equipo 			= '$tx_equipo' ";
						if ($par_status=="2"){}
						else $sql.= "	  AND a.tx_indicador 	= $par_status ";
						$sql.= " ORDER BY c.id_direccion  ";
						
					} else {
					
						$sql = "   SELECT count( * ) AS in_computo_total, SUM( g.fl_precio_usd ) + SUM( g.fl_precio_mxn / $tipo_cambio ) AS total_precio_usd ";
						$sql.= " 	 FROM tbl_empleado a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e, tbl_empleado_computo f, tbl_computo g ";
						$sql.= "    WHERE a.id_centro_costos  	= b.id_centro_costos  and b.tx_indicador='1' ";
						$sql.= "	  AND b.id_direccion 		= $par_direccion ";
						$sql.= "	  AND b.id_direccion 		= c.id_direccion  and c.tx_indicador='1' ";
					    $sql.= "  	 AND c.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and   id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
				
						$sql.= "	  AND b.id_subdireccion 	= $par_subdireccion ";
						$sql.= "	  AND b.id_subdireccion 	= d.id_subdireccion  and d.tx_indicador='1' ";
						$sql.= "	  AND b.id_departamento 	= e.id_departamento ";
						$sql.= "	  AND e.id_departamento 	= $par_departamento  and e.tx_indicador='1' ";
						$sql.= "	  AND a.id_empleado 		= f.id_empleado  and f.tx_indicador='1' ";
						$sql.= "      AND f.id_computo 			= g.id_computo  and g.tx_indicador='1' ";
						$sql.= "      AND g.tx_equipo 			= '$tx_equipo' ";
						if ($par_status=="2"){}
						else $sql.= "	  AND a.tx_indicador 	= $par_status ";
						$sql.= " ORDER BY c.id_direccion ";
					
					}
				}	
					
					//echo "aaa",$sql;	
					
					$result = mysqli_query($mysql, $sql);	
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{	
						$TheCatalogoTotal[] = array(			
							'in_computo_total'	=>$row["in_computo_total"],
							'total_precio_usd'	=>$row["total_precio_usd"]
						);
					} 
					
					$registros=count($TheCatalogoTotal);
					
					for ($i=0; $i < count($TheCatalogoTotal); $i++)	{ 	        			 
					while ($elemento = each($TheCatalogoTotal[$i]))					  		
						$in_computo_total	=$TheCatalogoTotal[$i]['in_computo_total'];		
						$total_precio_usd	=$TheCatalogoTotal[$i]['total_precio_usd'];		
					}

					if ($in_computo_total==NULL) $in_computo_total="-";						
					else {
						$in_computo_total_renglon=$in_computo_total_renglon + $in_computo_total;						
						$in_computo_total=number_format($in_computo_total,0);						
					}	
					
					if ($total_precio_usd==NULL) $total_precio_usd="-";						
					else {
						$total_precio_usd_renglon=$total_precio_usd_renglon + $total_precio_usd;						
						$total_precio_usd=number_format($total_precio_usd,0);						
					}	
					
					echo "<td width='$TheWidthComDet' class='ui-state-highlight align-center'>$in_computo_total</td>";	
					echo "<td width='$TheWidthComDet' class='ui-state-highlight align-right'>$total_precio_usd</td>";		
					
				// *********************************************************************************************************************************
				
			}	
			
			if ($in_computo_total_renglon==NULL) $in_computo_total_renglon="-";
			else $in_computo_total_renglon=number_format($in_computo_total_renglon,0);
				
			if ($total_precio_usd_renglon==NULL) $total_precio_usd_renglon=number_format($total_precio_usd_renglon,0);
			else $total_precio_usd_renglon=number_format($total_precio_usd_renglon,0);			
				
			echo "<td class='ui-state-highlight align-center'>$in_computo_total_renglon</td>";
			echo "<td class='ui-state-highlight align-right'>$total_precio_usd_renglon</td>";
			echo "</tr>";	
			
			# ==============================================================
			# Pinta Cabecera
			# ==============================================================		
			echo "<tr>";			
			for ($a=0; $a<4; $a++) {
				switch ($a) {   
					case 0 : $TheWidth	="2%"; $TheField	="#"; break;
					case 1 : $TheWidth	="2%"; $TheField	="-"; break;					
					case 2 : $TheWidth	="6%"; $TheField	="Registro"; break;					
					case 3 : $TheWidth	="20%"; $TheField	="Nombre"; break;					
				}		
				echo "<td width='$TheWidth' rowspan='3' class='ui-state-highlight align-center'>$TheField</td>";
			}	
				
			for ($a=0; $a < count($TheCatalogoComputo); $a++) { 					        			 
				echo "<td width='$TheWidthComDet' class='ui-state-highlight align-center'>Unidades</td>";	
				echo "<td width='$TheWidthComDet' class='ui-state-highlight align-center'>Costo</td>";				
			}	
			
			echo "<td width='$TheWidthComDet' class='ui-state-highlight align-center'>Unidades</td>";	
			echo "<td width='$TheWidthComDet' class='ui-state-highlight align-center'>Costo</td>";							
			echo "</tr>";	
			
			# ==============================================================
			# Pinta Cabecera de Computo
			# ==============================================================	
			echo "<tr>";				
			for ($a=0; $a < count($TheCatalogoComputo); $a++) { 	        			 
				while ($elemento = each($TheCatalogoComputo[$a]))	
					$tx_equipo	="";				  		
					$tx_equipo	=$TheCatalogoComputo[$a]['tx_equipo'];
					echo "<td width='$TheWidthCom' colspan='2' class='ui-state-highlight align-center'>$tx_equipo</td>";	
			}			
			echo "<td rowspan='2' colspan='2' class='ui-state-highlight align-center'>TOTALES</td>";				
			echo "</tr>";	
								
			echo "<tr>";
				echo "<td colspan='$titulos' class='ui-state-highlight align-center'>COMPUTO</td>";							
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan='$titulos' align='left'>";	
			echo "<ul type='square'> ";
			echo "<li>Unidades calculada en base al Equipamiento.</em></li> ";
			echo "<li>Monto calculado en base al Equipamiento.</li> ";
			echo "<li>Monto en USD (D&oacute;lares Americano).</li>";
			echo "<li>Tipo de cambio $13.50 pesos por d&oacute;lar (Fuente: Control Econ&oacute;mico).</li>";
			echo "</ul>";      
			echo "</td>";
			echo "</tr>";
			
		echo "</table>";
	}
	
	$valBita="par_direccion=$par_direccion "; 				
	$valBita.="par_subdireccion=$par_subdireccion "; 					
	$valBita.="par_departamento=$par_departamento "; 					
	$valBita.="par_equipo=$par_equipo "; 						
	$valBita.="par_status=$par_status "; 				
	$valBita.="par_equipo_com=$par_equipo_com ";					
	$valBita.="par_marca=$par_marca "; 						
	$valBita.="par_modelo=$par_modelo "; 					
	
	
	
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_EMPLEADO_COMPUTO" , "$id_login" ,   $valBita ,"" ,"inf_equipamiento_matriz.php");
	 //<\BITACORA>
	mysqli_close($mysql);
} 
else 
{
	echo "Sessi&oacute;n Invalida";
}	
?>      