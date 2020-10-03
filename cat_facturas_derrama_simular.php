<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">		
	
	$("#verSimular").find("tr").hover(		 
       	function() { $(this).addClass('ui-state-hover'); },
       	function() { $(this).removeClass('ui-state-hover'); }
    );
	 			
</script>
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");
	$mysql=conexion_db();
		$id_login = $_SESSION['sess_iduser'];
	
	# ============================================
	# Recibo variables
	# ============================================
	$id_cuenta		= $_GET['id_cuenta'];		
	$id_proveedor	= $_GET['id_proveedor'];
	$pagos			= $_GET['sel_pagos'];
	$cap_gasto		= $_GET['cap_gasto'];
	$fl_precio_usd  = $_GET['fl_precio_usd'];
	$fl_precio_mxn  = $_GET['fl_precio_mxn'];
	$fl_precio_eur  = $_GET['fl_precio_eur'];
	
	//echo "<br>";
	//echo "ac_moneda",$ac_moneda;
	
	$cap_gasto = ereg_replace( (","), "", $cap_gasto ); 
	
	$fl_precio_usd_f = number_format($fl_precio_usd,2);	
	$fl_precio_mxn_f = number_format($fl_precio_mxn,2);		
	$fl_precio_eur_f = number_format($fl_precio_eur,2);		
	
	if ($fl_precio_usd <>0 ) 	  { $ac_moneda = "USD"; $fl_precio_factura = $fl_precio_usd; }
	else if ($fl_precio_mxn <>0 ) { $ac_moneda = "MXN"; $fl_precio_factura = $fl_precio_mxn; }
	else if ($fl_precio_eur <>0 ) { $ac_moneda = "EUR"; $fl_precio_factura = $fl_precio_eur; }
	
	# ============================================
	# Carga Empleados con licencias
	# ============================================		
	$sql = "   SELECT d.id_producto, tx_producto, fl_precio, tx_moneda, COUNT(*) AS cuantos ";
	$sql.= "     FROM tbl_empleado a, tbl_licencia b, tbl_proveedor c, tbl_producto d, tbl_moneda e ";
	$sql.= "    WHERE a.id_empleado		= b.id_empleado and a.tx_indicador='1' ";
	$sql.= "      AND b.id_cuenta		= $id_cuenta  and b.tx_indicador='1' ";	
	$sql.= "      AND c.id_proveedor 	= $id_proveedor  and c.tx_indicador='1' ";
	$sql.= "      AND c.id_proveedor 	= d.id_proveedor and d.tx_indicador='1'  ";
	$sql.= "      AND b.id_producto 	= d.id_producto ";
	$sql.= "      AND d.id_moneda 		= e.id_moneda  and e.tx_indicador='1' ";
	$sql.= " GROUP BY tx_producto, fl_precio ";
		
	//echo "aaa", $sql;
			
	$result = mysqli_query($mysql, $sql);		
		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoProducto[] = array(
			'id_producto'	=>$row["id_producto"],
			'tx_producto'	=>$row["tx_producto"],
			'fl_precio'		=>$row["fl_precio"],
			'tx_moneda'		=>$row["tx_moneda"],
			'cuantos'		=>$row["cuantos"]
		);
	} 
	
	$valBita ="id_cuenta=$id_cuenta ";
	$valBita.="id_proveedor=$id_proveedor ";
	$valBita.="pagos=$pagos ";
	$valBita.="cap_gasto=$cap_gasto ";
	$valBita.="fl_precio_usd=$fl_precio_usd ";
	$valBita.="fl_precio_mxn=$fl_precio_mxn ";
	$valBita.="fl_precio_eur=$fl_precio_eur";
	
	
				//<BITACORA>
	 			$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "SIMULACION", "TBL_LICENCIA" , "$id_login" ,   $valBita , ""  ,  "cat_facturas_derrama_simular.php");
				//<\BITACORA>
	
	
	$registros=count($TheCatalogoProducto);		

	if ($registros==0) { }
	else {
		echo "<table id='verSimular' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<8; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					     echo "<td width='2%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='Producto'; 
					     echo "<td width='40%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Precio';
					     echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='Usuarios';
						 echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 4 : $TheField='Monto';
						 echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 5 : $TheField='Pagos';
						 echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 6 : $TheField='Total';
						 echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 7 : $TheField='Moneda';
						 echo "<td width='8%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
			}							
		}	
		echo "</tr>";	
			
		$j=0;		
		$in_total_licencias=0;	
		$fl_total_precio_usd=0;	
		$fl_total_precio_mxn=0;	
				
		for ($i=0; $i < count($TheCatalogoProducto); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogoProducto[$i]))					  		
				$id_producto	= $TheCatalogoProducto[$i]['id_producto'];
				$tx_producto	= $TheCatalogoProducto[$i]['tx_producto'];
				$fl_precio		= $TheCatalogoProducto[$i]['fl_precio'];
				$id_moneda		= $TheCatalogoProducto[$i]['id_moneda'];
				$tx_moneda		= $TheCatalogoProducto[$i]['tx_moneda'];
				$cuantos		= $TheCatalogoProducto[$i]['cuantos'];
				
				$fl_precio_total = $fl_precio_total + $fl_precio;
				$cuantos_total = $cuantos_total + $cuantos;
				
				$j++;	
						
				echo "<tr>";
				for ($a=0; $a<8; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$j; 									
								echo "<td class='align-center'>$TheColumn</td>";										
						break;						
						case 1: $TheColumn=$tx_producto;
								echo "<td class='align-left'>$TheColumn</td>";										
						break;
						case 2: $TheColumn=$fl_precio;
								echo "<td class='align-right'>$TheColumn</td>";										
						break;
						case 3: $TheColumn=$cuantos;
								echo "<td class='align-right'>$TheColumn</td>";										
						break;
						case 4: $monto = $fl_precio * $cuantos;
								$total_monto=$total_monto + $monto; 
								$TheColumn=number_format($monto,2); 
								echo "<td class='align-right'>$TheColumn</td>";										
						break;
						case 5: $TheColumn=number_format($pagos,0); 
								echo "<td class='align-right'>$TheColumn</td>";										
						break;
						case 6: $total = $monto * $pagos;
								$total_total=$total_total + $total; 
								$TheColumn=number_format($total,2); 
								echo "<td class='align-right'>$TheColumn</td>";										
						break;
						case 7: $TheColumn=$tx_moneda;
								echo "<td class='align-center'>$TheColumn</td>";										
						break;
					}							
				}
				echo "</tr>";					
			}	
					
			# =========================================================================
			# SUBTOTALES
			# =========================================================================	
				echo "<tr>";				
				for ($a=0; $a<8; $a++)
				{
					switch ($a) 
					{   
						case 0 : $TheField='#';
								 echo "<td class='ui-state-default align-center'>$TheField</td>";							 
						break;
						case 1 : $TheField='SubTotal'; 
							     echo "<td class='ui-state-default align-right'>$TheField</td>";					
						break;
						case 2 : $TheField=number_format($fl_precio_total,2);
								 echo "<td class='ui-state-default align-right'>$TheField</td>";	
						break;
						case 3 : $TheField=number_format($cuantos_total,0);
							     echo "<td class='ui-state-default align-right'>$TheField</td>";	
						break;
						case 4 : $TheField=number_format($total_monto,2);
							     echo "<td class='ui-state-default align-right'>$TheField</td>";	
						break;
						case 5 : $TheField=number_format($pagos,0);
								 echo "<td class='ui-state-default align-right'>$TheField</td>";	
						break;
						case 6 : $TheField=number_format($total_total,2);
								 echo "<td class='ui-state-default align-right'>$TheField</td>";						 
						break;
						case 7 : $TheField=$tx_moneda;
								 echo "<td class='ui-state-default align-center'>$TheField</td>";						 
						break;
					}							
				}	
				echo "</tr>";	
					
				# =========================================================================
				# GASTO COMPARTIDO
				# =========================================================================
				echo "<tr>";
				$j++;					
				for ($a=0; $a<8; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$j; 
								echo "<td class='align-center'>$TheColumn</td>";							 
						break;
						case 1: $TheColumn="GASTO COMPARTIDO";
								echo "<td class='align-left'>$TheColumn</td>";					
						break;
						case 2: $cap_gasto_unitario = ($cap_gasto / $cuantos_total);
								$TheColumn=number_format($cap_gasto_unitario,2);
								echo "<td class='align-right'>$TheColumn</td>";										
						break;
						case 3: $TheColumn=number_format($cuantos_total,0);
								echo "<td class='align-right'>$TheColumn</td>";										
						break;
						case 4: $TheColumn=number_format($cap_gasto,2);
								echo "<td class='align-right'>$TheColumn</td>";										
						break;
						case 5: $TheColumn="-";
								echo "<td class='align-right'>$TheColumn</td>";	
						break;
						case 6: $TheColumn=number_format($cap_gasto,2);
								 echo "<td class='align-right'>$TheColumn</td>";						 
						break;
						case 7: $TheColumn=$tx_moneda;
								echo "<td class='align-center'>$TheColumn</td>";						 
						break;
					}							
				}	
				echo "</tr>";	
					
				# =========================================================================
				# TOTALES
				# =========================================================================				
				echo "<tr>";				
				for ($a=0; $a<8; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheField='#';
								echo "<td class='ui-state-default align-center'>$TheField</td>";							 
						break;
						case 1: $TheField='Total'; 
								echo "<td class='ui-state-default align-right'>$TheField</td>";					
						break;
						case 2: $TheField=number_format($fl_precio_total + $cap_gasto_unitario,2);
								echo "<td class='ui-state-default align-right'>$TheField</td>";	
						break;
						case 3: $TheField=number_format($cuantos_total,0);
								echo "<td class='ui-state-default align-right'>$TheField</td>";	
						break;
						case 4: $TheField=number_format($total_monto + $cap_gasto,2);
								echo "<td class='ui-state-default align-right'>$TheField</td>";	
						break;
						case 5: $TheField=number_format($pagos,0);
								echo "<td class='ui-state-default align-right'>$TheField</td>";	
						break;
						case 6: $total_total=$total_total + $cap_gasto;
								$TheField=number_format($total_total + $cap_gasto,2);
								echo "<td class='ui-state-default align-right'>$TheField</td>";						 
						break;
						case 7: $TheField=$tx_moneda;
								echo "<td class='ui-state-default align-center'>$TheField</td>";						 
						break;
					}							
				}	
				echo "<tr>";	
				
				# =========================================================================
				# FACTURA
				# =========================================================================				
				echo "<tr>";				
				for ($a=0; $a<8; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheField='#';
								echo "<td class='ui-state-default align-center'>$TheField</td>";							 
						break;
						case 1: $TheField='Total Factura'; 
								echo "<td class='ui-state-default align-right'>$TheField</td>";					
						break;
						case 2: $TheField="-";
								echo "<td class='ui-state-default align-right'>$TheField</td>";	
						break;
						case 3: $TheField="-";
								echo "<td class='ui-state-default align-right'>$TheField</td>";	
						break;
						case 4: $TheField="-";
								echo "<td class='ui-state-default align-right'>$TheField</td>";	
						break;
						case 5: $TheField="-";
								echo "<td class='ui-state-default align-right'>$TheField</td>";	
						break;
						case 6: $TheField=number_format($fl_precio_factura,2);
								echo "<td class='ui-state-default align-right'>$TheField</td>";						 
						break;
						case 7: $TheField=$tx_moneda;
								echo "<td class='ui-state-default align-center'>$TheField</td>";						 
						break;
					}							
				}	
				echo "<tr>";	
				
				# =========================================================================
				# DIFERENCIA
				# =========================================================================				
				echo "<tr>";				
				
				$diferencia	= $fl_precio_factura-$total_total;
				for ($a=0; $a<8; $a++)
				{					
					switch ($a) 
					{   
						case 0: $TheField='#';
								if ($diferencia==0)	echo "<td class='ui-state-verde align-center'>$TheField</td>";							 
								else echo "<td class='ui-state-rojo align-center'>$TheField</td>";							 
						break;
						case 1: $TheField='Diferencia'; 
								if ($diferencia==0)	echo "<td class='ui-state-verde align-right'>$TheField</td>";							 
								else echo "<td class='ui-state-rojo align-right'>$TheField</td>";							 
						break;
						case 2: $TheField="-";
								if ($diferencia==0)	echo "<td class='ui-state-verde align-right'>$TheField</td>";							 
								else echo "<td class='ui-state-rojo align-right'>$TheField</td>";							 
						break;
						case 3: $TheField="-";
								if ($diferencia==0)	echo "<td class='ui-state-verde align-right'>$TheField</td>";							 
								else echo "<td class='ui-state-rojo align-right'>$TheField</td>";							 
						break;
						case 4: $TheField="-";
								if ($diferencia==0)	echo "<td class='ui-state-verde align-right'>$TheField</td>";							 
								else echo "<td class='ui-state-rojo align-right'>$TheField</td>";	
						break;
						case 5: $TheField="-";
								if ($diferencia==0)	echo "<td class='ui-state-verde align-right'>$TheField</td>";							 
								else echo "<td class='ui-state-rojo align-right'>$TheField</td>";	
						break;
						case 6: $TheField=number_format($fl_precio_factura-$total_total,2);
								if ($TheField <> 0 ) echo "<td class='ui-state-rojo align-right'>$TheField</td>";						 
								else echo "<td class='ui-state-verde align-right'>$TheField</td>";						 
						break;
						case 7: $TheField=$tx_moneda;
								if ($diferencia==0)	echo "<td class='ui-state-verde align-center'>$TheField</td>";							 
								else echo "<td class='ui-state-rojo align-center'>$TheField</td>";							 
						break;
					}							
				}	
				echo "<tr>";	

			echo "</table>";	
			?>           
            <input id="cap_gasto_unitario" name="cap_gasto_unitario" type="hidden" value="<? echo $cap_gasto_unitario ?>" />
            <?
			}					
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  