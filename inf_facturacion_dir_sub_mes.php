<?
session_start();
if 	(isset($_SESSION["sess_user"]))
{
	
	$id_login = $_SESSION['sess_iduser'];
	
?>
	<input id="par_subdireccion" name="par_subdireccion" type="hidden" value="<? echo $par_subdireccion ?>" /> 
	<script type="text/javascript">			
	 
		$("#verFacturas").find("tr").hover(		 
        	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
        );
		 
		function btnDetalleFacturaInf(valor0) {		
		
			var id0="id="+valor0;
			var dispatch="&dispatch=insert";
			$("#divAltaFac").html(""); 	
			if (valor0 == 0) { 
				$("#divInfDet").html(""); 
			} else { 		
				loadHtmlAjax(true, $("#divInfDet"), "inf_facturas_detalle.php?"+id0+dispatch); 
				//loadHtmlAjax(true, $("#divInfDet"), "cat_facturas_detalle.php?"+id0+dispatch); 
			}
		}
		
		function btnInformesDirFacDetMes(v1) {
		
			var p1= parseInt(v1); 			
			var id0="par_direccion="+$("#par_direccion").val();
			var id1="&par_subdireccion="+$("#par_subdireccion").val();
			var id2="&par_anio="+$("#par_anio").val();
			var id3="&par_moneda="+$("#sel_moneda_d").val();
			var id4="&par_estatus="+$("#sel_estatus_d").val();
			var id5="&par_factura="+p1;
			var id6="&par_cuenta="+$("#sel_cta_contable").val();
			
			//alert ("Entre "+id0+id1+id2+id3+id4+id5);
	
			$("#divDetalleFacDir").html("");			
	
			loadHtmlAjax(true, $("#divDetalleFacDir"), "inf_facturas_lista_detalle_direccion.php?"+id0+id1+id2+id3+id4+id5+id6); 
		}	 
      
        </script> 
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");
	
	$mysql=conexion_db();
	
	# ============================
	# Recibo Variables
	# ============================
	
	$par_direccion		= $_GET["par_direccion"]; 	
	$par_subdireccion	= $_GET["par_subdireccion"]; 	
	$par_anio			= $_GET["par_anio"]; 
	$par_moneda			= $_GET["par_moneda"];
	$par_estatus		= $_GET["par_estatus"]; 
	$par_mes			= $_GET["par_mes"]; 
	$par_proveedor		= $_GET["par_proveedor"];
	$par_cuenta		= $_GET["par_cuenta"];
	
	
	if ($par_anio=="2011") $in_tc = 13.5;
	else $in_tc = 14;
	
	if ($par_proveedor==1) $tx_proveedor="BLOOMBERG";
	elseif ($par_proveedor==2) $tx_proveedor="REUTERS";
	elseif ($par_proveedor==3) {
		$tx_proveedor1="BLOOMBERG";
		$tx_proveedor2="REUTERS";
	}	
	
	$tx_mes=mes_nombre($par_mes);
	
	# ===============================================================================
	# NOTAS	
	# ===============================================================================		
	if ($par_moneda=="USD") $nota0 ="Monto en USD (D&oacute;lares Americanos).";
	else if ($par_moneda=="MXN") $nota0 ="Monto en MXN (Pesos Mexicanos).";
	else if ($par_moneda=="EUR") $nota0 ="Monto en EUR (EUROS).";
	
	$fecha_hoy = date("j-m-Y");
	
	# ==========================================
	# Busco nombres de Direccion y Subdireccion
	# ==========================================
	
	$sql = "   SELECT tx_nombre_corto, tx_subdireccion ";
	$sql.= "	 FROM tbl_direccion a, tbl_subdireccion b ";
	$sql.= "    WHERE a.id_direccion 	= $par_direccion ";
	$sql.= "      AND a.id_direccion 	= b.id_direccion ";
	$sql.= "      AND b.id_subdireccion = $par_subdireccion ";
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeSubDireccion[] = array(		
			"tx_direccion"		=>$row["tx_nombre_corto"],
			"tx_subdireccion"	=>$row["tx_subdireccion"]
		);
	} 	
	
	for ($i=0; $i < count($TheInformeSubDireccion); $i++)
	{ 	        			 
			while ($elemento = each($TheInformeSubDireccion[$i]))				
				$tx_direccion		=$TheInformeSubDireccion[$i]["tx_direccion"];
				$tx_subdireccion	=$TheInformeSubDireccion[$i]["tx_subdireccion"];
	}
	
	//echo "par_proveedor", $tx_proveedor1;
	//echo "<br/>";
	//echo "par_cuenta",$par_cuenta;
	//echo "<br/>";
	//echo "par_anio",$par_anio;
	//echo "<br/>";
	//echo "origen",$origen;
	
