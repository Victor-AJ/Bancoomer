<?

session_start();
if 	(isset($_SESSION['sess_user']))
{
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$id_login = $_SESSION['sess_iduser'];
	$mysql=conexion_db();

	# ============================
	# Recibo variables
	# ============================
	$dispatch				= $_GET["dispatch"];
	$id_factura				= $_GET["id_factura"];
	$id_proveedor			= $_GET["id_proveedor"]; 		
	$id_cuenta				= $_GET["id_cuenta"]; 		
	$fl_precio_usd_cabecera	= $_GET["fl_precio_usd_cabecera"]; 	
	$fl_precio_mxn_cabecera	= $_GET["fl_precio_mxn_cabecera"]; 	
	$fl_precio_eur_cabecera	= $_GET["fl_precio_eur_cabecera"]; 	
	$id_derrama				= $_GET["sel_derrama"];
	$sel_pagos				= $_GET["sel_pagos"];
	$cap_gasto_unitario		= $_GET["cap_gasto_unitario"];
	$id_login 				= $_SESSION["sess_iduser"];		
	
	$fl_precio_usd_cabecera	= number_format($fl_precio_usd_cabecera,2);	
	$fl_precio_mxn_cabecera	= number_format($fl_precio_mxn_cabecera,2);		
	$fl_precio_eur_cabecera	= number_format($fl_precio_eur_cabecera,2);	
	
	if ($fl_precio_usd_cabecera <> 0) {
		$tx_moneda			= "USD";
		$fl_precio_cabecera	= $fl_precio_usd_cabecera;
	} else if ($fl_precio_mxn_cabecera <> 0) {
		$tx_moneda			= "MXN";
		$fl_precio_cabecera	= $fl_precio_mxn_cabecera;
	} else if ($fl_precio_eur_cabecera <> 0) {
		$tx_moneda			= "EUR";
		$fl_precio_cabecera	= $fl_precio_eur_cabecera;
	}	
	
	$fl_precio_cabecera = ereg_replace( (","), "", $fl_precio_cabecera ); 
	
	if ($dispatch=="insert") {		
		
		if ($id_derrama==1) {
		
			# ==========================================
			# Busca empleados con licencias (Cuantos)
			# ==========================================
			/* <query de rafa> */		
			//$sql = "   SELECT COUNT(*) as cuantos  ";
			//$sql.= "     FROM tbl_licencia b , tbl_empleado a,  tbl_proveedor c ";
			//$sql.= "    WHERE 	 b.id_empleado=a.id_empleado	 and a.tx_indicador='1'  ";
			//$sql.= "      AND    b.id_cuenta		= $id_cuenta   ";
			//$sql.= "      AND    c.id_proveedor	= $id_proveedor ";
			/*  </queri de rafa> */
			
			$sql = "   SELECT COUNT(*) as cuantos  ";
			$sql.= "     FROM tbl_licencia b , tbl_empleado a";
			$sql.= "    WHERE 	 b.id_empleado=a.id_empleado	 and a.tx_indicador='1' and b.tx_indicador='1'  ";
			$sql.= "      AND    b.id_cuenta		= $id_cuenta   ";
		
			
			
			//echo "aaa", $sql;
			//echo "<br>";
			
			$result = mysqli_query($mysql, $sql);		
			
			while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
			{	
				$TheCatalogoEmpleadoCount[] = array(
					'cuantos'	=>$row["cuantos"]
				);
			} 
		
			for ($i=0; $i < count($TheCatalogoEmpleadoCount); $i++)	{         			 
				while ($elemento = each($TheCatalogoEmpleadoCount[$i]))				
					$cuantos	=$TheCatalogoEmpleadoCount[$i]['cuantos'];	
			} 
		
			$fl_precio_calculado = $fl_precio_cabecera / $cuantos;		
			
			$fl_precio_usd=0;		
			$fl_precio_mxn=0;
			$fl_precio_eur=0;
			
			if ($tx_moneda=="USD") 		$fl_precio_usd=$fl_precio_calculado;
			else if ($tx_moneda=="MXN") $fl_precio_mxn=$fl_precio_calculado;
			else if ($tx_moneda=="EUR") $fl_precio_eur=$fl_precio_calculado;
			
			//$id_login		=1;
			$tx_notas		= NULL;				
			$tx_indicador	= "1";				
			$fh_alta		= date("Y-m-j, g:i");
			$id_usuarioalta	= $id_login;		
			$fh_mod			= date("Y-m-j, g:i");
			$id_usuariomod	= $id_login;			
			$result_insert 	= 0;	
			
			# ==========================================
			# Carga Empleados con licencias (Detalle)
			# ==========================================

			//CAMBIO CUENTAS CONTABLES, se quita del query tx_concepto_contable
			$sql = "   SELECT a.id_empleado, id_centro_costos, id_producto  ";
			$sql.= "     FROM tbl_empleado a, tbl_licencia b    ";
			$sql.= "    WHERE a.id_empleado		= b.id_empleado and a.tx_indicador='1' and b.tx_indicador='1'";
			$sql.= "      AND b.id_cuenta		= $id_cuenta ";
			
			
			//echo "aaa", $sql;
			//echo "<br>";
					
			$result = mysqli_query($mysql, $sql);		
				
			while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
			{	
				$TheCatalogoEmpleado[] = array(
					'id_empleado'			=>$row["id_empleado"],
					'id_centro_costos'		=>$row["id_centro_costos"],
					'id_producto'			=>$row["id_producto"]
					//CAMBIO CUENTAS CONTABLES
					//'tx_concepto_contable'	=>$row["tx_concepto_contable"]
				);
			} 
			
			for ($i=0; $i < count($TheCatalogoEmpleado); $i++)	{         			 
				while ($elemento = each($TheCatalogoEmpleado[$i]))					  		
					$id_empleado			=$TheCatalogoEmpleado[$i]['id_empleado'];		
					$id_centro_costos		=$TheCatalogoEmpleado[$i]['id_centro_costos'];					
					$id_producto			=$TheCatalogoEmpleado[$i]['id_producto'];
				//CAMBIO CUENTAS CONTABLES	
				//$tx_concepto_contable	=$TheCatalogoEmpleado[$i]['tx_concepto_contable'];	
						
				$sql = " INSERT INTO tbl_factura_detalle SET " ;  			
				$sql.= " 	id_factura			= $id_factura, ";
				$sql.= " 	id_empleado			= $id_empleado, ";
				$sql.= " 	id_centro_costos	= $id_centro_costos, ";
				$sql.= " 	id_cuenta			= $id_cuenta, ";
				$sql.= " 	id_producto			= $id_producto, ";
				$sql.= " 	fl_precio_usd		= '$fl_precio_usd', ";
				$sql.= " 	fl_precio_mxn		= '$fl_precio_mxn', ";
				$sql.= " 	fl_precio_eur		= '$fl_precio_eur', ";
				//	CAMBIO CUENTAS CONTABLES
				//	$sql.= " 	tx_concepto_contable= '$tx_concepto_contable', ";
				$sql.= " 	tx_notas			= '$tx_notas', ";
				$sql.= " 	tx_indicador		= '$tx_indicador', ";
				$sql.= " 	fh_alta				= '$fh_alta', ";
				$sql.= " 	id_usuarioalta		= '$id_usuarioalta', ";
				$sql.= " 	fh_mod 				= '$fh_mod', ";
				$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
							
				//echo "aaa", $sql;  
				//echo "<br>";
					
				if (mysqli_query($mysql, $sql)) {}
				else $result_insert = 1;	
					
			}
			
			if ($result_insert==0)
			{					
					# Busca si cuadra la factura					
					if ($tx_moneda=="USD") 		$campo = "fl_precio_usd";
					else if ($tx_moneda=="MXN") $campo = "fl_precio_mxn";
					else if ($tx_moneda=="EUR") $campo = "fl_precio_eur";	
				
					$sql = "   SELECT sum( $campo ) as total_derrama ";
					$sql.= "     FROM tbl_factura_detalle ";
					$sql.= "    WHERE id_factura = $id_factura  ";
				
					//echo "sql ",$sql;
					//echo "<br>";
												
					$result = mysqli_query($mysql, $sql);		
							
					while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
					{	
						$TheCuadre[] = array(
							"total_derrama"	=>$row["total_derrama"]							
						);
					}	
							
					for ($i=0; $i < count($TheCuadre); $i++)	{         			 
						while ($elemento = each($TheCuadre[$i]))					  		
							$total_derrama	=$TheCuadre[$i]["total_derrama"];		
					}					
								
					$dif_derrama = round($fl_precio_cabecera - $total_derrama, 3); 	
					
					if ($dif_derrama < 10)
					{					
						$sql = " UPDATE tbl_factura_detalle SET " ;  					
						$sql.= " 	$campo	= $campo + $dif_derrama ";
						$sql.= " WHERE id_factura	= $id_factura ";
						$sql.= " LIMIT 1 ";
						
						//echo " sql ",$sql;
						//echo " <br> ";								
						if (mysqli_query($mysql, $sql)) {}										
					}			
			
			//<BITACORA>
	 				$myBitacora = new Bitacora();
					$myBitacora->anotaBitacora ($mysql, "DERRAMA FIJA", "TBL_FACTURA_DETALLE" , "$id_login" ,   "id_factura=$id_factura" , "$id_factura"  ,  "process_factura_derrama.php");
				 //<\BITACORA>
				 
			
				$data = array("error" => false, "message" => "La Derrama se INSERTO correctamente ...", "html" => "cat_facturas_detalle.php?id=$id_factura" );				
				echo json_encode($data);
			} else {  		
				$data = array("error" => true, "message" => "ERROR al INSERTAR la  Derrama !</br></br>Por favor verifique ..." );				
				echo json_encode($data);
			} 	
			
		} else if ($id_derrama==2) {	
		
			$tx_notas		= NULL;				
			$tx_indicador	= "1";				
			$fh_alta		= date("Y-m-j, g:i");
			$id_usuarioalta	= $id_login;		
			$fh_mod			= date("Y-m-j, g:i");
			$id_usuariomod	= $id_login;			
			$result_insert 	= 0;	
		
			# ============================================
			# Carga Empleados con licencias
			# ============================================		
			//CAMBIO CUENTAS CONTABLES, se quita del query tx_concepto_contable
			$sql = "   SELECT d.id_producto, tx_producto, fl_precio, tx_moneda, a.id_empleado, id_centro_costos ";
			$sql.= "     FROM tbl_empleado a, tbl_licencia b, tbl_proveedor c, tbl_producto d, tbl_moneda e ";
			$sql.= "    WHERE a.id_empleado		= b.id_empleado and a.tx_indicador='1' ";
			$sql.= "      AND b.id_cuenta		= $id_cuenta and b.tx_indicador='1'  ";	
			$sql.= "      AND c.id_proveedor 	= $id_proveedor and c.tx_indicador='1' ";
			$sql.= "      AND c.id_proveedor 	= d.id_proveedor and d.tx_indicador='1' ";
			$sql.= "      AND b.id_producto 	= d.id_producto ";
			$sql.= "      AND d.id_moneda 		= e.id_moneda  and e.tx_indicador='1' ";
				
			//echo "aaa", $sql;
					
			$result = mysqli_query($mysql, $sql);		
				
			while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
			{	
				$TheCatalogoProducto[] = array(
					'id_producto'			=>$row["id_producto"],
					'tx_producto'			=>$row["tx_producto"],
					'fl_precio'				=>$row["fl_precio"],
					'tx_moneda'				=>$row["tx_moneda"],
					'id_empleado'			=>$row["id_empleado"],					
					'id_centro_costos'		=>$row["id_centro_costos"],
					'id_producto'			=>$row["id_producto"]
					//CAMBIO CUENTAS CONTABLES
					//'tx_concepto_contable'	=>$row["tx_concepto_contable"]
				);
			} 
			
			$registros=count($TheCatalogoProducto);		
			
			for ($i=0; $i < count($TheCatalogoProducto); $i++)	{         			 
				while ($elemento = each($TheCatalogoProducto[$i]))					  		
					$id_producto			=$TheCatalogoProducto[$i]['id_producto'];		
					$tx_producto			=$TheCatalogoProducto[$i]['tx_producto'];		
					$fl_precio				=$TheCatalogoProducto[$i]['fl_precio'];		
					$tx_moneda				=$TheCatalogoProducto[$i]['tx_moneda'];		
					$id_empleado			=$TheCatalogoProducto[$i]['id_empleado'];		
					$id_centro_costos		=$TheCatalogoProducto[$i]['id_centro_costos'];					
					$id_producto			=$TheCatalogoProducto[$i]['id_producto'];	
					//CAMBIO CUENTAS CONTABLES
					//$tx_concepto_contable	=$TheCatalogoProducto[$i]['tx_concepto_contable'];	
					
					$fl_precio_usd=0;		
					$fl_precio_mxn=0;
					$fl_precio_eur=0;
					
					$fl_precio_calculado = $fl_precio * $sel_pagos + $cap_gasto_unitario;
					
					if ($tx_moneda=="USD") 		$fl_precio_usd=$fl_precio_calculado;
					else if ($tx_moneda=="MXN") $fl_precio_mxn=$fl_precio_calculado;
					else if ($tx_moneda=="EUR") $fl_precio_eur=$fl_precio_calculado;	
				
					$sql = " INSERT INTO tbl_factura_detalle SET " ;  			
					$sql.= " 	id_factura			= $id_factura, ";
					$sql.= " 	id_empleado			= $id_empleado, ";
					$sql.= " 	id_centro_costos	= $id_centro_costos, ";
					$sql.= " 	id_cuenta			= $id_cuenta, ";
					$sql.= " 	id_producto			= $id_producto, ";
					$sql.= " 	fl_precio_usd		= '$fl_precio_usd', ";
					$sql.= " 	fl_precio_mxn		= '$fl_precio_mxn', ";
					$sql.= " 	fl_precio_eur		= '$fl_precio_eur', ";
					//CAMBIO CUENTAS CONTABLES
					//$sql.= " 	tx_concepto_contable= '$tx_concepto_contable', ";
					$sql.= " 	tx_notas			= '$tx_notas', ";
					$sql.= " 	tx_indicador		= '$tx_indicador', ";
					$sql.= " 	fh_alta				= '$fh_alta', ";
					$sql.= " 	id_usuarioalta		= '$id_usuarioalta', ";
					$sql.= " 	fh_mod 				= '$fh_mod', ";
					$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
							
					//echo "aaa", $sql;  
					//echo "<br>";
						
					if (mysqli_query($mysql, $sql)) {}
					else $result_insert = 1;	
						
				}
			
				if ($result_insert==0)
				{		
					# Busca si cuadra la factura					
					if ($tx_moneda=="USD") 		$campo = "fl_precio_usd";
					else if ($tx_moneda=="MXN") $campo = "fl_precio_mxn";
					else if ($tx_moneda=="EUR") $campo = "fl_precio_eur";	
				
					$sql = "   SELECT sum( $campo ) as total_derrama ";
					$sql.= "     FROM tbl_factura_detalle ";
					$sql.= "    WHERE id_factura = $id_factura  ";
				
					//echo "sql ",$sql;
					//echo "<br>";
												
					$result = mysqli_query($mysql, $sql);		
							
					while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
					{	
						$TheCuadre[] = array(
							"total_derrama"	=>$row["total_derrama"]							
						);
					}	
							
					for ($i=0; $i < count($TheCuadre); $i++)	{         			 
						while ($elemento = each($TheCuadre[$i]))					  		
							$total_derrama	=$TheCuadre[$i]["total_derrama"];		
					}					
								
					$dif_derrama = round($fl_precio_cabecera - $total_derrama, 3); 	
					
					if ($dif_derrama < 10)
					{					
						$sql = " UPDATE tbl_factura_detalle SET " ;  					
						$sql.= " 	$campo	= $campo + $dif_derrama ";
						$sql.= " WHERE id_factura	= $id_factura ";
						$sql.= " LIMIT 1 ";
						
						//echo " sql ",$sql;
						//echo " <br> ";								
						if (mysqli_query($mysql, $sql)) {}										
					}			
					
				//<BITACORA>
	 			$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "DERRAMA VARIABLE", "TBL_FACTURA_DETALLE" , "$id_login" ,   "id_factura=$id_factura" , "$id_factura"  ,  "process_factura_derrama.php");
				//<\BITACORA>
	 	
					$data = array("error" => false, "message" => "La Derrama se INSERTO correctamente ... $dif_derrama", "html" => "cat_facturas_detalle.php?id=$id_factura" );				
					echo json_encode($data);
					
				} else {  		
					$data = array("error" => true, "message" => "ERROR al INSERTAR la  Derrama !</br></br>Por favor verifique ..." );				
					echo json_encode($data);
				} 					
			}	
								
	} else if ($dispatch=='delete') {		
		
		$sql = " UPDATE  tbl_factura_detalle SET TX_INDICADOR='0' ";
		$sql.= "  WHERE id_factura = $id_factura ";
				
		//echo "aaa", $sql; 
				
		if (mysqli_query($mysql, $sql)) 
		{
		
				//<BITACORA>
	 			$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "BAJA", "TBL_FACTURA_DETALLE" , "$id_login" ,   "id_factura=$id_factura" , "$id_factura"  ,  "process_factura_derrama.php");
				//<\BITACORA>
				
			$data = array("error" => false, "message" => "Se ha dado de BAJA la derrama correctamente ...", "html" => "cat_facturas_detalle.php?id=$id_factura" );				
			echo json_encode($data);		
		} else {  
			$data = array("error" => true, "message" => "ERROR al BORRAR la Derrama ... </br></br>Por favor verifique ..." );				
			echo json_encode($data);
		}			
	}	

} else {
	echo "Sessi&oacute;n Invalida, Por favor vuelva a firmarse …";
}	
?>