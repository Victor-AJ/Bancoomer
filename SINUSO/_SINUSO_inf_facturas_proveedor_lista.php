<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
	<script type="text/javascript">			
	 
		$("#verFacturas").find("tr").hover(		 
        	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
         );
	</script> 
<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	# ============================
	# Recibo Variables
	# ============================
	$par_proveedor	= $_GET['id']; 	
	$par_cuenta		= $_GET['id1']; 
	
	//echo "busca_proveedor",$par_proveedor;
	//echo "<br/>";
	//echo "busca_proveedor",$par_cuenta;

	$banio 		= $id.'%';		
	$bproveedor	= '%'.$tx_busca_proveedor.'%';		
	$bfactura	= '%'.$tx_busca_factura.'%';		
	
	$sql = "   SELECT id_factura, tx_anio, tx_proveedor_corto, tx_cuenta, tx_factura, fh_inicio, fh_final, fl_precio_usd, fl_precio_mxn, fl_tipo_cambio, tx_mes ";
	$sql.= "     FROM tbl_factura a, tbl_proveedor b, tbl_cuenta c, tbl_mes d ";
	$sql.= "    WHERE tx_anio 				LIKE '$banio' ";
	$sql.= "	  AND tx_proveedor_corto 	LIKE '$bproveedor' ";
	$sql.= "	  AND tx_factura 			LIKE '$bfactura' ";
	$sql.= "      AND a.id_proveedor		= b.id_proveedor ";
	$sql.= "      AND a.id_cuenta 			= c.id_cuenta ";
	$sql.= "      AND a.id_mes 				= d.id_mes ";
	$sql.= " ORDER BY tx_anio, tx_proveedor_corto, tx_cuenta ";   
	
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
			'fh_inicio'			=>$row["fh_inicio"],
	  		'fh_final'			=>$row["fh_final"],
	  		'fl_precio_usd'		=>$row["fl_precio_usd"],
	  		'fl_precio_mxn'		=>$row["fl_precio_mxn"],
	  		'fl_tipo_cambio'	=>$row["fl_tipo_cambio"],
  			'tx_mes'			=>$row["tx_mes"]	  		
		);
	} 	
	
	$registros=count($TheCatalogo);	
	//echo "<br>";
	//echo "registros", $registros;
	
	?>
    <br>
  	<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:98%;"> 
    <fieldset>
    <legend class="ui-state-default"><b><em>RESULTADO DE LA B&Uacute;SQUEDA ...</em></b></legend>
	<div id="divListaFacturas"> 
	<?
	if ($registros==0) { 
		echo "<table id='verFacturas' align='center' width='100%'>";
		echo "<br>";
		echo "<tr>";
		echo "<td class='align-center'><em><b>Sin Informaci&oacute;n Encontrada ...</b></em></td>";
		echo "</tr>";	
		echo "<br>";		
		echo "</table>";
	} else {
		echo "<table id='verFacturas' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<15; $a++)
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
					echo "<td width='13%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='Cuenta';
					echo "<td width='13%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 4 : $TheField='Factura';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 5 : $TheField='Monto USD';
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField='Monto MXN';
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 7 : $TheField='Tipo de Cambio'; 
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 8 : $TheField='Periodo'; 
					echo "<td width='11%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 9 : $TheField='Amortizaci&oacute;n'; 
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				//case 10 : $TheField='Detalle'; 
				//	echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				//break;
				case 10 : $TheField='Detalle'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 11 : $TheField='Gr&aacute;fica'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 12 : $TheField='Factura'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 13 : $TheField='Editar'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 14 : $TheField='Borrar'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		
		//$j=0;		
		$in_total_licencias=0;	
		$fl_total_precio_usd=0;	
		$fl_total_precio_mxn=0;	
		
		for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$id_factura			=$TheCatalogo[$i]['id_factura'];
				$tx_anio			=$TheCatalogo[$i]['tx_anio'];
				$tx_proveedor_corto	=$TheCatalogo[$i]['tx_proveedor_corto'];
				$tx_cuenta			=$TheCatalogo[$i]['tx_cuenta'];
				$tx_factura			=$TheCatalogo[$i]['tx_factura'];
				$fh_inicio			=$TheCatalogo[$i]['fh_inicio'];
				$fh_final			=$TheCatalogo[$i]['fh_final'];
				$fl_precio_usd		=$TheCatalogo[$i]['fl_precio_usd'];
				$fl_precio_mxn		=$TheCatalogo[$i]['fl_precio_mxn'];
				$fl_tipo_cambio		=$TheCatalogo[$i]['fl_tipo_cambio'];	  		
				$tx_mes				=$TheCatalogo[$i]['tx_mes'];
				
				//$j++;				

				$fl_total_precio_usd=$fl_total_precio_usd+$fl_precio_usd;
				$fl_total_precio_mxn=$fl_total_precio_mxn+$fl_precio_mxn;
				
				//============================================
				//Busco moneda
				//============================================				
				if ($fl_precio_usd>0) $tx_moneda="USD";
				else $tx_moneda="MXN";
				
				//echo "<br>";
				//echo "USD",$fl_precio_usd;
				//echo "<br>";
				//echo "MXN",$fl_precio_mxn;
						
				//============================================
				//Busco cuadre de Factura
				//============================================
				$sql1 = " SELECT SUM( fl_precio_usd ) AS fl_total_detalle_usd, SUM( fl_precio_mxn ) AS fl_total_detalle_mxn ";
   				$sql1.= " 	FROM tbl_factura_detalle ";
   				$sql1.= "  WHERE id_factura	=$id_factura "; 
				//$sql1.= " GROUP BY id_factura "; 
				
				//echo " sql ",$sql1;
				
				$result1 = mysqli_query($mysql, $sql1);	
				while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
				{	
					$TheCatalogoTotal[] = array(
						'fl_total_detalle_usd'=>$row1["fl_total_detalle_usd"],
						'fl_total_detalle_mxn'=>$row1["fl_total_detalle_mxn"]
					);
				}	
				
				for ($j=0; $j < count($TheCatalogoTotal); $j++)	{         			 
					while ($elemento1 = each($TheCatalogoTotal[$j]))				
						$fl_total_detalle_usd	=$TheCatalogoTotal[$j]['fl_total_detalle_usd'];
						$fl_total_detalle_mxn	=$TheCatalogoTotal[$j]['fl_total_detalle_mxn'];						  		
				} 	
				
				$fl_diferencia_usd = $fl_total_detalle_usd - $fl_precio_usd;
			 	$fl_diferencia_mxn = $fl_total_detalle_mxn - $fl_precio_mxn;
				
				//echo "Diferencia USD", $fl_diferencia_usd;
				//echo "<br>";
				//echo "Diferencia MXN", $fl_diferencia_mxn;
				//echo "<br>";
				//echo "Moneda", $tx_moneda;
				//echo "<br>";
				//echo "Monto USD factura", $fl_total_detalle_usd;
				//echo "<br>";
				//echo "Monto USD detalle", $fl_precio_usd;				
				//echo "<br>";				
				//echo "Monto MXN factura", $fl_total_detalle_mxn;
				//echo "<br>";				
				//echo "Monto MXN detalle", $fl_precio_mxn;	
															
				if ($tx_moneda=="USD") {
					if($fl_total_detalle_usd==NULL) $TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-help' title='Factura sin Capturar la derrama ...'></span></a>";
					else if($fl_diferencia_usd > 1 OR $fl_diferencia_usd < -1) 					$TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-close' title='Factura sin cuadrar con la derrama ...'></span></a>";
					else $TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-check' title='Factura cuadrada con la derrama ...'></span></a>";
				} else {
					if($fl_total_detalle_mxn==NULL) $TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-help' title='Factura sin Capturar la derrama ...'></span></a>";
					else if($fl_diferencia_mxn > 1 OR $fl_diferencia_mxn < -1  ) $TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-close' title='Factura sin cuadrar con la derrama ...'></span></a>";
					else $TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-check' title='Factura cuadrada con la derrama ...'></span></a>";
				}
				
				//if ($fl_total_detalle_usd==NULL)
				//{	
				//	$TheColumnCuadre = "<a href='#'><span class='ui-icon ui-icon-check' title='Factura Cuadrada...'></span></a>";			
				//} else {
				//	$TheColumnCuadre = "<a href='#'><span class='ui-icon ui-icon-check' title='Factura Cuadrada...'></span></a>";			
				//}
				
				//echo "<br>";
				//echo "fl_total_detalle_usd",$fl_total_detalle_usd;
				//echo "<br>";
				//echo "fl_total_detalle_mxn",$fl_total_detalle_mxn;
				
				
				//==========================							  						
				
				for ($a=0; $a<15; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$i; 
								echo "<td class='align-center'>$TheColumn</td>";
						break;						
						case 1: $TheColumn=$tx_anio;
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 2: $TheColumn=$tx_proveedor_corto;
								echo "<td class='align-left'>$TheColumn</td>";
						break;
						case 3: $TheColumn=$tx_cuenta;
								echo "<td class='align-left'>$TheColumn</td>";
						break;
						case 4: $TheColumn=$tx_factura;
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 5: $TheColumn=number_format($fl_precio_usd,0); 
								if($TheColumn=="0") echo "<td class='align-right'>-</td>";
								else echo "<td class='align-right'>$TheColumn</td>";
						break;
						case 6: $TheColumn=number_format($fl_precio_mxn,0); 
								if($TheColumn=="0") echo "<td class='align-right'>-</td>";
								else echo "<td class='align-right'>$TheColumn</td>";
						break;
						case 7: $TheColumn=number_format($fl_tipo_cambio,2); 
								echo "<td class='align-right'>$TheColumn</td>";
						break;
						case 8: $TheColumn=cambiaf_a_normal($fh_inicio)." - ".cambiaf_a_normal($fh_final);
								if($TheColumn=="") echo "<td class='align-left'>&nbsp;</td>";					
								echo "<td class='align-center'>$TheColumn</td>";
						break;
						case 9: $TheColumn=$tx_mes; 
								if($TheColumn=="") echo "<td class='align-left'>&nbsp;</td>";						
								else echo "<td class='align-left'>$TheColumn</td>";							
						break;
						//case 10 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)';><span class='ui-icon ui-icon-grip-solid-horizontal' title='Presione para ver el Detalle de la Factura ...'></span></a>";				
						//		echo "<td class='align-center' valign='top'>$TheColumn</td>";
						//break;	
						case 10: $TheColumn=$TheColumnCuadre;
								 //$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)';><span class='ui-icon ui-icon-grip-solid-horizontal' title='Presione para ver el Detalle de la Factura ...'></span></a>";				
								echo "<td class='ui-widget-header align-center'>$TheColumn</td>";
						break;
						case 11 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnEditFactura($id_factura)';><span class='ui-icon ui-icon-signal' title='Presione para ver Gr&aacute;fica ...'></span></a>";				
								echo "<td class='ui-widget-header align-center' valign='top'>$TheColumn</td>";
						break;	
						case 12 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnEditFactura($id_factura)';><span class='ui-icon ui-icon-document' title='Presione para ver Factura en formato PDF ...'></span></a>";				
								echo "<td class='ui-widget-header align-center' valign='top'>$TheColumn</td>";
						break;	
						case 13 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnEditFactura($id_factura)';><span class='ui-icon ui-icon-pencil' title='Presione para EDITAR ...'></span></a>";				
								echo "<td class='ui-widget-header align-center' valign='top'>$TheColumn</td>";
						break;																					
						case 14 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnDeleteFactura($id_factura)';><span class='ui-icon ui-icon-trash' title='Presione para ELIMINAR ...'></span></a>";
								echo "<td class='ui-widget-header align-center' valign='top'>$TheColumn</td>";
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
						case 0 : $TheField="Totales";
							echo "<td colspan='5' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
						break;
						case 6 : $TheField=number_format($fl_total_precio_usd,0); 	
							echo "<td class='ui-state-highlight align-right'>$TheField</td>";						 
						break;
						case 7 : $TheField=number_format($fl_total_precio_mxn,0); 
							echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
						break;
						case 8 : $TheField="";
							echo "<td colspan='8' class='ui-state-highlight'>&nbsp;</td>";					
						break;
					}							
				}	
				echo "</tr>";	
				
				for ($a=0; $a<15; $a++)
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
				case 5 : $TheField='Monto USD';
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField='Monto MXN';
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 7 : $TheField='Tipo de Cambio'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 8 : $TheField='Periodo'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 9 : $TheField='Amortizaci&oacute;n'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				//case 10 : $TheField='Detalle'; 
				//	echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				//break;
				case 10 : $TheField='Detalle'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 11 : $TheField='Gr&aacute;fica'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 12 : $TheField='Factura'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 13 : $TheField='Editar'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 14 : $TheField='Borrar'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
			}							
		}	
		echo "</tr>";	
	echo "</table>";
	}
	?>
	</fieldset>   
    <div>
<?
mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      