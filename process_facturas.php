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
	$page 				= $_GET['page']; 
	$limit 				= $_GET['rows']; 
	$start				= $_GET['start'];
	$sidx 				= $_GET['sidx']; 
	$sord 				= $_GET['sord']; 
	$dispatch			= $_GET["dispatch"];
	$id_factura			= $_GET["id_factura"];
	$tx_indicador		= $_GET['tx_indicador']; 
	$tx_anio			= $_GET['sel_anio_cap']; 	
	$tx_proveedor		= $_GET['sel_proveedor']; 	
	$tx_cuenta			= $_GET['sel_cuenta'];	 
	$tx_factura			= $_GET['cap_factura']; 	
	$fl_precio 			= $_GET['cap_monto']; 
	$fl_precio_mxn		= $_GET['cap_monto_mxn']; 
	$tx_moneda 			= $_GET['sel_moneda']; 
	$id_moneda 			= $_GET['id_moneda']; 
	$fl_tipo_cambio		= $_GET['cap_tipo_cambio']; 
	$fh_factura			= $_GET['cap_fh_factura']; 
	$fh_inicio 			= $_GET['cap_fh_inicial']; 
	$fh_final 			= $_GET['cap_fh_final']; 
	$fh_contable 		= $_GET['cap_fh_contable']; 
	$tx_mes 			= $_GET['sel_amortiza']; 		
	$id_estatus			= $_GET['sel_estatus']; 		
	$tx_ruta 			= $_GET['cap_ruta']; 		
	$tx_referencia		= $_GET['cap_referencia']; 		
	$tx_notas 			= $_GET['cap_notas']; 		
	$origen 			= $_GET['origen']; 		
	$id_login 			= $_SESSION['sess_iduser'];	
	$q 					= $_GET["q"];		
	
	$fl_precio 		= ereg_replace( (","), "", $fl_precio ); 
	$fl_precio_mxn	= ereg_replace( (","), "", $fl_precio_mxn ); 
	
	$fh_factura	= cambiaf_a_mysql($fh_factura);
	$fh_inicio 	= cambiaf_a_mysql($fh_inicio);
	$fh_final 	= cambiaf_a_mysql($fh_final);	
	$fh_contable= cambiaf_a_mysql($fh_contable);	
	
	# ============================
	# Carga la informacion al grid
	# ============================
	if ($dispatch=="load") {}
	
	# ============================
	# Realiza INSERT
	# ============================
	else if ($dispatch=="insert") {	
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_factura ";
		$sql.= "  WHERE id_proveedor= $tx_proveedor ";	
		$sql.= "    AND tx_factura	= '$tx_factura' and tx_indicador= 1  ";	
		
		//echo "sql", $sql;
			
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
			
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "La Factura que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
			echo json_encode($data);		
		} else {  
			
			$fl_precio_usd=NULL;
			$fl_precio_mxn=NULL;
			$fl_precio_eur=NULL;
			
			if ($tx_moneda=="USD") 	
			{	
				if ($tx_anio=="2010") $fl_tipo_cambio = 14.00;
				else if ($tx_anio=="2011")	$fl_tipo_cambio = 13.50;				
				$fl_precio_usd  = $fl_precio;
								
			} elseif ($tx_moneda=="MXN") { 
				$fl_precio_mxn=$fl_precio; 
			} elseif ($tx_moneda=="EUR") {
				$fl_precio_eur=$fl_precio; 
			}				
			
			$tx_indicador="1";				
			$fh_alta=date("Y-m-j, g:i");
			$id_usuarioalta=$id_login;		
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;	
				
			$sql = " INSERT INTO tbl_factura SET " ;  			
			$sql.= " 	id_proveedor		= $tx_proveedor, ";
			$sql.= " 	id_cuenta			= $tx_cuenta, ";
			$sql.= " 	id_mes				= $tx_mes, ";
			$sql.= " 	id_factura_estatus	= $id_estatus, ";
			$sql.= " 	id_moneda			= $id_moneda, ";
			$sql.= " 	tx_anio				= '$tx_anio', ";
			$sql.= " 	tx_factura			= '$tx_factura', ";
			$sql.= " 	fh_factura			= '$fh_factura', ";
			$sql.= " 	fh_inicio			= '$fh_inicio', ";
			$sql.= " 	fh_final			= '$fh_final', ";
			$sql.= " 	fh_contable			= '$fh_contable', ";
			$sql.= " 	fl_precio_usd		= '$fl_precio_usd', ";
			$sql.= " 	fl_precio_mxn		= '$fl_precio_mxn', ";
			$sql.= " 	fl_precio_eur		= '$fl_precio_eur', ";
			$sql.= " 	fl_tipo_cambio		= '$fl_tipo_cambio', ";
			$sql.= " 	tx_referencia		= '$tx_referencia', ";
			$sql.= " 	tx_ruta				= '$tx_ruta', ";
			$sql.= " 	tx_notas			= '$tx_notas', ";
			$sql.= " 	tx_indicador		= '$tx_indicador', ";
			$sql.= " 	fh_alta				= '$fh_alta', ";
			$sql.= " 	id_usuarioalta		= '$id_usuarioalta', ";
			$sql.= " 	fh_mod 				= '$fh_mod', ";
			$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
						
			//echo "aaa", $sql;  

			$valBita= "id_proveedor=$tx_proveedor ";
			$valBita.= "id_cuenta=$tx_cuenta ";
			$valBita.= "id_mes=$tx_mes ";
			$valBita.= "id_factura_estatus=$id_estatus ";
			$valBita.= "id_moneda=$id_moneda ";
			$valBita.= "tx_anio=$tx_anio ";
			$valBita.= "tx_factura=$tx_factura ";
			$valBita.= "fh_factura=$fh_factura ";
			$valBita.= "fh_inicio=$fh_inicio ";
			$valBita.= "fh_final=$fh_final ";
			$valBita.= "fh_contable=$fh_contable ";
			$valBita.= "fl_precio_usd=$fl_precio_usd ";
			$valBita.= "fl_precio_mxn=$fl_precio_mxn ";
			$valBita.= "fl_precio_eur=$fl_precio_eur ";
			$valBita.= "fl_tipo_cambio=$fl_tipo_cambio ";
			$valBita.= "tx_referencia=$tx_referencia ";
			$valBita.= "tx_ruta=$tx_ruta ";
			$valBita.= "tx_notas=$tx_notas ";
			$valBita.= "tx_indicador=$tx_indicador ";
			$valBita.= "fh_alta=$fh_alta ";
			$valBita.= "id_usuarioalta=$id_usuarioalta ";
			$valBita.= "fh_mod=$fh_mod ";
			$valBita.= "id_usuariomod=$id_usuariomod "; 
			
			
			
			if (mysqli_query($mysql, $sql))
			{		
				
				
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL_FACTURA" , "$id_login" ,   $valBita ,"" ,"process_facturas.php");
	 //<\BITACORA>
	 
	 
				$data = array("error" => false, "message" => "El registro se INSERTO correctamente", "html" => "cat_facturas_lista.php?id=$tx_anio&dispatch=save" );				
				echo json_encode($data);
			} else {  		
				$data = array("error" => true, "message" => "ERROR al INSERTAR el registro !</br></br>Por favor verifique ..." );				
				echo json_encode($data);
			} 		
		}	
	} 

	// ACTUALIZA
	// ============================
	else if ($dispatch=="save") {			
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_factura ";
		$sql.= "  WHERE id_factura	<> $id_factura ";
		$sql.= "	AND id_proveedor= $tx_proveedor ";	
		$sql.= "    AND tx_factura	= '$tx_factura' and tx_indicador=1 ";			
			
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
			
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "La Factura que modifico ya existe!</br></br>Por favor verifique ... " );				
			echo json_encode($data);
		} else {  
			
			if ($tx_moneda=="USD") {
			
				$fl_precio_usd	= $fl_precio;
				$fl_precio_mxn	= $fl_precio_mxn;				
				$fl_precio_eur	= NULL;								
				$fl_tipo_cambio	= $fl_precio_mxn / $fl_precio_usd;				
				
			} else if ($tx_moneda=="MXN") {
			
				$fl_precio_mxn = $fl_precio;
				$fl_precio_usd = NULL;
				$fl_precio_eur = NULL;
				$fl_tipo_cambio = NULL;
				
			} else if ($tx_moneda=="EUR") {
			
				$fl_precio_usd	= NULL;
				$fl_precio_mxn	= $fl_precio_mxn;				
				$fl_precio_eur	= $fl_precio;								
				$fl_tipo_cambio	= $fl_precio_mxn / $fl_precio_eur;								
			}			
			
			if ($origen==1) {
				$tx_mes 	= substr($fh_contable,5,2);  //Mes de Pago
				$id_estatus = 4; 						 //Status PAGADA
			}	
			
			$tx_indicador="1";	
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;
			
			$sql = " UPDATE tbl_factura SET " ;  			
			$sql.= " 	id_proveedor		= $tx_proveedor, ";
			$sql.= " 	id_cuenta			= $tx_cuenta, ";
			$sql.= " 	id_mes				= $tx_mes, ";
			$sql.= " 	id_factura_estatus	= $id_estatus, ";
			$sql.= " 	id_moneda			= $id_moneda, ";
			$sql.= " 	tx_anio				= '$tx_anio', ";
			$sql.= " 	tx_factura			= '$tx_factura', ";
			$sql.= " 	fh_factura			= '$fh_factura', ";
			$sql.= " 	fh_inicio			= '$fh_inicio', ";
			$sql.= " 	fh_final			= '$fh_final', ";
			$sql.= " 	fh_contable			= '$fh_contable', ";
			$sql.= " 	fl_precio_usd		= '$fl_precio_usd', ";
			$sql.= " 	fl_precio_mxn		= '$fl_precio_mxn', ";
			$sql.= " 	fl_precio_eur		= '$fl_precio_eur', ";
			$sql.= " 	fl_tipo_cambio		= '$fl_tipo_cambio', ";
			$sql.= " 	tx_referencia		= '$tx_referencia', ";
			$sql.= " 	tx_ruta				= '$tx_ruta', ";
			$sql.= " 	tx_notas			= '$tx_notas', ";
			$sql.= " 	tx_indicador		= '$tx_indicador', ";
			$sql.= " 	fh_mod 				= '$fh_mod', ";
			$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
			$sql.= " WHERE id_factura		= $id_factura ";
					   
			//echo "aaa", $sql;      
			
			//<BITACORA>
			$myBitacora = new Bitacora();
			$valoresBita=$myBitacora->obtenvalores ($mysql, "TBL_FACTURA", $id_factura );   
			
				  
			if (mysqli_query($mysql, $sql))
			{
				if ($origen==1) 
				{					
					if ($tx_moneda=="USD") {
					
						# ======================================
						# Actualiza la derrama 
						# ======================================
						
						$sql1 = " UPDATE tbl_factura_detalle " ;  			
						$sql1.= " 	SET fl_precio_mxn	= ( fl_precio_usd * $fl_precio_mxn ) / $fl_precio_usd ";
						$sql1.= "  WHERE id_factura		= $id_factura  and tx_indicador=1 ";
						
						//echo "sql", $sql1;
						//echo "<br />";
						
						if (mysqli_query($mysql, $sql1))
						{						
							# ======================================
							# Revisa si cuadra 
							# ======================================	
								
							$sql2 = " SELECT sum(fl_precio_mxn) as total_derrama " ;  			
							$sql2.= "   FROM tbl_factura_detalle ";
							$sql2.= "  WHERE id_factura	= $id_factura  and tx_indicador=1  ";
							
							//echo "<br />";
								
							$result = mysqli_query($mysql, $sql2);	
							while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
							{	
								$TheCuadre[] = array(			
									'total_derrama'	=>$row["total_derrama"]
								);
							} 	
								
							for ($i=0; $i < count($TheCuadre); $i++) {         			 
								while ($elemento = each($TheCuadre[$i]))					  		
									$total_derrama	=$TheCuadre[$i]["total_derrama"];		
							}					
											
							$dif_derrama = round($fl_precio_mxn - $total_derrama, 3); 	
															
							if ($dif_derrama < 10)
							{					
								$sql = " UPDATE tbl_factura_detalle SET " ;  					
								$sql.= " 	fl_precio_mxn	= fl_precio_mxn + $dif_derrama ";
								$sql.= " WHERE id_factura	= $id_factura and tx_indicador=1 ";
								$sql.= " LIMIT 1 ";
								
								//echo " sql ",$sql;
								//echo " <br> ";								
								if (mysqli_query($mysql, $sql)) {}										
							}			
						}		
					}				

					//<BITACORA>
					$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_FACTURA TBL_FACTURA_DETALLE" , "$id_login" ,  $valoresBita , $id_factura   ,  "process_facturas.php");
					//<\BITACORA
				
					$data = array("error" => false, "message" => "El REGISTRO se ACTUALIZO correctamente", "html" => "inf_facturacion_proveedor_lista.php?par_anio=$tx_anio&dispatch=save&origen=1" );							
					echo json_encode($data);
					
				} else {
					
					//<BITACORA>
					$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_FACTURA" , "$id_login" ,  $valoresBita , $id_factura   ,  "process_facturas.php");
					//<\BITACORA
					
					$data = array("error" => false, "message" => "El REGISTRO se ACTUALIZO correctamente", "html" => "cat_facturas_lista.php?id=$tx_anio&dispatch=save" );																	
					echo json_encode($data);
				}
			} else {  
				$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Por favor verifique ..." );				
				echo json_encode($data);
			}	
		}			
		mysqli_free_result($result);
	} 
	
	else if ($dispatch=='find') {	
	
		//LL: NO SE UTILIZA AL PARECER FUE INTENTO DE METER GRID
		$auto = '%'.$q.'%';	
	
		if ($campo=='factura') {
			
			$sql = " SELECT tx_factura ";
			$sql.= "   FROM tbl_factura ";
			$sql.= "  WHERE tx_factura like '$auto' ";
			$sql.= "  GROUP BY tx_factura ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_factura'=>$row["tx_factura"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_factura	=$TheCatalogo[$i]['tx_factura'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_factura;
			}	
		}
	}
	
	// ============================
	// Realiza DELETE
	// ============================
	else if ($dispatch=='delete') {		
		
		$sql = " UPDATE  tbl_factura  SET tx_indicador='0' ";
		$sql.= "  WHERE id_factura = $id_factura ";
				
		//echo "aaa", $sql; 
				
		if (mysqli_query($mysql, $sql)) 
		{
			
			//<BITACORA>
			$myBitacora = new Bitacora();
			$myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL_FACTURA" , "$id_login" ,  "" , $id_factura   ,  "process_facturas.php");
			//<\BITACORA
			
			
			
			$data = array("error" => false, "message" => "El registro se BORRO correctamente", "html" => "cat_facturas_lista.php?id=$tx_anio" );				
			echo json_encode($data);		
		} else {  
			$data = array("error" => true, "message" => "ERROR al BORRAR el registro. </br></br>Por favor verifique ..." );				
			echo json_encode($data);
		}			
	}	

	// ============================
	// BUSQUEDA
	// ============================
	else if ($dispatch=="search") {	
	
		//LL: Al parecer no se usa, fue intento de meter grid
		$sql = " SELECT COUNT(*) AS count ";
		$sql.= "   FROM tbl_proveedor a, tbl_usuario b, tbl_usuario c ";
		$sql.= "  WHERE a.id_usuariomod = b.id_usuario ";
		$sql.= "    AND a.id_usuarioalta = c.id_usuario ".$wh ;
		
		//echo "sql",	$sql;
		//echo "<br>";
	
		$result = mysqli_query($mysql, $sql);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$count = $row['count'];
		//$count = 1;
		
		if( $count>0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; 	
		
		$sql = "   SELECT  a.id_estado, a.id_estado, a.tx_pais, a.tx_indicador, a.fh_mod, b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta " ; 
		$sql.= "     FROM  tbl_proveedor a, tbl_usuario b, tbl_usuario c " ; 
		$sql.= "    WHERE a.id_usuariomod = b.id_usuario " ;
		$sql.= " 	  AND a.id_usuarioalta = c.id_usuario ".$wh ;   
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= " 	LIMIT $start, $limit " ;	
			
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql,$sql); 
		
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
			$responce->rows[$i]['id']=$row[id_estado];
			$responce->rows[$i]['cell']=array($row[id_estado],$row[id_estado],$row[tx_pais],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		echo json_encode($responce);			 
	}	
	mysqli_close($mysql);	

} else {
	echo "Sessi&oacute;n Invalida, Por favor vuelva a firmarse …";
}	
?>