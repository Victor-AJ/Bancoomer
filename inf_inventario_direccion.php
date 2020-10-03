<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
//header('Content-Type: text/html; charset=utf-8'); 

session_start();
if 	(isset($_SESSION["sess_user"]))
{
	$id_login = $_SESSION['sess_iduser']
?>
	<script type="text/javascript">	
	
		function btnInformesDireccion(v0)
		{
			var id0="id_direccion="+v0;
			var id1="&fl_tipo_cambio="+$("#cap_tc").val();
			var id2="&tx_moneda="+$("#sel_moneda").val();	
			//alert ("Entre 1"+id0+id1+id2);
		
			$("#verInventarioDireccionN1").html("");
			$("#verInventarioDireccionN2").html("");
		
			loadHtmlAjax(true, $("#verInventarioDireccionN1"), "inf_inventario_direccion_subdireccion.php?"+id0+id1+id2); 
		}	
		
		function btnInformesDireccionLicencias(v0,v1,v2)
		{
			var id0="id_direccion="+v0;
			var id1="&id_proveedor="+v1;
			var id2="&id_proveedor_1="+v2;
			var id3="&fl_tipo_cambio="+$("#cap_tc").val();
			var id4="&tx_moneda="+$("#sel_moneda").val();
			//alert ("Entre 1"+id0+id1+id2+id3+id4);
			
			$("#verInventarioDireccionN1").html("");
			$("#verInventarioDireccionN2").html("");
			
			loadHtmlAjax(true, $("#verInventarioDireccionN1"), "inf_inventario_direccion_licencias_matriz.php?"+id0+id1+id2+id3+id4); 
		}
		
		function btnGraDirMonto(v0) {
		 	
			var id0="id_tiempo="+v0;
			var id1="&fl_tipo_cambio="+$("#cap_tc").val();
			var id2="&tx_moneda="+$("#sel_moneda").val();	
					
			var url = "gra_inv_dir_monto.php?"+id0+id1+id2;
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1000, height=700";
			var winName='_blank';  
								
			window.open(url,winName,windowprops); 
		} 	
		
		function btnGraDirLicencia() { 					

			var url = "gra_inv_dir_licencia.php";
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1000, height=700";
			var winName='_blank';  
								
			window.open(url,winName,windowprops); 
		} 	 
		
		function btnGraDirLicenciaBlo(v1) { 			
		
			//alert ("Entre");
			var p1= parseInt(v1); 	
			var url = "gra_inv_dir_licencia_blo.php?id=1&id_proveedor="+p1;
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1000, height=700";
			var winName="_blank";  		
			
								
			window.open(url,winName,windowprops); 
		} 	 
		 
		function btnGraDirDir(v1) { 			
		
			//alert ("Entre");
			var url = "gra_inv_dir_dir.php";
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1000, height=700";
			var winName='_blank';  
								
			window.open(url,winName,windowprops); 
		} 						
		
		function btnGraDirMontoBlo(v1,v2) { 	
		
			var id="id=1";
			var id1="&id_proveedor="+v1;
			var id2="&id_tiempo="+v2;
			var id3="&fl_tipo_cambio="+$("#cap_tc").val();
			var id4="&tx_moneda="+$("#sel_moneda").val();				
			//alert ("Entre"+id+id1+id2+id3+id4);				
			var url = "gra_inv_dir_monto_blo.php?"+id+id1+id2+id3+id4;			
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1000, height=700";
			var winName="_blank";  								
			window.open(url,winName,windowprops); 
			
		}
		
		// Graficas de Licencias vs Montos  
		// =========================================		
		
		function btnGraDirMonLic(v1)
		{ 					
			var id0="id_direccion="+v1;
			var id1="&fl_tipo_cambio="+$("#cap_tc").val();
			var id2="&tx_moneda="+$("#sel_moneda").val();			
			//alert("Valores"+id0+id1+id2);						
			var url = "gra_inv_dir_monto_lic.php?"+id0+id1+id2;
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1000, height=700";
			var winName='_blank';  								
			window.open(url,winName,windowprops); 
		}
		 
		$("#tbl_Inventario").find("tr").hover(		 
        	function() { $(this).addClass("ui-state-hover"); },
         	function() { $(this).removeClass("ui-state-hover"); }
        );	
		
		$("#btnFacturacion").click(function()
		{ 
			var id="id="+$("#sel_anio_inicio").val();		
			var url = "excel_facturacion.php?"+id;			
			//alert("url"+url);
			window.open( url,"_blank");		
			
		}).hover(function(){
			$(this).addClass("ui-state-hover")
		},function(){
			$(this).removeClass("ui-state-hover")
		});
		
		function btnExpInv(v0)
		{ 					
			var id0="id_direccion="+v0;
			var id1="&fl_tipo_cambio="+$("#cap_tc").val();
			var id2="&tx_moneda="+$("#sel_moneda").val();			
			var url = "excel_inventario_eficiencia.php?"+id0+id1+id2;
			//alert("url"+url);
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1100, height=700";
			var winName='_blank';  						
			window.open(url,winName,windowprops); 
		}
		
		function btnExpInvDet(v0)
		{ 					
			var id0="id_direccion="+v0;
			var id1="&fl_tipo_cambio="+$("#cap_tc").val();
			var id2="&tx_moneda="+$("#sel_moneda").val();			
			var url = "excel_inventario_direccion_licencias_matriz.php?"+id0+id1+id2;
			//alert("url"+url);
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1100, height=700";
			var winName='_blank';  						
			window.open(url,winName,windowprops); 
		}
		
	</script> 
    
<!--<form id="frm_inventario" method="post" action="inventario_eficiencia_excel.php" target="_blank">
<input type="hidden" id="datos_a_enviar" name="datos_a_enviar" /> -->
<?php

	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");  
	$mysql=conexion_db();	

		
		
		
	
	$id_direccion	= $_GET["id_direccion"];		
	$fl_tipo_cambio = $_GET["fl_tipo_cambio"];
	$tx_moneda 		= $_GET["tx_moneda"];
	
	$id_proveedor_3 = 0;
	
	if ($id_direccion==0) $dispatch="insert";	
	else $dispatch="save";
		
	if ($dispatch=="save") {					
		
		if ($tx_moneda=="USD") $sql = " SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
		else $sql = " SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";		
		$sql.= "     FROM tbl_licencia d  ";
		
		$sql.="    inner join tbl_producto e      on   (d.id_producto     = e.id_producto        and e.tx_indicador='1'   )  ";
		$sql.="    inner join tbl_empleado c      on  (c.id_empleado      = d.id_empleado          )  ";
		$sql.="    inner join tbl_centro_costos b on  (b.id_centro_costos = c.id_centro_costos    and b.tx_indicador='1' and b.id_direccion= $id_direccion)   "; 
		$sql.="   inner join tbl_direccion a      on   (b.id_direccion    = a.id_direccion       and a.tx_indicador='1' ) ";
		$sql.="    inner join tbl_perfil_direccion DIR on  ( a.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  
		$sql.= " GROUP BY a.id_direccion ";
		$sql.= " ORDER BY a.id_direccion ";
		
		//antes era este query
		/*  
		if ($tx_moneda=="USD") $sql = " SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
		else $sql = " SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";		
		$sql.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e ";
		$sql.= "    WHERE a.id_direccion 		= b.id_direccion ";
		$sql.= "      AND b.id_direccion 		= $id_direccion ";		
		$sql.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
		$sql.= "      AND c.id_empleado 		= d.id_empleado ";
		$sql.= "      AND d.id_producto 		= e.id_producto ";
		$sql.= " GROUP BY a.id_direccion ";
		$sql.= " ORDER BY a.id_direccion ";
		*/
		
	} 
	else if ($dispatch=="insert")  
	{		
		



		if ($tx_moneda=="USD") $sql = " SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
		else $sql = " SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";		
		$sql.= "     FROM tbl_licencia d ";
		$sql.="    inner join tbl_producto e      on   (d.id_producto     = e.id_producto        and e.tx_indicador='1'   )  ";
		$sql.="    inner join tbl_empleado c      on  (c.id_empleado      = d.id_empleado          )  ";
		$sql.="    inner join tbl_centro_costos b on  (b.id_centro_costos = c.id_centro_costos    and b.tx_indicador='1' )   "; 
		$sql.="   inner join tbl_direccion a      on   (b.id_direccion    = a.id_direccion       and a.tx_indicador='1' ) ";
		//SEGURIDAD: ACCESO A SUS DIRECCIONES
		$sql.="    inner join tbl_perfil_direccion DIR on  ( a.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  
		$sql.= " GROUP BY a.id_direccion ";
		$sql.= " ORDER BY total_precio_usd DESC, total_licencia DESC, a.id_direccion ";
		

		//antes era este query 
	/*
	if ($tx_moneda=="USD") $sql = " SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
		else $sql = " SELECT a.id_direccion, a.tx_nombre_corto, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";		
		$sql.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e ";
		$sql.= "    WHERE a.id_direccion 		= b.id_direccion ";
		$sql.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
		$sql.= "      AND c.id_empleado 		= d.id_empleado ";
		$sql.= "      AND d.id_producto 		= e.id_producto ";
		$sql.= " GROUP BY a.id_direccion ";
		$sql.= " ORDER BY total_precio_usd DESC, total_licencia DESC, a.id_direccion ";
	*/	
		
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
		echo "<div class='ui-widget-header align-center'>INVENTARIO POR DIRECCION</div>";	
		echo "<table id='tbl_Inventario' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		echo "<td width='3%' rowspan='3' class='ui-state-highlight align-center'>#</td>";							 
		echo "<td width='17%' rowspan='3' class='ui-state-highlight align-center'>DIRECCION</td>";					
		echo "<td width='18%' colspan='3' class='ui-state-highlight align-center'>BLOOMBERG</td>";	
		echo "<td width='18%' colspan='3' class='ui-state-highlight align-center'>REUTERS</td>";						 
		echo "<td width='18%' colspan='3' class='ui-state-highlight align-center'>OTROS</td>";	
		echo "<td width='18%' colspan='3' class='ui-state-highlight align-center'>TOTAL</td>";						 
		echo "<td width='3%' rowspan='3' class='ui-state-highlight align-center'>Gr&aacute;fica</td>";
		echo "<td width='3%' rowspan='3' class='ui-state-highlight align-center'>Exportar</td>";						 
		echo "<td width='3%' rowspan='3' class='ui-state-highlight align-center'>Detalle</td>";						 
		echo "</tr>";
		echo "<tr>";
		$LicenciaB = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirLicenciaBlo(1)' title='Presione para ver la Gr&aacute;fica'>Licencias</a>";
		echo "<td width='5%' rowspan='2' class='ui-state-highlight align-center'>$LicenciaB</td>";	
		echo "<td width='12%' colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		$LicenciaR = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirLicenciaBlo(2)' title='Presione para ver la Gr&aacute;fica'>Licencias</a>";
		echo "<td width='5%' rowspan='2' class='ui-state-highlight align-center'>$LicenciaR</td>";	
		echo "<td width='12%' colspan='2' class='ui-state-highlight align-center'>Monto</td>";		
		$LicenciaO = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirLicenciaBlo(3)' title='Presione para ver la Gr&aacute;fica'>Licencias</a>";
		echo "<td width='5%' rowspan='2' class='ui-state-highlight align-center'>$LicenciaO</td>";	
		echo "<td width='12%' colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		$LicenciaTotal = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirLicencia()' title='Presione para ver la Gr&aacute;fica'>Licencias</a>";
		echo "<td width='5%' rowspan='2' class='ui-state-highlight align-center'>$LicenciaTotal</td>";			
		echo "<td width='12%' colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		echo "</tr>";
		echo "<tr>";		
		$MontoB = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMontoBlo(1,1)' title='Presione para ver la Gr&aacute;fica'>Mensual</a>";
		echo "<td width='6%' class='ui-state-highlight align-center'>$MontoB</td>";
		$MontoBA = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMontoBlo(1,2)' title='Presione para ver la Gr&aacute;fica'>Anual</a>";
		echo "<td width='6%' class='ui-state-highlight align-center'>$MontoBA</td>";		
		$MontoR = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMontoBlo(2,1)' title='Presione para ver la Gr&aacute;fica'>Mensual</a>";
		echo "<td width='6%' class='ui-state-highlight align-center'>$MontoR</td>";
		$MontoRA = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMontoBlo(2,2)' title='Presione para ver la Gr&aacute;fica'>Anual</a>";		
		echo "<td width='6%' class='ui-state-highlight align-center'>$MontoRA</td>";		
		$MontoO = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMontoBlo(3,1)' title='Presione para ver la Gr&aacute;fica'>Mensual</a>";
		echo "<td width='6%' class='ui-state-highlight align-center'>$MontoO</td>";
		$MontoOA = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMontoBlo(3,2)' title='Presione para ver la Gr&aacute;fica'>Anual</a>";		
		echo "<td width='6%' class='ui-state-highlight align-center'>$MontoOA</td>";	
		$MontoTotal = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMonto(1)' title='Presione para ver la Gr&aacute;fica'>Mensual</a>";
		echo "<td width='6%' class='ui-state-highlight align-center'>$MontoTotal</td>";
		$MontoTotalA = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMonto(2)' title='Presione para ver la Gr&aacute;fica'>Anual</a>";
		echo "<td width='6%' class='ui-state-highlight align-center'>$MontoTotalA</td>";
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

				
				//antes era este query
				/*
				if ($tx_moneda=="USD" ) $sql1 = "   SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
				else $sql1 = "  SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";
				$sql1.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
				$sql1.= "    WHERE a.id_direccion 		= b.id_direccion ";
				$sql1.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
				$sql1.= "      AND c.id_empleado 		= d.id_empleado ";
				$sql1.= "      AND a.id_direccion 		= $id_direccion ";
				$sql1.= "      AND d.id_producto 		= e.id_producto ";
				$sql1.= "      AND e.id_proveedor 		= f.id_proveedor ";
				$sql1.= "      AND f.tx_proveedor_corto = '$tx_proveedor1' ";
				$sql1.= " GROUP BY a.id_direccion ";
				$sql1.= " ORDER BY total_precio_usd DESC, total_licencia DESC, a.id_direccion ";
				*/


				if ($tx_moneda=="USD" ) $sql1 = "   SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
				else $sql1 = "  SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";
				$sql1.= "     FROM  tbl_licencia d ";
				
				
				$sql1.="    inner join tbl_producto e     on   (d.id_producto     = e.id_producto        and e.tx_indicador='1'   )  ";
				$sql1.="    inner join tbl_proveedor f      on   (e.id_proveedor 		= f.id_proveedor      and f.tx_proveedor_corto = '$tx_proveedor1'   )  ";
				$sql1.="    inner join tbl_empleado c      on  (c.id_empleado      = d.id_empleado          )  ";
				$sql1.="    inner join tbl_centro_costos b on  (b.id_centro_costos = c.id_centro_costos    and b.tx_indicador='1' )   "; 
				$sql1.="   inner join tbl_direccion a      on   (b.id_direccion    = a.id_direccion       and a.tx_indicador='1' and a.id_direccion= $id_direccion ) ";
				$sql1.="    inner join tbl_perfil_direccion DIR on  ( a.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  
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
				

				//antes era este query
				/*
				if ($tx_moneda=="USD") $sql2 = "   SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
				else $sql2 = "  SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";
				$sql2.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
				$sql2.= "    WHERE a.id_direccion 		= b.id_direccion ";
				$sql2.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
				$sql2.= "      AND c.id_empleado 		= d.id_empleado ";
				$sql2.= "      AND a.id_direccion 		= $id_direccion ";
				$sql2.= "      AND d.id_producto 		= e.id_producto ";
				$sql2.= "      AND e.id_proveedor 		= f.id_proveedor ";
				$sql2.= "      AND f.tx_proveedor_corto = '$tx_proveedor2' ";
				$sql2.= " GROUP BY a.id_direccion ";
				$sql2.= " ORDER BY total_precio_usd DESC, total_licencia DESC, a.id_direccion ";
				*/
				
				if ($tx_moneda=="USD") $sql2 = "   SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
				else $sql2 = "  SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";
				$sql2.= "     FROM  tbl_licencia d ";
				
		
				$sql2.="    inner join tbl_producto e     on   (d.id_producto     = e.id_producto        and e.tx_indicador='1'   )  ";
				$sql2.="    inner join tbl_proveedor f      on   (e.id_proveedor 		= f.id_proveedor      and f.tx_proveedor_corto = '$tx_proveedor2'   )  ";
				$sql2.="    inner join tbl_empleado c      on  (c.id_empleado      = d.id_empleado          )  ";
				$sql2.="    inner join tbl_centro_costos b on  (b.id_centro_costos = c.id_centro_costos    and b.tx_indicador='1' )   "; 
				$sql2.="   inner join tbl_direccion a      on   (b.id_direccion    = a.id_direccion       and a.tx_indicador='1' and a.id_direccion= $id_direccion ) ";
				$sql2.="    inner join tbl_perfil_direccion DIR on  ( a.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  
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
				
				//antes era este query
				/*
				if ($tx_moneda=="USD") $sql3 = "   SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
				else $sql3 = "  SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";
				$sql3.= "     FROM tbl_direccion a, tbl_centro_costos b, tbl_empleado c, tbl_licencia d, tbl_producto e, tbl_proveedor f ";
				$sql3.= "    WHERE a.id_direccion 		= b.id_direccion ";
				$sql3.= "      AND b.id_centro_costos 	= c.id_centro_costos ";
				$sql3.= "      AND c.id_empleado 		= d.id_empleado ";
				$sql3.= "      AND a.id_direccion 		= $id_direccion ";
				$sql3.= "      AND d.id_producto 		= e.id_producto ";
				$sql3.= "      AND e.id_proveedor 		= f.id_proveedor ";
				$sql3.= "      AND f.tx_proveedor_corto NOT IN ('$tx_proveedor3','$tx_proveedor4') ";
				$sql3.= " GROUP BY a.id_direccion ";
				$sql3.= " ORDER BY total_precio_usd DESC, total_licencia DESC, a.id_direccion ";
				*/
				
				if ($tx_moneda=="USD") $sql3 = "   SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio) + SUM(e.fl_precio_mxn / $fl_tipo_cambio) AS total_precio_usd ";
				else $sql3 = "  SELECT f.id_proveedor, SUM( in_licencia ) AS total_licencia, SUM(fl_precio * $fl_tipo_cambio) + SUM(e.fl_precio_mxn) AS total_precio_usd ";
				$sql3.= "     FROM tbl_licencia d ";
				
				$sql3.="    inner join tbl_producto e     on   (d.id_producto     = e.id_producto        and e.tx_indicador='1'   )  ";
				$sql3.="    inner join tbl_proveedor f      on   (e.id_proveedor 		= f.id_proveedor      and f.tx_proveedor_corto NOT IN ('$tx_proveedor3','$tx_proveedor4')    )  ";
				$sql3.="    inner join tbl_empleado c      on  (c.id_empleado      = d.id_empleado         )  ";
				$sql3.="    inner join tbl_centro_costos b on  (b.id_centro_costos = c.id_centro_costos    and b.tx_indicador='1' )   "; 
				$sql3.="   inner join tbl_direccion a      on   (b.id_direccion    = a.id_direccion       and a.tx_indicador='1' and a.id_direccion= $id_direccion ) ";
				$sql3.="    inner join tbl_perfil_direccion DIR on  ( a.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  
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
				for ($a=0; $a<17; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$c; 									
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;						
						case 1: $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesDireccion($id_direccion)' title='Presione para ver el detalle de la $tx_nombre ...'>$tx_nombre</a>";
								echo "<td class='align-left' valign='top'>$TheColumn</td>";
						break;
						case 2: if ($total_licencia_1==0) $TheColumn="-";
								else 
								{
									$TheColumn = number_format($total_licencia_1,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesDireccionLicencias($id_direccion, $id_proveedor_1, $id_proveedor_1)' title='Presione para ver el detalle de las Licencias de $tx_nombre ...';>$TheColumn</a>";
								}	
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;																												
							case 3: if ($total_precio_usd_1==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_1,0); 
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;																												
							case 4: if ($total_precio_usd_1_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_1_a,0); 
									echo "<td class='ui-state-verde align-right' valign='top'>$TheColumn</td>";
							break;																												
							case 5: if ($total_licencia_2==0) $TheColumn="-";									
									else {
										$TheColumn = number_format($total_licencia_2,0); 
										$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesDireccionLicencias($id_direccion, $id_proveedor_2, $id_proveedor_2)' title='Presione para ver el detalle de las Licencias de $tx_nombre ...';>$TheColumn</a>";
									}	
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;																												
							case 6: if ($total_precio_usd_2==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_2,0); 
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;	
							case 7: if ($total_precio_usd_2_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_2_a,0); 
									echo "<td class='ui-state-verde align-right' valign='top'>$TheColumn</td>";
							break;																															
							case 8: if ($total_licencia_3==0) $TheColumn="-";									
									else {
										$TheColumn = number_format($total_licencia_3,0); 
										$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesDireccionLicencias($id_direccion, $id_proveedor_1, $id_proveedor_2)' title='Presione para ver el detalle de las Licencias de $tx_nombre ...';>$TheColumn</a>";
									}	
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;																												
							case 9: if ($total_precio_usd_3==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_3,0); 
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;
							case 10: if ($total_precio_usd_3_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_3_a,0); 
									echo "<td class='ui-state-verde align-right' valign='top'>$TheColumn</td>";
							break;	
							case 11: if ($total_licencia==0) $TheColumn="-";
									else {
										$TheColumn = number_format($total_licencia,0); 
										$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesDireccionLicencias($id_direccion, $id_proveedor_3, $id_proveedor_3)' title='Presione para ver el detalle de las Licencias de $tx_nombre ...';>$TheColumn</a>";
									}
									echo "<td class='ui-state-default align-center' valign='top'>$TheColumn</td>";
							break;												
							case 12: if ($total_precio_usd==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd,0); 
									echo "<td class='ui-state-default align-right' valign='top'>$TheColumn</td>";
							break;
							case 13: $total_precio_usd_a = $total_precio_usd * 12;
									if ($total_precio_usd_a==0) $TheColumn="-";
									else $TheColumn=number_format($total_precio_usd_a,0); 
									echo "<td class='ui-state-verde-total align-right' valign='top'>$TheColumn</td>";
							break;
							case 14 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraDirMonLic($id_direccion)' title='Presione para ver la Gr&aacute;fica'><span class='ui-icon ui-icon-signal'></span></a>";							
									echo "<td class='ui-widget-header' align='center' valign='top'>$TheColumn</td>";
							break;								
							case 15 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnExpInv($id_direccion)' title='Presione para Exportar'><span class='ui-icon ui-icon-extlink'></span></a>";							
									echo "<td class='ui-widget-header' align='center' valign='top'>$TheColumn</td>";
							break;								
							case 16 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnExpInvDet($id_direccion)' title='Presione para Exportar'><span class='ui-icon ui-icon-extlink'></span></a>";							
									echo "<td class='ui-widget-header' align='center' valign='top'>$TheColumn</td>";
							break;								
						}							
					}				
					echo "</tr>";					
				}	
		echo "<tr>";								  
		for ($a=0; $a<16; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField="TOTALES";
						 echo "<td colspan='2' class='ui-state-highlight align-right'>$TheField</td>";						 
				break;
				case 1 : if ($in_total_licencias_1==0) $TheField="-";
						 else $TheField=number_format($in_total_licencias_1,0); 	
					     echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 2 : if ($fl_total_precio_usd_1==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_1,0); 
						 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 3 : $fl_total_precio_usd_1_a = $fl_total_precio_usd_1 * 12;
						 if ($fl_total_precio_usd_1_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_1_a,0); 
						 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 4 : if ($in_total_licencias_2==0) $TheField="-";
						 $TheField=number_format($in_total_licencias_2,0); 	
					     echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 5 : if ($fl_total_precio_usd_2==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_2,0); 
						 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 6 : $fl_total_precio_usd_2_a = $fl_total_precio_usd_2 * 12;
						 if ($fl_total_precio_usd_2_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_2_a,0); 
						 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 7 : if ($in_total_licencias_3==0) $TheField="-";
						 else $TheField=number_format($in_total_licencias_3,0); 	
					     echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 8 : if ($fl_total_precio_usd_3==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_3,0); 
					     echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 9 : $fl_total_precio_usd_3_a = $fl_total_precio_usd_3 * 12;
						 if ($fl_total_precio_usd_3_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_3_a,0); 
					     echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 10 :if ($in_total_licencias==0) $TheField="-";
						 else $TheField=number_format($in_total_licencias,0); 	
						 echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 11 :if ($fl_total_precio_usd==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd,0); 
						 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 12 :$fl_total_precio_usd_a = $fl_total_precio_usd * 12;
						 if ($fl_total_precio_usd_a==0) $TheField="-";
						 else $TheField=number_format($fl_total_precio_usd_a,0); 
						 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 13 : $TheField="-"; 
						 echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 14 : $TheField="-"; 
						 echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 15 : $TheField="-"; 
						 echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
			}							
		}	
		echo "</tr>";
		
		echo "<tr>";
		echo "<td rowspan='3' class='ui-state-highlight align-center'>#</td>";	
		echo "<td rowspan='3' class='ui-state-highlight align-center'>DIRECCION</td>";	
		echo "<td rowspan='2' class='ui-state-highlight align-center'>$LicenciaB</td>";	
		echo "<td class='ui-state-highlight align-center'>$MontoB</td>";
		echo "<td class='ui-state-highlight align-center'>$MontoBA</td>";
		echo "<td rowspan='2' class='ui-state-highlight align-center'>$LicenciaR</td>";	
		echo "<td class='ui-state-highlight align-center'>$MontoR</td>";
		echo "<td class='ui-state-highlight align-center'>$MontoRA</td>";
		echo "<td rowspan='2' class='ui-state-highlight align-center'>$LicenciaO</td>";	
		echo "<td class='ui-state-highlight align-center'>$MontoO</td>";
		echo "<td class='ui-state-highlight align-center'>$MontoOA</td>";
		echo "<td rowspan='2' class='ui-state-highlight align-center'>$LicenciaTotal</td>";	
		echo "<td class='ui-state-highlight align-center'>$MontoTotal</td>";
		echo "<td class='ui-state-highlight align-center'>$MontoTotalA</td>";
		echo "<td rowspan='3' class='ui-state-highlight align-center'>Gr&aacute;fica</td>";	
		echo "<td rowspan='3' class='ui-state-highlight align-center'>Exportar</td>";	
		echo "<td rowspan='3' class='ui-state-highlight align-center'>Detalle</td>";	
		echo "</tr>";	
		
		echo "<tr>";
		echo "<td width='12%' colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		echo "<td width='12%' colspan='2' class='ui-state-highlight align-center'>Monto</td>";		
		echo "<td width='12%' colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		echo "<td width='12%' colspan='2' class='ui-state-highlight align-center'>Monto</td>";
		echo "</tr>";	
		
		echo "<tr>";
		echo "<td colspan='3' class='ui-state-highlight align-center'>BLOOMBERG</td>";	
		echo "<td colspan='3' class='ui-state-highlight align-center'>REUTERS</td>";						 
		echo "<td colspan='3' class='ui-state-highlight align-center'>OTROS</td>";	
		echo "<td colspan='3' class='ui-state-highlight align-center'>TOTAL</td>";	
		echo "</tr>";	
		
		echo "<tr>";
		echo "<td colspan='16' align='left'>";	
        echo "<ul type='square'> ";
		echo "<li>Licencias calculadas en base al Inventario.</em></li> ";
        echo "<li>Monto calculado en base al Inventario.</li> ";
        echo "<li>Monto en  $tx_moneda.</li>";
        echo "<li>Tipo de cambio $fl_tipo_cambio pesos por d&oacute;lar.</li>";
        echo "</ul>";      
        echo "</td>";
		echo "</tr>";
		
	echo "</table>";
    
	$id_direccion	= $_GET["id_direccion"];		
	$fl_tipo_cambio = $_GET["fl_tipo_cambio"];
	$tx_moneda 		= $_GET["tx_moneda"];
	
		//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_LICENCIA" , "$id_login" ,   "id_direccion=$id_direccion fl_tipo_cambio=$fl_tipo_cambio tx_moneda=$tx_moneda" ,"" ,"inf_inventario_direccion.php");
	 //<\BITACORA>
	 
	
	?>  
    <!-- </form> -->
    <div id="verInventarioDireccionN1"></div>
    <div id="verInventarioDireccionN2"></div>
    <?
	}
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      