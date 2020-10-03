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
		 
		function btnDetalleFactura(valor0) {		
		
			var id0="id="+valor0;
			var dispatch="&dispatch=insert";
			$("#divAltaFac").hide();	
			if (valor0 == 0) { 
				$("#divAltaFac").hide(); 
				$("#divCapturaDetalleLista").hide(); 
			} else { 		
				loadHtmlAjax(true, $("#divAltaFac"), "cat_facturas_detalle.php?"+id0+dispatch); 
			}
		}
		
		function btnGraficaFactura(valor0) {			

			var id	= "id="+valor0;
			var url = "gra_fac_detalle.php?"+id;
			var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1100, height=700";
			var winName='_blank';  	
					
			window.open(url,winName,windowprops); 			
		}

		function btnExportarFactura(valor0) {			

			var id	= "id="+valor0;
			var url = "excel_derrama.php?"+id;			
			//alert("url"+url);
			window.open( url,"_blank");			
		}
		 
		function btnCarta(valor0) {			

			var id	= "id="+valor0;
			var url = "excel_carta_aceptacion.php?"+id;			
			//alert("url"+url);
			window.open( url,"_blank");			
		}
	</script> 
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	if 	(isset($_SESSION["sess_user"])) 
		$id_login = $_SESSION['sess_iduser'];
	
	$mysql=conexion_db();
	
	# ============================
	# Recibo variables
	# ============================
	$id					= $_GET['id']; 	
	$tx_busca_proveedor	= $_GET['id1']; 
	$tx_busca_factura	= $_GET['id2']; 
	$sel_busca_estatus	= $_GET['id3']; 
	$limit	= $_GET['id4']; 
	
	//echo "busca_estado",$tx_busca_estado;
	//echo "<br>";

	$banio 		= $id.'%';		
	$bproveedor	= '%'.$tx_busca_proveedor.'%';		
	$bfactura	= '%'.$tx_busca_factura.'%';		
	
	/*
	$sql = "   SELECT id_factura, tx_anio, tx_proveedor_corto, tx_cuenta, tx_factura, fh_factura, fh_inicio, fh_final, fl_precio_usd, fl_precio_mxn, fl_precio_eur, fl_tipo_cambio, tx_mes, tx_estatus, tx_moneda ";
	$sql.= "     FROM tbl_factura a, tbl_proveedor b, tbl_cuenta c, tbl_mes d, tbl_factura_estatus e, tbl_moneda f ";
	$sql.= "    WHERE tx_anio 				LIKE '$banio' ";
	$sql.= "	  AND tx_proveedor_corto 	LIKE '$bproveedor' ";
	$sql.= "	  AND tx_factura 			LIKE '$bfactura' ";
	$sql.= "      AND a.id_proveedor		= b.id_proveedor ";
	$sql.= "      AND a.id_cuenta 			= c.id_cuenta ";
	$sql.= "      AND a.id_mes 				= d.id_mes ";
	$sql.= "      AND a.id_factura_estatus	= e.id_factura_estatus ";
	$sql.= "      AND a.id_moneda			= f.id_moneda ";
	*/
	$sql = "   SELECT id_factura, tx_anio, tx_proveedor_corto, tx_cuenta, tx_factura, fh_factura, fh_inicio, fh_final, fl_precio_usd, fl_precio_mxn, fl_precio_eur, fl_tipo_cambio, tx_mes, tx_estatus, tx_moneda ";
	$sql.= "     FROM tbl_factura a inner join tbl_proveedor b on a.id_proveedor		= b.id_proveedor ";
    $sql.= "    inner join tbl_cuenta c on a.id_cuenta 			= c.id_cuenta ";
    $sql.= "    inner join tbl_mes d on a.id_mes 				= d.id_mes ";
    $sql.= "    inner join tbl_factura_estatus e on a.id_factura_estatus	= e.id_factura_estatus ";
    $sql.= "    inner join tbl_moneda f on a.id_moneda			= f.id_moneda ";
	$sql.= "    WHERE tx_anio 				LIKE '$banio' ";
	$sql.= "	  AND tx_proveedor_corto 	LIKE '$bproveedor' ";
	$sql.= "	  AND tx_factura 			LIKE '$bfactura' ";

	//bitacora,seguridad,borrados logicos
	$sql.= "      AND a.tx_indicador			='1' ";
	
	
	if ($sel_busca_estatus <> 0)  $sql.= "      AND e.id_factura_estatus	= $sel_busca_estatus ";
	//$sql.= " ORDER BY tx_anio, tx_proveedor_corto, tx_cuenta ";   
	if($limit == "") $limit = "20";
	$sql.= "	 order by id_factura desc limit 0, ".$limit." ";
	
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
  			'tx_estatus'		=>$row["tx_estatus"],	  		
  			'tx_moneda'			=>$row["tx_moneda"]	  		
		);
	} 	
	
	
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_FACTURA" , "$id_login" ,   "tx_anio=$banio tx_provedor_corto=$bproveedor tx_factura=$bfactura id_factura_estatus=$sel_busca_estatus" ,"" ,"cat_facturas_lista.php");
	 //<\BITACORA>
	
	
	$registros=count($TheCatalogo);	
	
	?>
    <br>
 	<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:98%;"> 
    <fieldset>
    <legend class="ui-state-default"><em>RESULTADO DE LA B&Uacute;SQUEDA ...</em></legend>
    <br>
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
		echo "<table id='verFacturas' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<19; $a++)
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
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='Cuenta';
					echo "<td width='13%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 4 : $TheField='Factura';
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 5 : $TheField='Monto USD';
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField='Monto MXN';
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 7 : $TheField='Monto EUR';
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 8 : $TheField='T Cambio'; 
					echo "<td width='5%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 9 : $TheField='Fecha Factura'; 
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 10 : $TheField='Periodo'; 
					echo "<td width='11%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 11 : $TheField='Mes de Pago'; 
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 12 : $TheField='Estatus'; 
					echo "<td width='5%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 13 : $TheField='Derrama'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 14 : $TheField='Carta'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 15 : $TheField='Gr&aacute;fica'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 16 : $TheField='Exportar'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 17 : $TheField='Editar'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 18 : $TheField='Borrar'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		
		$m=0;		
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
				$fh_factura			=$TheCatalogo[$i]['fh_factura'];
				$fh_inicio			=$TheCatalogo[$i]['fh_inicio'];
				$fh_final			=$TheCatalogo[$i]['fh_final'];
				$fl_precio_usd		=$TheCatalogo[$i]['fl_precio_usd'];
				$fl_precio_mxn		=$TheCatalogo[$i]['fl_precio_mxn'];
				$fl_precio_eur		=$TheCatalogo[$i]['fl_precio_eur'];
				$fl_tipo_cambio		=$TheCatalogo[$i]['fl_tipo_cambio'];	  		
				$tx_mes				=$TheCatalogo[$i]['tx_mes'];
				$tx_estatus			=$TheCatalogo[$i]['tx_estatus'];
				$tx_moneda			=$TheCatalogo[$i]['tx_moneda'];
				
				$m++;				

				$fl_total_precio_usd=$fl_total_precio_usd+$fl_precio_usd;
				$fl_total_precio_mxn=$fl_total_precio_mxn+$fl_precio_mxn;
				$fl_total_precio_eur=$fl_total_precio_eur+$fl_precio_eur;
				
				if ($tx_estatus=="PAGADA") 	$tx_fondo="ui-state-verde";				
				else if ($tx_estatus=="TRAMITE") $tx_fondo="ui-state-amarillo";				
				else if ($tx_estatus=="CANCELADA") $tx_fondo="ui-state-rojo";	
				
				# ============================================
				# Busco moneda
				# ============================================				
				//if ($fl_precio_eur<>0) 		$tx_moneda="EUR";
				//else if ($fl_precio_usd<>0) $tx_moneda="USD";
				//else 						$tx_moneda="MXN";	
				
				# ============================================
				# Busco CR Unico 
				# ============================================
				
				$sql1 = "   SELECT a.id_centro_costos, tx_centro_costos ";
   				$sql1.= " 	  FROM tbl_factura_detalle a, tbl_centro_costos b ";
   				$sql1.= "    WHERE a.id_factura			= $id_factura "; 
				$sql1.= "      AND a.id_centro_costos	= b.id_centro_costos ";

				$sql1.= "      AND b.tx_indicador='1' ";
				
				$sql1.= " GROUP BY a.id_centro_costos ";	
				
				$num_rows_cr=0;			
				
				$result1 = mysqli_query($mysql, $sql1);					
				$num_rows_cr = mysqli_num_rows($result1); 				
				
				while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
				{	
					$TheCR_Unico[] = array(
						'id_cc' =>$row1["id_centro_costos"],
						'tx_cc'	=>$row1["tx_centro_costos"]	
					);
				}	
				
				for ($j=0; $j < count($TheCR_Unico); $j++)	{         			 
					while ($elemento1 = each($TheCR_Unico[$j]))				
						$id_cc	=$TheCR_Unico[$j]['id_cc'];
						$tx_cc	=$TheCR_Unico[$j]['tx_cc'];						
				} 	
						
				# ============================================
				# Busco cuadre de Factura
				# ============================================
				$sql1 = " SELECT SUM( fl_precio_usd ) AS fl_total_detalle_usd, SUM( fl_precio_mxn ) AS fl_total_detalle_mxn, SUM( fl_precio_eur ) AS fl_total_detalle_eur ";
   				$sql1.= " 	FROM tbl_factura_detalle ";
   				$sql1.= "  WHERE id_factura	=$id_factura and tx_indicador='1' "; 
				
				//echo " sql ",$sql1;
				
				$result1 = mysqli_query($mysql, $sql1);	
				while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
				{	
					$TheCatalogoTotal[] = array(
						'fl_total_detalle_usd'=>$row1["fl_total_detalle_usd"],
						'fl_total_detalle_mxn'=>$row1["fl_total_detalle_mxn"],
						'fl_total_detalle_eur'=>$row1["fl_total_detalle_eur"]
					);
				}	
				
				for ($j=0; $j < count($TheCatalogoTotal); $j++)	{         			 
					while ($elemento1 = each($TheCatalogoTotal[$j]))				
						$fl_total_detalle_usd	=$TheCatalogoTotal[$j]['fl_total_detalle_usd'];
						$fl_total_detalle_mxn	=$TheCatalogoTotal[$j]['fl_total_detalle_mxn'];						  		
						$fl_total_detalle_eur	=$TheCatalogoTotal[$j]['fl_total_detalle_eur'];						  		
				} 	
				
				$fl_diferencia_usd = $fl_total_detalle_usd - $fl_precio_usd;
			 	$fl_diferencia_mxn = $fl_total_detalle_mxn - $fl_precio_mxn;				
			 	$fl_diferencia_eur = $fl_total_detalle_eur - $fl_precio_eur;				
				
				$notas=0;
				
				if ($fl_precio_usd < 0) 		$notas = 1;
				else if ($fl_precio_mxn < 0) 	$notas = 1;
				else if ($fl_precio_eur < 0) 	$notas = 1;
																			
				if ($tx_moneda=="USD") {
					if($fl_total_detalle_usd==NULL) {
						$TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-help' title='Factura sin Capturar la derrama ...'></span></a>";
						$color ="ui-amarillo align-center";
					} else if($fl_diferencia_usd > 1 OR $fl_diferencia_usd < -1) {
						$TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-close' title='Factura sin cuadrar con la derrama ...'></span></a>";
					  	$color ="ui-rojo align-center";
					} else {
						$TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-check' title='Factura cuadrada con la derrama ...'></span></a>";
						$color ="ui-verde align-center";
					}	
				} else if ($tx_moneda=="MXN") {
					if($fl_total_detalle_mxn==NULL) {
						$TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-help' title='Factura sin Capturar la derrama ...'></span></a>";
						$color ="ui-amarillo align-center";
					} else if($fl_diferencia_mxn > 1 OR $fl_diferencia_mxn < -1 ) { 
						$TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-close' title='Factura sin cuadrar con la derrama ...'></span></a>";
						$color ="ui-rojo align-center";
					} else { 
						$TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-check' title='Factura cuadrada con la derrama ...'></span></a>";
						$color ="ui-verde align-center";
					}	
				} else if ($tx_moneda=="EUR") {
					if($fl_total_detalle_eur==NULL) {
						$TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-help' title='Factura sin Capturar la derrama ...'></span></a>";
						$color ="ui-amarillo align-center";
					} else if($fl_diferencia_eur > 1 OR $fl_diferencia_eur < -1  ) {
						$TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-close' title='Factura sin cuadrar con la derrama ...'></span></a>";
						$color ="ui-rojo align-center";
					} else {
						$TheColumnCuadre = "<a href='#' style='cursor:pointer' onclick='javascript:btnDetalleFactura($id_factura)'><span class='ui-icon ui-icon-check' title='Factura cuadrada con la derrama ...'></span></a>";
						$color ="ui-verde align-center";
					}	
				}
				
				//==========================							  						
				
				for ($a=0; $a<19; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$m; 
								if ($notas==1) echo "<td class='ui-notas align-center'>$TheColumn</td>";
								else echo "<td class='$tx_fondo align-center'>$TheColumn</td>";
						break;						
						case 1: $TheColumn=$tx_anio;
								if ($notas==1) echo "<td class='ui-notas align-center'>$TheColumn</td>";
								else echo "<td class='$tx_fondo align-center'>$TheColumn</td>";
						break;
						case 2: $TheColumn=$tx_proveedor_corto;
								if ($notas==1) echo "<td class='ui-notas align-left'>$TheColumn</td>";
								else echo "<td class='$tx_fondo align-left'>$TheColumn</td>";
						break;
						case 3: $TheColumn=$tx_cuenta;
								if ($notas==1) echo "<td class='ui-notas align-left'>$TheColumn</td>";
								else echo "<td class='$tx_fondo align-left'>$TheColumn</td>";
						break;
						case 4: $TheColumn=$tx_factura;
								if ($notas==1) echo "<td class='ui-notas align-center'>$TheColumn</td>";
								else echo "<td class='$tx_fondo align-center'>$TheColumn</td>";
						break;
						case 5: $TheColumn=number_format($fl_precio_usd,2); 
								if($TheColumn==0) $TheColumn="-";
								if ($notas==1) echo "<td class='ui-notas align-right'>$TheColumn</td>";
								else echo "<td class='$tx_fondo align-right'>$TheColumn</td>";
						break;
						case 6: $TheColumn=number_format($fl_precio_mxn,2); 
								if($TheColumn==0) $TheColumn="-";
								if ($notas==1) echo "<td class='ui-notas align-right'>$TheColumn</td>";
								else echo "<td class='$tx_fondo align-right'>$TheColumn</td>";
						break;
						case 7: $TheColumn=number_format($fl_precio_eur,2); 
								if($TheColumn==0) $TheColumn="-";
								if ($notas==1) echo "<td class='ui-notas align-right'>$TheColumn</td>";
								else echo "<td class='$tx_fondo align-right'>$TheColumn</td>";
						break;
						case 8: $TheColumn=number_format($fl_tipo_cambio,4); 
								if ($notas==1) echo "<td class='ui-notas align-right'>$TheColumn</td>";
								else echo "<td class='$tx_fondo align-right'>$TheColumn</td>";
						break;
						case 9: $TheColumn=cambiaf_a_normal($fh_factura);
								if($TheColumn=="") echo "<td class='$tx_fondo align-left'>&nbsp;</td>";					
								if ($notas==1) echo "<td class='ui-notas align-center'>$TheColumn</td>";					
								else echo "<td class='$tx_fondo align-center'>$TheColumn</td>";
						break;
						case 10: $TheColumn=cambiaf_a_normal($fh_inicio)." - ".cambiaf_a_normal($fh_final);
								if($TheColumn=="") echo "<td class='$tx_fondo align-left'>&nbsp;</td>";					
								if ($notas==1) echo "<td class='ui-notas align-center'>$TheColumn</td>";
								else echo "<td class='$tx_fondo align-center'>$TheColumn</td>";
						break;						
						case 11: $TheColumn=$tx_mes; 
								if($TheColumn=="") echo "<td class='$tx_fondo align-left'>&nbsp;</td>";						
								if ($notas==1) echo "<td class='ui-notas align-left'>$TheColumn</td>";							
								else echo "<td class='$tx_fondo align-left'>$TheColumn</td>";							
						break;
						case 12: $TheColumn=$tx_estatus;
								if($TheColumn=="") echo "<td class='$tx_fondo align-left'>&nbsp;</td>";					
								if ($notas==1) echo "<td class='ui-notas align-center'>$TheColumn</td>";							
								else echo "<td class='$tx_fondo align-center'>$TheColumn</td>";
						break;
						case 13: $TheColumn=$TheColumnCuadre;
								echo "<td class='$color'>$TheColumn</td>";
						break;
						case 14 : if ($num_rows_cr==1) {	
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnCarta($id_factura)'><span class='ui-icon ui-icon-document' title='Presione para ir a la Carta de Aceptaci&oacute;n... (CR Unico $tx_cc)'></span></a>";				
									echo "<td class='ui-verde align-center' valign='top'>$TheColumn</td>";
								} else { 	
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnCarta($id_factura)'><span class='ui-icon ui-icon-newwin' title='Presione para ir a la Carta de Aceptaci&oacute;n ...'></span></a>";				
									echo "<td class='ui-naranja align-center'>$TheColumn</td>";
								}	
						break;	
						case 15 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnGraficaFactura($id_factura)'><span class='ui-icon ui-icon-signal' title='Presione para ver Gr&aacute;fica ...'></span></a>";				
								echo "<td class='ui-widget-header align-center' valign='top'>$TheColumn</td>";
						break;	
						case 16 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnExportarFactura($id_factura)'><span class='ui-icon ui-icon-extlink' title='Presione para Exportar los datos de la Derrama ...'></span></a>";				
								echo "<td class='ui-widget-header align-center' valign='top'>$TheColumn</td>";
						break;								
						case 17 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnEditFactura($id_factura)'><span class='ui-icon ui-icon-pencil' title='Presione para EDITAR ...'></span></a>";				
								echo "<td class='ui-widget-header align-center' valign='top'>$TheColumn</td>";
						break;																					
						case 18 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnDeleteFactura($id_factura)'><span class='ui-icon ui-icon-trash' title='Presione para ELIMINAR ...'></span></a>";
								echo "<td class='ui-widget-header align-center' valign='top'>$TheColumn</td>";
						break;			
					}							
				}				
				echo "</tr>";		
				}								
				echo "<tr>";								  
				for ($a=0; $a<19; $a++)
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
						case 8 : $TheField=number_format($fl_total_precio_eur,0); 
							echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
						break;
						case 9 : $TheField="";
							echo "<td colspan='11' class='ui-state-highlight'>&nbsp;</td>";					
						break;
					}							
				}	
				echo "</tr>";	
				echo "<tr>";
				for ($a=0; $a<19; $a++)
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
						case 7 : $TheField='Monto EUR';
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
						break;
						case 8 : $TheField='T Cambio'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 9 : $TheField='Fecha Factura'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 10 : $TheField='Periodo'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 11 : $TheField='Amortizaci&oacute;n'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 12 : $TheField='Estatus'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 13 : $TheField='Detalle'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 14 : $TheField='Carta'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 15 : $TheField='Gr&aacute;fica'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 16 : $TheField='Factura'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 17 : $TheField='Editar'; 
							echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
						break;
						case 18 : $TheField='Borrar'; 
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