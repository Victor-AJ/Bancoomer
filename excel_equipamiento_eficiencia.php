<?php
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
session_start();
if 	(isset($_SESSION['sess_user']))
	$id_login = $_SESSION['sess_iduser'];


	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();		

?>

<html>
<head>
<title>.:: CSI: Bancomer Control Servicios Inform&aacute;ticos v2.0 ::.</title>
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
       	  <td colspan="2" align="center" style="font-size:16px;width:100%;">REPORTE DE EQUIPAMIENTO</td>
       	</tr>
    </table>   
<div align="center"><img src="images/logo_excel.jpg" class="botonExcel" alt="Presione para exportar a EXCEL"></div>
<?php

	$par_direccion 		= $_GET["par_direccion"];			
	$par_subdireccion 	= $_GET["par_subdireccion"];				
	$par_departamento 	= $_GET["par_departamento"];				
	$par_equipo 		= $_GET["par_equipo"];				
	$par_status 		= $_GET["par_status"];			
	$par_equipo_com		= $_GET["par_equipo_com"];			
	$par_marca 			= $_GET["par_marca"];			
	$par_modelo 		= $_GET["par_modelo"];				
	
	$tipo_cambio = 13.50;	
	
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
	
		$sql = "   SELECT a.id_empleado, tx_registro, tx_empleado, a.tx_indicador, tx_nombre_corto, d.id_subdireccion, tx_subdireccion, e.id_departamento, e.tx_departamento, tx_notas, tx_nombre as tx_direccion ";
		$sql.= " 	 FROM tbl_empleado a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e ";
		$sql.= "    WHERE a.id_centro_costos  	= b.id_centro_costos ";
		$sql.= "	  AND b.id_direccion 		= $par_direccion ";
		$sql.= "	  AND b.id_direccion 		= c.id_direccion ";
		$sql.= "	  AND b.id_subdireccion 	= d.id_subdireccion ";
		$sql.= "	  AND b.id_departamento 	= e.id_departamento ";
		if ($par_status=="2"){}
		else $sql.= "	  AND a.tx_indicador 		= $par_status ";
		$sql.= " ORDER BY tx_subdireccion, e.tx_departamento, tx_empleado ";
		
	} else {	
		
		if ($par_departamento==0) {
		
			$sql = "   SELECT a.id_empleado, tx_registro, tx_empleado, a.tx_indicador, tx_nombre_corto, d.id_subdireccion, tx_subdireccion, e.id_departamento, e.tx_departamento, tx_notas,  tx_nombre as tx_direccion ";
			$sql.= " 	 FROM tbl_empleado a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e ";
			$sql.= "    WHERE a.id_centro_costos  	= b.id_centro_costos ";
			$sql.= "	  AND b.id_direccion 		= $par_direccion ";
			$sql.= "	  AND b.id_direccion 		= c.id_direccion ";
			$sql.= "	  AND b.id_subdireccion 	= $par_subdireccion ";
			$sql.= "	  AND b.id_subdireccion 	= d.id_subdireccion ";
			$sql.= "	  AND b.id_departamento 	= e.id_departamento ";
			if ($par_status=="2"){}
			else $sql.= "	  AND a.tx_indicador 		= $par_status ";
			$sql.= " ORDER BY e.tx_departamento, tx_empleado ";
			
		} else {
		
			$sql = "   SELECT a.id_empleado, tx_registro, tx_empleado, a.tx_indicador, tx_nombre_corto, d.id_subdireccion, tx_subdireccion, e.id_departamento, e.tx_departamento, tx_notas, tx_nombre as tx_direccion ";
			$sql.= " 	 FROM tbl_empleado a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e ";
			$sql.= "    WHERE a.id_centro_costos  	= b.id_centro_costos ";
			$sql.= "	  AND b.id_direccion 		= $par_direccion ";
			$sql.= "	  AND b.id_direccion 		= c.id_direccion ";
			$sql.= "	  AND b.id_subdireccion 	= $par_subdireccion ";
			$sql.= "	  AND b.id_subdireccion 	= d.id_subdireccion ";
			$sql.= "	  AND b.id_departamento 	= e.id_departamento ";
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
			'tx_notas'			=>$row["tx_notas"],
			'tx_direccion'		=>$row["tx_direccion"]
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
	$cabecera=$reg_computo+7;
	$titulos=$reg_computo*2;
	$corte_1=$titulos+9;
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
		echo "<table id='Exportar_a_Excel' align='center' width='100%' border='1' cellspacing='1' cellpadding='1' style='font-size:11px;width:100%;'>";		
		echo "<tr><td colspan='$corte_1' align='center' bgcolor='#003366' style='font-size:16px;width:100%;'><font color=white><strong>EQUIPAMIENTO</strong></font></td></tr>";
		# ==============================================================
		# Pinta Cabecera
		# ==============================================================	
		echo "<tr>";							
			for ($a=0; $a<7; $a++)
			{
				switch ($a) {   
					case 0 : $TheWidth="2%"; $TheField="#"; break;
					case 1 : $TheWidth="15%"; $TheField="Direcci&oacute;n"; break;					
					case 2 : $TheWidth="15%"; $TheField="Subdireccion"; break;					
					case 3 : $TheWidth="15%"; $TheField="Departamento"; break;					
					case 4 : $TheWidth="6%"; $TheField="Estatus"; break;					
					case 5 : $TheWidth="6%"; $TheField="Registro"; break;					
					case 6 : $TheWidth="15%"; $TheField="Nombre"; break;					
			}		
			echo "<td width='$TheWidth' rowspan='3' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";
		}						
		echo "<td colspan='$titulos' align='center' bgcolor='#003366'><font color=white>COMPUTO</font></td>";
		echo "<td rowspan='2' colspan='2' align='center' bgcolor='#003366'><font color=white>TOTALES</font></td>";				
		echo "</tr>";	
			
		# ==============================================================
		# Pinta Cabecera de Computo
		# ==============================================================	
		echo "<tr>";	
		for ($a=0; $a < count($TheCatalogoComputo); $a++) { 	        			 
			while ($elemento = each($TheCatalogoComputo[$a]))					  		
				$tx_equipo	=$TheCatalogoComputo[$a]['tx_equipo'];
				echo "<td width='$TheWidthCom' colspan='2' align='center' bgcolor='#003366'><font color=white>$tx_equipo</font></td>";	
		}					
		echo "</tr>";					
			
		echo "<tr>";
		for ($a=0; $a < count($TheCatalogoComputo); $a++) { 					        			 
			echo "<td width='$TheWidthComDet' align='center' bgcolor='#003366'><font color=white>Unidades</font></td>";	
			echo "<td width='$TheWidthComDet' align='center' bgcolor='#003366'><font color=white>Costo</font></td>";				
		}	
		echo "<td width='$TheWidthComDet' align='center' bgcolor='#003366'><font color=white>Unidades</font></td>";	
		echo "<td width='$TheWidthComDet' align='center' bgcolor='#003366'><font color=white>Costo</font></td>";				
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
				$tx_direccion	=$TheCatalogoEmpleados[$i]['tx_direccion'];	
				
				if ($tx_indicador=="0") $tx_color = "#FF3333";					
				else $tx_color = "";
				
				$c++;							
				
			# =============================================
			# Pinta el Detalle
			# =============================================				
			echo "<tr>";	
			$in_unidades_renglon	=0;
			$fl_precio_usd_renglon	=0;
							
			for ($b=0; $b<7; $b++)
		 	{
				switch ($b) 
				{   
					case 0: $TheColumn=$c; 
							echo "<td bgcolor='$tx_color' align='center' valign='top'>$TheColumn</td>";
					break;	
					case 1: $TheColumn=$tx_direccion;	
							echo "<td bgcolor='$tx_color' align='left'>$TheColumn</td>";
					break;					
					case 2:	$TheColumn=$tx_subdireccion;	
							echo "<td bgcolor='$tx_color' align='left'>$TheColumn</td>";
					break;
					case 3:	$TheColumn=$tx_departamento;	
							echo "<td bgcolor='$tx_color' align='left'>$TheColumn</td>";
					break;
					case 4:	if ($tx_indicador=="0") $TheColumn="BAJA"; 
							else $TheColumn="ACTIVO";										
							echo "<td bgcolor='$tx_color' align='center'>$TheColumn</td>";
					break;
					case 5:	$TheColumn=$tx_registro;	
							echo "<td bgcolor='$tx_color' align='center' valign='top'>$TheColumn</td>";
					break;
					case 6:	$TheColumn=$tx_empleado;	
							echo "<td bgcolor='$tx_color' align='left' valign='top'>$TheColumn</td>";
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
					$sql2.= "      AND a.id_empleado 	= b.id_empleado ";
					$sql2.= "      AND b.id_computo 	= c.id_computo ";
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
						echo "<td bgcolor='$tx_color' align='center' valign='top'>$TheColumn</td>";
						echo "<td bgcolor='$tx_color' align='right' valign='top'>$TheColumn</td>";										
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
						
						echo "<td bgcolor='$tx_color' align='center' valign='top'>$in_computo</td>";
						echo "<td bgcolor='$tx_color' align='right' valign='top'>$total_precio_usd</td>";	
					}						
				}	
								
				if ($in_unidades_renglon==NULL) $in_unidades_renglon="-";
				else $in_unidades_renglon=number_format($in_unidades_renglon,0);
				
				if ($fl_precio_usd_renglon==NULL) $fl_precio_usd_renglon=number_format($fl_precio_usd_renglon,0);
				else $fl_precio_usd_renglon=number_format($fl_precio_usd_renglon,0);
				
				echo "<td bgcolor='$tx_color' align='center' valign='top'>$in_unidades_renglon</td>";
				echo "<td bgcolor='$tx_color' align='right' valign='top'>$fl_precio_usd_renglon</td>";									
			echo "</tr>";	
			}	
			
			# ==============================================================
			# Pinta Cabecera de Totales
			# ==============================================================	
			echo "<tr>";
			echo "<td colspan='7' align='center' bgcolor='#003366'><font color=white>TOTALES</totales></td>";				
			for ($a=0; $a < count($TheCatalogoComputo); $a++) { 					        			 
				$tx_equipo	="";				  		
				$tx_equipo	=$TheCatalogoComputo[$a]['tx_equipo'];		
							
				# ===========================================================================
				# Busca Empleados 
				# ===========================================================================
				
				if ($par_subdireccion==0) {
				
					$sql = "   SELECT count( * ) AS in_computo_total, SUM( g.fl_precio_usd ) + SUM( g.fl_precio_mxn / $tipo_cambio ) AS total_precio_usd ";
					$sql.= " 	 FROM tbl_empleado a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e, tbl_empleado_computo f, tbl_computo g ";
					$sql.= "    WHERE a.id_centro_costos  	= b.id_centro_costos ";
					$sql.= "	  AND b.id_direccion 		= $par_direccion ";
					$sql.= "	  AND b.id_direccion 		= c.id_direccion ";
					$sql.= "	  AND b.id_subdireccion 	= d.id_subdireccion ";
					$sql.= "	  AND b.id_departamento 	= e.id_departamento ";
					$sql.= "	  AND a.id_empleado 		= f.id_empleado ";
					$sql.= "      AND f.id_computo 			= g.id_computo ";
					$sql.= "      AND g.tx_equipo 			= '$tx_equipo' ";
					if ($par_status=="2"){}
					else $sql.= "	  AND a.tx_indicador 	= $par_status ";
					$sql.= " ORDER BY c.id_direccion  ";
					
				} else {	
					
					if ($par_departamento==0) 
					{					
						$sql = "   SELECT count( * ) AS in_computo_total, SUM( g.fl_precio_usd ) + SUM( g.fl_precio_mxn / $tipo_cambio ) AS total_precio_usd ";
						$sql.= " 	 FROM tbl_empleado a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e, tbl_empleado_computo f, tbl_computo g ";
						$sql.= "    WHERE a.id_centro_costos  	= b.id_centro_costos ";
						$sql.= "	  AND b.id_direccion 		= $par_direccion ";
						$sql.= "	  AND b.id_direccion 		= c.id_direccion ";
						$sql.= "	  AND b.id_subdireccion 	= $par_subdireccion ";
						$sql.= "	  AND b.id_subdireccion 	= d.id_subdireccion ";
						$sql.= "	  AND b.id_departamento 	= e.id_departamento ";
						$sql.= "	  AND a.id_empleado 		= f.id_empleado ";
						$sql.= "      AND f.id_computo 			= g.id_computo ";
						$sql.= "      AND g.tx_equipo 			= '$tx_equipo' ";
						if ($par_status=="2"){}
						else $sql.= "	  AND a.tx_indicador 	= $par_status ";
						$sql.= " ORDER BY c.id_direccion  ";						
					} 
					else 
					{					
						$sql = "   SELECT count( * ) AS in_computo_total, SUM( g.fl_precio_usd ) + SUM( g.fl_precio_mxn / $tipo_cambio ) AS total_precio_usd ";
						$sql.= " 	 FROM tbl_empleado a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e, tbl_empleado_computo f, tbl_computo g ";
						$sql.= "    WHERE a.id_centro_costos  	= b.id_centro_costos ";
						$sql.= "	  AND b.id_direccion 		= $par_direccion ";
						$sql.= "	  AND b.id_direccion 		= c.id_direccion ";
						$sql.= "	  AND b.id_subdireccion 	= $par_subdireccion ";
						$sql.= "	  AND b.id_subdireccion 	= d.id_subdireccion ";
						$sql.= "	  AND b.id_departamento 	= e.id_departamento ";
						$sql.= "	  AND e.id_departamento 	= $par_departamento ";
						$sql.= "	  AND a.id_empleado 		= f.id_empleado ";
						$sql.= "      AND f.id_computo 			= g.id_computo ";
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
					
					echo "<td width='$TheWidthComDet' align='center' bgcolor='#003366'><font color=white>$in_computo_total</font></td>";	
					echo "<td width='$TheWidthComDet' align='right' bgcolor='#003366'><font color=white>$total_precio_usd</font></td>";		
					
				// *********************************************************************************************************************************
				
			}	
			if ($in_computo_total_renglon==NULL) $in_computo_total_renglon="-";
			else $in_computo_total_renglon=number_format($in_computo_total_renglon,0);
				
			if ($total_precio_usd_renglon==NULL) $total_precio_usd_renglon=number_format($total_precio_usd_renglon,0);
			else $total_precio_usd_renglon=number_format($total_precio_usd_renglon,0);			
				
			echo "<td align='center' bgcolor='#003366'><font color=white>$in_computo_total_renglon</font></td>";
			echo "<td align='right' bgcolor='#003366'><font color=white>$total_precio_usd_renglon</font></td>";
			echo "</tr>";	
			echo "<tr>";
			echo "<td colspan='$titulos' align='left'>";	
			echo "<ul type='square'> ";
			echo "<li>Unidades calculada en base al Equipamiento.</em></li> ";
			echo "<li>Monto calculado en base al Equipamiento.</li> ";
			echo "<li>Monto en USD (D&oacute;lares Americano).</li>";
			echo "<li>Tipo de cambio $$tipo_cambio pesos por d&oacute;lar (Fuente: Control Econ&oacute;mico).</li>";
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
	$valBita.="par_marca=$par_marca  ";
	$valBita.="par_modelo=$par_modelo  ";		


	
	  //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_EMPLEADO_COMPUTO" , "$id_login" ,   $valBita ,"" ,"excel_equipamiento_eficiencia.php");
	 //<\BITACORA>


	mysqli_close($mysql);
?>
    <script language="JavaScript">	
		$("#divLoading").hide();	
	</script> 
</form>
</body>
</html>