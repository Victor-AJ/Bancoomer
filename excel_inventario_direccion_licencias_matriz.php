<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
session_start();
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();		
	
	if 	(isset($_SESSION['sess_user']))
		$id_login = $_SESSION['sess_iduser'];

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>.:: CSI: Bancomer Control Servicios Inform&aacute;ticos v2.0 ::.</title>
<link rel="stylesheet" type="text/css" media="screen" href="css/ui-personal/jquery-ui-1.7.2.custom.css"/> 
<link rel="stylesheet" type="text/css" media="screen" href="css/load.css"/> 
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script language="javascript">
$(document).ready(function() {
	$(".botonExcel").click(function(event) {
		$("#datos_a_enviar").val( $("<div>").append( $("#Exportar_a_Excel").eq(0).clone()).html());
		$("#FormularioExportacion").submit();
	});
			
});
</script>
<style type="text/css">
.botonExcel{cursor:pointer;}
</style>
</head>
<body>
<form action="excel_inventario_eficiencia_e.php" method="post" target="_blank" id="FormularioExportacion">
<input type="hidden" id="datos_a_enviar" name="datos_a_enviar"/>
	<div id="divLoading" align="center"><strong>Por favor espere...<br><br></strong><img alt="" src="images/ajax-loader-bert-1.gif"/></div>
	<table width="100%">
    	<tr>
        	<td width="50%" align="left"><img alt="" src="images/bbvabancomer.png" height="41px"></td>
            <td width="50%" align="right"><img alt="" src="images/asset.png" height="41px"></td>
        </tr>
       	<tr>
        	<td colspan="2" align="center" style="font-size:16px;width:100%;">REPORTE DE INVENTARIO POR DIRECCION A NIVEL DE EMPLEADO</td>
        </tr>
    </table>    
	<div align="center"><img src="images/logo_excel.jpg" class="botonExcel" alt="Presione para exportar a EXCEL"/></div>
