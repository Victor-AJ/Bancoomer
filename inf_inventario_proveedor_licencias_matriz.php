<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	$id_login = $_SESSION['sess_iduser'];
?>
	<script type="text/javascript">			
		 
		 $("#tablaInventarioProveedorN2").find("tr").hover(		 
         	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
         );		 

		function Ventana(valor) 
		{ 			
			var url = "ventana_empleado.php?id="+valor;
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1100, height=700";
			winName='_blank';  
			//alert ("url"+url);
			window.open(url,winName,windowprops); 
		} 
		 
	</script> 
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");
		
	$mysql=conexion_db();	
	
	$id_proveedor	 	= $_GET['id_proveedor'];	
	$id_direccion_a  	= $_GET['id_direccion'];		
	$id_cuenta			= $_GET['id_cuenta'];		
	
	if ($id_direccion_a==0) $dispatch="insert";
	else if (is_null($id_direccion_a)) { $dispatch="insert"; $id_direccion_a=0; }
	else $dispatch="save";

	if (is_null($id_direccion_a)) $id_direccion_a=0; 
	
	//echo "id_proveedor",$id_proveedor;
	//echo "<br>";
	//echo "id_direccion",$id_direccion;
	//echo "<br>";
	//echo "id_cuenta",$id_cuenta;
	//echo "<br>";

		
	// ============================================================
	// BUSCA INFORMACION DEL PROVEEDOR
	// ============================================================
	
	if ($id_cuenta==0) 
	{	
		$sql = " SELECT tx_proveedor_corto, tx_cuenta ";
		$sql.= "    FROM tbl_proveedor a, tbl_cuenta b ";
		$sql.= "   WHERE a.id_proveedor	= $id_proveedor ";
		$sql.= "     AND a.id_proveedor = b.id_proveedor ";
	} else {
		$sql = " SELECT tx_proveedor_corto, tx_cuenta ";
		$sql.= "    FROM tbl_proveedor a, tbl_cuenta b ";
		$sql.= "   WHERE a.id_proveedor	= $id_proveedor ";
		$sql.= "     AND a.id_proveedor = b.id_proveedor ";
		$sql.= "     AND b.id_cuenta 	= $id_cuenta ";
	}
	//echo "aaa",$sql;			
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoProveedorCuenta[] = array(			
	  		'tx_proveedor_corto'=>$row["tx_proveedor_corto"],
			'tx_cuenta'			=>$row["tx_cuenta"]
		);
	} 	
	
	for ($i=0; $i < count($TheCatalogoProveedorCuenta); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogoProveedorCuenta[$i]))					  		
			$tx_proveedor_corto	=$TheCatalogoProveedorCuenta[$i]['tx_proveedor_corto'];		
			$tx_cuenta			=$TheCatalogoProveedorCuenta[$i]['tx_cuenta'];		
	}		
	
	// ============================================================
	// BUSCA INFORMACION DEL PROVEEDOR Y PRODUCTOS
	// ============================================================
	
	if ($id_direccion_a==0) 	
	{
		$sql = "   SELECT d.id_producto, tx_producto ";
		$sql.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f  ";
		$sql.= "    WHERE a.id_proveedor	= $id_proveedor and a.tx_indicador='1' ";
		$sql.= "      AND a.id_proveedor	= d.id_proveedor and d.tx_indicador='1' ";
		$sql.= "      AND b.id_empleado 	= c.id_empleado  and b.tx_indicador='1' ";
		$sql.= "      AND b.id_producto 	= d.id_producto   ";
		$sql.= "      AND c.id_centro_costos= e.id_centro_costos and e.tx_indicador='1' ";
		$sql.= "      AND e.id_direccion 	= f.id_direccion and f.tx_indicador='1' ";
		$sql.= "   AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	
		$sql.= " GROUP BY d.id_producto, tx_producto ";						
		$sql.= " ORDER BY d.id_producto, tx_producto ";								
	} else {
		$sql = "   SELECT d.id_producto, tx_producto ";
		$sql.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f  ";
		$sql.= "    WHERE a.id_proveedor	= $id_proveedor and a.tx_indicador='1'";
		$sql.= "      AND a.id_proveedor	= d.id_proveedor and d.tx_indicador='1'";
		$sql.= "      AND b.id_empleado 	= c.id_empleado and b.tx_indicador='1'";
		$sql.= "      AND b.id_producto 	= d.id_producto ";
		$sql.= "      AND c.id_centro_costos= e.id_centro_costos and e.tx_indicador='1'";
		$sql.= "      AND e.id_direccion 	= f.id_direccion and f.tx_indicador='1'";
		$sql.= "      AND f.id_direccion 	= $id_direccion_a ";
		$sql.= "   AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	
		$sql.= " GROUP BY d.id_producto, tx_producto ";						
		$sql.= " ORDER BY d.id_producto, tx_producto ";						
	}	
	
	//echo "<br>";
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeProducto[] = array(
			'id_producto'	=>$row["id_producto"],		
			'tx_producto'	=>$row["tx_producto"]
		);
	} 	
	
	$registros=count($TheInformeProducto);	
	$cabecera_titulos=$registros*3+7;
	
	switch ($registros) 
	{   
		case 0 : $TheWidth='8%'; 	break;
		case 1 : $TheWidth='7%'; 	break;  
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
	echo "<br>";
	if ($id_cuenta==0) echo "<div class='ui-widget-header align-center'>INVENTARIO DE $tx_proveedor_corto</div>";	
	else echo "<div class='ui-widget-header align-center'>INVENTARIO DE $tx_proveedor_corto DE LA CUENTA $tx_cuenta</div>";	
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
			
			// ====================================================================================
			// Cabecera			 
		
			for ($a=0; $a<4; $a++) {
				switch ($a) {   
					case 0 : $TheField='#'; 
							 echo "<td width='3%' rowspan='2' class='ui-state-highlight align-center'>$TheField</td>";
					break;
					case 1 : $TheField='-'; 
							 echo "<td width='3%' rowspan='2' class='ui-state-highlight align-center'>$TheField</td>";
					break;					
					case 2 : $TheField='Registro'; 
 							 echo "<td width='7%' rowspan='2' class='ui-state-highlight align-center'>$TheField</td>";
					break;							 
					case 3 : $TheField='Nombre'; 
							 echo "<td width='10%' rowspan='2' class='ui-state-highlight align-center'>$TheField</td>";
					break;							  
				}		
			}	
			
			for ($i=0; $i < count($TheInformeProducto); $i++) { 	        			 
				while ($elemento = each($TheInformeProducto[$i]))					  		
					$id_producto	=$TheInformeProducto[$i]['id_producto'];
					$tx_producto	=$TheInformeProducto[$i]['tx_producto'];
					echo "<td colspan='3' class='ui-state-highlight align-center'>$tx_producto</td>";	
			}					
			echo "<td colspan='3' class='ui-state-highlight align-center'>TOTALES</td>";	
			 
			// Fin Cabecera
			// ====================================================================================
		echo "</tr>";	
		echo "<tr>";	
			// ====================================================================================
			// Acompleta Cabecera 			 
			
			for ($m=0; $m<$registros; $m++)	
			{ 	
				echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Licencias</td>";	
				echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Monto USD</td>";	
				echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Monto MXN</td>";	
			}		
			
			// Acompleta cabera para totales
			
			echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Licencias</td>";	
			echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Monto USD</td>";	
			echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Monto MXN</td>";				
				
			// Fin Acompleta Cabecera
			// ====================================================================================
		echo "</tr>";	
		
			//$in_licencia_renglon_total=0;
			//$fl_precio_usd_renglon_total=0;
			//$fl_precio_mxn_renglon_total=0;		
			
			if ($id_direccion_a==0) 	
			{			
				$sql1 = "   SELECT c.id_empleado, f.id_direccion, f.tx_nombre, tx_empleado, g.id_subdireccion, tx_subdireccion, tx_registro, c.tx_indicador ";
				$sql1.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f, tbl_subdireccion g ";
				$sql1.= "    WHERE a.id_proveedor		= $id_proveedor  and a.tx_indicador='1' ";
				$sql1.= "      AND a.id_proveedor		= d.id_proveedor and d.tx_indicador='1' ";
				$sql1.= "      AND b.id_empleado 		= c.id_empleado and b.tx_indicador='1' ";
				$sql1.= "      AND b.id_producto 		= d.id_producto ";
				$sql1.= "      AND c.id_centro_costos 	= e.id_centro_costos and e.tx_indicador='1' ";
				$sql1.= "      AND e.id_direccion 		= f.id_direccion and f.tx_indicador='1' ";
				$sql1.= "      AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	
				$sql1.= "      AND e.id_subdireccion 	= g.id_subdireccion and g.tx_indicador='1' ";
				$sql1.= " GROUP BY f.tx_nombre, tx_subdireccion, tx_empleado ";
				$sql1.= " ORDER BY f.tx_nombre, tx_subdireccion, tx_empleado ";	
			} else {
				$sql1 = "   SELECT c.id_empleado, f.id_direccion, f.tx_nombre, tx_empleado, g.id_subdireccion, tx_subdireccion, tx_registro, c.tx_indicador ";
				$sql1.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f, tbl_subdireccion g  ";
				$sql1.= "    WHERE a.id_proveedor		= $id_proveedor  and a.tx_indicador='1' ";
				$sql1.= "      AND a.id_proveedor		= d.id_proveedor and d.tx_indicador='1' ";
				$sql1.= "      AND b.id_empleado 		= c.id_empleado and b.tx_indicador='1' ";
				$sql1.= "      AND b.id_producto 		= d.id_producto ";
				$sql1.= "      AND c.id_centro_costos 	= e.id_centro_costos and e.tx_indicador='1' ";
				$sql1.= "      AND e.id_direccion 		= f.id_direccion and f.tx_indicador='1' ";
				$sql1.= "      AND f.id_direccion 		= $id_direccion_a ";
				$sql1.= "      AND e.id_subdireccion 	= g.id_subdireccion and g.tx_indicador='1' ";
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
					'tx_registro'		=>$row["tx_registro"],
					'tx_indicador'		=>$row["tx_indicador"]
				);
			} 	
			
			for ($k=0; $k<count($TheInformeEmpleado); $k++)	{ 	        			 
				while ($elemento = each($TheInformeEmpleado[$k]))					  		
					$id_empleado	=$TheInformeEmpleado[$k]['id_empleado'];
					$id_direccion	=$TheInformeEmpleado[$k]['id_direccion'];
					$tx_direccion	=$TheInformeEmpleado[$k]['tx_direccion'];
					$tx_empleado	=$TheInformeEmpleado[$k]['tx_empleado'];
					$id_subdireccion=$TheInformeEmpleado[$k]['id_subdireccion'];
					$tx_subdireccion=$TheInformeEmpleado[$k]['tx_subdireccion'];
					$tx_registro	=$TheInformeEmpleado[$k]['tx_registro'];
					$tx_indicador	=$TheInformeEmpleado[$k]['tx_indicador'];
				
				$j++;
				
				if ($k==0)	{
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
					else {
						echo "<tr>";							
						echo "<td class='ui-state-default' colspan='$cabecera_titulos'><em>$tx_direccion</em></td>";						
						echo "</tr>";	
						$id_direccion_tmp=$id_direccion;
					}
				} else {				
					$id_direccion_tmp=$id_direccion;					
				}	
				
				#=============================================
				# Corte 2
				#=============================================				
			
				if ($k>0)
				{	
					//echo "id_subdireccion_tmp",$id_subdireccion_tmp;
					//echo "<br>";
					//echo "id_subdireccion",$id_subdireccion;
					//echo "<br>";
														
					if ($id_subdireccion_tmp==$id_subdireccion) { }					
					else {
						echo "<tr>";	
						echo "<td class='ui-state-default align-center'>-</td>";							
						echo "<td class='ui-state-default' colspan='$cabecera_titulos-1'><em>$tx_subdireccion</em></td>";						
						echo "</tr>";	
						$id_subdireccion_tmp=$id_subdireccion;
					}
				} else {				
					$id_subdireccion_tmp=$id_subdireccion;
				}	
				
				// ========================================
				// Detalle
				
				echo "<tr>";
				for ($d=0; $d<4; $d++)
		 		{
					switch ($d) 
					{   
						case 0: $TheColumn=$j; 
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;		
						case 1: if ($tx_indicador=="0") $TheColumn="<img src='images/redball.png' alt='$tx_notas'/>"; 
								else $TheColumn="<img src='images/greenball.png'/>";
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;				
						case 2:	$TheColumn="<a href='#' onclick='javascript:Ventana($id_empleado)' title='Presione para ver el Detalle de $tx_empleado ...'>$tx_registro</a>";	
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;
						case 3:	$TheColumn=$tx_empleado;	
								echo "<td class='align-left' valign='top'>$TheColumn</td>";
						break;
					}								
				}	
				
				$in_licencia_renglon=0;
				$fl_precio_usd_renglon=0;
				$fl_precio_mxn_renglon=0;
				
				for ($i=0; $i < count($TheInformeProducto); $i++) { 	        			 
					while ($elemento = each($TheInformeProducto[$i]))					  		
						$id_producto	=$TheInformeProducto[$i]['id_producto'];
						$tx_producto	=$TheInformeProducto[$i]['tx_producto'];
						
						// ================================						
						// Busco id_producto
						// ================================						
						$sql8 = "   SELECT id_producto ";
						$sql8.= "     FROM tbl_producto ";
						$sql8.= "    WHERE tx_producto = '$tx_producto' ";
						
						//echo "<br>";
						//echo "sql ",$sql8;
												
						$result8 = mysqli_query($mysql, $sql8);	
						while ($row = mysqli_fetch_array($result8, MYSQLI_ASSOC))
						{	
							$TheInformeProductoId[] = array(
								'id_producto'	=>$row["id_producto"]
							);
						} 	
												
						for ($q=0; $q< count($TheInformeProductoId); $q++)	{ 	        			 
							while ($elemento8 = each($TheInformeProductoId[$q]))					  		
								$id_producto =$TheInformeProductoId[$q]['id_producto'];		
						}	
						
						$in_licencia_pan=0;
						$fl_precio_usd_pan=0;																			
						$fl_precio_mxn_pan=0;
						
						// ==================================
						// Fin Producto
						// ==================================
												
						$sql2 = "   SELECT in_licencia, fl_precio AS fl_precio_usd, d.fl_precio_mxn ";
						$sql2.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f  ";
						$sql2.= "    WHERE a.id_proveedor		= $id_proveedor and a.tx_indicador='1'  ";
						$sql2.= "      AND a.id_proveedor		= d.id_proveedor and d.tx_indicador='1'";
						$sql2.= "      AND b.id_producto 		= $id_producto and b.tx_indicador='1'";
						$sql2.= "      AND b.id_producto 		= d.id_producto  and d.tx_indicador='1'";
						$sql2.= "      AND c.id_centro_costos 	= e.id_centro_costos  and e.tx_indicador='1'";
						$sql2.= "      AND e.id_direccion 		= f.id_direccion  and f.tx_indicador='1'";
						$sql2.= "      AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
						
						$sql2.= "      AND b.id_empleado 		= $id_empleado ";
						$sql2.= "      AND b.id_empleado        = c.id_empleado ";
						
						//echo "<br>";
						//echo "sql 2 ",$sql2;							
												
						$result2 = mysqli_query($mysql, $sql2);	
						$count2 = mysqli_num_rows($result2);						
						
						while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC))
						{	
							$TheInformeDetalle[] = array(
								'in_licencia'	=>$row["in_licencia"],
								'fl_precio_usd'	=>$row["fl_precio_usd"],
								'fl_precio_mxn'	=>$row["fl_precio_mxn"]
							);
						} 		
												
						if ($count2==0) {
						
							$in_licencia_pan="-";
							$fl_precio_usd_pan="-";																			
							$fl_precio_mxn_pan="-";
						
						} else {
						
							for ($p=0; $p < count($TheInformeDetalle); $p++) { 	        			 
								while ($elemento = each($TheInformeDetalle[$p]))					  		
									$in_licencia	=$TheInformeDetalle[$p]['in_licencia'];
									$fl_precio_usd	=$TheInformeDetalle[$p]['fl_precio_usd'];
									$fl_precio_mxn	=$TheInformeDetalle[$p]['fl_precio_mxn'];
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

							if ($fl_precio_mxn==NULL) $fl_precio_mxn_pan="-";
							else {
								$fl_precio_mxn_renglon=$fl_precio_mxn_renglon+$fl_precio_mxn;						
								$fl_precio_mxn_pan=number_format($fl_precio_mxn,0);
							}	
						}						
						echo "<td class='align-center'>$in_licencia_pan</td>";	
						echo "<td class='align-right'>$fl_precio_usd_pan</td>";	
						echo "<td class='align-right'>$fl_precio_mxn_pan</td>";	
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
					
					if ($fl_precio_mxn_renglon==NULL) $fl_precio_mxn_renglon=number_format($fl_precio_mxn_renglon,0);	
					else { 
						//$fl_precio_mxn_renglon_total=$fl_precio_mxn_renglon_total + $fl_precio_mxn_renglon;
						$fl_precio_mxn_renglon=number_format($fl_precio_mxn_renglon,0);					
					}	
					
					echo "<td class='ui-state-default' align='center'>$in_licencia_renglon</td>";	
					echo "<td class='ui-state-default' align='right'>$fl_precio_usd_renglon</td>";	
					echo "<td class='ui-state-default' align='right'>$fl_precio_mxn_renglon</td>";						
				echo "</tr>";					
			}
			
			echo "<tr>";
			
			for ($a=0; $a<4; $a++) {
				switch ($a) {   
					case 0 : $TheField='#'; 
							 echo "<td rowspan='3' class='ui-state-highlight align-center'>$TheField</td>";
					break;
					case 1 : $TheField='-'; 
							 echo "<td rowspan='3' class='ui-state-highlight align-center'>$TheField</td>";
					break;					
					case 2 : $TheField='Registro'; 
 							 echo "<td rowspan='3' class='ui-state-highlight align-center'>$TheField</td>";
					break;							 
					case 3 : $TheField='Nombre'; 
							 echo "<td rowspan='3' class='ui-state-highlight align-center'>$TheField</td>";
					break;							  
				}		
			}	
			
			//echo "<br>";
			//echo "aaaa",$id_direccion_a;
						
			if ($id_direccion_a==0) 	
			{	
				$sql2 = "    SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, SUM( d.fl_precio ) AS fl_precio_usd_total, SUM( d.fl_precio_mxn ) AS fl_precio_mxn_total ";
				$sql2.= "      FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f ";
				$sql2.= "    WHERE a.id_proveedor 		= $id_proveedor and a.tx_indicador='1' ";
				$sql2.= "      AND a.id_proveedor	 	= d.id_proveedor and d.tx_indicador='1' ";
				$sql2.= "      AND b.id_empleado 		= c.id_empleado and b.tx_indicador='1' ";
				$sql2.= "      AND b.id_producto 		= d.id_producto  ";
				$sql2.= "      AND c.id_centro_costos 	= e.id_centro_costos and e.tx_indicador='1'";
				$sql2.= "      AND e.id_direccion 		= f.id_direccion and f.tx_indicador='1'";
				$sql2.= "      AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
					
				$sql2.= " GROUP BY d.id_producto, tx_producto ";
				$sql2.= " ORDER BY d.id_producto, tx_producto "; 	
			} else {
				$sql2 = "    SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, SUM( d.fl_precio ) AS fl_precio_usd_total, SUM( d.fl_precio_mxn ) AS fl_precio_mxn_total ";
				$sql2.= "      FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f ";
				$sql2.= "    WHERE a.id_proveedor 		= $id_proveedor and a.tx_indicador='1' ";
				$sql2.= "      AND a.id_proveedor	 	= d.id_proveedor and d.tx_indicador='1'  ";
				$sql2.= "      AND b.id_empleado 		= c.id_empleado  and b.tx_indicador='1' ";
				$sql2.= "      AND b.id_producto 		= d.id_producto ";
				$sql2.= "      AND c.id_centro_costos 	= e.id_centro_costos  and e.tx_indicador='1' ";
				$sql2.= "      AND e.id_direccion 		= f.id_direccion  and f.tx_indicador='1' ";
				$sql2.= "      AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
			
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
					'fl_precio_usd_total'	=>$row["fl_precio_usd_total"],
					'fl_precio_mxn_total'	=>$row["fl_precio_mxn_total"]
				);
			} 
						
			for ($p=0; $p < count($TheInformeTotal); $p++) { 	        			 
				while ($elemento = each($TheInformeTotal[$p]))					  		
					$in_licencia_total		=$TheInformeTotal[$p]['in_licencia_total'];
					$fl_precio_usd_total	=$TheInformeTotal[$p]['fl_precio_usd_total'];
					$fl_precio_mxn_total	=$TheInformeTotal[$p]['fl_precio_mxn_total'];
														
					if ($in_licencia_total==NULL) $in_licencia_tota="-";											
					echo "<td class='ui-state-highlight align-center'>$in_licencia_total</td>";								
					$in_licencia_renglon_total=$in_licencia_renglon_total+$in_licencia_total;
					
					if ($fl_precio_usd_total==NULL) $fl_precio_usd_total="-";
					else {
						$fl_precio_usd_renglon_total=$fl_precio_usd_renglon_total+$fl_precio_usd_total;	
						$fl_precio_usd_total=number_format($fl_precio_usd_total,0);		
					}	
					echo "<td class='ui-state-highlight align-right'>$fl_precio_usd_total</td>";												
						
					if ($fl_precio_mxn_total==NULL) $fl_precio_mxn_total="-";
					else {
						$fl_precio_mxn_renglon_total=$fl_precio_mxn_renglon_total+$fl_precio_mxn_total;		
						$fl_precio_mxn_total=number_format($fl_precio_mxn_total,0);	
					}	
					
					echo "<td class='ui-state-highlight align-right'>$fl_precio_mxn_total</td>";								
			}	
				
				if ($fl_precio_usd_renglon_total==NULL) $fl_precio_usd_renglon_total = number_format($fl_precio_usd_renglon_total,0);
				else $fl_precio_usd_renglon_total = number_format($fl_precio_usd_renglon_total,0);
				
				if ($fl_precio_mxn_renglon_total==NULL) $fl_precio_mxn_renglon_total = number_format($fl_precio_mxn_renglon_total,0);
				else $fl_precio_mxn_renglon_total = number_format($fl_precio_mxn_renglon_total,0);
			
				echo "<td class='ui-state-highlight align-center'>$in_licencia_renglon_total</td>";	
				echo "<td class='ui-state-highlight align-right'>$fl_precio_usd_renglon_total</td>";
				echo "<td class='ui-state-highlight align-right'>$fl_precio_mxn_renglon_total</td>";
									
			echo "</tr>";
			echo "<tr>";	
			// ====================================================================================
			// Acompleta Cabecera 			 
			
			for ($m=0; $m<$registros; $m++)	
			{ 	
				echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Licencias</td>";	
				echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Monto USD</td>";	
				echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Monto MXN</td>";	
			}	
			echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Licencias</td>";	
			echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Monto USD</td>";	
			echo "<td width='$TheWidth' class='ui-state-highlight align-center'>Monto MXN</td>";			
			// Fin Acompleta Cabecera
			// ====================================================================================
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
	echo "</table>";		
	
	}
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_LICENCIA" , "$id_login" ,   "id_direccion=$id_direccion_a id_proveedor=$id_proveedor id_cuenta=$id_cuenta"  ,"" ,"inf_inventario_proveedor_licencias_matriz.php");
	 //<\BITACORA>
	 
	 
	mysqli_close($mysql);
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      