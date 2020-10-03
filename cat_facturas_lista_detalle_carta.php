<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();
	$id_login = $_SESSION['sess_iduser'];
	
	# ============================
	# Recibo variables
	# ============================
	$id_factura	= $_GET['id']; 	
	
?>  
	<script type="text/javascript">			
	 
		$("#verFacturasDetalleCarta").find("tr").hover(		 
        	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
        );
		 
	</script> 
<?		
	# ============================
	# Factura Cabecera
	# ============================	
	$sql = "   SELECT a.*, tx_gps ";
	$sql.= "   	 FROM tbl_factura a, tbl_proveedor b ";
	$sql.= "    WHERE id_factura 		= $id_factura ";
	$sql.= "      AND a.id_proveedor 	= b.id_proveedor ";
	
	
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheFactura[] = array(
  			'tx_factura'	=>$row["tx_factura"],
			'fh_factura'	=>$row["fh_factura"],
			'fh_contable'	=>$row["fh_contable"],
	  		'fl_precio_usd'	=>$row["fl_precio_usd"],
	  		'fl_precio_mxn'	=>$row["fl_precio_mxn"],
			'fl_precio_eur'	=>$row["fl_precio_eur"],
			'fl_tipo_cambio'=>$row["fl_tipo_cambio"],
			'tx_referencia' =>$row["tx_referencia"],
			'tx_gps' 		=>$row["tx_gps"]
		);
	} 	
	
	for ($i=0; $i < count($TheFactura); $i++)	{ 	        			 
		while ($elemento = each($TheFactura[$i]))	
			$tx_factura			=$TheFactura[$i]['tx_factura'];				  		
			$fh_factura			=$TheFactura[$i]['fh_factura'];
			$fh_contable		=$TheFactura[$i]['fh_contable'];
			$fl_precio_usd		=$TheFactura[$i]['fl_precio_usd'];
			$fl_precio_mxn		=$TheFactura[$i]['fl_precio_mxn'];
			$fl_precio_eur		=$TheFactura[$i]['fl_precio_eur'];
			$fl_tipo_cambio		=$TheFactura[$i]['fl_tipo_cambio'];
			$tx_referencia		=$TheFactura[$i]['tx_referencia'];
			$tx_gps				=$TheFactura[$i]['tx_gps'];
	}	
	
	//echo "sql cabecera",$sql;	
	//echo "<br />";
	
	$fh_factura = cambiaf_a_normal($fh_factura);
	$fh_factura = ereg_replace( ("/"), "", $fh_factura ); 
	
	$fh_contable = cambiaf_a_normal($fh_contable);
	$fh_contable = ereg_replace( ("/"), "", $fh_contable ); 	
	
	if ($fl_precio_usd > 0.00) {
		
		$tx_moneda 		= "USD";
		$fl_precio 		= $fl_precio_usd;
		$fl_precio_mxn 	= $fl_precio_mxn;		
			
	} else if ($fl_precio_usd > 0.00 && $fl_precio_mxn > 0.00 ) {
		
		$tx_moneda 		= "USD";
		$fl_precio 		= $fl_precio_usd;
		$fl_precio_mxn 	= $fl_precio_mxn;	
			
	} else if ($fl_precio_usd == 0.00 && $fl_precio_mxn < 0.00 ) {
	
		$tx_moneda 		= "MXN";
		$fl_precio 		= $fl_precio_mxn;
			
	} else {
		
		$tx_moneda 		= "EUR";
		$fl_precio 		= $fl_precio_eur * $fl_tipo_cambio;	
		
	}
				
	# ============================
	# Factura Detalle
	# ============================				
	$sql = "   SELECT tx_centro_costos, SUM( fl_precio_usd ) AS fl_precio_usd_det, SUM( fl_precio_mxn ) AS fl_precio_mxn_det, SUM( fl_precio_eur ) AS fl_precio_eur_det ";
	$sql.= "   	 FROM tbl_factura_detalle a, tbl_centro_costos b ";
	$sql.= "    WHERE id_factura 			=$id_factura  and a.tx_indicador='1' ";
	$sql.= "      AND a.id_centro_costos 	= b.id_centro_costos ";
	$sql.= " GROUP BY tx_centro_costos ";
		
	//echo "sql detalle",$sql;		
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheFacturaDetalle[] = array(
			'tx_centro_costos'	=>$row["tx_centro_costos"],				
	 		'fl_precio_usd_det'	=>$row["fl_precio_usd_det"],
	 		'fl_precio_mxn_det'	=>$row["fl_precio_mxn_det"],
			'fl_precio_eur_det'	=>$row["fl_precio_eur_det"]
		);
	} 
	
	$registros=count($TheFacturaDetalle);

	
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA", "TBL_FACTURA_DETALLE" , "$id_login" ,   "id_factura=$id_factura" , "$id_factura"  ,  "cat_facturas_lista_detalle_carta.php");
	 //<\BITACORA>
	 
	 
	