/*	$sql = "   SELECT id_factura, tx_anio, tx_proveedor_corto, tx_cuenta, tx_factura, fh_factura, fh_inicio, fh_final, fl_precio_usd, fl_precio_mxn, fl_precio_eur, fl_tipo_cambio, tx_mes, tx_estatus ";
	$sql.= "     FROM tbl_factura a, tbl_proveedor b, tbl_cuenta c, tbl_mes d, tbl_factura_estatus e, tbl_factura_detalle f ";
	$sql.= "    WHERE tx_anio 				= '$par_anio' ";
	$sql.= "	  AND b.tx_proveedor_corto	= '$tx_proveedor1' ";
	$sql.= "      AND a.id_proveedor		= b.id_proveedor ";
	$sql.= "      AND a.id_cuenta 			= c.id_cuenta ";	
	$sql.= "      AND a.id_mes 				= d.id_mes ";
	$sql.= "      AND a.id_mes 				= $par_mes ";
	$sql.= "      AND a.id_factura_estatus	= e.id_factura_estatus ";
	if ($par_estatus <> 0) $sql.= "      AND e.id_factura_estatus	= $par_estatus ";
	$sql.= "      AND a.id_factura			= f.id_factura ";
	$sql.= " ORDER BY tx_anio, tx_proveedor_corto, d.id_mes, tx_cuenta ";   */	
		
	if ($par_moneda=="USD") $sql = "   SELECT a.id_factura, tx_anio, tx_proveedor_corto, tx_cuenta, tx_factura, fh_factura, fh_inicio, fh_final, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS fl_precio_usd, fl_tipo_cambio, tx_mes, tx_estatus ";
	else $sql = "   SELECT a.id_factura, tx_anio, tx_proveedor_corto, tx_cuenta, tx_factura, fh_factura, fh_inicio, fh_final, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS fl_precio_usd, fl_tipo_cambio, tx_mes, tx_estatus ";
	$sql.= "     FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_subdireccion e, tbl_proveedor f, tbl_factura_estatus g, tbl_cuenta h, tbl_mes i , tbl_producto p ";
	$sql.= "    WHERE tx_anio 				= '$par_anio' and a.tx_indicador='1' ";
	$sql.= "      AND a.id_factura 			= b.id_factura  and  b.tx_indicador='1' ";
	$sql.= "      AND b.id_centro_costos	= c.id_centro_costos  and  c.tx_indicador='1' ";
	$sql.= "      AND c.id_direccion 		= $par_direccion ";
	$sql.= "      AND c.id_direccion 		= d.id_direccion  and  d.tx_indicador='1' ";
	
	$sql.= "      AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
			
		
	$sql.= "      AND c.id_subdireccion 	= e.id_subdireccion and  e.tx_indicador='1'";
	$sql.= "      AND c.id_subdireccion 	= $par_subdireccion ";
	$sql.= "      AND a.id_mes 				= $par_mes ";
	$sql.= "      AND a.id_mes 				= i.id_mes  and i.tx_indicador='1' ";
	$sql.= "      AND a.id_proveedor 		= f.id_proveedor and  f.tx_indicador='1'";
	$sql.= " 	 AND b.id_producto 		= p.id_producto and  p.tx_indicador='1' ";
	
	if ($par_proveedor==3) 
		$sql.= "      AND tx_proveedor_corto NOT IN ('$tx_proveedor1','$tx_proveedor2')  ";  
	else 
		$sql.= "      AND tx_proveedor_corto	= '$tx_proveedor' "; 
	
		
	$sql.= " 	  AND a.id_factura_estatus	= g.id_factura_estatus and  g.tx_indicador='1'";						
	if ($par_estatus<>0) $sql.= " AND g.id_factura_estatus = $par_estatus ";	
	
	$sql.= "      AND a.id_cuenta 			= h.id_cuenta  and h.tx_indicador='1' ";
	
	if ($par_cuenta <> 0)
					$sql.= " 	AND p.id_cuenta_contable=  $par_cuenta "; 
						
	$sql.= " GROUP BY a.id_factura ";
	
	//echo "<br>";	
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			'id_factura'		=>$row["id_factura"],
			'tx_anio'			=>$row["tx_anio"],
	  		'tx_proveedor_corto'=>$row["tx_proveedor_corto"],
	  		'tx_cuenta'			=>$row["tx_cuenta"],
	  		'tx_factura'		=>$row["tx_factura"],
	  		'fh_factura'		=>$row["fh_factura"],
			'fh_inicio'			=>$row["fh_inicio"],
	  		'fh_final'			=>$row["fh_final"],
	  		'fl_precio_usd'		=>$row["fl_precio_usd"],
	  		'fl_precio_mxn'		=>$row["fl_precio_mxn"],
	  		'fl_precio_eur'		=>$row["fl_precio_eur"],
	  		'fl_tipo_cambio'	=>$row["fl_tipo_cambio"],
  			'tx_mes'			=>$row["tx_mes"],	  		
  			'tx_estatus'		=>$row["tx_estatus"]	  		
		);
	} 	
	
	$registros=count($TheCatalogo);		
	?>
    <br/>   
	<?
	if ($registros==0) { 
		echo "<table align='center' width='100%'>";
		echo "<br>";
		echo "<tr>";
		echo "<td class='align-center'><em><b>Sin Informaci&oacute;n Encontrada ...</b></em></td>";
		echo "</tr>";	
		echo "<br>";		
		echo "</table>";
	} else {
		echo "<div class='ui-widget-header align-center'>FACTURACION - $tx_direccion - $tx_subdireccion - $tx_mes</div>";
		echo "<table id='verFacturas' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<11; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					echo "<td width='2%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='A&ntilde;o'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Proveedor';
					echo "<td width='14%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='Cuenta';
					echo "<td width='15%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 4 : $TheField='Factura';
					echo "<td width='7%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 5 : $TheField='Monto '.$par_moneda;
					echo "<td width='7%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField='T Cambio'; 
					echo "<td width='7%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 7 : $TheField='Fecha Factura'; 
					echo "<td width='7%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 8 : $TheField='Periodo'; 
					echo "<td width='13%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 9 : $TheField='Mes de Pago'; 
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 10 : $TheField='Estatus'; 
					echo "<td width='5%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;					
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		
		$m=0;		
		$in_total_licencias=0;	
		$fl_total_precio_usd=0;	
		$fl_total_precio_mxn=0;	
		
		for ($i=0; $i < count($TheCatalogo); $i++)
		{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$id_factura			=$TheCatalogo[$i]["id_factura"];
				$tx_anio			=$TheCatalogo[$i]["tx_anio"];
				$tx_proveedor_corto	=$TheCatalogo[$i]["tx_proveedor_corto"];
				$tx_cuenta			=$TheCatalogo[$i]["tx_cuenta"];
				$tx_factura			=$TheCatalogo[$i]["tx_factura"];
				$fh_factura			=$TheCatalogo[$i]["fh_factura"];
				$fh_inicio			=$TheCatalogo[$i]["fh_inicio"];
				$fh_final			=$TheCatalogo[$i]["fh_final"];
				$fl_precio_usd		=$TheCatalogo[$i]["fl_precio_usd"];
				$fl_precio_mxn		=$TheCatalogo[$i]["fl_precio_mxn"];
				$fl_precio_eur		=$TheCatalogo[$i]["fl_precio_eur"];
				$fl_tipo_cambio		=$TheCatalogo[$i]["fl_tipo_cambio"];	  		
				$tx_mes				=$TheCatalogo[$i]["tx_mes"];
				$tx_estatus			=$TheCatalogo[$i]["tx_estatus"];
				
				$m++;				

				$fl_total_precio_usd=$fl_total_precio_usd+$fl_precio_usd;
				$fl_total_precio_mxn=$fl_total_precio_mxn+$fl_precio_mxn;
				$fl_total_precio_eur=$fl_total_precio_eur+$fl_precio_eur;
				
				# ============================================
				# Busco moneda
				# ============================================				
				if ($fl_precio_eur<>0) 		$tx_moneda="EUR";
				else if ($fl_precio_usd<>0) $tx_moneda="USD";
				else 						$tx_moneda="MXN";				
						
				# ============================================
				# Busco cuadre de Factura
				# ============================================
				$sql1 = " SELECT SUM( fl_precio_usd ) AS fl_total_detalle_usd, SUM( fl_precio_mxn ) AS fl_total_detalle_mxn, SUM( fl_precio_eur ) AS fl_total_detalle_eur ";
   				$sql1.= " 	FROM tbl_factura_detalle ";
   				$sql1.= "  WHERE id_factura	=$id_factura "; 
				
				//echo " sql ",$sql1;
				
				$result1 = mysqli_query($mysql, $sql1);	
				while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
				{	
					$TheCatalogoTotal[] = array(
						"fl_total_detalle_usd"=>$row1["fl_total_detalle_usd"],
						"fl_total_detalle_mxn"=>$row1["fl_total_detalle_mxn"],
						"fl_total_detalle_eur"=>$row1["fl_total_detalle_eur"]
					);
				}	
				
				for ($j=0; $j < count($TheCatalogoTotal); $j++)	{         			 
					while ($elemento1 = each($TheCatalogoTotal[$j]))				
						$fl_total_detalle_usd	=$TheCatalogoTotal[$j]["fl_total_detalle_usd"];
						$fl_total_detalle_mxn	=$TheCatalogoTotal[$j]["fl_total_detalle_mxn"];						  		
						$fl_total_detalle_eur	=$TheCatalogoTotal[$j]["fl_total_detalle_eur"];						  		
				} 	
				
				$fl_diferencia_usd = $fl_total_detalle_usd - $fl_precio_usd;
			 	$fl_diferencia_mxn = $fl_total_detalle_mxn - $fl_precio_mxn;				
			 	$fl_diferencia_eur = $fl_total_detalle_eur - $fl_precio_eur;				
				
				$notas=0;
				
				if ($fl_precio_usd < 0) $notas = 1;
				else if ($fl_precio_mxn < 0) $notas = 1;
				else if ($fl_precio_eur < 0) $notas = 1;	
				
				//==========================							  						
				
				for ($a=0; $a<11; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$m; 
								if ($notas==1) echo "<td class='ui-notas align-center'>$TheColumn</td>";
								else echo "<td class='align-center'>$TheColumn</td>";
						break;						
						case 1: $TheColumn=$tx_anio;
								if ($notas==1) echo "<td class='ui-notas align-center'>$TheColumn</td>";
								else echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 2: $TheColumn=$tx_proveedor_corto;
								if ($notas==1) echo "<td class='ui-notas align-left'>$TheColumn</td>";
								else echo "<td class='align-left'>$TheColumn</td>";
						break;
						case 3: $TheColumn=$tx_cuenta;
								if ($notas==1) echo "<td class='ui-notas align-left'>$TheColumn</td>";
								else echo "<td class='align-left'>$TheColumn</td>";
						break;
						case 4: $TheColumn=$tx_factura;
								if ($notas==1) echo "<td class='ui-notas align-center'>$TheColumn</td>";
								else echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 5: $TheColumn=number_format($fl_precio_usd,0); 
								if($TheColumn==0) $TheColumn="-";
								else $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesDirFacDetMes($id_factura)' title='Presione para ver el detalle de $tx_proveedor_corto - Factura $tx_factura ... '>$TheColumn</a>";
								if ($notas==1) echo "<td class='ui-notas align-right'>$TheColumn</td>";
								else echo "<td class='align-right'>$TheColumn</td>";
						break;					
						case 6: $TheColumn=number_format($fl_tipo_cambio,4); 
								if ($notas==1) echo "<td class='ui-notas align-right'>$TheColumn</td>";
								else echo "<td class='align-right'>$TheColumn</td>";
						break;
						case 7: $TheColumn=cambiaf_a_normal($fh_factura);
								if($TheColumn=="") echo "<td class='align-left'>&nbsp;</td>";					
								if ($notas==1) echo "<td class='ui-notas align-center'>$TheColumn</td>";					
								else echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 8: $TheColumn=cambiaf_a_normal($fh_inicio)." - ".cambiaf_a_normal($fh_final);
								if($TheColumn=="") echo "<td class='align-left'>&nbsp;</td>";					
								if ($notas==1) echo "<td class='ui-notas align-center'>$TheColumn</td>";
								else echo "<td class='align-center'>$TheColumn</td>";
						break;						
						case 9: $TheColumn=$tx_mes; 
								if($TheColumn=="") echo "<td class='align-left'>&nbsp;</td>";						
								if ($notas==1) echo "<td class='ui-notas align-left'>$TheColumn</td>";							
								else echo "<td class='align-left'>$TheColumn</td>";							
						break;
						case 10: $TheColumn=$tx_estatus;
								if($TheColumn=="") echo "<td class='align-left'>&nbsp;</td>";					
								if ($notas==1) echo "<td class='ui-notas align-center'>$TheColumn</td>";							
								else echo "<td class='align-center'>$TheColumn</td>";
						break;							
					}							
				}				
				echo "</tr>";		
				}								
				echo "<tr>";								  
				for ($a=0; $a<11; $a++)
				{
					switch ($a) 
					{   
						case 0 : $TheField="Totales";
							echo "<td colspan='5' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
						break;
						case 6 : $TheField=number_format($fl_total_precio_usd,0); 	
							echo "<td class='ui-state-highlight align-right'>$TheField</td>";						 
						break;					
						case 7 : $TheField="";
							echo "<td colspan='5' class='ui-state-highlight'>&nbsp;</td>";					
						break;
					}							
				}	
				echo "</tr>";	
				echo "<tr>";
				for ($a=0; $a<11; $a++)
				{
					switch ($a) 
					{   
						case 0 : $TheField='#';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";							 
						break;
						case 1 : $TheField='A&ntilde;o'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 2 : $TheField='Proveedor';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";	
						break;
						case 3 : $TheField='Cuenta';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
						break;
						case 4 : $TheField='Factura';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
						break;
						case 5 : $TheField='Monto '.$par_moneda;
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
						break;
						case 6 : $TheField='T Cambio'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 7 : $TheField='Fecha Factura'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 8 : $TheField='Periodo'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 9 : $TheField='Amortizaci&oacute;n'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 10 : $TheField='Estatus'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;									
					}							
				}	
				echo "</tr>";	
				echo "<tr>";
				echo "<td colspan='11' align='left'>";	
				echo "<ul type='square'> ";
				echo "<li>Monto calculado en base a la derrama.</li> ";
				echo "<li>$nota0</li>";
				echo "<li>Actualizado al $fecha_hoy.</li>";
				echo "</ul>";      
				echo "</td>";
				echo "</tr>";	
	echo "</table>";
	}
	?>
<br/>    
<?


	$valBita= "par_direccion=$par_direccion "; 	
	$valBita.= "par_subdireccion=$par_subdireccion "; 	
	$valBita.= "par_anio=$par_anio ";		 
	$valBita.= "par_moneda=$par_moneda ";	
	$valBita.= "par_estatus=$par_estatus ";	 
	$valBita.= "par_mes=$par_mes ";		 
	$valBita.= "par_proveedor=$par_proveedor ";
	$valBita.= "par_cuenta=$par_cuenta ";
	
	  //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_FACTURA TBL_FACTURA_DETALLE" , "$id_login" ,   $valBita ,"" ,"inf_facturacion_dir_sub_mes.php");
	 //<\BITACORA>
	 
	 
	 
mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      