<?php

	
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
			"tx_nombre_corto"	=>$rowd["tx_nombre_corto"]
		);
	} 	
	
	for ($k=0; $k < count($TheDireccion); $k++)
	{ 	        			 
		while ($elemento = each($TheDireccion[$k]))				
			$tx_nombre_corto = $TheDireccion[$k]["tx_nombre_corto"];	
	}
	
	# ===============================================================================
	# BUSCA NOMBRE DEL PROVEEDOR
	# ===============================================================================
	
	if ($tx_proveedor_opcion == 0)
	{
		$tx_proveedor_corto = "TOTAL";	
	} 
	else if ($tx_proveedor_opcion == 2) 
	{	
		$tx_proveedor_corto = "OTROS";	
	} 
	else 
	{			
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
		
		for ($k=0; $k < count($TheProveedor); $k++)
		{ 	        			 
			while ($elemento = each($TheProveedor[$k]))				
				$tx_proveedor_corto = $TheProveedor[$k]['tx_proveedor_corto'];	
		}
	}
			
	// ============================================================
	// BUSCA INFORMACION DEL PROVEEDOR Y PRODUCTOS
	// ============================================================
	
	if ($id_direccion_a==0) 
	{	
		$sql = "   SELECT d.id_producto, tx_producto ";
		$sql.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f  ";
		$sql.= "    WHERE a.id_proveedor	= $id_proveedor ";
		$sql.= "      AND a.id_proveedor	= d.id_proveedor and a.tx_indicador='1' and  d.tx_indicador='1'   ";
		$sql.= "      AND b.id_empleado 	= c.id_empleado  and b.tx_indicador='1'  ";
		$sql.= "      AND b.id_producto 	= d.id_producto ";
		$sql.= "      AND c.id_centro_costos= e.id_centro_costos and e.tx_indicador='1' ";
		$sql.= "      AND e.id_direccion 	= f.id_direccion  and f.tx_indicador='1' ";
		$sql.= " AND e.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
		
		$sql.= " GROUP BY d.id_producto, tx_producto ";						
		$sql.= " ORDER BY d.id_producto, tx_producto ";								
	} 
	else 
	{	
		$sql = "   SELECT d.id_producto, tx_producto ";
		$sql.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f  ";
		$sql.= "    WHERE a.id_proveedor	= d.id_proveedor and a.tx_indicador='1' and  d.tx_indicador='1'";
		if ($tx_proveedor_opcion == 1) 
			$sql.= " AND a.id_proveedor	= $id_proveedor ";
		else if ($tx_proveedor_opcion == 2) 
			$sql.= " AND a.id_proveedor NOT IN ($id_proveedor, $id_proveedor_1) ";
		
		$sql.= "      AND b.id_empleado 	= c.id_empleado and b.tx_indicador='1' ";
		$sql.= "      AND b.id_producto 	= d.id_producto ";
		$sql.= "      AND c.id_centro_costos= e.id_centro_costos and e.tx_indicador='1' ";
		$sql.= "      AND e.id_direccion 	= f.id_direccion and f.tx_indicador='1' ";
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

	echo "<br>";	
	if ($registros==0) { 
		echo "<table id='Exportar_a_Excel' align='center' width='100%' border='1' cellspacing='1' cellpadding='1' style='font-size:11px;width:100%;'>";	
		echo "<tr>";
		echo "<td align='center'>Sin informaci&oacute;n ...</td>";		
		echo "</tr>";					
		echo "</table>";
		
	} else {	
		// ==============================================================
		// Empieza Reporte
		// ==============================================================
		echo "<table id='Exportar_a_Excel' align='center' width='100%' border='1' cellspacing='1' cellpadding='1' style='font-size:11px;width:100%;'>";	
		if ($id_cuenta==0) echo "<tr><td colspan='$cabecera_titulos' align='center' bgcolor='#003366' style='font-size:16px;width:100%;'><font color=white><strong>INVENTARIO DE $tx_nombre_corto - $tx_proveedor_corto</strong></font></td></tr>";	
		else echo "<tr><td colspan='3' align='center' bgcolor='#003366' style='font-size:16px;width:100%;'><font color=white><strong>INVENTARIO DE $tx_nombre_corto - $tx_proveedor_corto</strong></font></td></tr>";	
		echo "<tr>";	
			
			# ====================================================================================
			# Cabecera			 
			# ====================================================================================
		
			for ($a=0; $a<3; $a++)
			{
				switch ($a) {   
					case 0 : $TheField='#'; break;
					case 1 : $TheField='-'; break;	
					case 2 : $TheField='Nombre'; break;					
				}		
				echo "<td rowspan='3' align='center' bgcolor='#003366'><font color=white>$TheField</td>";
			}	
			
			for ($i=0; $i < count($TheInformeProducto); $i++) 
			{ 	        			 
				while ($elemento = each($TheInformeProducto[$i]))					  		
					$id_producto	=$TheInformeProducto[$i]["id_producto"];
					$tx_producto	=$TheInformeProducto[$i]["tx_producto"];
					echo "<td colspan='3' align='center' bgcolor='#003366'><font color=white>$tx_producto</font></td>";	
			}					
			echo "<td colspan='3' align='center' bgcolor='#003366'><font color=white>TOTALES</font></td>";	
					
		echo "</tr>";	
		
		# ====================================================================================
		# Acompleta Cabecera 			 
		# ====================================================================================
			
		echo "<tr>";	
			for ($m=0; $m<$registros; $m++)	{ 									
				echo "<td rowspan='2' width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";
				echo "<td colspan='2' width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
			}
			echo "<td rowspan='2' width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";
			echo "<td colspan='2' width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
		echo "</tr>";
		echo "<tr>";
			for ($m=0; $m<$registros; $m++)	{
				echo "<td width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Mensual</font></td>";	
				echo "<td width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Anual</font></td>";				
			}
			echo "<td width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Mensual</font></td>";	
			echo "<td width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Anual</font></td>";	
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
				$sql1.= "    WHERE a.id_proveedor		= $id_proveedor  and a.tx_indicador='1' ";
				$sql1.= "      AND a.id_proveedor		= d.id_proveedor  and d.tx_indicador='1' ";
				$sql1.= "      AND b.id_empleado 		= c.id_empleado  and b.tx_indicador='1' ";
				$sql1.= "      AND b.id_producto 		= d.id_producto   ";
				$sql1.= "      AND c.id_centro_costos 	= e.id_centro_costos  and e.tx_indicador='1'  ";
				$sql1.= "      AND e.id_direccion 		= f.id_direccion  and f.tx_indicador='1' ";
				$sql1.= "      AND e.id_subdireccion 	= g.id_subdireccion  and g.tx_indicador='1'  ";
				$sql1.= " AND e.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
		
				$sql1.= " GROUP BY f.tx_nombre, tx_subdireccion, tx_empleado ";
				$sql1.= " ORDER BY f.tx_nombre, tx_subdireccion, tx_empleado ";	
			} else {
				$sql1 = "   SELECT c.id_empleado, f.id_direccion, f.tx_nombre, tx_empleado, g.id_subdireccion, tx_subdireccion, c.tx_indicador, tx_notas ";
				$sql1.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f, tbl_subdireccion g  ";
				$sql1.= "    WHERE a.id_proveedor		= d.id_proveedor ";
				if ($tx_proveedor_opcion == 1) $sql1.= " AND a.id_proveedor	= $id_proveedor ";
				else if ($tx_proveedor_opcion == 2) $sql1.= " AND a.id_proveedor NOT IN ($id_proveedor, $id_proveedor_1) ";
				$sql1.= "      AND b.id_empleado 		= c.id_empleado and a.tx_indicador='1' and d.tx_indicador='1' ";
				$sql1.= "      AND b.id_producto 		= d.id_producto and b.tx_indicador='1' ";
				$sql1.= "      AND c.id_centro_costos 	= e.id_centro_costos ";
				$sql1.= "      AND e.id_direccion 		= f.id_direccion and e.tx_indicador='1'";
				$sql1.= "      AND f.id_direccion 		= $id_direccion_a and f.tx_indicador='1' ";
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
					
					if ($tx_indicador=="0") $tx_color = "#FF3333";
					else $tx_color = "";
				
				$j++;
				
				if ($k==0)	{
					echo "<tr>";							
					echo "<td colspan='$cabecera_titulos' bgcolor='#c8ddf2'><em>$tx_direccion</em></td>";						
					echo "</tr>";	
					echo "<tr>";							
					echo "<td align=letf bgcolor='#c8ddf2'>-</td>";	
					echo "<td colspan='$cabecera_titulos-2' bgcolor='#c8ddf2'><em>$tx_subdireccion</em></td>";						
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
						echo "<td align='left' colspan='$cabecera_titulos' bgcolor='#c8ddf2'><em>$tx_direccion</em></td>";						
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
					if ($id_subdireccion_tmp==$id_subdireccion) { }					
					else {
						echo "<tr>";	
						echo "<td align='center' bgcolor='#c8ddf2'>-</td>";							
						echo "<td align='left' colspan='$cabecera_titulos-2' bgcolor='#c8ddf2'><em>$tx_subdireccion</em></td>";						
						echo "</tr>";	
						$id_subdireccion_tmp=$id_subdireccion;
					}
				} else {				
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
								echo "<td align='center' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;	
						case 1:	if ($tx_indicador=="0") $TheColumn="BAJA"; 
								else $TheColumn="ACTIVO";																	
								echo "<td align='center' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;					
						case 2:	$TheColumn=$tx_empleado;	
								echo "<td align='left' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;
					}								
				}	
				
				$in_licencia_renglon=0;
				$fl_precio_usd_renglon=0;
				$fl_precio_anual_renglon=0;
				
				for ($i=0; $i < count($TheInformeProducto); $i++)
				{ 	        			 
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
								$id_producto =$TheInformeProductoId[$q]["id_producto"];		
						}	
						
						$in_licencia_pan=0;
						$fl_precio_usd_pan=0;																			
						$fl_precio_mxn_pan=0;
						
						// ==================================
						// Fin Producto
						// ==================================
												
						//$sql2 = "   SELECT in_licencia, IF (d.id_moneda=1, fl_precio/14, fl_precio ) AS fl_precio ";
						
						if ($tx_moneda=="USD") $sql2 = " SELECT in_licencia, fl_precio + (d.fl_precio_mxn / $fl_tipo_cambio) AS fl_precio ";
						else $sql2 = " SELECT in_licencia, (fl_precio * $fl_tipo_cambio) + d.fl_precio_mxn AS fl_precio ";				
						$sql2.= "     FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f  ";
						$sql2.= "    WHERE a.id_proveedor= d.id_proveedor and a.tx_indicador='1' and d.tx_indicador='1'  ";
						if ($tx_proveedor_opcion == 1) 
						$sql2.= "      AND a.id_proveedor		= $id_proveedor ";
						else if ($tx_proveedor_opcion == 2) 
						$sql2.= " AND a.id_proveedor NOT IN ($id_proveedor, $id_proveedor_1) ";
						
						$sql2.= "      AND b.id_producto 		= $id_producto and b.tx_indicador='1' ";
						$sql2.= "      AND b.id_producto 		= d.id_producto ";
						$sql2.= "      AND c.id_centro_costos 	= e.id_centro_costos and e.tx_indicador='1'";
						$sql2.= "      AND e.id_direccion 		= f.id_direccion and f.tx_indicador='1'";
						$sql2.= " AND e.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
		
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
								'fl_precio_usd'	=>$row["fl_precio"]							
							);
						} 		
												
						if ($count2==0) {
						
							$in_licencia_pan="-";
							$fl_precio_usd_pan="-";																			
							$fl_precio_anual_pan="-";
						
						} else {
						
							for ($p=0; $p < count($TheInformeDetalle); $p++) { 	        			 
								while ($elemento = each($TheInformeDetalle[$p]))					  		
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
						echo "<td align='center' bgcolor='$tx_color'>$in_licencia_pan</td>";	
						echo "<td align='right' bgcolor='$tx_color'>$fl_precio_usd_pan</td>";	
						echo "<td align='right' bgcolor='$tx_color'>$fl_precio_anual_pan</td>";	
					}	
					
					if ($in_licencia_renglon==NULL) $in_licencia_renglon="-";
					else 
					{
						//$in_licencia_renglon_total=$in_licencia_renglon_total + $in_licencia_renglon;
						$in_licencia_renglon=number_format($in_licencia_renglon,0);
					}	
					
					if ($fl_precio_usd_renglon==NULL) $fl_precio_usd_renglon=number_format($fl_precio_usd_renglon,0);
					else 
					{
						//$fl_precio_usd_renglon_total=$fl_precio_usd_renglon_total + fl_precio_usd_renglon;
					 	$fl_precio_usd_renglon=number_format($fl_precio_usd_renglon,0);
					}	
					
					if ($fl_precio_anual_renglon==NULL) $fl_precio_anual_renglon=number_format($fl_precio_anual_renglon,0);	
					else 
					{ 
						//$fl_precio_mxn_renglon_total=$fl_precio_mxn_renglon_total + $fl_precio_mxn_renglon;
						$fl_precio_anual_renglon=number_format($fl_precio_anual_renglon,0);					
					}	
					
					echo "<td align='center' bgcolor='$tx_color'>$in_licencia_renglon</td>";	
					echo "<td align='right' bgcolor='$tx_color'>$fl_precio_usd_renglon</td>";	
					echo "<td align='right' bgcolor='$tx_color'>$fl_precio_anual_renglon</td>";						
				echo "</tr>";					
			}
			
			echo "<tr>";
			$TheField='TOTALES';
			echo "<td colspan='3' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";
			//echo "<br>";
			//echo "aaaa",$id_direccion_a;
						
			if ($id_direccion_a==0) 	
			{	
				//$sql2 = "    SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, IF (d.id_moneda=1,SUM( fl_precio / 14 ), SUM( fl_precio )) AS fl_precio_usd_total, SUM( fl_precio_mxn ) AS fl_precio_mxn_total ";
				
				
				if ($tx_moneda=="USD") $sql2 = " SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, SUM(fl_precio) + SUM(d.fl_precio_mxn / $fl_tipo_cambio) AS fl_precio_usd_total ";
				else $sql2 = " SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, SUM(fl_precio * $fl_tipo_cambio) + d.fl_precio_mxn  AS fl_precio_usd_total ";				
				$sql2.= "      FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f ";
				$sql2.= "    WHERE a.id_proveedor 		= $id_proveedor and a.tx_indicador='1'  ";
				$sql2.= "      AND a.id_proveedor	 	= d.id_proveedor and d.tx_indicador='1' ";
				$sql2.= "      AND b.id_empleado 		= c.id_empleado and b.tx_indicador='1' ";
				$sql2.= "      AND b.id_producto 		= d.id_producto ' ";
				$sql2.= "      AND c.id_centro_costos 	= e.id_centro_costos and e.tx_indicador='1' ";
				$sql2.= "      AND e.id_direccion 		= f.id_direccion and f.tx_indicador='1' ";
				$sql2.= " AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
		
				$sql2.= " GROUP BY d.id_producto, tx_producto ";
				$sql2.= " ORDER BY d.id_producto, tx_producto "; 	
			} 
			else 
			{
				//$sql2 = "    SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, IF (d.id_moneda=1,SUM( fl_precio / 14 ), SUM( fl_precio )) AS fl_precio_usd_total, SUM( fl_precio_mxn ) AS fl_precio_mxn_total ";
				
				if ($tx_moneda=="USD") $sql2 = " SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, SUM(fl_precio) + SUM(d.fl_precio_mxn / $fl_tipo_cambio) AS fl_precio_usd_total ";
				else $sql2 = " SELECT d.id_producto, tx_producto, SUM( in_licencia ) AS in_licencia_total, SUM(fl_precio * $fl_tipo_cambio) + d.fl_precio_mxn  AS fl_precio_usd_total ";						
				$sql2.= "      FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f ";
				$sql2.= "    WHERE a.id_proveedor	 	= d.id_proveedor and a.tx_indicador='1' and d.tx_indicador='1'";
				if ($tx_proveedor_opcion == 1) $sql2.= " AND a.id_proveedor 	= $id_proveedor ";
				else if ($tx_proveedor_opcion == 2) $sql2.= " AND a.id_proveedor NOT IN ($id_proveedor, $id_proveedor_1) ";
				$sql2.= "      AND b.id_empleado 		= c.id_empleado and b.tx_indicador='1'";
				$sql2.= "      AND b.id_producto 		= d.id_producto ";
				$sql2.= "      AND c.id_centro_costos 	= e.id_centro_costos and e.tx_indicador='1' ";
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
					"in_licencia_total"		=>$row["in_licencia_total"],
					"fl_precio_usd_total"	=>$row["fl_precio_usd_total"]				
				);
			} 
						
			for ($p=0; $p < count($TheInformeTotal); $p++)
			{ 	        			 
				while ($elemento = each($TheInformeTotal[$p]))					  		
					$in_licencia_total		=$TheInformeTotal[$p]["in_licencia_total"];
					$fl_precio_usd_total	=$TheInformeTotal[$p]["fl_precio_usd_total"];
					$fl_precio_anual_total	=$fl_precio_usd_total * $in_anio;
														
					if ($in_licencia_total==NULL) $in_licencia_tota="-";											
					echo "<td align='center' bgcolor='#003366'><font color=white>$in_licencia_total</td>";								
					$in_licencia_renglon_total=$in_licencia_renglon_total+$in_licencia_total;
					
					if ($fl_precio_usd_total==NULL) $fl_precio_usd_total="-";
					else {
						$fl_precio_usd_renglon_total=$fl_precio_usd_renglon_total+$fl_precio_usd_total;	
						$fl_precio_usd_total=number_format($fl_precio_usd_total,0);		
					}	
					echo "<td align='center' bgcolor='#003366'><font color=white>$fl_precio_usd_total</td>";												
						
					if ($fl_precio_anual_total==NULL) $fl_precio_anual_total="-";
					else {
						$fl_precio_anual_renglon_total=$fl_precio_anual_renglon_total+$fl_precio_anual_total;		
						$fl_precio_anual_total=number_format($fl_precio_anual_total,0);	
					}	
					
					echo "<td align='center' bgcolor='#003366'><font color=white>$fl_precio_anual_total</font></td>";								
			}	
				
				if ($fl_precio_usd_renglon_total==NULL) $fl_precio_usd_renglon_total = number_format($fl_precio_usd_renglon_total,0);
				else $fl_precio_usd_renglon_total = number_format($fl_precio_usd_renglon_total,0);
				
				if ($fl_precio_anual_renglon_total==NULL) $fl_precio_anual_renglon_total = number_format($fl_precio_anual_renglon_total,0);
				else $fl_precio_anual_renglon_total = number_format($fl_precio_anual_renglon_total,0);
			
				echo "<td align='center' bgcolor='#003366'><font color=white>$in_licencia_renglon_total</font></td>";	
				echo "<td align='center' bgcolor='#003366'><font color=white>$fl_precio_usd_renglon_total</font></td>";
				echo "<td align='center' bgcolor='#003366'><font color=white>$fl_precio_anual_renglon_total</font></td>";									
			echo "</tr>";
			
			echo "<tr>";			
			for ($a=0; $a<3; $a++) {
				switch ($a) {   
					case 0 : $TheField='#'; break;
					case 1 : $TheField='-'; break;		
					case 2 : $TheField='Nombre'; break;					
				}		
				echo "<td rowspan='4' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";
			}	
			echo "</tr>";
			
			echo "<tr>";
			for ($m=0; $m<$registros; $m++)	{
			
				echo "<td rowspan='2' width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";
				echo "<td width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Mensual</font></td>";	
				echo "<td width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Anual</font></td>";	
							
			}
			echo "<td rowspan='2' width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";
			echo "<td width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Mensual</font></td>";	
			echo "<td width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Anual</font></td>";	
			echo "</tr>";				
			echo "<tr>";	
			for ($m=0; $m<$registros; $m++)	{ 	
				echo "<td colspan='2' width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
			}			
			echo "<td colspan='2' width='$TheWidth' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
			echo "</tr>";		
			echo "<tr>";			
			for ($i=0; $i < count($TheInformeProducto); $i++)
			{ 	        			 
				while ($elemento = each($TheInformeProducto[$i]))					  		
					$id_producto	=$TheInformeProducto[$i]['id_producto'];
					$tx_producto	=$TheInformeProducto[$i]['tx_producto'];
					echo "<td colspan='3' align='center' bgcolor='#003366'><font color=white>$tx_producto</font></td>";	
			}
			echo "<td colspan='3' align='center' bgcolor='#003366'><font color=white>TOTALES</font></td>";			
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
	}
	
	$valBita= "id_direccion_a=$id_direccion_a ";		
	$valBita.= "id_proveedor=$id_proveedor ";	
	$valBita.= "id_proveedor_1=$id_proveedor_1 ";	
	$valBita.= "fl_tipo_cambio=$fl_tipo_cambio ";		
	$valBita.= "tx_moneda=$tx_moneda ";
	
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_LICENCIA" , "$id_login" ,   $valBita ,"" ,"excel_inventario_direccion_licencias_matriz.php");
	 //<\BITACORA>
	 
	mysqli_close($mysql);
	?>
    <script language="JavaScript">	
		$("#divLoading").hide();	
	</script> 
</form>
</body>
</html>