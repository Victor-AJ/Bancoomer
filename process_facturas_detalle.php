<?

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
	$dispatch				= $_GET["dispatch"];
	$id_factura				= $_GET["id_factura"];
	$id_factura_detalle		= $_GET["id_factura_detalle"];
	$id_empleado			= $_GET["id_empleado"]; 		
	$id_centro_costos		= $_GET["id_centro_costos"]; 	
	$id_cuenta				= $_GET["id_cuenta"]; 	
	$id_producto			= $_GET["sel_producto"];	 
	$fl_precio 				= $_GET["cap_monto"]; 
	$tx_moneda 				= $_GET["sel_moneda"]; 
	$tx_notas 				= $_GET["cap_notas"]; 		
	
	// <CAMBIO CUENTAS CONTABLES>
	//$tx_concepto_contable	= $_GET["cap_concepto_contable"];
	$tx_concepto_contable= null;
	
	$fl_precio_usd_cabecera	= $_GET["fl_precio_usd_cabecera"]; 	
	$fl_precio_mxn_cabecera	= $_GET["fl_precio_mxn_cabecera"]; 	
	$fl_precio_eur_cabecera	= $_GET["fl_precio_eur_cabecera"]; 	
	$id_login 				= $_SESSION["sess_iduser"];			
	
	$fl_precio = ereg_replace( (","), "", $fl_precio ); 

	// ============================
	// Carga la informacion al grid
	// ============================
	if ($dispatch=="load") {}
	
	// ============================
	// Realiza INSERT
	// ============================
	else if ($dispatch=="insert") {	
		
	  	$sql = " SELECT * ";
		$sql.= "   FROM tbl_factura_detalle ";
		$sql.= "  WHERE id_factura	= $id_factura ";
		$sql.= "    AND id_empleado	= $id_empleado ";
		$sql.= " 	AND id_producto	= $id_producto  ";
		
		$sql.= " 	AND tx_indicador= '1' ";
		
		//echo "sql", $sql;
			
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
			
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "El empleado con el producto que desea dar de alta en la derrama ya existe !</br></br> Por favor vefique ..." );					
			echo json_encode($data);		
		} else {  
			
			//echo "Moneda",$tx_moneda;	
			
			$fl_precio_usd=0.00;
			$fl_precio_mxn=0.00;
			$fl_precio_eur=0.00;
					
			if ($tx_moneda=="USD") $fl_precio_usd=$fl_precio;
			else if ($tx_moneda=="MXN") $fl_precio_mxn=$fl_precio;
			else if ($tx_moneda=="EUR") $fl_precio_eur=$fl_precio;
						
			$tx_indicador="1";				
			$fh_alta=date("Y-m-j, g:i");
			$id_usuarioalta=$id_login;		
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;		
				
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
			
			$valBita= "id_factura=$id_factura ";
			$valBita.= "id_empleado=$id_empleado ";
			$valBita.= "id_centro_costos=$id_centro_costos ";
			$valBita.= "id_cuenta=$id_cuenta ";
			$valBita.= "id_producto=$id_producto ";
			$valBita.= "fl_precio_usd=$fl_precio_usd ";
			$valBita.= "fl_precio_mxn=$fl_precio_mxn ";
			$valBita.= "fl_precio_eur=$fl_precio_eur ";
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
				$myBitacora->anotaBitacora ($mysql, "ALTA", "TBL_FACTURA_DETALLE" , "$id_login" ,   $valBita , ""  ,  "process_facturas_detalle.php");
				//<\BITACORA>
				
				
				$data = array("error" => false, "message" => "El registro se INSERTO correctamente", "html" => "cat_facturas_detalle.php?id=$id_factura" );				
				echo json_encode($data);
			} else {  		
				$data = array("error" => true, "message" => "ERROR al INSERTAR el registro !</br></br>Por favor verifique ..." );				
				echo json_encode($data);
			} 		
		}	
	} 

	// ACTUALIZA
	// ============================
	else if ($dispatch=="save") 
	{	
	
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_factura_detalle ";
		$sql.= "  WHERE id_factura_detalle <> $id_factura_detalle ";
		$sql.= "	AND id_factura	= $id_factura ";
		$sql.= "    AND id_empleado	= $id_empleado ";
		$sql.= " 	AND id_producto	= $id_producto ";		
		
		$sql.= " 	AND tx_indicador= '1' ";		
			
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
			
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "El empleado con el producto que desea ACTUALIZAR en la derrama ya existe !</br></br> Por favor vefique ..." );				
			echo json_encode($data);
		} else {  
			
			$fl_precio_usd=0.00;
			$fl_precio_mxn=0.00;
			$fl_precio_eur=0.00;
					
			if ($tx_moneda=="USD") $fl_precio_usd=$fl_precio;
			else if ($tx_moneda=="MXN") $fl_precio_mxn=$fl_precio;
			else if ($tx_moneda=="EUR") $fl_precio_eur=$fl_precio;
			
			$tx_indicador="1";	
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;
			
			$sql = " UPDATE tbl_factura_detalle SET " ;  			
			$sql.= " 	id_producto			= $id_producto, ";
			$sql.= " 	fl_precio_usd		= '$fl_precio_usd', ";
			$sql.= " 	fl_precio_mxn		= '$fl_precio_mxn', ";
			$sql.= " 	fl_precio_eur		= '$fl_precio_eur', ";

			//CAMBIO CUENTAS CONTABLES
			//$sql.= " 	tx_concepto_contable= '$tx_concepto_contable', ";
			
			$sql.= " 	tx_notas			= '$tx_notas', ";
			$sql.= " 	fh_mod 				= '$fh_mod', ";
			$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
			$sql.= " WHERE id_factura_detalle	= $id_factura_detalle ";
					   
			//echo "aaa", $sql;      
			
			//<BITACORA>
	 			$myBitacora = new Bitacora();
				$valores=$myBitacora->obtenvalores ($mysql, "TBL_FACTURA_DETALLE",$id_factura_detalle );
				
				  
			if (mysqli_query($mysql, $sql))
			{
				//$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente", "html" => "cat_facturas_lista_detalle.php?id=$id_factura&fl_precio_usd_cabecera=$fl_precio_usd_cabecera&fl_precio_mxn_cabecera=$fl_precio_mxn_cabecera&fl_precio_eur_cabecera=$fl_precio_eur_cabecera" );		
				
				
				//<BITACORA>

				$myBitacora->anotaBitacora ($mysql, "MODIFICACION", "TBL_FACTURA_DETALLE" , "$id_login" ,   $valBita , "$id_factura_detalle"  ,  "process_facturas_detalle.php");
				//<\BITACORA>
				
				
				$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente", "html" => "cat_facturas_detalle.php?id=$id_factura" );		
				echo json_encode($data);
			} else {  
				$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Por favor verifique ..." );				
				echo json_encode($data);
			}	
		}			
		mysqli_free_result($result);
	} 
	
	// ============================
	// Realiza DELETE
	// ============================
	else if ($dispatch=='delete') {		
		
		$sql = " UPDATE  tbl_factura_detalle SET TX_INDICADOR='0' ";
		$sql.= "  WHERE id_factura_detalle = $id_factura_detalle ";
				
		//echo "aaa", $sql; 
				
		if (mysqli_query($mysql, $sql)) 
		{
		
				//<BITACORA>
				$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "BAJA", "TBL_FACTURA_DETALLE" , "$id_login" ,   "" , $id_factura_detalle  ,  "process_facturas_detalle.php");
				//<\BITACORA>
		
			$data = array("error" => false, "message" => "Se ha dado BAJA al registro  correctamente", "html" => "cat_facturas_detalle.php?id=$id_factura" );				
			echo json_encode($data);		
		} else {  
			$data = array("error" => true, "message" => "ERROR al BORRAR el registro. </br></br>Por favor verifique ..." );				
			echo json_encode($data);
		}			
	}	

	// BUSQUEDA
	// ============================
	//else if ($dispatch=="search") {	}	
	//mysqli_close($mysql);	

} else {
	echo "Sessi&oacute;n Invalida, Por favor vuelva a firmarse …";
}	
?>