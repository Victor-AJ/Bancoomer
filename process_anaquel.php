<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	include("includes/funciones.php");  
	$mysql=conexion_db();

	# ============================
	# Recibo variables
	# ============================
	$page 				= $_GET['page']; 
	$limit 				= $_GET['rows']; 
	$start				= $_GET['start'];
	$sidx 				= $_GET['sidx']; 
	$sord 				= $_GET['sord']; 
	$dispatch			= $_GET["dispatch"];
	$id_anaquel			= $_GET["id_anaquel"];
	$id_macroiniciativa	= $_GET['sel_macroiniciativa']; 	
	$id_empleado		= $_GET['sel_empleado'];	 
	$id_direccion		= $_GET['cap_direccion_id'];	 
	$id_subdireccion	= $_GET['cap_subdireccion_id'];	 
	$id_departamento	= $_GET['cap_departamento_id']; 	
	$id_glg				= $_GET['sel_cuenta']; 		
	$id_prioridad		= $_GET['sel_prioridad']; 	
	$id_anaquel_tipo	= $_GET['sel_tipo']; 	
	$tx_anio			= $_GET['sel_anio_cap']; 	
	$tx_proyecto		= $_GET['cap_proyecto']; 	
	$tx_descripcion		= $_GET['cap_descripcion']; 
	$fl_monto 			= $_GET['cap_monto']; 
	$tx_moneda 			= $_GET['sel_moneda']; 
	$in_consecutivo		= $_GET['cap_negocio']; 	
	$tx_notas 			= $_GET['cap_notas']; 	
	$tx_indicador		= $_GET['tx_indicador']; 
	$id_login 			= $_SESSION['sess_iduser'];	
	$q 					= $_GET["q"];		
	
	$fl_monto = ereg_replace( (","), "", $fl_monto ); 	

	// ============================
	// Carga la informacion al grid
	// ============================
	if ($dispatch=="load") {}
	
	// ============================
	// Realiza INSERT
	// ============================
	else if ($dispatch=="insert") {	
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_factura ";
		//$sql.= "  WHERE id_proveedor= $tx_proveedor ";	
		//$sql.= "    AND tx_factura	= '$tx_factura' ";	
		
		//echo "sql", $sql;
			
		//$result = mysqli_query($mysql, $sql);
		//$row = mysqli_fetch_row($result);
		//$count = $row[0];	
			
		//if ($count > 0)	{	
		//	$data = array("error" => true, "message" => "La Factura que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
		//	echo json_encode($data);		
		//} else {  
			
			$fl_monto_usd=0;
			$fl_monto_mxn=0;
			$fl_monto_eur=0;
			
			if ($tx_moneda=="USD") $fl_monto_usd=$fl_monto;
			else if ($tx_moneda=="MXN")	$fl_monto_mxn=$fl_monto;
			else if ($tx_moneda=="EUR") $fl_monto_eur=$fl_monto; 				
			
			$tx_indicador="1";				
			$fh_alta=date("Y-m-j, g:i");
			$id_usuarioalta=$id_login;		
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;	
				
			$sql = " INSERT INTO tbl_anaquel SET " ;  			
			$sql.= " 	id_empleado			= $id_empleado, ";
			$sql.= " 	id_macroiniciativa	= $id_macroiniciativa, ";
			$sql.= " 	id_direccion		= $id_direccion, ";
			$sql.= " 	id_subdireccion		= $id_subdireccion, ";
			$sql.= " 	id_departamento		= $id_departamento, ";
			$sql.= " 	id_glg				= $id_glg, ";
			$sql.= " 	id_prioridad		= $id_prioridad, ";
			$sql.= " 	id_anaquel_tipo		= $id_anaquel_tipo, ";
			$sql.= " 	tx_anio				= '$tx_anio', ";
			$sql.= " 	tx_proyecto			= '$tx_proyecto', ";
			$sql.= " 	tx_descripcion		= '$tx_descripcion', ";
			$sql.= " 	fl_monto_usd		= '$fl_monto_usd', ";
			$sql.= " 	fl_monto_mxn		= '$fl_monto_mxn', ";
			$sql.= " 	fl_monto_eur		= '$fl_monto_eur', ";
			$sql.= " 	in_consecutivo		= '$in_consecutivo', ";
			$sql.= " 	tx_notas			= '$tx_notas', ";
			$sql.= " 	tx_indicador		= '$tx_indicador', ";
			$sql.= " 	fh_alta				= '$fh_alta', ";
			$sql.= " 	id_usuarioalta		= '$id_usuarioalta', ";
			$sql.= " 	fh_mod 				= '$fh_mod', ";
			$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
						
			//echo "aaa", $sql;  
				
			if (mysqli_query($mysql, $sql))	{		
				$data = array("error" => false, "message" => "El registro se INSERTO correctamente", "html" => "cat_anaquel_lista.php?id=$tx_anio&dispatch=save" );				
				echo json_encode($data);
			} else {  		
				$data = array("error" => true, "message" => "ERROR al INSERTAR el registro !</br></br>Por favor verifique ..." );				
				echo json_encode($data);
			} 		
		//}	
	} 

	// ACTUALIZA
	// ============================
	else if ($dispatch=="save") {			
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_factura ";
		//$sql.= "  WHERE id_factura	<> $id_factura ";
		//$sql.= "	AND id_proveedor= $tx_proveedor ";	
		//$sql.= "    AND tx_factura	= '$tx_factura' ";			
			
		//echo "sql",	$sql;
		
		//$result = mysqli_query($mysql, $sql);
		//$row = mysqli_fetch_row($result);
		//$count = $row[0];	
			
		//if ($count > 0)	{	
		//	$data = array("error" => true, "message" => "La Factura que modifico ya existe!</br></br>Por favor verifique ... " );				
		//	echo json_encode($data);
		//} else {  
			
			//if ($tx_moneda=="USD")
			//{
			//	$fl_precio_usd=$fl_precio;
			//	$fl_precio_mxn=NULL;
			//} else if ($tx_moneda=="MXN") {
			//	$fl_precio_usd=NULL;
			//	$fl_precio_mxn=$fl_precio;
			//}
			
			$fl_monto_usd=0;
			$fl_monto_mxn=0;
			$fl_monto_eur=0;
			
			if ($tx_moneda=="USD") $fl_monto_usd=$fl_monto;
			else if ($tx_moneda=="MXN")	$fl_monto_mxn=$fl_monto;
			else if ($tx_moneda=="EUR") $fl_monto_eur=$fl_monto; 	
			
			$tx_indicador="1";	
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;
			
			$sql = " UPDATE tbl_anaquel SET " ;  			
			$sql.= " 	id_empleado			= $id_empleado, ";
			$sql.= " 	id_macroiniciativa	= $id_macroiniciativa, ";			
			$sql.= " 	id_direccion		= $id_direccion, ";
			$sql.= " 	id_subdireccion		= $id_subdireccion, ";
			$sql.= " 	id_departamento		= $id_departamento, ";
			$sql.= " 	id_glg				= $id_glg, ";
			$sql.= " 	id_prioridad		= $id_prioridad, ";
			$sql.= " 	id_anaquel_tipo		= $id_anaquel_tipo, ";
			$sql.= " 	tx_anio				= '$tx_anio', ";
			$sql.= " 	tx_proyecto			= '$tx_proyecto', ";
			$sql.= " 	tx_descripcion		= '$tx_descripcion', ";
			$sql.= " 	fl_monto_usd		= '$fl_monto_usd', ";
			$sql.= " 	fl_monto_mxn		= '$fl_monto_mxn', ";
			$sql.= " 	fl_monto_eur		= '$fl_monto_eur', ";
			$sql.= " 	in_consecutivo		= '$in_consecutivo', ";
			$sql.= " 	tx_notas			= '$tx_notas', ";
			$sql.= " 	tx_indicador		= '$tx_indicador', ";
			$sql.= " 	fh_mod 				= '$fh_mod', ";
			$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
			$sql.= " WHERE id_anaquel		= $id_anaquel ";
					   
			//echo "aaa", $sql;      
				  
			if (mysqli_query($mysql, $sql))
			{
				$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente", "html" => "cat_anaquel_lista.php?id=$tx_anio&dispatch=save" );										
				echo json_encode($data);
			} else {  
				$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Por favor verifique ..." );				
				echo json_encode($data);
			}	
		//}			
		//mysqli_free_result($result);
	} 
	
	else if ($dispatch=='find') {	
	
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
		
		$sql = " DELETE FROM tbl_anaquel ";
		$sql.= "  WHERE id_anaquel = $id_anaquel ";
				
		//echo "aaa", $sql; 
				
		if (mysqli_query($mysql, $sql)) {
			$data = array("error" => false, "message" => "El registro se BORRO correctamente", "html" => "cat_anaquel_lista.php?id=$tx_anio" );				
			echo json_encode($data);		
		} else {  
			$data = array("error" => true, "message" => "ERROR al BORRAR el registro. </br></br>Por favor verifique ..." );				
			echo json_encode($data);
		}			
	}	

	// BUSQUEDA
	// ============================
	else if ($dispatch=="search") {	
	
		$sql = " SELECT COUNT(*) AS count ";
		$sql.= "   FROM tbl_macroiniciativa a, tbl_usuario b, tbl_usuario c ";
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
		
		$sql = "   SELECT  a.id_macroiniciativa, a.id_macroiniciativa, a.tx_macroiniciativa, a.tx_indicador, a.fh_mod, b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta " ; 
		$sql.= "     FROM  tbl_macroiniciativa a, tbl_usuario b, tbl_usuario c " ; 
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
			$responce->rows[$i]['id']=$row[id_macroiniciativa];
			$responce->rows[$i]['cell']=array($row[id_macroiniciativa],$row[id_macroiniciativa],$row[tx_macroiniciativa],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		echo json_encode($responce);			 
	}	
	mysqli_close($mysql);	

} else {
	echo "Sessi&oacute;n Invalida, Por favor vuelva a firmarse …";
}	
?>