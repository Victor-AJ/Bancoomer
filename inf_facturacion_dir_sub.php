<?php
session_start();
if 	(isset($_SESSION["sess_user"]))
{
	
	$id_login = $_SESSION['sess_iduser'];
	
?>
	<input id="par_direccion" name="par_direccion" type="hidden" value="<?php echo $par_direccion ?>" /> 
	<script type="text/javascript">			
	 
		$("#lineaFacMes").find("tr").hover(		 
        	function() { $(this).addClass("ui-state-hover"); },
         	function() { $(this).removeClass("ui-state-hover"); }
        );
		
		function btnInformesDirFacDet(v1) 
		{
			var p1= parseInt(v1); 			
			var id0="par_direccion="+$("#par_direccion").val();
			var id1="&par_subdireccion="+p1;
			var id2="&par_anio="+$("#par_anio").val();
			var id3="&par_moneda="+$("#sel_moneda_d").val();
			var id4="&par_estatus="+$("#sel_estatus_d").val();
			var id5= "&par_cuenta="+$("#sel_cta_contable").val();
			
			//alert ("Entre "+id0+id1+id2+id3);
	
			$("#divDetalleFac").html("");	
			$("#divDetalleFacDir").html("");			
	
			loadHtmlAjax(true, $("#divDetalleFac"), "inf_facturacion_dir_sub_dep.php?"+id0+id1+id2+id3+id4+id5); 
		}	 
		
		function btnInformesFacDetMes(v1,v2,v3) {
		
			var p1= parseInt(v1);
			var p2= parseInt(v2);
			var p3= parseInt(v3); 			
			var id0="par_direccion="+$("#par_direccion").val();
			var id1="&par_subdireccion="+p1;
			var id2="&par_anio="+$("#par_anio").val();
			var id3="&par_moneda="+$("#sel_moneda_d").val();
			var id4="&par_estatus="+$("#sel_estatus_d").val();
			var id5="&par_mes="+p2;
			var id6="&par_proveedor="+p3;
			var id7= "&par_cuenta="+$("#sel_cta_contable").val();
			
			
			//alert ("Entre "+id0+id1+id2+id3+id4+id5+id6);
	
			$("#divDetalleFac").html("");			
			$("#divDetalleFacDir").html("");			
	
			loadHtmlAjax(true, $("#divDetalleFac"), "inf_facturacion_dir_sub_mes.php?"+id0+id1+id2+id3+id4+id5+id6+id7); 
		}	 
		 
	</script> 
<?php
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");  
	$mysql=conexion_db();	

	$par_anio 		= $_GET["par_anio"];	
	$par_direccion 	= $_GET["par_direccion"];
	$par_moneda 	= $_GET["par_moneda"];
	$par_estatus 	= $_GET["par_estatus"];
	$par_cuenta = $_GET["par_cuenta"];
	
	if ($par_anio=="2011") $in_tc = 13.5;
	else $in_tc = 14;
	
	$tx_mes1="ENERO";
	$tx_mes2="FEBRERO";
	$tx_mes3="MARZO";
	$tx_mes4="ABRIL";
	$tx_mes5="MAYO";
	$tx_mes6="JUNIO";
	$tx_mes7="JULIO";
	$tx_mes8="AGOSTO";
	$tx_mes9="SEPTIEMBRE";
	$tx_mes10="OCTUBRE";
	$tx_mes11="NOVIEMBRE";
	$tx_mes12="DICIEMBRE";	
	
	//echo "<br>"; 
	//echo "Moneda",$par_moneda; 
	
	# ===============================================================================
	# NOTAS	
	# ===============================================================================
		
	if ($par_moneda=="USD") $nota0 ="Monto en USD (D&oacute;lares Americanos).";
	else if ($par_moneda=="MXN") $nota0 ="Monto en MXN (Pesos Mexicanos).";
	else if ($par_moneda=="EUR") $nota0 ="Monto en EUR (EUROS).";
	
	$fecha_hoy = date("j-m-Y");
	
	# ==========================================
	# Busco Direccion
	# ==========================================
	
	$sql = "   SELECT tx_nombre_corto ";
	$sql.= "	 FROM tbl_direccion ";
	$sql.= "    WHERE id_direccion = $par_direccion  ";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeDireccion[] = array(
			"tx_direccion"	=>$row["tx_nombre_corto"]
		);
	} 	
	
	for ($i=0; $i < count($TheInformeDireccion); $i++)	{ 	        			 
			while ($elemento = each($TheInformeDireccion[$i]))				
				$tx_direccion	=$TheInformeDireccion[$i]["tx_direccion"];
	}
	
	// ===========================================
	
	// echo "A&ntilde;o = ",$par_anio;	
	// echo "<br/>";
	// echo "Direccion = ",$par_direccion;	
	// echo "<br/>";
	
	if ($par_moneda=="USD") 
		$sql = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
	else 
		$sql = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	 
	
	$sql.= "     FROM tbl_factura_detalle b, tbl_factura a,  tbl_centro_costos c,  tbl_direccion d, tbl_subdireccion e, tbl_factura_estatus f  , tbl_producto p ";
	$sql.= "    WHERE tx_anio 				= '$par_anio' and a.tx_indicador ='1' ";
	$sql.= "      AND a.id_factura 			= b.id_factura  and b.tx_indicador ='1' ";
	$sql.= "      AND b.id_centro_costos	= c.id_centro_costos  and c.tx_indicador ='1' ";
	$sql.= "      AND c.id_direccion	= d.id_direccion  and d.tx_indicador ='1' ";
	$sql.= "      AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and   id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	
	$sql.= "      AND c.id_direccion 		= $par_direccion ";
	$sql.= "      AND c.id_subdireccion 	= e.id_subdireccion  and e.tx_indicador ='1' ";
	$sql.= " 	  AND a.id_factura_estatus	= f.id_factura_estatus  and f.tx_indicador ='1' ";	
	$sql.= " 	  AND b.id_producto 		= p.id_producto  and p.tx_indicador ='1' ";
	
	if ($par_estatus==0) 
		{ }
	else 
		$sql.= " AND f.id_factura_estatus	= $par_estatus ";

				
	if ($par_cuenta <> 0)
		$sql.= " 	AND p.id_cuenta_contable=  $par_cuenta "; 
	
	
	
	$sql.= " GROUP BY e.id_subdireccion ";
	$sql.= " ORDER BY total_precio_usd DESC ";

	//echo "sql",$sql;
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeSubdireccionFac[] = array(
			"id_subdireccion"	=>$row["id_subdireccion"],
			"tx_subdireccion"	=>$row["tx_subdireccion"],
	  		"total_precio_usd"	=>$row["total_precio_usd"]
		);
	} 	
	
	$registros=count($TheInformeSubdireccionFac);	
	echo "<br/>";
	echo "<div class='ui-widget-header align-center'>FACTURACION - $tx_direccion</div>";
	if ($registros==0) {	
		echo "<table align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<br/>";
		echo "<tr>";
		echo "<td class='align-center'><em><b>Sin Informaci&oacute;n ...</b></em></td>";
		echo "</tr>";	
		echo "<br/>";		
		echo "</table>";	 
	} else {			
		echo "<table id='lineaFacMes' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		echo "<td width='3%' class='ui-state-highlight align-center'>#</td>";							 
		echo "<td width='10%' class='ui-state-highlight align-center'>DIRECCION</td>";	
		echo "<td width='7%' class='ui-state-highlight align-center'>PROVEEDOR</td>";				
		echo "<td width='6%' class='ui-state-highlight align-center'>$tx_mes1</td>";	
		echo "<td width='6%' class='ui-state-highlight align-center'>$tx_mes2</td>";						 
		echo "<td width='6%' class='ui-state-highlight align-center'>$tx_mes3</td>";	
		echo "<td width='6%' class='ui-state-highlight align-center'>$tx_mes4</td>";	
		echo "<td width='6%' class='ui-state-highlight align-center'>$tx_mes5</td>";						 
		echo "<td width='6%' class='ui-state-highlight align-center'>$tx_mes6</td>";	
		echo "<td width='6%' class='ui-state-highlight align-center'>$tx_mes7</td>";	
		echo "<td width='6%' class='ui-state-highlight align-center'>$tx_mes8</td>";						 
		echo "<td width='6%' class='ui-state-highlight align-center'>$tx_mes9</td>";	
		echo "<td width='6%' class='ui-state-highlight align-center'>$tx_mes10</td>";	
		echo "<td width='6%' class='ui-state-highlight align-center'>$tx_mes11</td>";						 
		echo "<td width='6%' class='ui-state-highlight align-center'>$tx_mes12</td>";	
		echo "<td width='8%' class='ui-state-highlight align-center'>TOTAL</td>";						 
		echo "</tr>";		
			
		$fl_total_precio_usd=0;		
		$fl_total_precio_usd_1=0;			
		$fl_total_precio_usd_2=0;			
		$fl_total_precio_usd_3=0;	
		
		for ($i=0; $i < count($TheInformeSubdireccionFac); $i++)
		{ 	        			 
			while ($elemento = each($TheInformeSubdireccionFac[$i]))				
				$id_subdireccion	=$TheInformeSubdireccionFac[$i]["id_subdireccion"];	  		
				$tx_subdireccion	=$TheInformeSubdireccionFac[$i]["tx_subdireccion"];						
				
				$total_precio_usd_b_ren=0;
				$total_precio_usd_r_ren=0;
				$total_precio_usd_o_ren=0;
				
				$c++;	
				
				# ========================================
				#  Busco BLOOMBERG por mes
				# ========================================
				
				$tx_proveedor1 = "BLOOMBERG";
				$nu_proveedor1 = 1;
				
				for ($m=1; $m<13; $m++)
				{ 										
					if ($par_moneda=="USD") $sql1 = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
					else if ($par_moneda=="MXN") $sql1 = " SELECT c.id_subdireccion, e.tx_subdireccion,  SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd  ";	 
					$sql1.= "     FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_subdireccion e, tbl_proveedor f, tbl_factura_estatus g  , tbl_producto p  ";
					$sql1.= "    WHERE tx_anio 				= '$par_anio'  and a.tx_indicador ='1'  ";
					$sql1.= "      AND a.id_factura 		= b.id_factura  and b.tx_indicador ='1' ";
					$sql1.= "      AND b.id_centro_costos	= c.id_centro_costos and c.tx_indicador ='1' ";
					$sql1.= "      AND c.id_direccion 		= $par_direccion ";
					
					$sql1.= "      AND c.id_direccion	= d.id_direccion  and d.tx_indicador ='1' ";
					$sql1.= "      AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
					
	
					$sql1.= "      AND c.id_subdireccion 	= e.id_subdireccion  and e.tx_indicador ='1' ";
					$sql1.= "      AND c.id_subdireccion 	= $id_subdireccion ";
					$sql1.= "      AND a.id_mes 			= $m ";
					$sql1.= "      AND a.id_proveedor 		= f.id_proveedor  and f.tx_indicador ='1' ";
					$sql1.= "      AND tx_proveedor_corto	= '$tx_proveedor1' "; 
					$sql1.= " 	   AND a.id_factura_estatus	= g.id_factura_estatus  and g.tx_indicador ='1' ";
					$sql1.= " 	  AND b.id_producto 		= p.id_producto  and p.tx_indicador ='1' ";

						
					if ($par_estatus==0) { }
					else $sql1.= " AND g.id_factura_estatus = $par_estatus ";	
					
					
					if ($par_cuenta <> 0)
						$sql1.= " 	AND p.id_cuenta_contable=  $par_cuenta "; 
	
		
		
					$sql1.= " GROUP BY e.id_subdireccion ";
					
					//echo "sql ",$sql1;
					//echo "<br>";
					
					$result1 = mysqli_query($mysql, $sql1);	
					while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{	
						$TheInformeSubdireccionFac_1[] = array(						
							'total_precio_usd_b_0'	=>$row1["total_precio_usd"]
						);
					} 
					
					$num_result1=mysqli_num_rows($result1);	
					//echo "<br/>";
					//echo "num_result1 = ",$num_result1;
					//echo "<br/>";
					
					for ($j=0; $j < count($TheInformeSubdireccionFac_1); $j++)
					{ 	        			 
						while ($elemento = each($TheInformeSubdireccionFac_1[$j]))	
							$total_precio_usd_b_0 =$TheInformeSubdireccionFac_1[$j]['total_precio_usd_b_0'];	
					}							
					
					if ($m==1) {
						if ($num_result1==0) { $total_precio_usd_b_1=0; }
						else{
							$total_precio_usd_b_1		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_1	=$fl_total_precio_usd_b_1+$total_precio_usd_b_1;
						}	
					} else if ($m==2) {
					
					/*	echo " m ", $m;
						echo "<br>";
						echo " resultado del query ", $num_result1;
						echo "<br>";
						echo " Precio ", $total_precio_usd_b_0;
						echo "<br>";	*/				
					
						if ($num_result1==0)  { $total_precio_usd_b_2=0;  }
						else{							
							$total_precio_usd_b_2		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_2	=$fl_total_precio_usd_b_2+$total_precio_usd_b_2;
							
						/*	echo " total_precio_usd_b_0 ", $total_precio_usd_b_0;
							echo "<br>";
							echo " total_precio_usd_b_2 ", $total_precio_usd_b_2;
							echo "<br>"; */
							
						}					
					} else if ($m==3){
						if ($num_result1==0)  { $total_precio_usd_b_3=0; }
						else{
							$total_precio_usd_b_3		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_3	=$fl_total_precio_usd_b_3+$total_precio_usd_b_3;
						}						
					} else if ($m==4){
						if ($num_result1==0)  { $total_precio_usd_b_4=0; }
						else{
							$total_precio_usd_b_4		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_4	=$fl_total_precio_usd_b_4+$total_precio_usd_b_4;
						}						
					} else if ($m==5){
						if ($num_result1==0)  { $total_precio_usd_b_5=0; }
						else{
							$total_precio_usd_b_5		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_5	=$fl_total_precio_usd_b_5+$total_precio_usd_b_5;
						}						
					} else if ($m==6){
						if ($num_result1==0)  { $total_precio_usd_b_6=0; }
						else{
							$total_precio_usd_b_6		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_6	=$fl_total_precio_usd_b_6+$total_precio_usd_b_6;
						}						
					} else if ($m==7){
						if ($num_result1==0)  { $total_precio_usd_b_7=0; }
						else{
							$total_precio_usd_b_7		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_7	=$fl_total_precio_usd_b_7+$total_precio_usd_b_7;
						}						
					} else if ($m==8){
						if ($num_result1==0)  { $total_precio_usd_b_8=0; }
						else{
							$total_precio_usd_b_8		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_8	=$fl_total_precio_usd_b_8+$total_precio_usd_b_8;
						}						
					} else if ($m==9){
						if ($num_result1==0)  { $total_precio_usd_b_9=0; }
						else{
							$total_precio_usd_b_9		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_9	=$fl_total_precio_usd_b_9+$total_precio_usd_b_9;
						}						
					} else if ($m==10){
						if ($num_result1==0)  { $total_precio_usd_b_10=0; }
						else{
							$total_precio_usd_b_10		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_10	=$fl_total_precio_usd_b_10+$total_precio_usd_b_10;
						}						
					} else if ($m==11){
						if ($num_result1==0)  { $total_precio_usd_b_11=0; }
						else{
							$total_precio_usd_b_11		=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_11	=$fl_total_precio_usd_b_11+$total_precio_usd_b_11;
						}						
					} else if ($m==12){
						if ($num_result1==0)  { $total_precio_usd_b_12=0; }
						else{
							$total_precio_usd_b_12	 	=$total_precio_usd_b_0; 
							$fl_total_precio_usd_b_12	=$fl_total_precio_usd_b_12+$total_precio_usd_b_12;
						}						
					}					
					$total_precio_usd_b_ren=$total_precio_usd_b_1+$total_precio_usd_b_2+$total_precio_usd_b_3+$total_precio_usd_b_4+$total_precio_usd_b_5+$total_precio_usd_b_6+$total_precio_usd_b_7+$total_precio_usd_b_8+$total_precio_usd_b_9+$total_precio_usd_b_10+$total_precio_usd_b_11+$total_precio_usd_b_12;
				}				
				
				# ========================================
				#  Busco REUTERS por mes
				# ========================================
				
				$tx_proveedor2 = "REUTERS";
				$nu_proveedor2 = 2;
								
				for ($m=1; $m<13; $m++)
				{ 	
					if ($par_moneda=="USD") $sql1 = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
					else if ($par_moneda=="MXN") $sql1 = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	 
					$sql1.= "     FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_subdireccion e, tbl_proveedor f, tbl_factura_estatus g , tbl_producto p   ";
					$sql1.= "    WHERE tx_anio 				= '$par_anio' and a.tx_indicador='1' ";
					$sql1.= "      AND a.id_factura 		= b.id_factura  and b.tx_indicador='1' ";
					$sql1.= "      AND b.id_centro_costos	= c.id_centro_costos  and c.tx_indicador='1' ";
					
					$sql1.= "      AND c.id_direccion	= d.id_direccion  and d.tx_indicador ='1' ";
					$sql1.= "      AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
			
					
					$sql1.= "      AND c.id_direccion 		= $par_direccion ";
					$sql1.= "      AND c.id_subdireccion 	= e.id_subdireccion  and e.tx_indicador='1'";
					$sql1.= "      AND c.id_subdireccion 	= $id_subdireccion ";
					$sql1.= "      AND a.id_mes 			= $m ";
					$sql1.= "      AND a.id_proveedor 		= f.id_proveedor   and f.tx_indicador='1'";
					$sql1.= "      AND tx_proveedor_corto	= '$tx_proveedor2' "; 
					$sql1.= " 	   AND a.id_factura_estatus	= g.id_factura_estatus   and g.tx_indicador='1'";	
					$sql1.= " 	  AND b.id_producto 		= p.id_producto   and p.tx_indicador='1' ";
										
										
					if ($par_estatus==0) { }
					else $sql1.= " AND g.id_factura_estatus = $par_estatus ";

					if ($par_cuenta <> 0)
						$sql1.= " 	AND p.id_cuenta_contable=  $par_cuenta "; 
	
		
						
						
					$sql1.= " GROUP BY e.id_subdireccion ";
					
					//echo "sql ",$sql1;
					
					$result1 = mysqli_query($mysql, $sql1);	
					while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{	
						$TheInformeSubdireccionFac_1[] = array(						
							'total_precio_usd_r_0'	=>$row1["total_precio_usd"]
						);
					} 
					
					$num_result1=mysqli_num_rows($result1);	
					
					for ($j=0; $j < count($TheInformeSubdireccionFac_1); $j++)	{ 	        			 
						while ($elemento = each($TheInformeSubdireccionFac_1[$j]))	
							$total_precio_usd_r_0 =$TheInformeSubdireccionFac_1[$j]['total_precio_usd_r_0'];	
					}							
					
					if ($m==1) {
						if ($num_result1==0) { $total_precio_usd_r_1=0; }
						else{
							$total_precio_usd_r_1		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_1	=$fl_total_precio_usd_r_1+$total_precio_usd_r_1;
						}	
					} else if ($m==2) {
						if ($num_result1==0)  { $total_precio_usd_r_2=0;  }
						else{
							$total_precio_usd_r_2		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_2	=$fl_total_precio_usd_r_2+$total_precio_usd_r_2;
						}					
					} else if ($m==3){
						if ($num_result1==0)  { $total_precio_usd_r_3=0; }
						else{
							$total_precio_usd_r_3		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_3	=$fl_total_precio_usd_r_3+$total_precio_usd_r_3;
						}						
					} else if ($m==4){
						if ($num_result1==0)  { $total_precio_usd_r_4=0; }
						else{
							$total_precio_usd_r_4		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_4	=$fl_total_precio_usd_r_4+$total_precio_usd_r_4;
						}						
					} else if ($m==5){
						if ($num_result1==0)  { $total_precio_usd_r_5=0; }
						else{
							$total_precio_usd_r_5		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_5	=$fl_total_precio_usd_r_5+$total_precio_usd_r_5;
						}						
					} else if ($m==6){
						if ($num_result1==0)  { $total_precio_usd_r_6=0; }
						else{
							$total_precio_usd_r_6		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_6	=$fl_total_precio_usd_r_6+$total_precio_usd_r_6;
						}						
					} else if ($m==7){
						if ($num_result1==0)  { $total_precio_usd_r_7=0; }
						else{
							$total_precio_usd_r_7		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_7	=$fl_total_precio_usd_r_7+$total_precio_usd_r_7;
						}						
					} else if ($m==8){
						if ($num_result1==0)  { $total_precio_usd_r_8=0; }
						else{
							$total_precio_usd_r_8		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_8	=$fl_total_precio_usd_r_8+$total_precio_usd_r_8;
						}						
					} else if ($m==9){
						if ($num_result1==0)  { $total_precio_usd_r_9=0; }
						else{
							$total_precio_usd_r_9		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_9	=$fl_total_precio_usd_r_9+$total_precio_usd_r_9;
						}						
					} else if ($m==10){
						if ($num_result1==0)  { $total_precio_usd_r_10=0; }
						else{
							$total_precio_usd_r_10		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_10	=$fl_total_precio_usd_r_10+$total_precio_usd_r_10;
						}						
					} else if ($m==11){
						if ($num_result1==0)  { $total_precio_usd_r_11=0; }
						else{
							$total_precio_usd_r_11		=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_11	=$fl_total_precio_usd_r_11+$total_precio_usd_r_11;
						}						
					} else if ($m==12){
						if ($num_result1==0)  { $total_precio_usd_r_12=0; }
						else{
							$total_precio_usd_r_12	 	=$total_precio_usd_r_0; 
							$fl_total_precio_usd_r_12	=$fl_total_precio_usd_r_12+$total_precio_usd_r_12;
						}						
					}
					$total_precio_usd_r_ren=$total_precio_usd_r_1+$total_precio_usd_r_2+$total_precio_usd_r_3+$total_precio_usd_r_4+$total_precio_usd_r_5+$total_precio_usd_r_6+$total_precio_usd_r_7+$total_precio_usd_r_8+$total_precio_usd_r_9+$total_precio_usd_r_10+$total_precio_usd_r_11+$total_precio_usd_r_12;
				}	
				
				# ========================================
				#  Busco OTROS por mes
				# ========================================
				
				$tx_proveedor3 = "BLOOMBERG";
				$tx_proveedor4 = "REUTERS";
				$tx_proveedor5 = "OTROS";					
				$nu_proveedor3 = 3;
								
				for ($m=1; $m<13; $m++)
				{ 					
					if ($par_moneda=="USD") $sql1 = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=1,IF(a.fl_tipo_cambio=0,(b.fl_precio_mxn/$in_tc),(b.fl_precio_mxn/a.fl_tipo_cambio)),(b.fl_precio_usd))) AS total_precio_usd ";	
					else 					$sql1 = " SELECT c.id_subdireccion, e.tx_subdireccion, SUM(IF(a.id_moneda=2,IF(a.fl_tipo_cambio=0,(b.fl_precio_usd*$in_tc),(b.fl_precio_usd*a.fl_tipo_cambio)),(b.fl_precio_mxn))) AS total_precio_usd ";	 
					$sql1.= "     FROM tbl_factura a, tbl_factura_detalle b, tbl_centro_costos c, tbl_direccion d, tbl_subdireccion e, tbl_proveedor f, tbl_factura_estatus g , tbl_producto p  ";
					$sql1.= "    WHERE tx_anio 				= '$par_anio' and a.tx_indicador='1' ";
					$sql1.= "      AND a.id_factura 		= b.id_factura  and b.tx_indicador='1'  ";
					$sql1.= "      AND b.id_centro_costos	= c.id_centro_costos  and c.tx_indicador='1' ";
					$sql1.= "      AND c.id_direccion 		= $par_direccion ";
					
					$sql1.= "      AND c.id_direccion	= d.id_direccion  and d.tx_indicador ='1' ";
					$sql1.= "      AND d.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
					
					$sql1.= "      AND c.id_subdireccion 	= e.id_subdireccion  and e.tx_indicador='1'  ";
					$sql1.= "      AND c.id_subdireccion 	= $id_subdireccion ";
					$sql1.= "      AND a.id_mes 			= $m ";
					$sql1.= "      AND a.id_proveedor 		= f.id_proveedor and f.tx_indicador='1'  ";
					$sql1.= "      AND tx_proveedor_corto NOT IN ('$tx_proveedor3','$tx_proveedor4')  "; 
					$sql1.= " 	   AND a.id_factura_estatus	= g.id_factura_estatus  and g.tx_indicador='1'  ";
					$sql1.= " 	  AND b.id_producto 		= p.id_producto  and p.tx_indicador='1'  ";
					
					if ($par_estatus==0) { }
					else $sql1.= " AND g.id_factura_estatus = $par_estatus ";

					if ($par_cuenta <> 0)
						$sql1.= " 	AND p.id_cuenta_contable=  $par_cuenta "; 
	
					
					$sql1.= " GROUP BY e.id_subdireccion ";
					
					//echo "<br>";
					//echo "sql ",$sql1;
					
					$result1 = mysqli_query($mysql, $sql1);	
					while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{	
						$TheInformeSubdireccionFac_1[] = array(						
							'total_precio_usd_o_0'	=>$row1["total_precio_usd"]
						);
					} 
					
					$num_result1=mysqli_num_rows($result1);	
					
					for ($j=0; $j < count($TheInformeSubdireccionFac_1); $j++)	{ 	        			 
						while ($elemento = each($TheInformeSubdireccionFac_1[$j]))	
							$total_precio_usd_o_0 =$TheInformeSubdireccionFac_1[$j]['total_precio_usd_o_0'];	
					}							
					
					if ($m==1) {
						if ($num_result1==0) { $total_precio_usd_o_1=0; }
						else{
							$total_precio_usd_o_1		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_1	=$fl_total_precio_usd_o_1+$total_precio_usd_o_1;
						}	
					} else if ($m==2) {
						if ($num_result1==0)  { $total_precio_usd_o_2=0;  }
						else{
							$total_precio_usd_o_2		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_2	=$fl_total_precio_usd_o_2+$total_precio_usd_o_2;
						}					
					} else if ($m==3){
						if ($num_result1==0)  { $total_precio_usd_o_3=0; }
						else{
							$total_precio_usd_o_3		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_3	=$fl_total_precio_usd_o_3+$total_precio_usd_o_3;
						}						
					} else if ($m==4){
						if ($num_result1==0)  { $total_precio_usd_o_4=0; }
						else{
							$total_precio_usd_o_4		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_4	=$fl_total_precio_usd_o_4+$total_precio_usd_o_4;
						}						
					} else if ($m==5){
						if ($num_result1==0)  { $total_precio_usd_o_5=0; }
						else{
							$total_precio_usd_o_5		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_5	=$fl_total_precio_usd_o_5+$total_precio_usd_o_5;
						}						
					} else if ($m==6){
						if ($num_result1==0)  { $total_precio_usd_o_6=0; }
						else{
							$total_precio_usd_o_6		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_6	=$fl_total_precio_usd_o_6+$total_precio_usd_o_6;
						}						
					} else if ($m==7){
						if ($num_result1==0)  { $total_precio_usd_o_7=0; }
						else{
							$total_precio_usd_o_7		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_7	=$fl_total_precio_usd_o_7+$total_precio_usd_o_7;
						}						
					} else if ($m==8){
						if ($num_result1==0)  { $total_precio_usd_o_8=0; }
						else{
							$total_precio_usd_o_8		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_8	=$fl_total_precio_usd_o_8+$total_precio_usd_o_8;
						}						
					} else if ($m==9){
						if ($num_result1==0)  { $total_precio_usd_o_9=0; }
						else{
							$total_precio_usd_o_9		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_9	=$fl_total_precio_usd_o_9+$total_precio_usd_o_9;
						}						
					} else if ($m==10){
						if ($num_result1==0)  { $total_precio_usd_o_10=0; }
						else{
							$total_precio_usd_o_10		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_10	=$fl_total_precio_usd_o_10+$total_precio_usd_o_10;
						}						
					} else if ($m==11){
						if ($num_result1==0)  { $total_precio_usd_o_11=0; }
						else{
							$total_precio_usd_o_11		=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_11	=$fl_total_precio_usd_o_11+$total_precio_usd_o_11;
						}						
					} else if ($m==12){
						if ($num_result1==0)  { $total_precio_usd_o_12=0; }
						else{
							$total_precio_usd_o_12	 	=$total_precio_usd_o_0; 
							$fl_total_precio_usd_o_12	=$fl_total_precio_usd_o_12+$total_precio_usd_o_12;
						}						
					}					
					$total_precio_usd_o_ren=$total_precio_usd_o_1+$total_precio_usd_o_2+$total_precio_usd_o_3+$total_precio_usd_o_4+$total_precio_usd_o_5+$total_precio_usd_o_6+$total_precio_usd_o_7+$total_precio_usd_o_8+$total_precio_usd_o_9+$total_precio_usd_o_10+$total_precio_usd_o_11+$total_precio_usd_o_12;
				}					
				
				echo "<tr>";
									
				for ($a=0; $a<16; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$c; 									
								echo "<td rowspan='3' class='align-center' valign='center'>$TheColumn</td>";
						break;						
						case 1: $TheColumn =$tx_subdireccion;
								$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesDirFacDet($id_subdireccion)' title='Presione para ver el detalle de $tx_subdireccion ...'>$tx_subdireccion</a>";
								echo "<td rowspan='3' class='align-left' valign='center'>$TheColumn</td>";
						break;																																		
						case 2: $TheColumn=$tx_proveedor1;							
								echo "<td class='ui-state-default align-left' valign='top'>$TheColumn</td>";					
						break;																									
						case 3: $total_precio_usd_ren_tot_1=$total_precio_usd_ren_tot_1+$total_precio_usd_b_1; 
								if ($total_precio_usd_b_1==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_b_1,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,1,$nu_proveedor1)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor1 - $tx_mes1 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																									
						case 4: $total_precio_usd_ren_tot_2=$total_precio_usd_ren_tot_2+$total_precio_usd_b_2; 
								/* 	echo "<br>";
									echo " total_precio_usd_b_2 ", $total_precio_usd_b_2;
									echo "<br>"; */
								if ($total_precio_usd_b_2==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_b_2,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,2,$nu_proveedor1)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor1 - $tx_mes2 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																		
						case 5: $total_precio_usd_ren_tot_3=$total_precio_usd_ren_tot_3+$total_precio_usd_b_3; 
								if ($total_precio_usd_b_3==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_b_3,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,3,$nu_proveedor1)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor1 - $tx_mes3 $par_anio ... '>$TheColumn</a>";
								}
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;
						case 6: $total_precio_usd_ren_tot_4=$total_precio_usd_ren_tot_4+$total_precio_usd_b_4; 
								if ($total_precio_usd_b_4==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_b_4,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,4,$nu_proveedor1)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor1 - $tx_mes4 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																									
						case 7: $total_precio_usd_ren_tot_5=$total_precio_usd_ren_tot_5+$total_precio_usd_b_5; 
								if ($total_precio_usd_b_5==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_b_5,0);
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,5,$nu_proveedor1)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor1 - $tx_mes5 $par_anio ... '>$TheColumn</a>"; 
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																		
						case 8: $total_precio_usd_ren_tot_6=$total_precio_usd_ren_tot_6+$total_precio_usd_b_6; 
								if ($total_precio_usd_b_6==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_b_6,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,6,$nu_proveedor1)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor1 - $tx_mes6 $par_anio ... '>$TheColumn</a>"; 
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;
						case 9: $total_precio_usd_ren_tot_7=$total_precio_usd_ren_tot_7+$total_precio_usd_b_7; 
								if ($total_precio_usd_b_7==0) $TheColumn="-";
								else { 
									$TheColumn=number_format($total_precio_usd_b_7,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,7,$nu_proveedor1)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor1 - $tx_mes7 $par_anio ... '>$TheColumn</a>"; 
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																									
						case 10: $total_precio_usd_ren_tot_8=$total_precio_usd_ren_tot_8+$total_precio_usd_b_8; 
								 if ($total_precio_usd_b_8==0) $TheColumn="-";
								 else {
								 	$TheColumn=number_format($total_precio_usd_b_8,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,8,$nu_proveedor1)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor1 - $tx_mes8 $par_anio ... '>$TheColumn</a>"; 
								 }	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																		
						case 11: $total_precio_usd_ren_tot_9=$total_precio_usd_ren_tot_9+$total_precio_usd_b_9; 
								 if ($total_precio_usd_b_9==0) $TheColumn="-";
								 else {
								 	$TheColumn=number_format($total_precio_usd_b_9,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,9,$nu_proveedor1)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor1 - $tx_mes9 $par_anio ... '>$TheColumn</a>"; 
								 }	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;
						case 12: $total_precio_usd_ren_tot_10=$total_precio_usd_ren_tot_10+$total_precio_usd_b_10; 
								 if ($total_precio_usd_b_10==0) $TheColumn="-";
								 else { 
								 	$TheColumn=number_format($total_precio_usd_b_10,0); 
								 	$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,10,$nu_proveedor1)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor1 - $tx_mes10 $par_anio ... '>$TheColumn</a>"; 
								 }	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																									
						case 13: $total_precio_usd_ren_tot_11=$total_precio_usd_ren_tot_11+$total_precio_usd_b_11; 
								 if ($total_precio_usd_b_11==0) $TheColumn="-";
								 else {
								 	$TheColumn=number_format($total_precio_usd_b_11,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,11,$nu_proveedor1)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor1 - $tx_mes11 $par_anio ... '>$TheColumn</a>"; 
								 }	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																		
						case 14: $total_precio_usd_ren_tot_12=$total_precio_usd_ren_tot_12+$total_precio_usd_b_12; 
								 if ($total_precio_usd_b_12==0) $TheColumn="-";
								 else {
								 	$TheColumn=number_format($total_precio_usd_b_12,0); 
								 	$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,12,$nu_proveedor1)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor1 - $tx_mes12 $par_anio ... '>$TheColumn</a>"; 
								 }	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;
						case 15: $total_precio_usd_ren_tot=$total_precio_usd_ren_tot+$total_precio_usd_b_ren;
								 $TheColumn=number_format($total_precio_usd_b_ren,0); 								 
								 echo "<td class='ui-state-default align-right' valign='top'>$TheColumn</td>";							
						break;						
					}							
				}				
				echo "</tr>";	
				echo "<tr>";
				for ($a=0; $a<16; $a++)
				{
					switch ($a) 
					{   																																			
						case 2: $TheColumn=$tx_proveedor2;							
								echo "<td class='ui-state-default align-left' valign='top'>$TheColumn</td>";					
						break;																									
						case 3: $total_precio_usd_ren_tot_1=$total_precio_usd_ren_tot_1+$total_precio_usd_r_1; 
								if ($total_precio_usd_r_1==0) $TheColumn="-";
								else { 
									$TheColumn=number_format($total_precio_usd_r_1,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,1,$nu_proveedor2)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor2 - $tx_mes1 $par_anio ... '>$TheColumn</a>";
								}
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																									
						case 4: $total_precio_usd_ren_tot_2=$total_precio_usd_ren_tot_2+$total_precio_usd_r_2; 
								if ($total_precio_usd_r_2==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_r_2,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,2,$nu_proveedor2)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor2 - $tx_mes2 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																		
						case 5: $total_precio_usd_ren_tot_3=$total_precio_usd_ren_tot_3+$total_precio_usd_r_3; 
								if ($total_precio_usd_r_3==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_r_3,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,3,$nu_proveedor2)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor2 - $tx_mes3 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;
						case 6: $total_precio_usd_ren_tot_4=$total_precio_usd_ren_tot_4+$total_precio_usd_r_4; 
								if ($total_precio_usd_r_4==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_r_4,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,4,$nu_proveedor2)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor2 - $tx_mes4 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																									
						case 7: $total_precio_usd_ren_tot_5=$total_precio_usd_ren_tot_5+$total_precio_usd_r_5; 
								if ($total_precio_usd_r_5==0) $TheColumn="-";
								else { 
									$TheColumn=number_format($total_precio_usd_r_5,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,5,$nu_proveedor2)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor2 - $tx_mes5 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																		
						case 8: $total_precio_usd_ren_tot_6=$total_precio_usd_ren_tot_6+$total_precio_usd_r_6; 
								if ($total_precio_usd_r_6==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_r_6,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,6,$nu_proveedor2)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor2 - $tx_mes6 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;
						case 9: if ($total_precio_usd_r_7==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_r_7,0);
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,7,$nu_proveedor2)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor2 - $tx_mes7 $par_anio ... '>$TheColumn</a>";
								}	 
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																									
						case 10: if ($total_precio_usd_r_8==0) $TheColumn="-";
								 else {
								  	$TheColumn=number_format($total_precio_usd_r_8,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,8,$nu_proveedor2)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor2 - $tx_mes8 $par_anio ... '>$TheColumn</a>";
								 }	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																		
						case 11: if ($total_precio_usd_r_9==0) $TheColumn="-";
								 else {
								 	$TheColumn=number_format($total_precio_usd_r_9,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,9,$nu_proveedor2)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor2 - $tx_mes9 $par_anio ... '>$TheColumn</a>";
								 }		
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;
						case 12: if ($total_precio_usd_r_10==0) $TheColumn="-";
								 else {
								 	$TheColumn=number_format($total_precio_usd_r_10,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,10,$nu_proveedor2)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor2 - $tx_mes10 $par_anio ... '>$TheColumn</a>";
								 }	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																									
						case 13: if ($total_precio_usd_r_11==0) $TheColumn="-";
								 else {
								 	$TheColumn=number_format($total_precio_usd_r_11,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,11,$nu_proveedor2)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor2 - $tx_mes11 $par_anio ... '>$TheColumn</a>";
								 }		
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																		
						case 14: if ($total_precio_usd_r_12==0) $TheColumn="-";
								 else {
								 	$TheColumn=number_format($total_precio_usd_r_12,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,12,$nu_proveedor2)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor2 - $tx_mes11 $par_anio ... '>$TheColumn</a>";
								 }	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;
						case 15: $total_precio_usd_ren_tot=$total_precio_usd_ren_tot+$total_precio_usd_r_ren; 
						 		 $TheColumn=number_format($total_precio_usd_r_ren,0);
								 echo "<td class='ui-state-default align-right' valign='top'>$TheColumn</td>";							
						break;
					}							
				}				
				echo "</tr>";	
				echo "<tr>";
				for ($a=0; $a<16; $a++)
				{
					switch ($a) 
					{   																																			
						case 2: $TheColumn="OTROS";							
								echo "<td class='ui-state-default align-left' valign='top'>$TheColumn</td>";					
						break;																									
						case 3: $total_precio_usd_ren_tot_1=$total_precio_usd_ren_tot_1+$total_precio_usd_o_1; 
								if ($total_precio_usd_o_1==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_o_1,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,1,$nu_proveedor3)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor5 - $tx_mes1 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																									
						case 4: $total_precio_usd_ren_tot_2=$total_precio_usd_ren_tot_2+$total_precio_usd_o_2; 
								if ($total_precio_usd_o_2==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_o_2,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,2,$nu_proveedor3)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor5 - $tx_mes2 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																		
						case 5: $total_precio_usd_ren_tot_3=$total_precio_usd_ren_tot_3+$total_precio_usd_o_3; 
								if ($total_precio_usd_o_3==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_o_3,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,3,$nu_proveedor3)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor5 - $tx_mes3 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;
						case 6: $total_precio_usd_ren_tot_4=$total_precio_usd_ren_tot_4+$total_precio_usd_o_4; 
								if ($total_precio_usd_o_4==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_o_4,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,4,$nu_proveedor3)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor5 - $tx_mes4 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																									
						case 7: $total_precio_usd_ren_tot_5=$total_precio_usd_ren_tot_5+$total_precio_usd_o_5; 
								if ($total_precio_usd_o_5==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_o_5,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,5,$nu_proveedor3)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor5 - $tx_mes5 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																		
						case 8: $total_precio_usd_ren_tot_6=$total_precio_usd_ren_tot_6+$total_precio_usd_o_6; 
								if ($total_precio_usd_o_6==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_o_6,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,6,$nu_proveedor3)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor5 - $tx_mes6 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;
						case 9: $total_precio_usd_ren_tot_7=$total_precio_usd_ren_tot_7+$total_precio_usd_o_7; 
								if ($total_precio_usd_o_7==0) $TheColumn="-";
								else {
									$TheColumn=number_format($total_precio_usd_o_7,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,7,$nu_proveedor3)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor5 - $tx_mes7 $par_anio ... '>$TheColumn</a>";
								}	
								echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																									
						case 10: $total_precio_usd_ren_tot_8=$total_precio_usd_ren_tot_8+$total_precio_usd_o_8; 
								 if ($total_precio_usd_o_8==0) $TheColumn="-";
								 else {
								 	$TheColumn=number_format($total_precio_usd_o_8,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,8,$nu_proveedor3)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor5 - $tx_mes8 $par_anio ... '>$TheColumn</a>";
								 }	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																		
						case 11: $total_precio_usd_ren_tot_9=$total_precio_usd_ren_tot_9+$total_precio_usd_o_9; 
								 if ($total_precio_usd_o_9==0) $TheColumn="-";
								 else {
								 	$TheColumn=number_format($total_precio_usd_o_9,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,9,$nu_proveedor3)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor5 - $tx_mes9 $par_anio ... '>$TheColumn</a>";
								 }	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;
						case 12: $total_precio_usd_ren_tot_10=$total_precio_usd_ren_tot_10+$total_precio_usd_o_10; 
								 if ($total_precio_usd_o_10==0) $TheColumn="-";
								 else {
								 	$TheColumn=number_format($total_precio_usd_o_10,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,10,$nu_proveedor3)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor5 - $tx_mes10 $par_anio ... '>$TheColumn</a>";
								 }	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																									
						case 13: if ($total_precio_usd_o_11==0) $TheColumn="-";
								 else {
								 	$TheColumn = number_format($total_precio_usd_o_11,0); 
									$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,11,$nu_proveedor3)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor5 - $tx_mes11 $par_anio ... '>$TheColumn</a>";
								}	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;																		
						case 14: if ($total_precio_usd_o_12==0) $TheColumn="-";
								 else {
								 	$TheColumn=number_format($total_precio_usd_o_12,0); 
								 	$TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:btnInformesFacDetMes($id_subdireccion,12,$nu_proveedor3)' title='Presione para ver el detalle de $tx_subdireccion - $tx_proveedor5 - $tx_mes12 $par_anio ... '>$TheColumn</a>";
								 }	
								 echo "<td class='align-right' valign='top'>$TheColumn</td>";							
						break;
						case 15: $total_precio_usd_ren_tot=$total_precio_usd_ren_tot+$total_precio_usd_o_ren;
								 $TheColumn=number_format($total_precio_usd_o_ren,0); 
								 echo "<td class='ui-state-default align-right' valign='top'>$TheColumn</td>";							
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
					case 0 : $TheField='TOTAL';
							 echo "<td colspan='3' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
					break;				
					case 1 : if ($total_precio_usd_ren_tot_1==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_1,0); 
							 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;				
					case 2 : if ($total_precio_usd_ren_tot_2==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_2,0); 
							 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;				
					case 3 : if ($total_precio_usd_ren_tot_3==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_3,0); 
							 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;			
					case 4 : if ($total_precio_usd_ren_tot_4==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_4,0); 
							 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;		
					case 5 : if ($total_precio_usd_ren_tot_5==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_5,0); 
							 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;		
					case 6 : if ($total_precio_usd_ren_tot_6==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_6,0); 
							 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;		
					case 7 : if ($total_precio_usd_ren_tot_7==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_7,0); 							 
							 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;
					case 8 : if ($total_precio_usd_ren_tot_8==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_8,0); 
							 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;
					case 9 : if ($total_precio_usd_ren_tot_9==0) $TheField="-";
							 else $TheField=number_format($total_precio_usd_ren_tot_9,0); 
							 echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;
					case 10 : if ($total_precio_usd_ren_tot_10==0) $TheField="-";
							  else $TheField=number_format($total_precio_usd_ren_tot_10,0); 
							  echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;	
					case 11 : if ($total_precio_usd_ren_tot_11==0) $TheField="-";
							  else $TheField=number_format($total_precio_usd_ren_tot_11,0);  
							  echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;	
					case 12 : if ($total_precio_usd_ren_tot_12==0) $TheField="-";
							  else $TheField=number_format($total_precio_usd_ren_tot_12,0); 
							  echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;	
					case 13 : $TheField=number_format($total_precio_usd_ren_tot,0); 
							  echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;						
				}							
			}	
			echo "</tr>";	
			echo "<tr>";
			echo "<td class='ui-state-highlight align-center'>#</td>";							 
			echo "<td class='ui-state-highlight align-center'>DIRECCION</td>";	
			echo "<td class='ui-state-highlight align-center'>PROVEEDOR</td>";				
			echo "<td class='ui-state-highlight align-center'>$tx_mes1</td>";	
			echo "<td class='ui-state-highlight align-center'>$tx_mes2</td>";						 
			echo "<td class='ui-state-highlight align-center'>$tx_mes3</td>";	
			echo "<td class='ui-state-highlight align-center'>$tx_mes4</td>";	
			echo "<td class='ui-state-highlight align-center'>$tx_mes5</td>";						 
			echo "<td class='ui-state-highlight align-center'>$tx_mes6</td>";	
			echo "<td class='ui-state-highlight align-center'>$tx_mes7</td>";	
			echo "<td class='ui-state-highlight align-center'>$tx_mes8</td>";						 
			echo "<td class='ui-state-highlight align-center'>$tx_mes9</td>";	
			echo "<td class='ui-state-highlight align-center'>$tx_mes10</td>";	
			echo "<td class='ui-state-highlight align-center'>$tx_mes11</td>";						 
			echo "<td class='ui-state-highlight align-center'>$tx_mes12</td>";	
			echo "<td class='ui-state-highlight align-center'>TOTAL</td>";						 
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan='16' align='left'>";	
        	echo "<ul type='square'> ";
        	echo "<li>Gasto calculado en base a la derrama.</li> ";
        	echo "<li>$nota0</li>";
        	echo "<li>Actualizado al $fecha_hoy.</li>";
        	echo "</ul>";      
        	echo "</td>";
			echo "</tr>";	
		echo "</tabla>";
		}	
		
		$valBita ="par_anio=$par_anio ";
		$valBita.="par_direccion=$par_direccion ";
		$valBita.="par_moneda=$par_moneda ";
		$valBita.="par_estatus=$par_estatus ";
		$valBita.="par_cuenta=$par_cuenta ";
		
		
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_FACTURA TBL_FACTURA_DETALLE" , "$id_login" ,  $valBita ,"" ,"inf_facturacion_dir_sub.php");
	 //<\BITACORA>
		
	mysqli_close($mysql);
} 
else 
{
	echo "Sessi&oacute;n Invalida";
}	
?>      