if ($registros==1) 
{
?>
<br>
<center> NO APLICA - AFECTACI&Oacute;N A CR &Uacute;NICO </center>
<br>
<script type="text/javascript">
$("#btnDerrama").addClass('ui-state-disabled').attr("disabled","disabled"); 
</script>
	
<?php  
}
	elseif ($registros==0) { }
		else {	
			echo "<br>";
			echo "<table id='verFacturasDetalleCarta' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
			echo "<tr>";
			for ($a=0; $a<15; $a++)
			{
			switch ($a) 
			{   
				case 0 : $TheField='No. Docto';
						 echo "<td width='2%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='Sociedad'; 
						 echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Fecha Docto';
						 echo "<td width='7%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='Fecha Contable';
						 echo "<td width='7%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 4 : $TheField='Tipo Docto';
						 echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 5 : $TheField='Moneda';
						 echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField='Texto Cabecera';
						 echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 7 : $TheField='Referencia';
						 echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 8 : $TheField='Clave Cont'  ;
						 echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 9 : $TheField='Cuenta';
						 echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 10 : $TheField='Importe';
						  echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 11 : $TheField='Ind Iva';
						  echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 12 : $TheField='CeCo';
						  echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 13 : $TheField='Texto Posici&oacute;n';
						  echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 14 : $TheField='Asignaci&oacute;n';
						  echo "<td width='8%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				}							
			}	
		echo "</tr>";	
				
		$j=0;		
		$tx_total_usuario=0;	
		$fl_total_precio_usd=0;	
		$fl_total_precio_mxn=0;	
		$fl_total_precio_eur=0;	
		
		for ($i=0; $i < count($TheFacturaDetalle); $i++)	{ 	        			 
			while ($elemento = each($TheFacturaDetalle[$i]))					  		
				$tx_centro_costos	=$TheFacturaDetalle[$i]['tx_centro_costos'];
				$fl_precio_usd_det	=$TheFacturaDetalle[$i]['fl_precio_usd_det'];
				$fl_precio_mxn_det	=$TheFacturaDetalle[$i]['fl_precio_mxn_det'];
				$fl_precio_eur_det	=$TheFacturaDetalle[$i]['fl_precio_eur_det'];				
						
				//if ($fl_precio_usd_det <> 0 ) 		$fl_precio_det = $fl_precio_usd_det * $fl_tipo_cambio;
				//else if ($fl_precio_mxn_det <> 0 ) 	$fl_precio_det = $fl_precio_mxn_det;
				//else if ($fl_precio_eur_det <> 0 )	$fl_precio_det = $fl_precio_eur_det * $fl_tipo_cambio;
				
				//echo "fl_precio_usd_det", $fl_precio_usd_det;
				//echo "<br>";
				//echo "fl_precio_mxn", $fl_precio_mxn;
				//echo "<br>";
				//echo "fl_precio_usd", $fl_precio;								
				
				if ($fl_precio_usd_det <> 0 ) 		$fl_precio_det = $fl_precio_mxn_det;
				else if ($fl_precio_mxn_det <> 0 ) 	$fl_precio_det = $fl_precio_mxn_det;
				else if ($fl_precio_eur_det <> 0 )	$fl_precio_det = $fl_precio_eur_det * $fl_tipo_cambio;

				$fl_precio_det_total = $fl_precio_det_total + $fl_precio_det;
				
				# =========================================
				# Ciclo para primer renglon
				# =========================================
				
				if ($j==0){				
					$j++;
					echo "<tr>";
					for ($a=0; $a<15; $a++)
					{
						switch ($a) 
						{   							
							case 0: //$TheColumn=$j; 									
									$TheColumn=1; 									
									echo "<td class='align-center'>$TheColumn</td>";
							break;						
							case 1: $TheColumn="MX11";
									echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 2: $TheColumn=$fh_factura;
									if ($TheColumn=="") $TheColumn="-";
									echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 3: $TheColumn=$fh_contable;
									if ($TheColumn=="") $TheColumn="-";
									echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 4: $TheColumn="SD";
									echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 5: $TheColumn="MXN";
									echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 6: $TheColumn=$tx_gps;								
									if ($TheColumn=="") $TheColumn="-";										
									echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 7: $TheColumn=$tx_referencia;
									if ($TheColumn=="") $TheColumn="-";									
									echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 8: $TheColumn=50;									
									echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 9: $TheColumn=32533003; 
									echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 10: $TheColumn=number_format($fl_precio_mxn,2); 
									 if($TheColumn=="0") echo "<td class='align-right'>-</td>";	
									 else echo "<td class='align-right'>$TheColumn</td>";
							break;
							case 11: $TheColumn="-";									
									 echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 12: $TheColumn="MX11007202"; 								
									 echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 13: $TheColumn="Servicios de Informaci&oacute;n";									
									 echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 14: $TheColumn=$tx_factura; 
									 echo "<td class='align-center'>$TheColumn</td>";
							break;
						}							
					}				
					echo "</tr>";				
				}
				
				$j++;				
				
				echo "<tr>";
				for ($a=0; $a<15; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=1; 
								echo "<td class='align-center'>$TheColumn</td>";
						break;						
						case 1: $TheColumn="MX11";
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 2: $TheColumn=$fh_factura;
								if ($TheColumn=="") $TheColumn="-";
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 3: $TheColumn=$fh_contable;
								if ($TheColumn=="") $TheColumn="-";
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 4: $TheColumn="SD";
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 5: $TheColumn="MXN";
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 6: $TheColumn=$tx_gps;								
								if ($TheColumn=="") $TheColumn="-";											
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 7: $TheColumn=$tx_referencia;
								if ($TheColumn=="") $TheColumn="-";											
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 8: $TheColumn=40;								
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 9: $TheColumn=32533003; 
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 10: $TheColumn=number_format($fl_precio_det,2); 
								if($TheColumn=="0") echo "<td class='align-right'>-</td>";	
								else echo "<td class='align-right'>$TheColumn</td>";
						break;
						case 11: $TheColumn="-";									
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 12: $TheColumn="MX1100".$tx_centro_costos; 								
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 13: $TheColumn="Servicios de Informaci&oacute;n";									
									echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 14: $TheColumn=$tx_factura; 
								echo "<td class='align-center'>$TheColumn</td>";
						break;
					}							
				}				
				echo "</tr>";					
				}	
				echo "<tr>";								  
				for ($a=0; $a<15; $a++)
				{
					switch ($a) 
					{   
						case 0 : $TheField='Diferencia';
								echo "<td colspan='10' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
						break;
						case 8 : $fl_diferencia=$fl_precio_mxn-$fl_precio_det_total;
								 $TheField=number_format($fl_diferencia,2); 
								 if($TheField=="0") echo "<td class='ui-state-highlight align-right'>-</td>";		
								 else echo "<td class='ui-state-rojo align-right'>$TheField</td>";						 
						break;
						case 9 : echo "<td colspan='4' class='ui-state-highlight'>&nbsp;</td>";						 
						break;
					}							
				}	
				echo "</tr>";	
			echo "</table>";
		}
		
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      