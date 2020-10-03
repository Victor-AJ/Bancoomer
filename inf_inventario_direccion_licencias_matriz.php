<?php
session_start();
if 	(isset($_SESSION["sess_user"]))
{
	$id_login = $_SESSION['sess_iduser']
?>
	<script type="text/javascript">			
		 
		 $("#tablaInventarioProveedorN2").find("tr").hover(		 
         	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
         );		 

		function Ventana(valor) { 			
		
			var url = "ventana_empleado.php?id="+valor;
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1100, height=700";
			winName='_blank';  
			//alert ("url"+url);
			window.open(url,winName,windowprops); 
		} 
		
		$(".botonExcel").click(function(event) {
			$("#datos_a_enviar").val( $("<div>").append( $("#tablaInventarioProveedorN2").eq(0).clone()).html());
			$("#FormularioExportacion").submit();
		});
		 
	</script> 
    
    <style type="text/css">
		.botonExcel{cursor:pointer;}
	</style>
    <form action="excel_inventario_eficiencia_e.php" method="post" target="_blank" id="FormularioExportacion">
    <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
    
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();		

	$id_direccion_a = $_GET["id_direccion"];		
	$id_proveedor	= $_GET["id_proveedor"];	
	$id_proveedor_1	= $_GET["id_proveedor_1"];	
	$fl_tipo_cambio	= $_GET["fl_tipo_cambio"];		
	$tx_moneda		= $_GET["tx_moneda"];		
	$in_anio 		= 12;
	
	if ($id_direccion_a==0) $dispatch="insert";
	else if (is_null($id_direccion_a)) { $dispatch="insert"; $id_direccion_a=0; }
	else $dispatch="save";
	
	if (is_null($id_direccion_a)) $id_direccion_a=0; 
	
	if ($id_proveedor == 0)  $tx_proveedor_opcion = 0;
	else if ($id_proveedor == $id_proveedor_1) $tx_proveedor_opcion = 1;
	else $tx_proveedor_opcion = 2;
	
	# ===============================================================================
	# BUSCA NOMBRE DE LA DIRECCION	
	# ===============================================================================
	
	$sqld = "   SELECT tx_nombre_corto ";
	$sqld.= "	 FROM tbl_direccion ";
	$sqld.= "    WHERE id_direccion = $id_direccion_a ";
	
	$resultd = mysqli_query($mysql, $sqld);	
	while ($rowd = mysqli_fetch_array($resultd, MYSQLI_ASSOC))
	{	
		$TheDireccion[] = array(
			'tx_nombre_corto'	=>$rowd["tx_nombre_corto"]
		);
	} 	
	
	for ($k=0; $k < count($TheDireccion); $k++)	{ 	        			 
		while ($elemento = each($TheDireccion[$k]))				
			$tx_nombre_corto = $TheDireccion[$k]['tx_nombre_corto'];	
	}
	
	# ===============================================================================
	# BUSCA NOMBRE DEL PROVEEDOR
	# ===============================================================================
	
	if ($tx_proveedor_opcion == 0) {
	
		$tx_proveedor_corto = "TOTAL";
	
	} else if ($tx_proveedor_opcion == 2) {
	
		$tx_proveedor_corto = "OTROS";
	
	} else {
				
		$sqlp = "   SELECT tx_proveedor_corto ";
		$sqlp.= "	 FROM tbl_proveedor ";
		$sqlp.= "    WHERE id_proveedor = $id_proveedor ";
		
		$resultp = mysqli_query($mysql, $sqlp);	
		while ($rowp = mysqli_fetch_array($resultp, MYSQLI_ASSOC))
		{	
			$TheProveedor[] = array(
				'tx_proveedor_corto'	=>$rowp["tx_proveedor_corto"]
			);
		} 	
		
		for ($k=0; $k < count($TheProveedor); $k++)	{ 	        			 
			while ($elemento = each($TheProveedor[$k]))				
				$tx_proveedor_corto = $TheProveedor[$k]['tx_proveedor_corto'];	
		}
	
	}
		
	//echo "id_proveedor",$id_proveedor;
	//echo "<br>";
	//echo "id_direccion",$id_direccion;
	//echo "<br>";
	//echo "id_cuenta",$id_cuenta;
	//echo "<br>";
		
	// ============================================================
	// BUSCA INFORMACION DEL PROVEEDOR Y PRODUCTOS
	// ============================================================
	
	if ($id_direccion_a==0) {
	
		$sql = " SELECT d.id_producto, tx_producto ";
		$sql.= " FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f  ";
		$sql.= " WHERE a.id_proveedor	= $id_proveedor and a.tx_indicador='1' ";
		$sql.= " AND a.id_proveedor	= d.id_proveedor  and d.tx_indicador='1'  ";
		$sql.= " AND b.id_empleado 	= c.id_empleado  and b.tx_indicador='1'   ";
		$sql.= " AND b.id_producto 	= d.id_producto  ";
		$sql.= " AND c.id_centro_costos= e.id_centro_costos and e.tx_indicador='1'  ";
		$sql.= " AND e.id_direccion 	= f.id_direccion and f.tx_indicador='1'  ";
		$sql.= " AND e.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
		$sql.= " GROUP BY d.id_producto, tx_producto ";						
		$sql.= " ORDER BY d.id_producto, tx_producto ";								
		
	} else {
	
		$sql = "   SELECT d.id_producto, tx_producto ";
		$sql.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f  ";
		$sql.= "    WHERE a.id_proveedor	= d.id_proveedor ";
		
		if ($tx_proveedor_opcion == 1) 
			$sql.= " AND a.id_proveedor	= $id_proveedor ";
		else if ($tx_proveedor_opcion == 2) 
			$sql.= " AND a.id_proveedor NOT IN ($id_proveedor, $id_proveedor_1) ";
			
		$sql.= "      AND a.tx_indicador='1'  and d.tx_indicador='1' ";
		
		$sql.= "      AND b.id_empleado 	= c.id_empleado and b.tx_indicador='1'  ";
		$sql.= "      AND b.id_producto 	= d.id_producto ";
		$sql.= "      AND c.id_centro_costos= e.id_centro_costos and e.tx_indicador='1'  ";
		$sql.= "      AND e.id_direccion 	= f.id_direccion  and f.tx_indicador='1'   ";
		$sql.= "      AND f.id_direccion 	= $id_direccion_a ";
		$sql.= " GROUP BY d.id_producto, tx_producto ";						
		$sql.= " ORDER BY d.id_producto, tx_producto ";						
		
	}	
	
	//echo "<br>";
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeProducto[] = array(
			'id_producto'			=>$row["id_producto"],		
			'tx_producto'			=>$row["tx_producto"]			
		);
	} 	
	
	$registros=count($TheInformeProducto);	
	
	$cabecera_titulos=$registros*3+6;
	
	switch ($registros) 
	{   
		case 0 : $TheWidth='8%'; 	break;
		case 1 : $TheWidth='8%'; 	break;  
		case 2 : $TheWidth='7%'; 	break; 
		case 3 : $TheWidth='6%'; 	break; 
		case 4 : $TheWidth='5%'; 	break; 
		case 5 : $TheWidth='4%'; 	break; 
		case 6 : $TheWidth='4%'; 	break; 
		case 7 : $TheWidth='4%'; 	break; 
		case 8 : $TheWidth='4%'; 	break; 
		case 9 : $TheWidth='4%'; 	break; 
		case 10 : $TheWidth='4%'; 	break; 
		default : $TheWidth='4%'; 	break; 
	}	

	//echo "<br>";	
	//echo "Registros  ",$registros;
	//echo "<br>";
	//echo "Cabecera Titulos ",$cabecera_titulos;	
	
	
	echo "<div align='center'><img src='images/logo_excel.jpg' class='botonExcel' alt='Presione para exportar a EXCEL'/></div>";
	echo "<br>";
	if ($id_cuenta==0) echo "<div class='ui-widget-header align-center'>INVENTARIO DE $tx_nombre_corto - $tx_proveedor_corto</div>";	
	else echo "<div class='ui-widget-header align-center'>INVENTARIO DE $tx_nombre_corto - $tx_proveedor_corto</div>";	
	if ($registros==0) { 
	
		echo "<table id='tablaInventarioProveedorN2' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		echo "<td class='align-center'>Sin informaci&oacute;n ...</td>";		
		echo "</tr>";					
		echo "</table>";
		
	} else {	
		// ==============================================================
		// Empieza Reporte
		// ==============================================================
		echo "<table id='tablaInventarioProveedorN2' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";	
			
			# ====================================================================================
			# Cabecera			 
			# ====================================================================================
		
			for ($a=0; $a<3; $a++) {
				switch ($a) {   
					case 0 : $TheField='#'; break;
					case 1 : $TheField='-'; break;	
					case 2 : $TheField='Nombre'; break;					
				}		
				echo "<td rowspan='3' class='ui-state-highlight align-center'>$TheField</td>";
			}	
			
			for ($i=0; $i < count($TheInformeProducto); $i++) { 	        			 
				while ($elemento = each($TheInformeProducto[$i]))					  		
					$id_producto	=$TheInformeProducto[$i]['id_producto'];
					$tx_producto	=$TheInformeProducto[$i]['tx_producto'];
					echo "<td colspan='3' class='ui-state-highlight align-center'>$tx_producto</td>";	
			}					
			echo "<td colspan='3' class='ui-state-highlight align-center'>TOTALES</td>";	
					
		echo "</tr>";	
		
		# ====================================================================================
		# Acompleta Cabecera 			 
		# ====================================================================================
			
		echo "<tr>";	
			for ($m=0; $m<$registros; $m++)	{ 									
				echo "<td rowspan='2' width='$TheWidth' class='ui-state-highlight align-center'>Licencias</td>";
				echo "<td colspan='2' width='$TheWidth' class='ui-state-highlight align-center'>Monto</td>";
			}
			echo "<td rowspan='2' width='$TheWidth' class='ui-state-highlight align-center'>Licencias</td>";
			echo "<td colspan='2' width='$TheWidth' class='ui-state-highlight align-center'>Monto</td>";
		echo "</tr>";
		echo "<tr>";
			for ($m=0; $m<$registros; $m++)	{
				echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Mensual</td>";	
				echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Anual</td>";				
			}
			echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Mensual</td>";	
			echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Anual</td>";	
		echo "</tr>";				
							
		# Fin Acompleta Cabecera
		# ====================================================================================
				
			//$in_licencia_renglon_total=0;
			//$fl_precio_usd_renglon_total=0;
			//$fl_precio_mxn_renglon_total=0;		
			
			if ($id_direccion_a==0) 	
			{			
				$sql1 = "   SELECT c.id_empleado, f.id_direccion, f.tx_nombre, tx_empleado, g.id_subdireccion, tx_subdireccion, c.tx_indicador, tx_notas ";
				$sql1.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f, tbl_subdireccion g ";
				$sql1.= "    WHERE a.id_proveedor		= $id_proveedor and a.tx_indicador='1' ";
				$sql1.= "      AND a.id_proveedor		= d.id_proveedor and d.tx_indicador='1' ";
				$sql1.= "      AND b.id_empleado 		= c.id_empleado and b.tx_indicador='1' ";
				$sql1.= "      AND b.id_producto 		= d.id_producto ";
				$sql1.= "      AND c.id_centro_costos 	= e.id_centro_costos  and e.tx_indicador='1'";
				$sql1.= "      AND e.id_direccion 		= f.id_direccion and f.tx_indicador='1' ";
				$sql1.= "   AND e.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
				$sql1.= "      AND e.id_subdireccion 	= g.id_subdireccion and g.tx_indicador='1'  ";
				$sql1.= " GROUP BY f.tx_nombre, tx_subdireccion, tx_empleado ";
				$sql1.= " ORDER BY f.tx_nombre, tx_subdireccion, tx_empleado ";	
			} else {
				$sql1 = "   SELECT c.id_empleado, f.id_direccion, f.tx_nombre, tx_empleado, g.id_subdireccion, tx_subdireccion, c.tx_indicador, tx_notas ";
				$sql1.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f, tbl_subdireccion g  ";
				$sql1.= "    WHERE a.id_proveedor		= d.id_proveedor   ";
				if ($tx_proveedor_opcion == 1) 
				$sql1.= " AND a.id_proveedor	= $id_proveedor ";
				else if ($tx_proveedor_opcion == 2) 
				$sql1.= " AND a.id_proveedor NOT IN ($id_proveedor, $id_proveedor_1) ";
				
				$sql1.= "      AND a.tx_indicador='1'  and d.tx_indicador='1' ";
				$sql1.= "      AND b.id_empleado 		= c.id_empleado  and b.tx_indicador='1' ";
				$sql1.= "      AND b.id_producto 		= d.id_producto ";
				$sql1.= "      AND c.id_centro_costos 	= e.id_centro_costos and e.tx_indicador='1' ";
				$sql1.= "      AND e.id_direccion 		= f.id_direccion and f.tx_indicador='1'";
				$sql1.= "      AND f.id_direccion 		= $id_direccion_a ";
				$sql1.= "      AND e.id_subdireccion 	= g.id_subdireccion and g.tx_indicador='1'";
				$sql1.= " GROUP BY f.tx_nombre, tx_subdireccion, tx_empleado ";
				$sql1.= " ORDER BY f.tx_nombre, tx_subdireccion, tx_empleado ";	
			}	
			
		
	
			//echo "<br>";
			//echo "sql1",$sql1;	
			
			$result1 = mysqli_query($mysql, $sql1);	
			while ($row = mysqli_fetch_array($result1, MYSQLI_ASSOC))
			{	
				$TheInformeEmpleado[] = array(
					'id_empleado'		=>$row["id_empleado"],
					'id_direccion'		=>$row["id_direccion"],
					'tx_direccion'		=>$row["tx_nombre"],
					'tx_empleado'		=>$row["tx_empleado"],
					'id_subdireccion'	=>$row["id_subdireccion"],
					'tx_subdireccion'	=>$row["tx_subdireccion"],
					'tx_indicador'		=>$row["tx_indicador"],
					'tx_notas'			=>$row["tx_notas"]
					
				);
			} 	
			
			for ($k=0; $k<count($TheInformeEmpleado); $k++)	
			
			{ 	        			 
				while ($elemento = each($TheInformeEmpleado[$k]))
									  		
					$id_empleado	 = $TheInformeEmpleado[$k]['id_empleado'];

					$id_direccion	 = $TheInformeEmpleado[$k]['id_direccion'];
					$tx_direccion	 = $TheInformeEmpleado[$k]['tx_direccion'];
					$tx_empleado	 = $TheInformeEmpleado[$k]['tx_empleado'];
					$id_subdireccion = $TheInformeEmpleado[$k]['id_subdireccion'];
					$tx_subdireccion = $TheInformeEmpleado[$k]['tx_subdireccion'];
					$tx_indicador	 = $TheInformeEmpleado[$k]['tx_indicador'];
					$tx_notas		 = $TheInformeEmpleado[$k]['tx_notas'];
				
				$j++;
				
				if ($k==0)	
				{
					echo "<tr>";							
					echo "<td class='ui-state-default' colspan='$cabecera_titulos'><em>$tx_direccion</em></td>";						
					echo "</tr>";	
					echo "<tr>";							
					echo "<td class='ui-state-default align-center'>-</td>";	
					echo "<td class='ui-state-default' colspan='$cabecera_titulos-1'><em>$tx_subdireccion</em></td>";						
					echo "</tr>";	
				}
				
				#=============================================
				# Corte 1
				#=============================================
				
				if ($k>0)
				{	
					if ($id_direccion_tmp==$id_direccion) { }
					else 
					{
						echo "<tr>";							
						echo "<td class='ui-state-default' colspan='$cabecera_titulos'><em>$tx_direccion</em></td>";						
						echo "</tr>";	
						$id_direccion_tmp=$id_direccion;
					}
				} else 
					{				
					$id_direccion_tmp=$id_direccion;					
					}	
				
				#=============================================
				# Corte 2
				#=============================================				
			
				if ($k>0)
				{															
					if ($id_subdireccion_tmp==$id_subdireccion) { }					
					else 
					{
						echo "<tr>";	
						echo "<td class='ui-state-default align-center'>-</td>";							
						echo "<td class='ui-state-default' colspan='$cabecera_titulos-1'><em>$tx_subdireccion</em></td>";						
						echo "</tr>";	
						$id_subdireccion_tmp=$id_subdireccion;
					}
				} else 
				{				
					$id_subdireccion_tmp=$id_subdireccion;
				}	
				
				// ========================================
				// Detalle
				
				echo "<tr>";
				for ($d=0; $d<3; $d++)
		 		{
					switch ($d) 
					{   
						case 0: $TheColumn=$j; 
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;	
						case 1:	if ($tx_indicador=="0") $TheColumn="<img src='images/redball.png' alt='$tx_notas'/>"; 
								else $TheColumn="<img src='images/greenball.png'/>";
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;					
						case 2:	$TheColumn="<a href='#' onclick='javascript:Ventana($id_empleado)' title='Presione para ver el Detalle de $tx_empleado ...'>$tx_empleado</a>";	
								echo "<td class='align-left' valign='top'>$TheColumn</td>";
						break;
					}								
				}	
				
				$in_licencia_renglon=0;
				$fl_precio_usd_renglon=0;
				$fl_precio_anual_renglon=0;
				
				for ($i=0; $i < count($TheInformeProducto); $i++) 
				{ 	        			 
					 //while ($elemento = each($TheInformeProducto[$i]))   //se puede omitir
										  		
						$id_producto	=$TheInformeProducto[$i]['id_producto'];
						$tx_producto	=$TheInformeProducto[$i]['tx_producto'];
						
					/*	
						// ================================						
						// Busco id_producto
						// ================================						
						$sql8 = "   SELECT id_producto ";
						$sql8.= "     FROM tbl_producto ";
						$sql8.= "    WHERE tx_producto = '$tx_producto' and id_producto=$id_producto ";
						
						//echo "<br>";
						//echo "sql ",$sql8;
												
						$result8 = mysqli_query($mysql, $sql8);	
						while ($row = mysqli_fetch_array($result8, MYSQLI_ASSOC))
						{	
							$TheInformeProductoId[] = array(
								'id_producto'	=>$row["id_producto"]
							);
						} 	
										*/
						/*						
						for ($q=0; $q< count($TheInformeProducto); $q++)	
						{ 	        			 
							while ($elemento8 = each($TheInformeProducto[$q]))					  		
								$id_producto =$TheInformeProducto[$q]['id_producto'];		
						}	
						*/
					
						$in_licencia_pan=0;
						$fl_precio_usd_pan=0;																			
						$fl_precio_mxn_pan=0;
						
						// ==================================
						// Fin Producto
						// ==================================
												
						//$sql2 = "   SELECT in_licencia, IF (d.id_moneda=1, fl_precio/14, fl_precio ) AS fl_precio ";
						
						if ($tx_moneda=="USD") $sql2 = " SELECT sum(in_licencia) as in_licencia, sum( fl_precio + (d.fl_precio_mxn / $fl_tipo_cambio) ) AS fl_precio ";
						else $sql2 = " SELECT sum(in_licencia)  as in_licencia ,  sum( (fl_precio * $fl_tipo_cambio) + d.fl_precio_mxn ) AS fl_precio ";				
						$sql2.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f  ";
						$sql2.= "    WHERE a.id_proveedor		= d.id_proveedor ";
						if ($tx_proveedor_opcion == 1) 
						$sql2.= "      AND a.id_proveedor		= $id_proveedor ";
						else if ($tx_proveedor_opcion == 2) 
						$sql2.= " AND a.id_proveedor NOT IN ($id_proveedor, $id_proveedor_1) ";
						
						$sql2.= "      AND a.tx_indicador 	= '1' and d.tx_indicador='1' ";
						$sql2.= "      AND b.id_producto 		= $id_producto ";
						$sql2.= "      AND b.id_producto 		= d.id_producto and b.tx_indicador='1'  ";
						$sql2.= "      AND c.id_centro_costos 	= e.id_centro_costos  and e.tx_indicador='1' ";
						$sql2.= "      AND e.id_direccion 		= f.id_direccion and f.tx_indicador='1' ";
						$sql2.= "   AND e.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
						$sql2.= "      AND b.id_empleado 		= $id_empleado ";
						$sql2.= "      AND b.id_empleado        = c.id_empleado and b.tx_indicador='1' ";
						

				
				
						//echo "<br>";
						//echo "sql 2 ",$sql2;							
												
						$result2 = mysqli_query($mysql, $sql2);	
						$count2 = mysqli_num_rows($result2);						
						
						while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC))
						{	
							$TheInformeDetalle =null;
							$TheInformeDetalle [] = array(
								'in_licencia'	=>$row["in_licencia"],
								'fl_precio_usd'	=>$row["fl_precio"]							
							);
						} 		
												
						if ($count2==0) {
						
							$in_licencia_pan="-";
							$fl_precio_usd_pan="-";																			
							$fl_precio_anual_pan="-";
						
						} else {
						
							for ($p=0; $p < count($TheInformeDetalle); $p++) 
							{ 	        			 
								//while ($elemento = each($TheInformeDetalle[$p]))					  		
									$in_licencia	=$TheInformeDetalle[$p]['in_licencia'];
									$fl_precio_usd	=$TheInformeDetalle[$p]['fl_precio_usd'];
									$fl_precio_anual=$fl_precio_usd*$in_anio;
							}
							
							if ($in_licencia==NULL) $in_licencia_pan="-";
							else {
								$in_licencia_pan = $in_licencia_pan + $in_licencia;
								$in_licencia_renglon = $in_licencia_renglon + $in_licencia;
							}	
															
							if ($fl_precio_usd==NULL) $fl_precio_usd_pan="-";						
							else {
								$fl_precio_usd_renglon=$fl_precio_usd_renglon+$fl_precio_usd;						
								$fl_precio_usd_pan=number_format($fl_precio_usd,0);														
							}	

							if ($fl_precio_anual==NULL) $fl_precio_anual_pan="-";
							else {
								$fl_precio_anual_renglon=$fl_precio_anual_renglon+$fl_precio_anual;						
								$fl_precio_anual_pan=number_format($fl_precio_anual,0);
							}	
						}						
						echo "<td class='align-center'>$in_licencia_pan</td>";	
						echo "<td class='align-right'>$fl_precio_usd_pan</td>";	
						echo "<td class='ui-state-verde align-right'>$fl_precio_anual_pan</td>";	
					}	
					
					if ($in_licencia_renglon==NULL) $in_licencia_renglon="-";
					else {
						//$in_licencia_renglon_total=$in_licencia_renglon_total + $in_licencia_renglon;
						$in_licencia_renglon=number_format($in_licencia_renglon,0);
					}	
					
					if ($fl_precio_usd_renglon==NULL) $fl_precio_usd_renglon=number_format($fl_precio_usd_renglon,0);
					else {
						//$fl_precio_usd_renglon_total=$fl_precio_usd_renglon_total + fl_precio_usd_renglon;
					 	$fl_precio_usd_renglon=number_format($fl_precio_usd_renglon,0);
					}	
					
					if ($fl_precio_anual_renglon==NULL) $fl_precio_anual_renglon=number_format($fl_precio_anual_renglon,0);	
					else { 
						//$fl_precio_mxn_renglon_total=$fl_precio_mxn_renglon_total + $fl_precio_mxn_renglon;
						$fl_precio_anual_renglon=number_format($fl_precio_anual_renglon,0);					
					}	
					
					echo "<td class='ui-state-default' align='center'>$in_licencia_renglon</td>";	
					echo "<td class='ui-state-default' align='right'>$fl_precio_usd_renglon</td>";	
					echo "<td class='ui-state-verde-total' align='right'>$fl_precio_anual_renglon</td>";						
				echo "</tr>";					
			}
			
			echo "<tr>";
			$TheField='TOTALES';
			echo "<td colspan='3' class='ui-state-highlight align-center'>$TheField</td>";
			//echo "<br>";
			//echo "aaaa",$id_direccion_a;
						
			if ($id_direccion_a==0) 	
			{	
				//$sql2 = "    SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, IF (d.id_moneda=1,SUM( fl_precio / 14 ), SUM( fl_precio )) AS fl_precio_usd_total, SUM( fl_precio_mxn ) AS fl_precio_mxn_total ";
				
				
				if ($tx_moneda=="USD") $sql2 = " SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, SUM(fl_precio) + SUM(d.fl_precio_mxn / $fl_tipo_cambio) AS fl_precio_usd_total ";
				else $sql2 = " SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, SUM(fl_precio * $fl_tipo_cambio) + d.fl_precio_mxn  AS fl_precio_usd_total ";				
				$sql2.= "      FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f ";
				$sql2.= "    WHERE a.id_proveedor 		= $id_proveedor ";
				$sql2.= "      AND a.id_proveedor	 	= d.id_proveedor and d.tx_indicador='1' ";
				$sql2.= "      AND b.id_empleado 		= c.id_empleado  and b.tx_indicador='1' ";
				$sql2.= "      AND b.id_producto 		= d.id_producto ";
				$sql2.= "      AND c.id_centro_costos 	= e.id_centro_costos and e.tx_indicador='1'";
				$sql2.= "      AND e.id_direccion 		= f.id_direccion and f.tx_indicador='1'";
				$sql2.= "   AND e.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
				$sql2.= " GROUP BY d.id_producto, tx_producto ";
				$sql2.= " ORDER BY d.id_producto, tx_producto "; 	
			} else {
				//$sql2 = "    SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, IF (d.id_moneda=1,SUM( fl_precio / 14 ), SUM( fl_precio )) AS fl_precio_usd_total, SUM( fl_precio_mxn ) AS fl_precio_mxn_total ";
				
				if ($tx_moneda=="USD") $sql2 = " SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, SUM(fl_precio) + SUM(d.fl_precio_mxn / $fl_tipo_cambio) AS fl_precio_usd_total ";
				else $sql2 = " SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, SUM(fl_precio * $fl_tipo_cambio) + d.fl_precio_mxn  AS fl_precio_usd_total ";						
				$sql2.= "      FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f ";
				$sql2.= "    WHERE a.id_proveedor	 	= d.id_proveedor ";
				if ($tx_proveedor_opcion == 1) 
				$sql2.= " AND a.id_proveedor 	= $id_proveedor ";
				else if ($tx_proveedor_opcion == 2) 
				$sql2.= " AND a.id_proveedor NOT IN ($id_proveedor, $id_proveedor_1) ";
				
				$sql2.= "      AND a.tx_indicador='1'  ";
				$sql2.= "      AND b.id_empleado 		= c.id_empleado and b.tx_indicador='1'  ";
				$sql2.= "      AND b.id_producto 		= d.id_producto and d.tx_indicador='1' ";
				$sql2.= "      AND c.id_centro_costos 	= e.id_centro_costos  and e.tx_indicador='1' ";
				$sql2.= "      AND e.id_direccion 		= f.id_direccion and f.tx_indicador='1' ";
				$sql2.= "      AND f.id_direccion 		= $id_direccion_a ";
				$sql2.= " GROUP BY d.id_producto, tx_producto ";
				$sql2.= " ORDER BY d.id_producto, tx_producto "; 	
			}
						
			//echo "<br>";
			//echo "sql 2 ",$sql2;		
						
			$result2 = mysqli_query($mysql, $sql2);	
			$count2 = mysqli_num_rows($result2);						
						
			while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC))
			{	
				$TheInformeTotal[] = array(
					'in_licencia_total'		=>$row["in_licencia_total"],
					'fl_precio_usd_total'	=>$row["fl_precio_usd_total"]				
				);
			} 
						
			for ($p=0; $p < count($TheInformeTotal); $p++) { 	        			 
				while ($elemento = each($TheInformeTotal[$p]))					  		
					$in_licencia_total		=$TheInformeTotal[$p]['in_licencia_total'];
					$fl_precio_usd_total	=$TheInformeTotal[$p]['fl_precio_usd_total'];
					$fl_precio_anual_total	=$fl_precio_usd_total * $in_anio;
														
					if ($in_licencia_total==NULL) $in_licencia_tota="-";											
					echo "<td class='ui-state-highlight align-center'>$in_licencia_total</td>";								
					$in_licencia_renglon_total=$in_licencia_renglon_total+$in_licencia_total;
					
					if ($fl_precio_usd_total==NULL) $fl_precio_usd_total="-";
					else {
						$fl_precio_usd_renglon_total=$fl_precio_usd_renglon_total+$fl_precio_usd_total;	
						$fl_precio_usd_total=number_format($fl_precio_usd_total,0);		
					}	
					echo "<td class='ui-state-highlight align-right'>$fl_precio_usd_total</td>";												
						
					if ($fl_precio_anual_total==NULL) $fl_precio_anual_total="-";
					else {
						$fl_precio_anual_renglon_total=$fl_precio_anual_renglon_total+$fl_precio_anual_total;		
						$fl_precio_anual_total=number_format($fl_precio_anual_total,0);	
					}	
					
					echo "<td class='ui-state-highlight align-right'>$fl_precio_anual_total</td>";								
			}	
				
				if ($fl_precio_usd_renglon_total==NULL) $fl_precio_usd_renglon_total = number_format($fl_precio_usd_renglon_total,0);
				else $fl_precio_usd_renglon_total = number_format($fl_precio_usd_renglon_total,0);
				
				if ($fl_precio_anual_renglon_total==NULL) $fl_precio_anual_renglon_total = number_format($fl_precio_anual_renglon_total,0);
				else $fl_precio_anual_renglon_total = number_format($fl_precio_anual_renglon_total,0);
			
				echo "<td class='ui-state-highlight align-center'>$in_licencia_renglon_total</td>";	
				echo "<td class='ui-state-highlight align-right'>$fl_precio_usd_renglon_total</td>";
				echo "<td class='ui-state-highlight align-right'>$fl_precio_anual_renglon_total</td>";									
			echo "</tr>";
			
			echo "<tr>";			
			for ($a=0; $a<3; $a++) {
				switch ($a) {   
					case 0 : $TheField='#'; break;
					case 1 : $TheField='-'; break;		
					case 2 : $TheField='Nombre'; break;					
				}		
				echo "<td rowspan='4' class='ui-state-highlight align-center'>$TheField</td>";
			}	
			echo "</tr>";
			
			echo "<tr>";
			for ($m=0; $m<$registros; $m++)	{
			
				echo "<td rowspan='2' width='$TheWidth' class='ui-state-highlight align-center'>Licencias</td>";
				echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Mensual</td>";	
				echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Anual</td>";	
							
			}
			echo "<td rowspan='2' width='$TheWidth' class='ui-state-highlight align-center'>Licencias</td>";
			echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Mensual</td>";	
			echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Anual</td>";	
			echo "</tr>";				
			echo "<tr>";	
			for ($m=0; $m<$registros; $m++)	{ 	
				echo "<td colspan='2' width='$TheWidth' class='ui-state-highlight align-center'>Monto</td>";
			}			
			echo "<td colspan='2' width='$TheWidth' class='ui-state-highlight align-center'>Monto</td>";
			echo "</tr>";		
			echo "<tr>";			
			for ($i=0; $i < count($TheInformeProducto); $i++) { 	        			 
				while ($elemento = each($TheInformeProducto[$i]))					  		
					$id_producto	=$TheInformeProducto[$i]['id_producto'];
					$tx_producto	=$TheInformeProducto[$i]['tx_producto'];
					echo "<td colspan='3' class='ui-state-highlight align-center'>$tx_producto</td>";	
			}
			echo "<td colspan='3' class='ui-state-highlight align-center'>TOTALES</td>";			
			echo "</tr>";	
			echo "<tr>";
			echo "<td colspan='$cabecera_titulos' align='left'>";	
			echo "<ul type='square'> ";
			echo "<li>Licencias calculadas en base al Inventario.</em></li> ";
        	echo "<li>Monto calculado en base al Inventario.</li> ";
        	echo "<li>Monto en  $tx_moneda.</li>";
        	echo "<li>Tipo de cambio $fl_tipo_cambio pesos por d&oacute;lar.</li>";
        	echo "</ul>";      
			echo "</td>";
			echo "</tr>";	
	echo "</table>";		
	
	
	$id_direccion_a = $_GET["id_direccion"];		
	$id_proveedor	= $_GET["id_proveedor"];	
	$id_proveedor_1	= $_GET["id_proveedor_1"];	
	$fl_tipo_cambio	= $_GET["fl_tipo_cambio"];		
	$tx_moneda		= $_GET["tx_moneda"];	
	
		//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_LICENCIA" , "$id_login" ,   "id_direccion=$id_direccion_a fl_tipo_cambio=$fl_tipo_cambio tx_moneda=$tx_moneda id_proveedor=$id_proveedor id_proveedor_1=$id_proveedor_1" ,"" ,"inf_inventario_direccion_licencias_matriz.php");
	 //<\BITACORA>
	 
	 
	
	}
	mysqli_close($mysql);
	?>
	</form>
<?php    
} 
else 
{
	echo "Sessi&oacute;n Invalida";
}	
?>      