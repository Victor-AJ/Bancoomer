<?php
session_start();
if 	(isset($_SESSION["sess_user"]))
	$id_login = $_SESSION['sess_iduser']
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
<input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
	<div id="divLoading" align="center"><strong>Por favor espere...<br><br></strong><img alt="" src="images/ajax-loader-bert-1.gif"/></div>
	<table width="100%">
    	<tr>
        	<td width="50%" align="left"><img alt="" src="images/bbvabancomer.png" height="41px"></td>
            <td width="50%" align="right"><img alt="" src="images/asset.png" height="41px"></td>
        </tr>
       	<tr>
        	<td colspan="2" align="center" style="font-size:16px;width:100%;">REPORTE DE INVENTARIO POR DIRECCION</td>
        </tr>
    </table>                             
	<div align="center"><img src="images/logo_excel.jpg" class="botonExcel" alt="Presione para exportar a EXCEL"/></div>
<?php
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();	
	
	$id_direccion	= $_GET["id_direccion"];		
	$fl_tipo_cambio = $_GET["fl_tipo_cambio"];
	$tx_moneda 		= $_GET["tx_moneda"];
	$par_cuenta		= $_GET["par_cuenta"];
	
	
	$id_proveedor_3 = 0;
	
	if ($id_direccion==0) $dispatch="insert";	
	else $dispatch="save";
		
	if ($dispatch=="save")
	{					
		if ($tx_moneda=="USD") $sql = " SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
		else $sql = " SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";		
		$sql.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e ";
		$sql.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1' ";
		$sql.= " AND a.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
		$sql.= "      AND b.id_direccion 		= $id_direccion ";		
		$sql.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
		$sql.= "      AND c.id_empleado 		= d.id_empleado  and d.tx_indicador='1' ";
		$sql.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1' ";
		
		if ($par_cuenta <> 0)
					$sql.= " 	AND e.id_cuenta_contable=  $par_cuenta "; 
		
		$sql.= " GROUP BY a.id_direccion ";
		$sql.= " ORDER BY a.id_direccion ";

	} 
	else if ($dispatch=="insert")
	{	
		if ($tx_moneda=="USD") $sql = " SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
		else $sql = " SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";		
		$sql.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e ";
		$sql.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1' ";
		$sql.= " AND a.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
		$sql.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
		$sql.= "      AND c.id_empleado 		= d.id_empleado  and d.tx_indicador='1' ";
		$sql.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1' ";
			
		if ($par_cuenta <> 0)
					$sql.= " 	AND e.id_cuenta_contable=  $par_cuenta "; 
	
					
		$sql.= " GROUP BY a.id_direccion ";
		$sql.= " ORDER BY total_precio_usd DESC, total_licencia DESC, a.id_direccion ";		
	}
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeDireccion[] = array(
			'id_direccion'		=>$row["id_direccion"],
			'tx_nombre'			=>$row["tx_nombre_corto"],
			'total_licencia'	=>$row["total_licencia"],
	  		'total_precio_usd'	=>$row["total_precio_usd"]
		);
	} 	
	
	$registros=count($TheInformeDireccion);	
	
	if ($registros==0) { }
	else 
	{		
		echo "<table id='Exportar_a_Excel' align='center' width='100%' border='1' cellspacing='1' cellpadding='1' style='font-size:11px;width:100%;'>";
        echo "<tr><td colspan='14' align='center' bgcolor='#003366' style='font-size:16px;width:100%;'><font color=white><strong>INVENTARIO POR DIRECCION</strong></font></td></tr>";		
		echo "<tr>";
		echo "<td width='3%' rowspan='3' align='center' bgcolor='#003366'><font color=white>#</font></td>";							 
		echo "<td width='20%' rowspan='3' align='center' bgcolor='#003366'><font color=white>DIRECCION</font></td>";					
		echo "<td width='18%' colspan='3' align='center' bgcolor='#003366'><font color=white>BLOOMBERG</font></td>";	
		echo "<td width='18%' colspan='3' align='center' bgcolor='#003366'><font color=white>REUTERS</font></td>";						 
		echo "<td width='18%' colspan='3' align='center' bgcolor='#003366'><font color=white>OTROS</font></td>";	
		echo "<td width='18%' colspan='3' align='center' bgcolor='#003366'><font color=white>TOTAL</font></td>";						 
		echo "</tr>";
		echo "<tr>";		
		echo "<td width='5%' rowspan='2' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";	
		echo "<td width='12%' colspan='2' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
		echo "<td width='5%' rowspan='2' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";	
		echo "<td width='12%' colspan='2' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";		
		echo "<td width='5%' rowspan='2' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";	
		echo "<td width='12%' colspan='2' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
		echo "<td width='5%' rowspan='2' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";			
		echo "<td width='12%' colspan='2' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
		echo "</tr>";
		echo "<tr>";		
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Mensual</font></td>";
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Anual</font></td>";		
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Mensual</font></td>";
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Anual</font></td>";		
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Mensual</font></td>";
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Anual</font></td>";	
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Mensual</font></td>";
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Anual</font></td>";
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
		
		for ($i=0; $i < count($TheInformeDireccion); $i++)
		{ 	        			 
			while ($elemento = each($TheInformeDireccion[$i]))				
				$id_direccion		=$TheInformeDireccion[$i]["id_direccion"];	  		
				$tx_nombre			=$TheInformeDireccion[$i]["tx_nombre"];
				$total_licencia		=$TheInformeDireccion[$i]["total_licencia"];
				$total_precio_usd	=$TheInformeDireccion[$i]["total_precio_usd"];
				
				$in_total_licencias	=$in_total_licencias+$total_licencia;
				$fl_total_precio_usd=$fl_total_precio_usd+$total_precio_usd;
				
				$c++;
				
				# ============================					
				# Busco Licencias de BLOOMBERG
				# ============================
				
				$tx_proveedor1 = "BLOOMBERG";				

				if ($tx_moneda=="USD" ) $sql1 = "   SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
				else $sql1 = "  SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";
				$sql1.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
				$sql1.= "    WHERE a.id_direccion 		= b.id_direccion  and a.tx_indicador='1' and b.tx_indicador='1' ";
				$sql1.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
				$sql1.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1' ";
				$sql1.= "      AND a.id_direccion 		= $id_direccion ";
				$sql1.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1' ";
				$sql1.= "      AND e.id_proveedor 		= f.id_proveedor and f.tx_indicador='1' ";
				$sql1.= "      AND f.tx_proveedor_corto = '$tx_proveedor1' ";
				
				if ($par_cuenta <> 0)
					$sql1.= " 	AND e.id_cuenta_contable=  $par_cuenta "; 
	
					
				$sql1.= " GROUP BY a.id_direccion ";
				$sql1.= " ORDER BY total_precio_usd DESC, total_licencia DESC, a.id_direccion ";
				
				//echo "sql",$sql1;	
				
				$result1 = mysqli_query($mysql, $sql1);	
				while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
				{	
					$TheInformeDireccion_1[] = array(
						'id_proveedor_1'	=>$row1["id_proveedor"],
						'total_licencia_1'	=>$row1["total_licencia"],
						'total_precio_usd_1'=>$row1["total_precio_usd"]
					);
				} 
				
				$num_result1=mysqli_num_rows($result1);	
				
				for ($j=0; $j < count($TheInformeDireccion_1); $j++)
				{ 	        			 
					while ($elemento = each($TheInformeDireccion_1[$j]))				
						$id_proveedor_1		=$TheInformeDireccion_1[$j]['id_proveedor_1'];	  		
						$total_licencia_1	=$TheInformeDireccion_1[$j]['total_licencia_1'];	  		
						$total_precio_usd_1	=$TheInformeDireccion_1[$j]['total_precio_usd_1'];	
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
				
				if ($tx_moneda=="USD") $sql2 = "   SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
				else $sql2 = "  SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";
				$sql2.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
				$sql2.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1' ";
				$sql2.= "      AND b.id_centro_costos 	= c.id_centro_costos  ";
				$sql2.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1'";
				$sql2.= "      AND a.id_direccion 		= $id_direccion ";
				$sql2.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1'";
				$sql2.= "      AND e.id_proveedor 		= f.id_proveedor and f.tx_indicador='1'";
				$sql2.= "      AND f.tx_proveedor_corto = '$tx_proveedor2' ";
				
				if ($par_cuenta <> 0)
					$sql2.= " 	AND e.id_cuenta_contable=  $par_cuenta "; 
	
				
				$sql2.= " GROUP BY a.id_direccion ";
				$sql2.= " ORDER BY total_precio_usd DESC, total_licencia DESC, a.id_direccion ";
				
				//echo "<br>";
				//echo "sql 2 ",$sql2;	
								
				$result2 = mysqli_query($mysql, $sql2);						
					
				while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
				{	
					$TheInformeDireccion_2[] = array(
						'id_proveedor_2'	=>$row2["id_proveedor"],
						'total_licencia_2'	=>$row2["total_licencia"],
						'total_precio_usd_2'=>$row2["total_precio_usd"]
					);
				} 
				
				$num_result2=mysqli_num_rows($result2);	
				
				for ($k=0; $k < count($TheInformeDireccion_2); $k++)
				{ 	        			 
					while ($elemento = each($TheInformeDireccion_2[$k]))
						$id_proveedor_2		=$TheInformeDireccion_2[$k]['id_proveedor_2'];	  						
						$total_licencia_2	=$TheInformeDireccion_2[$k]['total_licencia_2'];	  		
						$total_precio_usd_2	=$TheInformeDireccion_2[$k]['total_precio_usd_2'];	
				}
				
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
				
				if ($tx_moneda=="USD") $sql3 = "   SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
				else $sql3 = "  SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";
				$sql3.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
				$sql3.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1' ";
				$sql3.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
				$sql3.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1' ";
				$sql3.= "      AND a.id_direccion 		= $id_direccion ";
				$sql3.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1' ";
				$sql3.= "      AND e.id_proveedor 		= f.id_proveedor and f.tx_indicador='1' ";
				$sql3.= "      AND f.tx_proveedor_corto NOT IN ('$tx_proveedor3','$tx_proveedor4') ";
				
				if ($par_cuenta <> 0)
					$sql3.= " 	AND e.id_cuenta_contable=  $par_cuenta "; 
	
					
				$sql3.= " GROUP BY a.id_direccion ";
				$sql3.= " ORDER BY total_precio_usd DESC, total_licencia DESC, a.id_direccion ";
				
				//echo "<br>";
				//echo "sql 3 ",$sql3;	
								
				$result3 = mysqli_query($mysql, $sql3);						
					
				while ($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC))
				{	
					$TheInformeDireccion_3[] = array(
						'total_licencia_3'	=>$row3["total_licencia"],
						'total_precio_usd_3'=>$row3["total_precio_usd"]
					);
				} 
				
				$num_result3=mysqli_num_rows($result3);	
				
				for ($l=0; $l < count($TheInformeDireccion_3); $l++)
				{ 	        			 
					while ($elemento = each($TheInformeDireccion_3[$l]))	
						$total_licencia_3	= $TheInformeDireccion_3[$l]['total_licencia_3'];	  		
						$total_precio_usd_3	= $TheInformeDireccion_3[$l]['total_precio_usd_3'];	
				}						
				
				if ($num_result3==0)
				{
					$total_licencia_3=0;
					$total_precio_usd_3=0;
				}
				
				$in_total_licencias_3	= $in_total_licencias_3 +$total_licencia_3;
				$fl_total_precio_usd_3	= $fl_total_precio_usd_3+$total_precio_usd_3;
				$total_precio_usd_3_a   = $total_precio_usd_3 * 12;
				
				# ============================	
							
				echo "<tr>";							
				for ($a=0; $a<16; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$c; 									
								echo "<td align='center' valign='top'>$TheColumn</td>";
						break;						
						case 1: $TheColumn = $tx_nombre;
								echo "<td class='align-left' valign='top'>$TheColumn</td>";
						break;
						case 2: if ($total_licencia_1==0) $TheColumn="-";
								else $TheColumn = number_format($total_licencia_1,0); 								
								echo "<td align='center' valign='top'>$TheColumn</td>";
							break;																												
							case 3: if ($total_precio_usd_1==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_1,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;																												
							case 4: if ($total_precio_usd_1_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_1_a,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;																												
							case 5: if ($total_licencia_2==0) $TheColumn="-";									
									else $TheColumn = number_format($total_licencia_2,0); 
									echo "<td align='center' valign='top'>$TheColumn</td>";
							break;																												
							case 6: if ($total_precio_usd_2==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_2,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;	
							case 7: if ($total_precio_usd_2_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_2_a,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;																															
							case 8: if ($total_licencia_3==0) $TheColumn="-";									
									else $TheColumn = number_format($total_licencia_3,0); 
									echo "<td align='center' valign='top'>$TheColumn</td>";
							break;																												
							case 9: if ($total_precio_usd_3==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_3,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;
							case 10: if ($total_precio_usd_3_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_3_a,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;	
							case 11: if ($total_licencia==0) $TheColumn="-";
									else $TheColumn = number_format($total_licencia,0); 										
									echo "<td align='center' valign='top'>$TheColumn</td>";
							break;												
							case 12: if ($total_precio_usd==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;
							case 13: $total_precio_usd_a = $total_precio_usd * 12;
									if ($total_precio_usd_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_a,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;
						}							
					}				
					echo "</tr>";					
				}	
		echo "<tr>";								  
		for ($a=0; $a<13; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField="TOTALES";
						 echo "<td colspan='2' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 1 : if ($in_total_licencias_1==0) $TheField="-";
						 else $TheField=number_format($in_total_licencias_1,0); 	
					     echo "<td align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 2 : if ($fl_total_precio_usd_1==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_1,0); 
						 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 3 : $fl_total_precio_usd_1_a = $fl_total_precio_usd_1 * 12;
						 if ($fl_total_precio_usd_1_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_1_a,0); 
						 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 4 : if ($in_total_licencias_2==0) $TheField="-";
						 $TheField=number_format($in_total_licencias_2,0); 	
					     echo "<td align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 5 : if ($fl_total_precio_usd_2==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_2,0); 
						 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 6 : $fl_total_precio_usd_2_a = $fl_total_precio_usd_2 * 12;
						 if ($fl_total_precio_usd_2_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_2_a,0); 
						 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 7 : if ($in_total_licencias_3==0) $TheField="-";
						 else $TheField=number_format($in_total_licencias_3,0); 	
					     echo "<td align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 8 : if ($fl_total_precio_usd_3==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_3,0); 
					     echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 9 : $fl_total_precio_usd_3_a = $fl_total_precio_usd_3 * 12;
						 if ($fl_total_precio_usd_3_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_3_a,0); 
					     echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 10 :if ($in_total_licencias==0) $TheField="-";
						 else $TheField=number_format($in_total_licencias,0); 	
						 echo "<td align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 11 :if ($fl_total_precio_usd==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd,0); 
						 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 12 :$fl_total_precio_usd_a = $fl_total_precio_usd * 12;
						 if ($fl_total_precio_usd_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_a,0); 
						 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
			}							
		}	
		echo "</tr>";
		
		echo "<tr>";
		echo "<td colspan='14' align='left'>";	
        echo "<ul type='square'> ";
		echo "<li>Licencias calculadas en base al Inventario.</li> ";
        echo "<li>Monto calculado en base al Inventario.</li> ";
        echo "<li>Monto en  $tx_moneda.</li>";
        echo "<li>Tipo de cambio $fl_tipo_cambio pesos por d&oacute;lar.</li>";
        echo "</ul>";      
        echo "</td>";
		echo "</tr>";
		
//	echo "</table>";
	
	# ============= Subdireccion
	if ($dispatch=="save") 
	{							
		if ($tx_moneda=="USD") $sql = " SELECT a.id_subdireccion, a.tx_subdireccion, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
		else $sql = " SELECT a.id_subdireccion, a.tx_subdireccion, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";				
		$sql.= "	 FROM tbl_subdireccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e ";
		$sql.= "    WHERE a.id_subdireccion = b.id_subdireccion and a.tx_indicador='1' and b.tx_indicador='1'";
		$sql.= "      AND a.id_direccion 		= $id_direccion ";
		$sql.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
		$sql.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1'";
		$sql.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1'";
		
		if ($par_cuenta <> 0)
					$sql.= " 	AND e.id_cuenta_contable=  $par_cuenta "; 
	
		
		$sql.= " GROUP BY a.id_subdireccion ";
		$sql.= " ORDER BY total_precio_usd DESC , total_licencia ";

	} 
	else if ($dispatch=="insert")  
	{				
		if ($tx_moneda=="USD") $sql = " SELECT a.id_subdireccion, a.tx_subdireccion, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
		else $sql = " SELECT a.id_subdireccion, a.tx_subdireccion, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";	
		$sql.= "	 FROM tbl_subdireccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e ";
		$sql.= "    WHERE a.id_subdireccion = b.id_subdireccion and a.tx_indicador='1' and b.tx_indicador='1' ";
		$sql.= "      AND a.id_direccion 		= $id_direccion ";
		$sql.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
		$sql.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1'";
		$sql.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1'";
		
		if ($par_cuenta <> 0)
					$sql.= " 	AND e.id_cuenta_contable=  $par_cuenta "; 
		
					
					
		$sql.= " GROUP BY a.id_subdireccion ";
		$sql.= " ORDER BY total_precio_usd DESC , total_licencia ";		
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
	
//	echo "<table id='tablaInventarioSubdireccion' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		echo "<td width='3%' rowspan='3' align='center' bgcolor='#003366'><font color=white>#</font></td>";							 
		echo "<td width='23%' rowspan='3' align='center' bgcolor='#003366'><font color=white>DIRECCION</font></td>";					
		echo "<td width='18%' colspan='3' align='center' bgcolor='#003366'><font color=white>BLOOMBERG</font></td>";	
		echo "<td width='18%' colspan='3' align='center' bgcolor='#003366'><font color=white>REUTERS</font></td>";						 
		echo "<td width='18%' colspan='3' align='center' bgcolor='#003366'><font color=white>OTROS</font></td>";	
		echo "<td width='21%' colspan='3' align='center' bgcolor='#003366'><font color=white>TOTAL</font></td>";		
		echo "</tr>";	
		echo "<tr>";
		echo "<td width='5%' rowspan='2' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";	
		echo "<td width='12%' colspan='2' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
		echo "<td width='5%' rowspan='2' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";	
		echo "<td width='12%' colspan='2' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
		echo "<td width='5%' rowspan='2' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";	
		echo "<td width='12%' colspan='2' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
		echo "<td width='5%' rowspan='2' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";	
		echo "<td width='15%' colspan='2' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
		echo "</tr>";	
		echo "<tr>";		
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Mensual</font></td>";
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Anual</font></td>";		
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Mensual</font></td>";
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Anual</font></td>";		
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Mensual</font></td>";
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Anual</font></td>";	
		echo "<td width='6%' align='center' bgcolor='#003366'><font color=white>Mensual</font></td>";
		echo "<td width='9%' align='center' bgcolor='#003366'><font color=white>Anual</font></td>";
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
		
		for ($i=0; $i < count($TheInformeSubdireccion); $i++)
		{ 	        			 
			while ($elemento = each($TheInformeSubdireccion[$i]))				
				$id_subdireccion	=$TheInformeSubdireccion[$i]["id_subdireccion"];	  		
				$tx_subdireccion	=$TheInformeSubdireccion[$i]["tx_subdireccion"];
				$total_licencia		=$TheInformeSubdireccion[$i]["total_licencia"];
				$total_precio_usd	=$TheInformeSubdireccion[$i]["total_precio_usd"];
				
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
				$sql1.= "    WHERE a.id_subdireccion 	= b.id_subdireccion and a.tx_indicador='1' and b.tx_indicador='1'";
				$sql1.= "      AND a.id_subdireccion 	= $id_subdireccion ";
				$sql1.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
				$sql1.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1'";
				$sql1.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1'";
				$sql1.= "      AND e.id_proveedor 		= f.id_proveedor and f.tx_indicador='1'";
				$sql1.= "      AND f.tx_proveedor_corto = '$tx_proveedor1' ";
				if ($par_cuenta <> 0)
					$sql1.= " 	AND e.id_cuenta_contable=  $par_cuenta "; 
				
					
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
				
				for ($j=0; $j < count($TheInformeSubdireccion_1); $j++)	
				{ 	        			 
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
				$sql2.= "    WHERE a.id_subdireccion 	= b.id_subdireccion and a.tx_indicador='1' and b.tx_indicador='1' ";
				$sql2.= "      AND a.id_subdireccion 	= $id_subdireccion ";
				$sql2.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
				$sql2.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1' ";
				$sql2.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1'";
				$sql2.= "      AND e.id_proveedor 		= f.id_proveedor and f.tx_indicador='1'";
				$sql2.= "      AND f.tx_proveedor_corto = '$tx_proveedor2' ";
				
				if ($par_cuenta <> 0)
					$sql2.= " 	AND e.id_cuenta_contable=  $par_cuenta ";
					
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
				$sql3.= "    WHERE a.id_subdireccion 	= b.id_subdireccion and a.tx_indicador='1' and b.tx_indicador='1'";
				$sql3.= "      AND a.id_subdireccion 	= $id_subdireccion ";
				$sql3.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
				$sql3.= "      AND c.id_empleado 		= d.id_empleado and d.tx_indicador='1'";
				$sql3.= "      AND d.id_producto 		= e.id_producto and e.tx_indicador='1'";
				$sql3.= "      AND e.id_proveedor 		= f.id_proveedor and f.tx_indicador='1'";
				$sql3.= "      AND f.tx_proveedor_corto NOT IN ('$tx_proveedor3','$tx_proveedor4') ";
				
				if ($par_cuenta <> 0)
					$sql3.= " 	AND e.id_cuenta_contable=  $par_cuenta ";
					
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
									echo "<td align='center' valign='top'>$TheColumn</td>";
							break;						
							case 1: $TheColumn = "$tx_subdireccion";
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 2: $TheColumn = number_format($total_licencia_1,0); 
									echo "<td align='center' valign='top'>$TheColumn</td>";
							break;																												
							case 3: $TheColumn=number_format($total_precio_usd_1,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;	
							case 4: if ($total_precio_usd_1_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_1_a,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;																													
							case 5: $TheColumn =  number_format($total_licencia_2,0); 
									echo "<td align='center' valign='top'>$TheColumn</td>";
							break;																												
							case 6: $TheColumn=number_format($total_precio_usd_2,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;	
							case 7: if ($total_precio_usd_2_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_2_a,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;																											
							case 8: $TheColumn = number_format($total_licencia_3,0); 
									echo "<td align='center' valign='top'>$TheColumn</td>";
							break;																												
							case 9: $TheColumn=number_format($total_precio_usd_3,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;
							case 10: if ($total_precio_usd_3_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_3_a,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;	
							case 11: $TheColumn = $total_licencia;
								echo "<td align='center' valign='top'>$TheColumn</td>";
							break;												
							case 12: $TheColumn=number_format($total_precio_usd,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
							break;	
							case 13: $total_precio_usd_a = $total_precio_usd * 12;
									if ($total_precio_usd_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_a,0); 
									echo "<td align='right' valign='top'>$TheColumn</td>";
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
					echo "<td colspan='2' align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 1 : $TheField=number_format($in_total_licencias_1,0); 	
					echo "<td align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 2 : $TheField=number_format($fl_total_precio_usd_1,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 3 : $fl_total_precio_usd_1_a = $fl_total_precio_usd_1 * 12;
						 if ($fl_total_precio_usd_1_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_1_a,0); 
						 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 4 : $TheField=number_format($in_total_licencias_2,0); 	
					echo "<td align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 5 : $TheField=number_format($fl_total_precio_usd_2,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 6 : $fl_total_precio_usd_2_a = $fl_total_precio_usd_2 * 12;
						 if ($fl_total_precio_usd_2_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_2_a,0); 
						 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 7 : $TheField=number_format($in_total_licencias_3,0); 	
					echo "<td align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 8 : $TheField=number_format($fl_total_precio_usd_3,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 9 : $fl_total_precio_usd_3_a = $fl_total_precio_usd_3 * 12;
						 if ($fl_total_precio_usd_3_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_3_a,0); 
					     echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 10 : $TheField=number_format($in_total_licencias,0); 	
					echo "<td align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 11 : $TheField=number_format($fl_total_precio_usd,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;		
				case 12 :$fl_total_precio_usd_a = $fl_total_precio_usd * 12;
						 if ($fl_total_precio_usd_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_a,0); 
						 echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
			}							
		}		
		echo "<tr>";
		echo "<td colspan='14' align='left'>";	
        echo "<ul type='square'> ";
		echo "<li>Licencias calculadas en base al Inventario.</em></li> ";
        echo "<li>Monto calculado en base al Inventario a.</li> ";
        echo "<li>Monto en  $tx_moneda.</li>";
        echo "<li>Tipo de cambio $fl_tipo_cambio pesos por d&oacute;lar.</li>";
        echo "</ul>";      
        echo "</td>";
		echo "</tr>";
		
		# ============= Detalle
		
		if ($dispatch=="save") 
		{			
			$sql = "   SELECT tx_centro_costos, tx_nombre, tx_subdireccion, d.id_departamento, d.tx_departamento, tx_registro, f.id_empleado, tx_empleado, e.tx_indicador, e.tx_notas, SUM( in_licencia ) AS total_licencia, SUM( fl_precio ) AS total_precio_usd ";
			$sql.= "     FROM tbl_centro_costos a, tbl_direccion b, tbl_subdireccion c, tbl_departamento d, tbl_empleado e, tbl_licencia f, tbl_producto g ";
			$sql.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1'";
			$sql.= "      AND a.id_direccion 		= $id_direccion ";
			$sql.= "      AND a.id_subdireccion 	= c.id_subdireccion and c.tx_indicador='1' "; 
			$sql.= "      AND a.id_departamento 	= d.id_departamento and d.tx_indicador='1'";
		//	$sql.= "      AND c.id_subdireccion 	= $id_subdireccion ";
			$sql.= "      AND a.id_centro_costos 	= e.id_centro_costos  ";
			$sql.= "      AND e.id_empleado 		= f.id_empleado and f.tx_indicador='1'";
			$sql.= "      AND f.id_producto 		= g.id_producto and g.tx_indicador='1'";
			
			if ($par_cuenta <> 0)
					$sql.= " 	AND g.id_cuenta_contable=  $par_cuenta ";
					
					
			$sql.= " GROUP BY f.id_empleado ";
			$sql.= " ORDER BY tx_nombre, tx_subdireccion, d.tx_departamento, tx_empleado ";		
		} 
		else if ($dispatch=="insert")  
		{		
			$sql = "   SELECT tx_centro_costos, tx_nombre, tx_subdireccion, d.id_departamento, d.tx_departamento, tx_registro, f.id_empleado, tx_empleado, e.tx_indicador, e.tx_notas, SUM( in_licencia ) AS total_licencia, SUM( fl_precio ) AS total_precio_usd ";		
			$sql.= "     FROM tbl_centro_costos a, tbl_direccion b, tbl_subdireccion c, tbl_departamento d, tbl_empleado e, tbl_licencia f, tbl_producto g ";
			$sql.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1'  and b.tx_indicador='1' ";
			$sql.= "      AND a.id_direccion 		= $id_direccion ";
			$sql.= "      AND a.id_subdireccion 	= c.id_subdireccion and c.tx_indicador='1' "; 
			$sql.= "      AND a.id_departamento 	= d.id_departamento and d.tx_indicador='1' ";
		//  $sql.= "      AND c.id_subdireccion 	= $id_subdireccion ";
			$sql.= "      AND a.id_centro_costos 	= e.id_centro_costos ";
			$sql.= "      AND e.id_empleado 		= f.id_empleado and f.tx_indicador='1'";
			$sql.= "      AND f.id_producto 		= g.id_producto and g.tx_indicador='1'";
			
			if ($par_cuenta <> 0)
					$sql.= " 	AND g.id_cuenta_contable=  $par_cuenta ";
			
					
					
			$sql.= " GROUP BY f.id_empleado ";
			$sql.= " ORDER BY tx_nombre, tx_subdireccion, d.tx_departamento, tx_empleado ";		
		
	    }
		//echo " Departamento ",$sql;
		
		
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{		
			$TheInformeDepartamento[] = array(
				"tx_centro_costos"	=>$row["tx_centro_costos"],
				"tx_nombre"			=>$row["tx_nombre"],
				"tx_subdireccion"	=>$row["tx_subdireccion"],
				"id_departamento"	=>$row["id_departamento"],
				"tx_departamento"	=>$row["tx_departamento"],
				"tx_registro"		=>$row["tx_registro"],
				"id_empleado"		=>$row["id_empleado"],
				"tx_empleado"		=>$row["tx_empleado"],
				"tx_indicador"		=>$row["tx_indicador"],
				"tx_notas"			=>$row["tx_notas"],
				"total_licencia"	=>$row["total_licencia"],
	  			"total_precio_usd"	=>$row["total_precio_usd"]
			);
		} 			
		
		echo "<tr>";
		echo "<tr>";
		echo "<td width='3%' rowspan='2' align='center' bgcolor='#003366'><font color=white>#</font></td>";							 
		echo "<td width='3%' rowspan='2' align='center' bgcolor='#003366'><font color=white>-</font></td>";							 
		echo "<td width='8%' rowspan='2' align='center' bgcolor='#003366'><font color=white>Registro</font></td>";					
		echo "<td width='19%' rowspan='2' colspan='3' align='center' bgcolor='#003366'><font color=white>Nombre</font></td>";					
		echo "<td width='16%' colspan='2' align='center' bgcolor='#003366'><font color=white>BLOOMBERG</font></td>";	
		echo "<td width='16%' colspan='2' align='center' bgcolor='#003366'><font color=white>REUTERS</font></td>";						 
		echo "<td width='16%' colspan='2' align='center' bgcolor='#003366'><font color=white>OTROS</font></td>";	
		echo "<td width='19%' colspan='2' align='center' bgcolor='#003366'><font color=white>TOTAL</font></td>";						 
		echo "</tr>";	
		echo "<tr>";
		echo "<td width='7%' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";	
		echo "<td width='9%' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
		echo "<td width='7%' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";	
		echo "<td width='9%' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
		echo "<td width='7%' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";	
		echo "<td width='9%' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
		echo "<td width='7%' align='center' bgcolor='#003366'><font color=white>Licencias</font></td>";	
		echo "<td width='11%' align='center' bgcolor='#003366'><font color=white>Monto</font></td>";
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
		
		for ($i=0; $i < count($TheInformeDepartamento); $i++)
		{ 	        			 
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
					echo "<td  align='left' bgcolor='#c8ddf2' colspan='14'><em>$tx_subdireccion</em></td>";						
					echo "</tr>";	
					echo "<tr>";							
					echo "<td align='left' bgcolor='#c8ddf2'>-</td>";	
					echo "<td colspan='13' align='left' bgcolor='#c8ddf2'><em>$tx_departamento</em></td>";						
					echo "</tr>";	
				}		
				
				# =============================================
				# Corte 1 - Subdireccion
				# =============================================				
				if ($i>0)
				{			
					if ($tx_subdireccion_tmp==$tx_subdireccion) { }					
					else 
					{
						echo "<tr>";		
						echo "<td align='left' bgcolor='#c8ddf2' colspan='14'><em>$tx_subdireccion</em></td>";					
						echo "</tr>";	
						$tx_subdireccion_tmp=$tx_subdireccion;
					}
				} 
				else 
				{				
					$tx_subdireccion_tmp=$tx_subdireccion;
				}	
				
				# =============================================
				# Corte 2 - Departamento
				# =============================================				
				if ($i>0)
				{															
					if ($id_departamento_tmp==$id_departamento) { }					
					else {
						echo "<tr>";	
						echo "<td align='left' bgcolor='#c8ddf2'>-</td>";							
						echo "<td align='left' bgcolor='#c8ddf2' colspan='13'><em>$tx_departamento</em></td>";						
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
				$sql1.= "      AND a.id_direccion 		= $id_direccion ";
				$sql1.= "      AND a.id_subdireccion 	= c.id_subdireccion and c.tx_indicador='1' "; 
				$sql1.= "      AND a.id_departamento 	= d.id_departamento and d.tx_indicador='1' ";
			//	$sql1.= "      AND c.id_subdireccion 	= $id_subdireccion ";
				$sql1.= "      AND a.id_centro_costos 	= e.id_centro_costos  ";
				$sql1.= "      AND e.id_empleado 		= f.id_empleado and f.tx_indicador='1' ";
				$sql1.= "      AND e.id_empleado 		= $id_empleado ";
				$sql1.= "      AND f.id_producto 		= g.id_producto and g.tx_indicador='1'";
				$sql1.= "      AND g.id_proveedor 		= h.id_proveedor and h.tx_indicador='1'";
				$sql1.= "      AND h.tx_proveedor_corto  = '$tx_proveedor1' ";
				
				if ($par_cuenta <> 0)
					$sql1.= " 	AND g.id_cuenta_contable=  $par_cuenta ";
				
					
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
				
				for ($j=0; $j < count($TheInformeDepartamento_1); $j++)
				{ 	        			 
					while ($elemento = each($TheInformeDepartamento_1[$j]))				
						$total_licencia_1	=$TheInformeDepartamento_1[$j]["total_licencia_1"];	  		
						$total_precio_usd_1	=$TheInformeDepartamento_1[$j]["total_precio_usd_1"];	
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
				$sql2.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1' ";
				$sql2.= "      AND a.id_direccion 		= $id_direccion ";
				$sql2.= "      AND a.id_subdireccion 	= c.id_subdireccion and c.tx_indicador='1' "; 
				$sql2.= "      AND a.id_departamento 	= d.id_departamento and d.tx_indicador='1'";
				//$sql2.= "      AND c.id_subdireccion 	= $id_subdireccion ";
				$sql2.= "      AND a.id_centro_costos 	= e.id_centro_costos ";
				$sql2.= "      AND e.id_empleado 		= f.id_empleado and f.tx_indicador='1' ";
				$sql2.= "      AND e.id_empleado 		= $id_empleado ";
				$sql2.= "      AND f.id_producto 		= g.id_producto and g.tx_indicador='1'";
				$sql2.= "      AND g.id_proveedor 		= h.id_proveedor and h.tx_indicador='1'";
				$sql2.= "      AND h.tx_proveedor_corto = '$tx_proveedor2' ";
				if ($par_cuenta <> 0)
					$sql2.= " 	AND g.id_cuenta_contable=  $par_cuenta ";
				
					
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
				$sql3.= "    WHERE a.id_direccion 		= b.id_direccion and a.tx_indicador='1' and b.tx_indicador='1'";
				$sql3.= "      AND a.id_direccion 		= $id_direccion ";
				$sql3.= "      AND a.id_subdireccion 	= c.id_subdireccion and c.tx_indicador='1'"; 
				$sql3.= "      AND a.id_departamento 	= d.id_departamento and d.tx_indicador='1'";
				//$sql3.= "      AND c.id_subdireccion 	= $id_subdireccion ";
				$sql3.= "      AND a.id_centro_costos 	= e.id_centro_costos ";
				$sql3.= "      AND e.id_empleado 		= f.id_empleado and f.tx_indicador='1'";
				$sql3.= "      AND e.id_empleado 		= $id_empleado ";
				$sql3.= "      AND f.id_producto 		= g.id_producto and g.tx_indicador='1'";
				$sql3.= "      AND g.id_proveedor 		= h.id_proveedor and h.tx_indicador='1'";
				$sql3.= "      AND h.tx_proveedor_corto NOT IN ('$tx_proveedor3','$tx_proveedor4') ";
				if ($par_cuenta <> 0)
					$sql3.= " 	AND g.id_cuenta_contable=  $par_cuenta ";
					
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
				
				if ($tx_indicador=="0") $tx_color = "#FF3333";
				else $tx_color = "";
							
				for ($a=0; $a<12; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$c; 									
								echo "<td class='align-center' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;							
						case 1:	if ($tx_indicador=="0") $TheColumn="BAJA"; 
								else $TheColumn="ACTIVO";																	
								echo "<td align='center' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;
						case 2:	$TheColumn="$tx_registro";	
								echo "<td align='center' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;
						case 3:	$TheColumn="$tx_empleado";	
								echo "<td colspan='3' align='left' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;
						case 4: $TheColumn=number_format($total_licencia_1,0); 
								if ($total_licencia_1==0) $TheColumn="-";									
								echo "<td align='center' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;																												
						case 5: $TheColumn=number_format($total_precio_usd_1,0); 
								if ($total_precio_usd_1==0) $TheColumn="-";
								echo "<td align='right' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;																												
						case 6: $TheColumn=number_format($total_licencia_2,0); 
								if ($total_licencia_2==0) $TheColumn="-";	
								echo "<td align='center' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;																												
						case 7: $TheColumn=number_format($total_precio_usd_2,0); 
								if ($total_precio_usd_2==0) $TheColumn="-";
								echo "<td align='right' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;																												
						case 8: $TheColumn = number_format($total_licencia_3,0); 
								if ($total_licencia_3==0) $TheColumn="-";	
								echo "<td align='center' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;																												
						case 9: $TheColumn=number_format($total_precio_usd_3,0); 
								if ($total_precio_usd_3==0) $TheColumn="-";
								echo "<td align='right' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;
						case 10: $TheColumn = $total_licencia;
								echo "<td align='center' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
						break;												
						case 11: $TheColumn=number_format($total_precio_usd,0); 
								echo "<td align='right' bgcolor='$tx_color' valign='top'>$TheColumn</td>";
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
				case 0 : $TheField='Totales';
					echo "<td colspan='6' align='center' bgcolor='#003366'><font color=white>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
				break;
				case 3 : $TheField=number_format($in_total_licencias_1,0); 	
					echo "<td align='center' bgcolor='#003366'><font color=white>$TheField</td>";						 
				break;
				case 4 : $TheField=number_format($fl_total_precio_usd_1,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 5 : $TheField=number_format($in_total_licencias_2,0); 	
					echo "<td align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 6 : $TheField=number_format($fl_total_precio_usd_2,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 7 : $TheField=number_format($in_total_licencias_3,0); 	
					echo "<td align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 8 : $TheField=number_format($fl_total_precio_usd_3,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;
				case 9 : $TheField=number_format($in_total_licencias,0); 	
					echo "<td align='center' bgcolor='#003366'><font color=white>$TheField</font></td>";						 
				break;
				case 10 : $TheField=number_format($fl_total_precio_usd,0); 
					echo "<td align='right' bgcolor='#003366'><font color=white>$TheField</font></td>";					
				break;		
			}							
		}	
		echo "</tr>";				
	echo "</table>";	
	
	
	
	$valBita= "id_direccion_a=$id_direccion ";		
	$valBita.= "fl_tipo_cambio=$fl_tipo_cambio ";	
	$valBita.= "tx_moneda=$tx_moneda ";	
	$valBita.= "fl_tipo_cambio=$fl_tipo_cambio ";		
	$valBita.= "par_cuenta=$par_cuenta ";
	
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_LICENCIA" , "$id_login" ,   $valBita ,"" ,"excel_inventario_eficiencia.php");
	 //<\BITACORA>
	 
	 
	} 
	mysqli_close($mysql);
?>    
 <script language="JavaScript">	
		$("#divLoading").hide();	
	</script> 
</form>
</body>
</html>