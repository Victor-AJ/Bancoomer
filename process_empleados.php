<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$id_login = $_SESSION['sess_iduser'];
	$mysql=conexion_db();

	// Recibo variables
	// ============================
	$page 				= $_GET['page']; 
	$limit 				= $_GET['rows']; 
	$start				= $_GET['start'];
	$sidx 				= $_GET['sidx']; 
	$sord 				= $_GET['sord']; 
	$dispatch			= $_GET["dispatch"];
	$id					= $_GET["id"];
	$tx_indicador		= $_GET['tx_indicador']; 	
	$tx_centro 			= $_GET['sel_centro']; 
	$tx_empleado		= $_GET['cap_empleado']; 
	$tx_categoria 		= $_GET['cap_categoria']; 
	$tx_tipologia 		= $_GET['cap_tipologia']; 
	$tx_funcion 		= $_GET['cap_funcion']; 
	$tx_responsable		= $_GET['cap_responsable']; 
	$tx_ubicacion		= $_GET['sel_ubicacion']; 
	$tx_registro 		= $_GET['cap_registro']; 
	$tx_usuario_red		= $_GET['cap_usuario_red']; 
	$tx_usuario_espacio	= $_GET['cap_usuario_espacio']; 
	$tx_telefono 		= $_GET['cap_telefono']; 
	$tx_correo 			= $_GET['cap_correo']; 
	$tx_notas 			= $_GET['cap_notas']; 
	$tx_n3 				= $_GET['cap_n3']; 
	$tx_n4 				= $_GET['cap_n4']; 
	$tx_n5 				= $_GET['cap_n5']; 
	$tx_n6 				= $_GET['cap_n6']; 
	$tx_n7 				= $_GET['cap_n7']; 
	$tx_n8 				= $_GET['cap_n8']; 
	$tx_n9 				= $_GET['cap_n9']; 
	$id_login 			= $_SESSION['sess_iduser'];	
	$q 					= $_GET['q'];	

	if(!$sidx) $sidx = 1;

	// Carga la informacion al grid
	// ============================
	if ($dispatch=="load") { }

	// INSERTA
	// ============================
	else if ($dispatch=="insert") {	
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_proveedor ";
		//$sql.= "  WHERE tx_proveedor = '$tx_proveedor' ";	
	
		//echo "sql", $sql;
		
		//$result = mysqli_query($mysql, $sql);
		//$row = mysqli_fetch_row($result);
		//$count = $row[0];	
		
		//if ($count > 0)	{	
		//	$data = array("error" => true, "message" => "La Raz&oacute;n Social  $tx_proveedor que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
		//	echo json_encode($data);		
		//} else {  
		
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_empleado ";
			$sql.= "  WHERE tx_registro = '$tx_registro' ";	
		
			//echo "sql", $sql;
			
			$result = mysqli_query($mysql, $sql);
			$row = mysqli_fetch_row($result);
			$count = $row[0];	
			
			if ($count > 0)	{	
				$data = array("error" => true, "message" => "El Empleado con registro $tx_registro que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
				echo json_encode($data);		
			} else {  
								
				$fh_alta=date("Y-m-j, g:i");
				$id_usuarioalta=$id_login;		
				$fh_mod=date("Y-m-j, g:i");
				$id_usuariomod=$id_login;	
				
				$sql = " INSERT INTO tbl_empleado SET " ;   			
				$sql.= " 	id_ubicacion		= '$tx_ubicacion', ";
				$sql.= " 	id_centro_costos	= '$tx_centro', ";
				$sql.= " 	tx_registro			= '$tx_registro', ";
				$sql.= " 	tx_usuario_red		= '$tx_usuario_red', ";
				$sql.= " 	tx_usuario_espacio	= '$tx_usuario_espacio', ";
				$sql.= " 	tx_empleado			= '$tx_empleado', ";
				$sql.= " 	tx_categoria		= '$tx_categoria', ";
				$sql.= " 	tx_tipologia		= '$tx_tipologia', ";
				$sql.= " 	tx_funcion			= '$tx_funcion', ";
				$sql.= " 	tx_responsable		= '$tx_responsable', ";
				$sql.= " 	tx_telefono			= '$tx_telefono', ";
				$sql.= " 	tx_correo			= '$tx_correo', ";
				$sql.= " 	tx_notas			= '$tx_notas', ";
				$sql.= " 	tx_n3				= '$tx_n3', ";
				$sql.= " 	tx_n4				= '$tx_n4', ";
				$sql.= " 	tx_n5				= '$tx_n5', ";
				$sql.= " 	tx_n6				= '$tx_n6', ";
				$sql.= " 	tx_n7				= '$tx_n7', ";
				$sql.= " 	tx_n8				= '$tx_n8', ";
				$sql.= " 	tx_n9				= '$tx_n9', ";
				$sql.= " 	tx_indicador		= '$tx_indicador', ";
				$sql.= " 	fh_alta				= '$fh_alta', ";
				$sql.= " 	id_usuarioalta		= '$id_usuarioalta', ";
				$sql.= " 	fh_mod 				= '$fh_mod', ";
				$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
						
				//echo "aaa", $sql; 

				$valBita = "id_ubicacion=$tx_ubicacion ";
				$valBita.= "id_centro_costos=$tx_centro ";
				$valBita.= "tx_registro=$tx_registro ";
				$valBita.= "tx_usuario_red=$tx_usuario_red ";
				$valBita.= "tx_usuario_espacio=$tx_usuario_espacio ";
				$valBita.= "tx_empleado=$tx_empleado ";
				$valBita.= "tx_categoria=$tx_categoria ";
				$valBita.= "tx_tipologia=$tx_tipologia ";
				$valBita.= "tx_funcion=$tx_funcion ";
				$valBita.= "tx_responsable=$tx_responsable ";
				$valBita.= "tx_telefono=$tx_telefono ";
				$valBita.= "tx_correo=$tx_correo ";
				$valBita.= "tx_notas=$tx_notas ";
				$valBita.= "tx_n3=$tx_n3 ";
				$valBita.= "tx_n4=$tx_n4 ";
				$valBita.= "tx_n5=$tx_n5 ";
				$valBita.= "tx_n6=$tx_n6 ";
				$valBita.= "tx_n7=$tx_n7 ";
				$valBita.= "tx_n8=$tx_n8 ";
				$valBita.= "tx_n9=$tx_n9 ";
				$valBita.= "tx_indicador=$tx_indicador ";
				$valBita.= "fh_alta=$fh_alta ";
				$valBita.= "id_usuarioalta=$id_usuarioalta ";
				$valBita.= "fh_mod=$fh_mod ";
				$valBita.= "id_usuariomod=$id_usuariomod ";
				
				
				if (mysqli_query($mysql, $sql))
				{		
					
				//<BITACORA>
	 			$myBitacora = new Bitacora();
	 			$myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL_EMPLEADO" , "$id_login" ,   $valBita  ,"" ,"process_empleados.php");
				//<\BITACORA>
					
					
					$data = array("error" => false, "message" => "El registro se INSERTO correctamente", "html" => "cat_empleado_inventario.php?tx_registro=$tx_registro&dispatch=save&accion=nuevo" );				
					echo json_encode($data);
				} else {  		
					$data = array("error" => true, "message" => "ERROR al INSERTAR el registro !</br></br>Por favor verifique ..." );				
					echo json_encode($data);
				} 		
			//}	
		}	
		mysqli_free_result($result);		
	} 

	// ACTUALIZA
	// ============================
	else if ($dispatch=="save") {	
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_empleado "; 
		$sql.= "  WHERE id_empleado 		<> $id "; 		
		$sql.= " 	AND tx_registro 		= '$tx_registro'  ";
		
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
		
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "El empleado con registro $tx_registro ya existe!</br></br>Por favor verifique ... " );				
			echo json_encode($data);
		} else {  			
			
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;
			
			$sql = " UPDATE tbl_empleado SET " ; 
			$sql.= " 	id_ubicacion		= '$tx_ubicacion', ";
			$sql.= " 	id_centro_costos	= '$tx_centro', ";
			$sql.= " 	tx_registro			= '$tx_registro', ";
			$sql.= " 	tx_usuario_red		= '$tx_usuario_red', ";
			$sql.= " 	tx_usuario_espacio	= '$tx_usuario_espacio', ";
			$sql.= " 	tx_empleado			= '$tx_empleado', ";
			$sql.= " 	tx_categoria		= '$tx_categoria', ";
			$sql.= " 	tx_tipologia		= '$tx_tipologia', ";
			$sql.= " 	tx_funcion			= '$tx_funcion', ";
			$sql.= " 	tx_responsable		= '$tx_responsable', ";
			$sql.= " 	tx_telefono			= '$tx_telefono', ";
			$sql.= " 	tx_correo			= '$tx_correo', ";
			$sql.= " 	tx_notas			= '$tx_notas', ";
			$sql.= " 	tx_n3				= '$tx_n3', ";
			$sql.= " 	tx_n4				= '$tx_n4', ";
			$sql.= " 	tx_n5				= '$tx_n5', ";
			$sql.= " 	tx_n6				= '$tx_n6', ";
			$sql.= " 	tx_n7				= '$tx_n7', ";
			$sql.= " 	tx_n8				= '$tx_n8', ";
			$sql.= " 	tx_n9				= '$tx_n9', ";
			$sql.= " 	tx_indicador		= '$tx_indicador', ";
			$sql.= " 	fh_mod 				= '$fh_mod', ";
			$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
			$sql.= " WHERE id_empleado		= $id ";
					   
			//echo "aaa", $sql;

			//<BITACORA>
	 		$myBitacora = new Bitacora();
			$valores=$myBitacora->obtenvalores ($mysql, "TBL_EMPLEADO",$id );
				  
			if (mysqli_query($mysql, $sql))
			{
				
			//<BITACORA>
 			$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_EMPLEADO" , "$id_login" ,   $valores ,$id ,"process_empleados.php");
			//<\BITACORA>
				
	 			
				$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente", "html" => "cat_empleado_inventario.php?id=$id&dispatch=save" ); 							
				echo json_encode($data);
			} else {  
				$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Por favor verifique ..." );				
				echo json_encode($data);
			}		
		}	
		mysqli_free_result($result);
	} 
	
	else if ($dispatch=='find') {	
	//LL:Al parecer este codigo fue un intento de meter grid, y no se usa
		$auto = '%'.$q.'%';	
	
		if ($campo=='responsable') {
			
			$sql = " SELECT tx_empleado, id_empleado ";
			$sql.= "   FROM tbl_empleado ";
			$sql.= "  WHERE tx_empleado like '$auto' ";
			$sql.= "  GROUP BY tx_empleado ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_empleado'=>$row["tx_empleado"],
					'id_empleado'=>$row["id_empleado"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_empleado	=$TheCatalogo[$i]['tx_empleado'];	
				$id_empleado	=$TheCatalogo[$i]['id_empleado'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_empleado;
				echo $id_empleado;
			}	
			
		} else if ($campo=='categoria') {
			
			$sql = " SELECT tx_categoria ";
			$sql.= "   FROM tbl_empleado ";
			$sql.= "  WHERE tx_categoria like '$auto' ";
			$sql.= "  GROUP BY tx_categoria ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_categoria'=>$row["tx_categoria"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_categoria	=$TheCatalogo[$i]['tx_categoria'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_categoria;
			}	
						
		} else if ($campo=='tipologia') {
			
			$sql = " SELECT tx_tipologia ";
			$sql.= "   FROM tbl_empleado ";
			$sql.= "  WHERE tx_tipologia like '$auto' ";
			$sql.= "  GROUP BY tx_tipologia ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_tipologia'=>$row["tx_tipologia"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_tipologia	=$TheCatalogo[$i]['tx_tipologia'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_tipologia;
			}				
		
		} else if ($campo=='tipologia') {
			
			$sql = " SELECT tx_tipologia ";
			$sql.= "   FROM tbl_empleado ";
			$sql.= "  WHERE tx_tipologia like '$auto' ";
			$sql.= "  GROUP BY tx_tipologia ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_tipologia'=>$row["tx_tipologia"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_tipologia	=$TheCatalogo[$i]['tx_tipologia'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_tipologia;
			}	
						
		} else if ($campo=='funcion') {
			
			$sql = " SELECT tx_funcion ";
			$sql.= "   FROM tbl_empleado ";
			$sql.= "  WHERE tx_funcion like '$auto' ";
			$sql.= "  GROUP BY tx_funcion ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_funcion'=>$row["tx_funcion"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_funcion	=$TheCatalogo[$i]['tx_funcion'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_funcion;
			}				
		}
	}
	
	// BORRA
	// ============================
	else if ($dispatch=='delete') {		
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_proveedor a, tbl_estado b ";
		//$sql.= "  WHERE a.id_proveedor = $id "; 
		//$sql.= "    AND a.id_estado = b.id_estado ";
		
		//echo "aaa", $sql;
		
		//$result = mysqli_query($mysql, $sql);
		//$row = mysqli_fetch_row($result);
		//$count = $row[0];	
		
		//if ($count > 0)	{			
		//	while($row = mysqli_fetch_array($result))
		//	{  	
		//		$tx_estado=$row["tx_estado"];	
		//	}	
		//	$data = array("error" => true, "message" => "La ubicaci&oacute;n de esta relacionado al cat&aacute;logo de proveedores, no es posible eliminarlo ... " );				
		//	echo json_encode($data);			
		//} else {  
		
			$sql = " UPDATE tbl_empleado SET TX_INDICADOR=0";
			$sql.= "  WHERE id_empleado = $id ";
				
			//echo "aaa", $sql; 
				
			if (mysqli_query($mysql, $sql)) 
			{
			

				//<BITACORA>
		 		$myBitacora = new Bitacora();
 				$myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL_EMPLEADO" , "$id_login" ,   "" ,$id ,"process_empleados.php");
				//<\BITACORA>
			
				$data = array("error" => false, "message" => "Se ha dado BAJA al registro correctamente" );				
				echo json_encode($data);		
			} else {  
				$data = array("error" => true, "message" => "ERROR al BORRAR el registro. </br></br>Por favor verifique ..." );				
				echo json_encode($data);
			}	
		//}		
		//mysqli_free_result($result);								
	}	

	// BUSQUEDA
	// ============================
	else if ($dispatch=="search") {	
	
		//LL:Al parecer este codigo fue un intento de meter grid, y no se usa
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