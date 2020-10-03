<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	$id_login = $_SESSION['sess_iduser']
?>
	<script type="text/javascript">				
		 
		$("#tablaInventarioDepartamento").find("tr").hover(		 
         	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
        );		 
		 
	</script> 
<?
	include("includes/funciones.php"); 
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();	
	
	$id_subdireccion= $_GET['id_subdireccion'];	
	$fl_tipo_cambio = $_GET['fl_tipo_cambio'];
	
	if ($id_subdireccion==0) $dispatch="insert";
	else if (is_null($id_subdireccion)) { $dispatch="insert"; $id_subdireccion=0; }
	else $dispatch="save";
	
	
	$sql = "   SELECT tx_subdireccion ";
	$sql.= "	 FROM tbl_subdireccion ";
	$sql.= "    WHERE id_subdireccion = $id_subdireccion  ";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeSubdireccion_T[] = array(
			'tx_subdireccion'	=>$row["tx_subdireccion"]
		);
	} 	
	
	for ($i=0; $i < count($TheInformeSubdireccion_T); $i++)	{ 	        			 
			while ($elemento = each($TheInformeSubdireccion_T[$i]))				
				$tx_subdireccion	=$TheInformeSubdireccion_T[$i]['tx_subdireccion'];
	}
		
	if ($dispatch=="save") 
	{		
	
		//$sql = "   SELECT tx_centro_costos, tx_nombre, tx_subdireccion, d.id_departamento, d.tx_departamento, tx_registro, f.id_empleado, tx_empleado, e.tx_indicador, e.tx_notas, SUM( in_licencia ) AS total_licencia, SUM( fl_precio_usd ) + SUM( fl_precio_mxn /$fl_tipo_cambio ) AS total_precio_usd ";
		$sql = "   SELECT tx_centro_costos, tx_nombre, tx_subdireccion, d.id_departamento, d.tx_departamento, tx_registro, f.id_empleado, tx_empleado, e.tx_indicador, e.tx_notas, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(g.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
		$sql.= "     FROM tbl_centro_costos a, tbl_direccion b, tbl_subdireccion c, tbl_departamento d, tbl_empleado e, tbl_licencia f, tbl_producto g ";
		$sql.= "    WHERE a.id_direccion 		= b.id_direccion  and a.tx_indicador='1'  and b.tx_indicador='1' ";
		$sql.= "      AND a.id_subdireccion 	= c.id_subdireccion  and c.tx_indicador='1' "; 
		$sql.= "      AND a.id_departamento 	= d.id_departamento  and d.tx_indicador='1' ";
		$sql.= "      AND c.id_subdireccion 	= $id_subdireccion ";
		$sql.= "      AND a.id_centro_costos 	= e.id_centro_costos   ";
		$sql.= "      AND e.id_empleado 		= f.id_empleado  and f.tx_indicador='1' ";
		$sql.= "      AND f.id_producto 		= g.id_producto  and g.tx_indicador='1' ";
		$sql.= " GROUP BY f.id_empleado ";
		$sql.= " ORDER BY tx_nombre, tx_subdireccion, d.tx_departamento, tx_empleado ";		

	} else if ($dispatch=="insert")  {
		
		//$sql = "   SELECT tx_centro_costos, tx_nombre, tx_subdireccion, d.id_departamento, d.tx_departamento, tx_registro, f.id_empleado, tx_empleado, e.tx_indicador, e.tx_notas, SUM( in_licencia ) AS total_licencia, SUM( fl_precio_usd ) + SUM( fl_precio_mxn /13 ) AS total_precio_usd ";
		$sql = "   SELECT tx_centro_costos, tx_nombre, tx_subdireccion, d.id_departamento, d.tx_departamento, tx_registro, f.id_empleado, tx_empleado, e.tx_indicador, e.tx_notas, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(g.fl_precio_mxn / $fl_tipo_cambio)  AS total_precio_usd ";		
		$sql.= "     FROM tbl_centro_costos a, tbl_direccion b, tbl_subdireccion c, tbl_departamento d, tbl_empleado e, tbl_licencia f, tbl_producto g ";
		$sql.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1'  and b.tx_indicador='1' ";
		$sql.= "      AND a.id_subdireccion 	= c.id_subdireccion and c.tx_indicador='1' "; 
		$sql.= "      AND a.id_departamento 	= d.id_departamento and d.tx_indicador='1' ";
		$sql.= "      AND c.id_subdireccion 	= $id_subdireccion ";
		$sql.= "      AND a.id_centro_costos 	= e.id_centro_costos ";
		$sql.= "      AND e.id_empleado 		= f.id_empleado and f.tx_indicador='1' ";
		$sql.= "      AND f.id_producto 		= g.id_producto and g.tx_indicador='1' ";
		$sql.= " GROUP BY f.id_empleado ";
		$sql.= " ORDER BY tx_nombre, tx_subdireccion, d.tx_departamento, tx_empleado ";		
		
	}

	//echo "sql",$sql;	
	//echo "<br>";	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeDepartamento[] = array(
			'tx_centro_costos'	=>$row["tx_centro_costos"],
			'tx_nombre'			=>$row["tx_nombre"],
			'tx_subdireccion'	=>$row["tx_subdireccion"],
			'id_departamento'	=>$row["id_departamento"],
			'tx_departamento'	=>$row["tx_departamento"],
			'tx_registro'		=>$row["tx_registro"],
			'id_empleado'		=>$row["id_empleado"],
			'tx_empleado'		=>$row["tx_empleado"],
			'tx_indicador'		=>$row["tx_indicador"],
			'tx_notas'			=>$row["tx_notas"],
			'total_licencia'	=>$row["total_licencia"],
	  		'total_precio_usd'	=>$row["total_precio_usd"]
		);
	} 	
	
	$registros=count($TheInformeDepartamento);	
	
	if ($registros==0) {
		echo "<table id='tablaInventarioDepartamento' align='center' width='100%'>";
		echo "<br>";
		echo "<tr>";
		echo "<td class='align-center'><em><b>Sin Informaci&oacute;n de $tx_subdireccion ...</b></em></td>";
		echo "</tr>";	
		echo "<br>";		
		echo "</table>";	
	 } else {
		echo "<br>";
		echo "<div class='ui-widget-header align-center'>INVENTARIO - $tx_subdireccion</div>";	
		echo "<table id='tablaInventarioDepartamento' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		echo "<td width='3%' rowspan='2' class='ui-state-highlight align-center'>#</td>";							 
		echo "<td width='3%' rowspan='2' class='ui-state-highlight align-center'>-</td>";							 
		echo "<td width='8%' rowspan='2' class='ui-state-highlight align-center'>Registro</td>";					
		echo "<td width='19%' rowspan='2' class='ui-state-highlight align-center'>Nombre</td>";					
		echo "<td width='16%' colspan='2' class='ui-state-highlight align-center'>BLOOMBERG</td>";	
		echo "<td width='16%' colspan='2' class='ui-state-highlight align-center'>REUTERS</td>";						 
		echo "<td width='16%' colspan='2' class='ui-state-highlight align-center'>OTROS</td>";	
		echo "<td width='19%' colspan='2' class='ui-state-highlight align-center'>TOTAL</td>";						 
	//  echo "<td width='3%' rowspan='2' class='ui-state-highlight align-center'>Gr&aacute;fica</td>";						 
		echo "</tr>";	
		echo "<tr>";
		echo "<td width='7%' class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td width='9%' class='ui-state-highlight align-center'>Monto</td>";
		echo "<td width='7%' class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td width='9%' class='ui-state-highlight align-center'>Monto</td>";
		echo "<td width='7%' class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td width='9%' class='ui-state-highlight align-center'>Monto</td>";
		echo "<td width='7%' class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td width='11%' class='ui-state-highlight align-center'>Monto</td>";
		echo "</tr>";	
		echo "<tr>";
		
		$c=0;
		$in_total_licencias=0;	
		$fl_total_precio_usd=0;	
		$in_total_licencias_1=0;
		$fl_total_precio_usd_1=0;	
		$in_total_licencias_2=0;
		$fl_total_precio_usd_2=0;	
		$in_total_licencias_3=0;
		$fl_total_precio_usd_3=0;	
		
		for ($i=0; $i < count($TheInformeDepartamento); $i++)	{ 	        			 
			while ($elemento = each($TheInformeDepartamento[$i]))				
				$tx_centro_costos	=$TheInformeDepartamento[$i]['tx_centro_costos'];	  		
				$tx_nombre			=$TheInformeDepartamento[$i]['tx_nombre'];	  		
				$tx_subdireccion	=$TheInformeDepartamento[$i]['tx_subdireccion'];
				$id_departamento	=$TheInformeDepartamento[$i]['id_departamento'];
				$tx_departamento	=$TheInformeDepartamento[$i]['tx_departamento'];
				$tx_registro		=$TheInformeDepartamento[$i]['tx_registro'];
				$id_empleado		=$TheInformeDepartamento[$i]['id_empleado'];
				$tx_empleado		=$TheInformeDepartamento[$i]['tx_empleado'];
				$tx_indicador		=$TheInformeDepartamento[$i]['tx_indicador'];
				$tx_notas			=$TheInformeDepartamento[$i]['tx_notas'];
				$total_licencia		=$TheInformeDepartamento[$i]['total_licencia'];
				$total_precio_usd	=$TheInformeDepartamento[$i]['total_precio_usd'];
				
				$in_total_licencias	=$in_total_licencias+$total_licencia;
				$fl_total_precio_usd=$fl_total_precio_usd+$total_precio_usd;
				
				$c++;
				
				# ============================					
				# Inicio de los cortes
				# ============================				
				if ($i==0)	{
					echo "<tr>";							
					echo "<td class='ui-state-default' colspan='13'><em>$tx_subdireccion</em></td>";						
					echo "</tr>";	
					echo "<tr>";							
					echo "<td class='ui-state-default align-center'>-</td>";	
					echo "<td class='ui-state-default' colspan='12'><em>$tx_departamento</em></td>";						
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
						echo "<td class='ui-state-default' colspan='13'><em>$tx_subdireccion</em></td>";					
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
						echo "<td class='ui-state-default' colspan='12'><em>$tx_departamento</em></td>";						
						echo "</tr>";	
						$id_departamento_tmp=$id_departamento;
					}
				} else {				
					$id_departamento_tmp=$id_departamento;
				}				
										
				# ============================					
				# Busco Licencias de BLOOMBERG
				# ============================				
				$tx_proveedor1 = "BLOOMBERG";
				
				//$sql1 = "   SELECT SUM( in_licencia ) AS total_licencia, SUM( fl_precio_usd ) + SUM( fl_precio_mxn /13 ) AS total_precio_usd ";
				$sql1 = "   SELECT SUM( in_licencia ) AS total_licencia, SUM( fl_precio ) AS total_precio_usd ";
				$sql1.= "     FROM tbl_centro_costos a, tbl_direccion b, tbl_subdireccion c, tbl_departamento d, tbl_empleado e, tbl_licencia f, tbl_producto g, tbl_proveedor h ";
				$sql1.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1' ";
				$sql1.= "      AND a.id_subdireccion 	= c.id_subdireccion and c.tx_indicador='1' "; 
				$sql1.= "      AND a.id_departamento 	= d.id_departamento and d.tx_indicador='1'";
				$sql1.= "      AND c.id_subdireccion 	= $id_subdireccion ";
				$sql1.= "      AND a.id_centro_costos 	= e.id_centro_costos ";
				$sql1.= "      AND e.id_empleado 		= f.id_empleado and f.tx_indicador='1'";
				$sql1.= "      AND e.id_empleado 		= $id_empleado  ";
				$sql1.= "      AND f.id_producto 		= g.id_producto and g.tx_indicador='1' ";
				$sql1.= "      AND g.id_proveedor 		= h.id_proveedor and h.tx_indicador='1' ";
				$sql1.= "      AND h.tx_proveedor_corto  = '$tx_proveedor1' ";
				$sql1.= " GROUP BY f.id_empleado ";
				$sql1.= " ORDER BY tx_nombre, tx_subdireccion, d.tx_departamento, tx_empleado ";		
								
				//echo "sql",$sql1;	
				//echo "<br>";	
				
				$result1 = mysqli_query($mysql, $sql1);	
				while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
				{	
					$TheInformeDepartamento_1[] = array(
						'total_licencia_1'		=>$row1["total_licencia"],
						'total_precio_usd_1'	=>$row1["total_precio_usd"]
					);
				} 
				
				$num_result1=mysqli_num_rows($result1);	
				
				for ($j=0; $j < count($TheInformeDepartamento_1); $j++)	{ 	        			 
					while ($elemento = each($TheInformeDepartamento_1[$j]))				
						$total_licencia_1	=$TheInformeDepartamento_1[$j]['total_licencia_1'];	  		
						$total_precio_usd_1	=$TheInformeDepartamento_1[$j]['total_precio_usd_1'];	
				}
				
				if ($num_result1==0)
				{
					$total_licencia_1=0;
					$total_precio_usd_1=0;
				}
				
				$in_total_licencias_1	=$in_total_licencias_1 +$total_licencia_1;
				$fl_total_precio_usd_1	=$fl_total_precio_usd_1+$total_precio_usd_1;
				
				# ============================					
				# Busco Licencias de REUTERS
				# ============================				
				$tx_proveedor2 = "REUTERS";
				
				//$sql2 = "   SELECT SUM( in_licencia ) AS total_licencia, SUM( fl_precio_usd ) + SUM( fl_precio_mxn /13 ) AS total_precio_usd ";
				
				$sql2 = "   SELECT SUM( in_licencia ) AS total_licencia, SUM( fl_precio ) AS total_precio_usd ";
				$sql2.= "     FROM tbl_centro_costos a, tbl_direccion b, tbl_subdireccion c, tbl_departamento d, tbl_empleado e, tbl_licencia f, tbl_producto g, tbl_proveedor h ";
				$sql2.= "    WHERE a.id_direccion 		= b.id_direccion  and a.tx_indicador='1' and b.tx_indicador='1' ";
				$sql2.= "      AND a.id_subdireccion 	= c.id_subdireccion  and c.tx_indicador='1'  "; 
				$sql2.= "      AND a.id_departamento 	= d.id_departamento  and d.tx_indicador='1' ";
				$sql2.= "      AND c.id_subdireccion 	= $id_subdireccion ";
				$sql2.= "      AND a.id_centro_costos 	= e.id_centro_costos   ";
				$sql2.= "      AND e.id_empleado 		= f.id_empleado  and f.tx_indicador='1' ";
				$sql2.= "      AND e.id_empleado 		= $id_empleado  ";
				$sql2.= "      AND f.id_producto 		= g.id_producto  and g.tx_indicador='1' ";
				$sql2.= "      AND g.id_proveedor 		= h.id_proveedor  and h.tx_indicador='1' ";
				$sql2.= "      AND h.tx_proveedor_corto = '$tx_proveedor2' ";
				$sql2.= " GROUP BY f.id_empleado ";
				$sql2.= " ORDER BY tx_nombre, tx_subdireccion, d.tx_departamento, tx_empleado ";	
				
				//echo "<br>";
				//echo "sql 2 ",$sql2;	
								
				$result2 = mysqli_query($mysql, $sql2);						
					
				while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
				{	
					$TheInformeDepartamento_2[] = array(
						'total_licencia_2'		=>$row2["total_licencia"],
						'total_precio_usd_2'	=>$row2["total_precio_usd"]
					);
				} 
				
				$num_result2=mysqli_num_rows($result2);	
				
				for ($k=0; $k < count($TheInformeDepartamento_2); $k++)	{ 	        			 
					while ($elemento = each($TheInformeDepartamento_2[$k]))				
						$total_licencia_2	=$TheInformeDepartamento_2[$k]['total_licencia_2'];	  		
						$total_precio_usd_2	=$TheInformeDepartamento_2[$k]['total_precio_usd_2'];	
				}
				
				if ($num_result2==0)
				{
					$total_licencia_2=0;
					$total_precio_usd_2=0;
				}
				
				$in_total_licencias_2	=$in_total_licencias_2 +$total_licencia_2;
				$fl_total_precio_usd_2	=$fl_total_precio_usd_2+$total_precio_usd_2;
				
			
				# ============================					
				# Busco Licencias de OTROS
				# ============================
				
				$tx_proveedor3 = "BLOOMBERG";
				$tx_proveedor4 = "REUTERS";				
				
				//$sql3 = "   SELECT SUM( in_licencia ) AS total_licencia, SUM( fl_precio_usd ) + SUM( fl_precio_mxn /13 ) AS total_precio_usd ";
				
				$sql3 = "   SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(g.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
				$sql3.= "     FROM tbl_centro_costos a, tbl_direccion b, tbl_subdireccion c, tbl_departamento d, tbl_empleado e, tbl_licencia f, tbl_producto g, tbl_proveedor h ";
				$sql3.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1' ";
				$sql3.= "      AND a.id_subdireccion 	= c.id_subdireccion and c.tx_indicador='1' "; 
				$sql3.= "      AND a.id_departamento 	= d.id_departamento and d.tx_indicador='1' ";
				$sql3.= "      AND c.id_subdireccion 	= $id_subdireccion ";
				$sql3.= "      AND a.id_centro_costos 	= e.id_centro_costos ";
				$sql3.= "      AND e.id_empleado 		= f.id_empleado and f.tx_indicador='1' ";
				$sql3.= "      AND e.id_empleado 		= $id_empleado ";
				$sql3.= "      AND f.id_producto 		= g.id_producto  and g.tx_indicador='1' ";
				$sql3.= "      AND g.id_proveedor 		= h.id_proveedor  and h.tx_indicador='1' ";
				$sql3.= "      AND h.tx_proveedor_corto NOT IN ('$tx_proveedor3','$tx_proveedor4') ";
				$sql3.= " GROUP BY f.id_empleado ";
				$sql3.= " ORDER BY tx_nombre, tx_subdireccion, d.tx_departamento, tx_empleado ";	
								
				//echo "<br>";
				//echo "sql 3 ",$sql3;	
								
				$result3 = mysqli_query($mysql, $sql3);						
					
				while ($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC))
				{	
					$TheInformeDepartamento_3[] = array(
						'total_licencia_3'		=>$row3["total_licencia"],
						'total_precio_usd_3'	=>$row3["total_precio_usd"]
					);
				} 
				
				$num_result3=mysqli_num_rows($result3);	
				
				for ($l=0; $l < count($TheInformeDepartamento_3); $l++)	{ 	        			 
					while ($elemento = each($TheInformeDepartamento_3[$l]))				
						$total_licencia_3	=$TheInformeDepartamento_3[$l]['total_licencia_3'];	  		
						$total_precio_usd_3	=$TheInformeDepartamento_3[$l]['total_precio_usd_3'];	
				}						
				
				if ($num_result3==0)
				{
					$total_licencia_3=0;
					$total_precio_usd_3=0;
				}
				
				$in_total_licencias_3	=$in_total_licencias_3 +$total_licencia_3;
				$fl_total_precio_usd_3	=$fl_total_precio_usd_3+$total_precio_usd_3;
				
				# ============================	
							
							
				for ($a=0; $a<12; $a++)
					{
						switch ($a) 
						{   
							case 0: $TheColumn=$c; 									
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;							
							case 1:	if ($tx_indicador=="0") $TheColumn="<img src='images/redball.png' alt='$tx_notas'/>"; 
									else $TheColumn="<img src='images/greenball.png'/>";									
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;
							case 2:	$TheColumn="$tx_registro";	
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;
							case 3:	$TheColumn="<a href='#' onclick='javascript:Ventana($id_empleado)' title='Presione para ver el Detalle de $tx_empleado ...'>$tx_empleado</a>";	
								echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 4: $TheColumn = number_format($total_licencia_1,0); 
									if ($total_licencia_1==0) $TheColumn="-";									
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;																												
							case 5: $TheColumn=number_format($total_precio_usd_1,0); 
									if ($total_precio_usd_1==0) $TheColumn="-";
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;																												
							case 6: $TheColumn =  number_format($total_licencia_2,0); 
									if ($total_licencia_2==0) $TheColumn="-";	
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;																												
							case 7: $TheColumn=number_format($total_precio_usd_2,0); 
									if ($total_precio_usd_2==0) $TheColumn="-";
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;																												
							case 8: $TheColumn = number_format($total_licencia_3,0); 
									if ($total_licencia_3==0) $TheColumn="-";	
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;																												
							case 9: $TheColumn=number_format($total_precio_usd_3,0); 
									if ($total_precio_usd_3==0) $TheColumn="-";
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;
							case 10: $TheColumn = $total_licencia;
								echo "<td class='ui-state-default align-center' valign='top'>$TheColumn</td>";
							break;												
							case 11: $TheColumn=number_format($total_precio_usd,0); 
									echo "<td class='ui-state-default align-right' valign='top'>$TheColumn</td>";
							break;
						//	case 12 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnEditFactura($id_factura)'><span class='ui-icon ui-icon-signal' title='Presione para ver Gr&aacute;fica ...'></span></a>";				
						//		echo "<td class='ui-widget-header' align='center' valign='top'>$TheColumn</td>";
						//	break;								
						}							
					}				
					echo "</tr>";					
		}	
		echo "<tr>";								  
		for ($a=0; $a<12; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='Totales';
					echo "<td colspan='4' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
				break;
				case 3 : $TheField=number_format($in_total_licencias_1,0); 	
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 4 : $TheField=number_format($fl_total_precio_usd_1,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 5 : $TheField=number_format($in_total_licencias_2,0); 	
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField=number_format($fl_total_precio_usd_2,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 7 : $TheField=number_format($in_total_licencias_3,0); 	
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 8 : $TheField=number_format($fl_total_precio_usd_3,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 9 : $TheField=number_format($in_total_licencias,0); 	
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 10 : $TheField=number_format($fl_total_precio_usd,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
			// case 11 : $TheField=""; 
			//		echo "<td class='ui-state-highlight align-center'>&nbsp;</td>";					
			//	break;
			}							
		}	
		echo "</tr>";
		echo "<tr>";
		echo "<td rowspan='2' class='ui-state-highlight align-center'>#</td>";	
		echo "<td rowspan='2' class='ui-state-highlight align-center'>-</td>";	
		echo "<td rowspan='2' class='ui-state-highlight align-center'>Registro</td>";	
		echo "<td rowspan='2' class='ui-state-highlight align-center'>Nombre</td>";	
		echo "<td class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td class='ui-state-highlight align-center'>Monto</td>";
		echo "<td class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td class='ui-state-highlight align-center'>Monto</td>";
		echo "<td class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td class='ui-state-highlight align-center'>Monto</td>";
		echo "<td class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td class='ui-state-highlight align-center'>Monto</td>";
		//echo "<td rowspan='2' class='ui-state-highlight align-center'>Gr&aacute;fica</td>";	
		echo "</tr>";	
		echo "<tr>";
		echo "<td colspan='2' class='ui-state-highlight align-center'>BLOOMBERG</td>";	
		echo "<td colspan='2' class='ui-state-highlight align-center'>REUTERS</td>";						 
		echo "<td colspan='2' class='ui-state-highlight align-center'>OTROS</td>";	
		echo "<td colspan='2' class='ui-state-highlight align-center'>TOTAL</td>";	
		echo "</tr>";	
	echo "</table>";	
	}
	
	
		//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_LICENCIA" , "$id_login" ,   "id_subdireccion=$id_subdireccion fl_tipo_cambio=$fl_tipo_cambio" ,"" ,"inf_inventario_direccion_subdireccion_usuario.php");
	 //<\BITACORA>
	 
	 
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      