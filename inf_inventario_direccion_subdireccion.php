<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION["sess_user"]))
{
	$id_login = $_SESSION['sess_iduser']
?>
	<script type="text/javascript">	
	
		function btnInformesSubdireccion(valor) 
		{
			var p1= parseInt(valor); 			
			var id="id_subdireccion="+p1;
			var id1="&fl_tipo_cambio="+$("#cap_tc").val();
			//alert ("Entre 1"+id);	
			loadHtmlAjax(true, $("#verInventarioDireccionN2"), "inf_inventario_direccion_subdireccion_usuario.php?"+id+id1); 
		}		
		 
		$("#tablaInventarioSubdireccion").find("tr").hover(		 
         	function() { $(this).addClass("ui-state-hover"); },
         	function() { $(this).removeClass("ui-state-hover"); }
        );		 
		 
	</script> 
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");
	$mysql=conexion_db();	
	
	$id_direccion	= $_GET['id_direccion'];	
	$fl_tipo_cambio	= $_GET['fl_tipo_cambio'];		
	$tx_moneda		= $_GET['tx_moneda'];		
	
	if ($id_direccion==0) $dispatch="insert";
	else if (is_null($id_direccion)) { $dispatch="insert"; $id_direccion=0; }
	else $dispatch="save";
	
	$sql = "   SELECT tx_nombre ";
	$sql.= "	 FROM tbl_direccion ";
	$sql.= "    WHERE id_direccion = $id_direccion  ";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeDireccion_T[] = array(
			"tx_direccion"	=>$row["tx_nombre"]
		);
	} 	
	
	for ($i=0; $i < count($TheInformeDireccion_T); $i++)
	{ 	        			 
		while ($elemento = each($TheInformeDireccion_T[$i]))				
			$tx_direccion	=$TheInformeDireccion_T[$i]["tx_direccion"];
	}
		
	if ($dispatch=="save") 
	{							
		if ($tx_moneda=="USD") $sql = " SELECT a.id_subdireccion, a.tx_subdireccion, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
		else $sql = " SELECT a.id_subdireccion, a.tx_subdireccion, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";				
		$sql.= "	 FROM tbl_subdireccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e ";
		$sql.= "    WHERE a.id_subdireccion = b.id_subdireccion and a.tx_indicador='1'  ";
		$sql.= "      AND a.id_direccion 		= $id_direccion ";
		$sql.= "      AND b.id_centro_costos 	= c.id_centro_costos and b.tx_indicador='1' ";
		$sql.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1' ";
		$sql.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1'";
		$sql.= " GROUP BY a.id_subdireccion ";
		$sql.= " ORDER BY total_precio_usd DESC , total_licencia ";
	} 
	else if ($dispatch=="insert") 
	{				
		if ($tx_moneda=="USD") $sql = " SELECT a.id_subdireccion, a.tx_subdireccion, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
		else $sql = " SELECT a.id_subdireccion, a.tx_subdireccion, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";	
		$sql.= "	 FROM tbl_subdireccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e ";
		$sql.= "    WHERE a.id_subdireccion = b.id_subdireccion and a.tx_indicador='1' ";
		$sql.= "      AND a.id_direccion 		= $id_direccion ";
		$sql.= "      AND b.id_centro_costos 	= c.id_centro_costos  and b.tx_indicador='1'  ";
		$sql.= "      AND c.id_empleado 		= d.id_empleado  and d.tx_indicador='1' ";
		$sql.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1' ";
		$sql.= " GROUP BY a.id_subdireccion ";
		$sql.= " ORDER BY total_precio_usd DESC, total_licencia ";		
	}

	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeSubdireccion[] = array(
			'id_subdireccion'	=>$row["id_subdireccion"],
			'tx_subdireccion'	=>$row["tx_subdireccion"],
			'total_licencia'	=>$row["total_licencia"],
	  		'total_precio_usd'	=>$row["total_precio_usd"]
		);
	} 	
	
	$registros=count($TheInformeSubdireccion);	
	
	if ($registros==0) {
		echo "<table id='tablaInventarioSubdireccion' align='center' width='100%'>";
		echo "<br>";
		echo "<tr>";
		echo "<td class='align-center'><em><b>Sin Informaci&oacute;n Encontrada ...</b></em></td>";
		echo "</tr>";	
		echo "<br>";		
		echo "</table>";	
	 } else {
		echo "<br>";
		echo "<div class='ui-widget-header align-center'>INVENTARIO - $tx_direccion</div>";	
		echo "<table id='tablaInventarioSubdireccion' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		echo "<td width='3%' rowspan='3' class='ui-state-highlight align-center'>#</td>";							 
		echo "<td width='23%' rowspan='3' class='ui-state-highlight align-center'>DIRECCION</td>";					
		echo "<td width='18%' colspan='3' class='ui-state-highlight align-center'>BLOOMBERG</td>";	
		echo "<td width='18%' colspan='3' class='ui-state-highlight align-center'>REUTERS</td>";						 
		echo "<td width='18%' colspan='3' class='ui-state-highlight align-center'>OTROS</td>";	
		echo "<td width='21%' colspan='3' class='ui-state-highlight align-center'>TOTAL</td>";		
		echo "</tr>";	
		echo "<tr>";
		echo "<td width='5%' rowspan='2' class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td width='12%' colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		echo "<td width='5%' rowspan='2' class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td width='12%' colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		echo "<td width='5%' rowspan='2' class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td width='12%' colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		echo "<td width='5%' rowspan='2' class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td width='15%' colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		echo "</tr>";	
		echo "<tr>";		
		echo "<td width='6%' class='ui-state-highlight align-center'>Mensual</td>";
		echo "<td width='6%' class='ui-state-highlight align-center'>Anual</td>";		
		echo "<td width='6%' class='ui-state-highlight align-center'>Mensual</td>";
		echo "<td width='6%' class='ui-state-highlight align-center'>Anual</td>";		
		echo "<td width='6%' class='ui-state-highlight align-center'>Mensual</td>";
		echo "<td width='6%' class='ui-state-highlight align-center'>Anual</td>";	
		echo "<td width='6%' class='ui-state-highlight align-center'>Mensual</td>";
		echo "<td width='9%' class='ui-state-highlight align-center'>Anual</td>";
		echo "</tr>";	
		
		$c=0;
		$in_total_licencias=0;	
		$fl_total_precio_usd=0;	
		$in_total_licencias_1=0;
		$fl_total_precio_usd_1=0;	
		$in_total_licencias_2=0;
		$fl_total_precio_usd_2=0;	
		$in_total_licencias_3=0;
		$fl_total_precio_usd_3=0;	
		
		for ($i=0; $i < count($TheInformeSubdireccion); $i++)	{ 	        			 
			while ($elemento = each($TheInformeSubdireccion[$i]))				
				$id_subdireccion	=$TheInformeSubdireccion[$i]['id_subdireccion'];	  		
				$tx_subdireccion	=$TheInformeSubdireccion[$i]['tx_subdireccion'];
				$total_licencia		=$TheInformeSubdireccion[$i]['total_licencia'];
				$total_precio_usd	=$TheInformeSubdireccion[$i]['total_precio_usd'];
				
				$in_total_licencias	=$in_total_licencias+$total_licencia;
				$fl_total_precio_usd=$fl_total_precio_usd+$total_precio_usd;
				
				$c++;
				
				# ============================					
				# Busco Licencias de BLOOMBERG
				# ============================
				
				$tx_proveedor1 = "BLOOMBERG";
				
				if ($tx_moneda=="USD") $sql1 = " SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn/$fl_tipo_cambio) AS total_precio_usd ";
				else $sql1 = " SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";				
				$sql1.= "	 FROM tbl_subdireccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
				$sql1.= "    WHERE a.id_subdireccion 	= b.id_subdireccion and a.tx_indicador='1' ";
				$sql1.= "      AND a.id_subdireccion 	= $id_subdireccion ";
				$sql1.= "      AND b.id_centro_costos 	= c.id_centro_costos  and b.tx_indicador='1'  ";
				$sql1.= "      AND c.id_empleado 		= d.id_empleado  and d.tx_indicador='1' ";
				$sql1.= "      AND d.id_producto 		= e.id_producto  and e.tx_indicador='1' ";
				$sql1.= "      AND e.id_proveedor 		= f.id_proveedor  and f.tx_indicador='1'  ";
				$sql1.= "      AND f.tx_proveedor_corto = '$tx_proveedor1' ";
				$sql1.= " GROUP BY a.id_subdireccion ";
				$sql1.= " ORDER BY total_precio_usd DESC , total_licencia ";
				
				//echo "sql",$sql1;	
				
				$result1 = mysqli_query($mysql, $sql1);	
				while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
				{	
					$TheInformeSubdireccion_1[] = array(
						'total_licencia_1'		=>$row1["total_licencia"],
						'total_precio_usd_1'	=>$row1["total_precio_usd"]
					);
				} 
				
				$num_result1=mysqli_num_rows($result1);	
				
				for ($j=0; $j < count($TheInformeSubdireccion_1); $j++)	{ 	        			 
					while ($elemento = each($TheInformeSubdireccion_1[$j]))				
						$total_licencia_1	=$TheInformeSubdireccion_1[$j]['total_licencia_1'];	  		
						$total_precio_usd_1	=$TheInformeSubdireccion_1[$j]['total_precio_usd_1'];	
				}
				
				if ($num_result1==0)
				{
					$total_licencia_1=0;
					$total_precio_usd_1=0;
				}
				
				$in_total_licencias_1	=$in_total_licencias_1 +$total_licencia_1;
				$fl_total_precio_usd_1	=$fl_total_precio_usd_1+$total_precio_usd_1;
				$total_precio_usd_1_a   =$total_precio_usd_1 * 12;				
				
				# ============================					
				# Busco Licencias de REUTERS
				# ============================
				
				$tx_proveedor2 = "REUTERS";
				
				if ($tx_moneda=="USD") $sql2 = " SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn/$fl_tipo_cambio) AS total_precio_usd ";
				else $sql2 = " SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";	
				$sql2.= "	 FROM tbl_subdireccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
				$sql2.= "    WHERE a.id_subdireccion 	= b.id_subdireccion  and a.tx_indicador='1'  ";
				$sql2.= "      AND a.id_subdireccion 	= $id_subdireccion ";
				$sql2.= "      AND b.id_centro_costos 	= c.id_centro_costos  and b.tx_indicador='1' ";
				$sql2.= "      AND c.id_empleado 		= d.id_empleado  and d.tx_indicador='1' ";
				$sql2.= "      AND d.id_producto 		= e.id_producto  and e.tx_indicador='1'  ";
				$sql2.= "      AND e.id_proveedor 		= f.id_proveedor  and f.tx_indicador='1' ";
				$sql2.= "      AND f.tx_proveedor_corto = '$tx_proveedor2' ";
				$sql2.= " GROUP BY a.id_subdireccion ";
				
				//echo "<br>";
				//echo "sql 2 ",$sql2;	
								
				$result2 = mysqli_query($mysql, $sql2);						
					
				while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
				{	
					$TheInformeSubdireccion_2[] = array(
						'total_licencia_2'		=>$row2["total_licencia"],
						'total_precio_usd_2'	=>$row2["total_precio_usd"]
					);
				} 
				
				$num_result2=mysqli_num_rows($result2);	
				
				for ($k=0; $k < count($TheInformeSubdireccion_2); $k++)	{ 	        			 
					while ($elemento = each($TheInformeSubdireccion_2[$k]))				
						$total_licencia_2	=$TheInformeSubdireccion_2[$k]['total_licencia_2'];	  		
						$total_precio_usd_2	=$TheInformeSubdireccion_2[$k]['total_precio_usd_2'];	
				}
				
				/*echo "<br>";
				echo "registro 2 ",$num_result2;
				echo "<br>";
				echo "Licencia 2 ",$total_licencia_2;
				echo "<br>";
				echo "Precio 2 ",$total_precio_usd_2; */
				
				if ($num_result2==0)
				{
					$total_licencia_2=0;
					$total_precio_usd_2=0;
				}
				
				$in_total_licencias_2	=$in_total_licencias_2 +$total_licencia_2;
				$fl_total_precio_usd_2	=$fl_total_precio_usd_2+$total_precio_usd_2;
				$total_precio_usd_2_a   =$total_precio_usd_2 * 12;
				
			
				# ============================					
				# Busco Licencias de OTROS
				# ============================
				
				$tx_proveedor3 = "BLOOMBERG";
				$tx_proveedor4 = "REUTERS";

				if ($tx_moneda=="USD") $sql3 = " SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn/$fl_tipo_cambio) AS total_precio_usd ";
				else $sql3 = " SELECT SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";	
				$sql3.= "	 FROM tbl_subdireccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
				$sql3.= "    WHERE a.id_subdireccion 	= b.id_subdireccion  and a.tx_indicador='1'  ";
				$sql3.= "      AND a.id_subdireccion 	= $id_subdireccion ";
				$sql3.= "      AND b.id_centro_costos 	= c.id_centro_costos  and b.tx_indicador='1'  ";
				$sql3.= "      AND c.id_empleado 		= d.id_empleado  and d.tx_indicador='1'  ";
				$sql3.= "      AND d.id_producto 		= e.id_producto  and e.tx_indicador='1'  ";
				$sql3.= "      AND e.id_proveedor 		= f.id_proveedor  and f.tx_indicador='1'  ";
				$sql3.= "      AND f.tx_proveedor_corto NOT IN ('$tx_proveedor3','$tx_proveedor4') ";
				$sql3.= " GROUP BY a.id_subdireccion ";
				
				//echo "<br>";
				//echo "sql 3 ",$sql3;	
								
				$result3 = mysqli_query($mysql, $sql3);						
					
				while ($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC))
				{	
					$TheInformeSubdireccion_3[] = array(
						'total_licencia_3'		=>$row3["total_licencia"],
						'total_precio_usd_3'	=>$row3["total_precio_usd"]
					);
				} 
				
				$num_result3=mysqli_num_rows($result3);	
				
				for ($l=0; $l < count($TheInformeSubdireccion_3); $l++)	{ 	        			 
					while ($elemento = each($TheInformeSubdireccion_3[$l]))				
						$total_licencia_3	=$TheInformeSubdireccion_3[$l]['total_licencia_3'];	  		
						$total_precio_usd_3	=$TheInformeSubdireccion_3[$l]['total_precio_usd_3'];	
				}						
				
				if ($num_result3==0)
				{
					$total_licencia_3=0;
					$total_precio_usd_3=0;
				}
				
				$in_total_licencias_3	=$in_total_licencias_3 +$total_licencia_3;
				$fl_total_precio_usd_3	=$fl_total_precio_usd_3+$total_precio_usd_3;
				$total_precio_usd_3_a   = $total_precio_usd_3 * 12;
				
				# ============================	
							
				echo "<tr>";			
				for ($a=0; $a<14; $a++)
					{
						switch ($a) 
						{   
							case 0: $TheColumn=$c; 									
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;						
							case 1: $TheColumn = "<a href='#' onclick='javascript:btnInformesSubdireccion($id_subdireccion)' title='Presione para ver el detalle de $tx_subdireccion ...'>$tx_subdireccion</a>";
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 2: $TheColumn = number_format($total_licencia_1,0); 
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;																												
							case 3: $TheColumn=number_format($total_precio_usd_1,0); 
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;	
							case 4: if ($total_precio_usd_1_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_1_a,0); 
									echo "<td class='ui-state-verde align-right' valign='top'>$TheColumn</td>";
							break;																													
							case 5: $TheColumn =  number_format($total_licencia_2,0); 
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;																												
							case 6: $TheColumn=number_format($total_precio_usd_2,0); 
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;	
							case 7: if ($total_precio_usd_2_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_2_a,0); 
									echo "<td class='ui-state-verde align-right' valign='top'>$TheColumn</td>";
							break;																											
							case 8: $TheColumn = number_format($total_licencia_3,0); 
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;																												
							case 9: $TheColumn=number_format($total_precio_usd_3,0); 
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;
							case 10: if ($total_precio_usd_3_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_3_a,0); 
									echo "<td class='ui-state-verde align-right' valign='top'>$TheColumn</td>";
							break;	
							case 11: $TheColumn = $total_licencia;
								echo "<td class='ui-state-default align-center' valign='top'>$TheColumn</td>";
							break;												
							case 12: $TheColumn=number_format($total_precio_usd,0); 
									echo "<td class='ui-state-default align-right' valign='top'>$TheColumn</td>";
							break;	
							case 13: $total_precio_usd_a = $total_precio_usd * 12;
									if ($total_precio_usd_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_a,0); 
									echo "<td class='ui-state-verde-total align-right' valign='top'>$TheColumn</td>";
							break;										
						}							
					}				
					echo "</tr>";					
		}	
		echo "<tr>";								  
		for ($a=0; $a<14; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='TOTALES';
					echo "<td colspan='2' class='ui-state-highlight align-right'>$TheField</td>";						 
				break;
				case 1 : $TheField=number_format($in_total_licencias_1,0); 	
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 2 : $TheField=number_format($fl_total_precio_usd_1,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 3 : $fl_total_precio_usd_1_a = $fl_total_precio_usd_1 * 12;
						 if ($fl_total_precio_usd_1_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_1_a,0); 
						 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 4 : $TheField=number_format($in_total_licencias_2,0); 	
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 5 : $TheField=number_format($fl_total_precio_usd_2,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 6 : $fl_total_precio_usd_2_a = $fl_total_precio_usd_2 * 12;
						 if ($fl_total_precio_usd_2_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_2_a,0); 
						 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 7 : $TheField=number_format($in_total_licencias_3,0); 	
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 8 : $TheField=number_format($fl_total_precio_usd_3,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 9 : $fl_total_precio_usd_3_a = $fl_total_precio_usd_3 * 12;
						 if ($fl_total_precio_usd_3_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_3_a,0); 
					     echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 10 : $TheField=number_format($in_total_licencias,0); 	
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 11 : $TheField=number_format($fl_total_precio_usd,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;		
				case 12 :$fl_total_precio_usd_a = $fl_total_precio_usd * 12;
						 if ($fl_total_precio_usd_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_a,0); 
						 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
			}							
		}	
		echo "<tr>";
		echo "<td rowspan='3' class='ui-state-highlight align-center'>#</td>";	
		echo "<td rowspan='3' class='ui-state-highlight align-center'>DIRECCION</td>";	
		echo "<td rowspan='2' class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td class='ui-state-highlight align-center'>Mensual</td>";
		echo "<td class='ui-state-highlight align-center'>Anual</td>";
		echo "<td rowspan='2' class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td class='ui-state-highlight align-center'>Mensual</td>";
		echo "<td class='ui-state-highlight align-center'>Anual</td>";
		echo "<td rowspan='2' class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td class='ui-state-highlight align-center'>Mensual</td>";
		echo "<td class='ui-state-highlight align-center'>Anual</td>";
		echo "<td rowspan='2' class='ui-state-highlight align-center'>Licencias</td>";	
		echo "<td class='ui-state-highlight align-center'>Mensual</td>";
		echo "<td class='ui-state-highlight align-center'>Anual</td>";
		echo "</tr>";			
		echo "<tr>";
		echo "<td colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		echo "<td colspan='2' class='ui-state-highlight align-center'>Monto</td>";		
		echo "<td colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		echo "<td colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		echo "</tr>";			
		echo "<tr>";
		echo "<td colspan='3' class='ui-state-highlight align-center'>BLOOMBERG</td>";	
		echo "<td colspan='3' class='ui-state-highlight align-center'>REUTERS</td>";						 
		echo "<td colspan='3' class='ui-state-highlight align-center'>OTROS</td>";	
		echo "<td colspan='3' class='ui-state-highlight align-center'>TOTAL</td>";	
		echo "</tr>";			
		echo "<tr>";
		echo "<td colspan='14' align='left'>";	
        echo "<ul type='square'> ";
		echo "<li>Licencias calculadas en base al Inventario.</em></li> ";
        echo "<li>Monto calculado en base al Inventario.</li> ";
        echo "<li>Monto en  $tx_moneda.</li>";
        echo "<li>Tipo de cambio $fl_tipo_cambio pesos por d&oacute;lar.</li>";
        echo "</ul>";      
        echo "</td>";
		echo "</tr>";
	echo "</table>";
	}
	
	$id_direccion	= $_GET['id_direccion'];	
	$fl_tipo_cambio	= $_GET['fl_tipo_cambio'];		
	$tx_moneda		= $_GET['tx_moneda'];		
	
	
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_LICENCIA" , "$id_login" ,   "id_direccion=$id_direccion fl_tipo_cambio=$fl_tipo_cambio tx_moneda=$tx_moneda" ,"" ,"inf_inventario_direccion_subdireccion.php");
	 //<\BITACORA>
	 
	 
	mysqli_close($mysql);

} 
else 
{
	echo "Sessi&oacute;n Invalida";
}	
?>   