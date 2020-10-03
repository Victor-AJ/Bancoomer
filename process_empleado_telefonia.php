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

	// ============================
	// Recibo variables
	// ============================
	$page 				= $_GET['page']; 
	$limit 				= $_GET['rows']; 
	$start				= $_GET['start'];
	$sidx 				= $_GET['sidx']; 
	$sord 				= $_GET['sord']; 
	$dispatch			= $_GET["dispatchTel"];
	$id					= $_GET["id"];
	$id_tel				= $_GET["id_tel"];
	$tx_indicador		= $_GET['tx_indicador']; 	
	$tx_tipo			= $_GET['sel_tipo']; 	
	$tx_marca 			= $_GET['sel_marca']; 		
	$tx_modelo			= $_GET['sel_modelo']; 	
	$tx_numero			= $_GET['cap_numero']; 	
	$tx_proveedor		= $_GET['sel_proveedor']; 	
	$tx_plan			= $_GET['sel_plan']; 	
	$tx_serie			= $_GET['cap_serie']; 	
	$tx_siaf			= $_GET['cap_siaf']; 	
	$id_login 			= $_SESSION['sess_iduser'];	
	$campo 				= $_GET['campo'];	
	$q 					= $_GET['q'];	

	if(!$sidx) $sidx = 1;
	
	//echo "dispatch",$dispatch;
	
	// ============================	
	// Busca el id de la telefonia
	// ============================	
	$sql = " SELECT * ";
	$sql.= "   FROM tbl_telefonia ";
	$sql.= "  WHERE tx_tipo 	= '$tx_tipo' ";	
	$sql.= "    AND tx_marca 	= '$tx_marca' ";				
	$sql.= "    AND tx_modelo 	= '$tx_modelo' ";				
		
	//echo "sql", $sql;
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoInicial[] = array(				
			'id_telefonia'=>$row["id_telefonia"]
		);
	} 	
			
	for ($i=0; $i < count($TheCatalogoInicial); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogoInicial[$i]))					  		
			$id_telefonia	=$TheCatalogoInicial[$i]['id_telefonia'];					
	}	
	
	if ($tx_proveedor=="0") 
	{
		$id_telefonia_plan=1;
	} else {
		// ============================	
		// Busca el id del plan
		// ============================	
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_telefonia_plan ";
		$sql.= "  WHERE tx_proveedor		= '$tx_proveedor' ";	
		$sql.= "    AND tx_plan 			= '$tx_plan' ";				
			
		//echo "sql", $sql;
			
		$result = mysqli_query($mysql, $sql);	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{	
			$TheCatalogoPlan[] = array(				
				'id_telefonia_plan'=>$row["id_telefonia_plan"]
			);
		} 	
				
		for ($i=0; $i < count($TheCatalogoPlan); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogoPlan[$i]))					  		
				$id_telefonia_plan	=$TheCatalogoPlan[$i]['id_telefonia_plan'];					
		}
	}		

	// Carga la informacion al grid
	// ============================
	if ($dispatch=="load") {
	}

	// INSERTA
	// ============================
	else if ($dispatch=="insert") {	
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_empleado_telefonia ";
		//$sql.= "  WHERE id_empleado			= $id ";	
		//$sql.= "    AND id_telefonia		= $id_telefonia ";				
		//$sql.= "    AND id_telefonia_plan	= $id_telefonia_plan ";				
		
		//echo "sql", $sql;
		
		//$men = $tx_tipo." ".$tx_marca." ".$tx_modelo;
			
		//$result = mysqli_query($mysql, $sql);
		//$row = mysqli_fetch_row($result);
		//$count = $row[0];	
			
		//if ($count > 0)	{	
		//	$data = array("error" => true, "message" => "La telefon&iacute;a $men que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
		//	echo json_encode($data);		
		//} else {  
			$tx_indicador="1";						
			$fh_alta=date("Y-m-j, g:i");
			$id_usuarioalta=$id_login;		
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;	
				
			$sql = " INSERT INTO tbl_empleado_telefonia SET " ;
			$sql.= " id_empleado		= $id, ";  			
			$sql.= " id_telefonia		= $id_telefonia, ";
			$sql.= " id_telefonia_plan	= $id_telefonia_plan, ";
			$sql.= " tx_numero			= '$tx_numero', ";
			$sql.= " tx_serie			= '$tx_serie', ";
			$sql.= " tx_siaf			= '$tx_siaf', ";
			$sql.= " tx_indicador		= '$tx_indicador', ";
			$sql.= " fh_alta			= '$fh_alta', ";
			$sql.= " id_usuarioalta		= '$id_usuarioalta', ";
			$sql.= " fh_mod 			= '$fh_mod', ";
			$sql.= " id_usuariomod		= '$id_usuariomod' "; 
					
			//echo "aaa", $sql;  
			//bitacora
			
			$valoresBita= "id_empleado=$id ";  			
			$valoresBita.= "id_telefonia=$id_telefonia ";
			$valoresBita.= "id_telefonia_plan=$id_telefonia_plan ";
			$valoresBita.= "tx_numero=$tx_numero ";
			$valoresBita.= "tx_serie=$tx_serie ";
			$valoresBita.= "tx_siaf=$tx_siaf ";
			$valoresBita.= "tx_indicador=$tx_indicador ";
			$valoresBita.= "fh_alta=$fh_alta ";
			$valoresBita.= "id_usuarioalta=$id_usuarioalta ";
			$valoresBita.= "fh_mod=$fh_mod ";
			$valoresBita.= "id_usuariomod=$id_usuariomod "; 
			
			if (mysqli_query($mysql, $sql))
			{		
				
				//<BITACORA>
				$myBitacora = new Bitacora();
	 			$myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL_EMPLEADO_TELEFONIA" , "$id_login" ,  $valoresBita , ""  ,  "process_empleado_telefonia.php");
				//<\BITACORA
				
				
				
				$data = array("error" => false, "message" => "El registro se INSERTO correctamente", "html" => "cat_telefonia_lista.php?id=$id&dispatch=save" );				
				echo json_encode($data);
			} else {  		
				$data = array("error" => true, "message" => "ERROR al INSERTAR el registro !</br></br>Por favor verifique ..." );				
				echo json_encode($data);
			} 		
		//}			
		mysqli_free_result($result);		
	} 

	// ACTUALIZA
	// ============================
	else if ($dispatch=="save") {			
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_empleado_telefonia ";
		//$sql.= "  WHERE id_empleado			<> $id ";	
		//$sql.= "    AND id_telefonia		= $id_telefonia ";				
		//$sql.= "    AND id_telefonia_plan	= $id_telefonia_plan ";					
		
		//echo "sql",	$sql;
		//$men = $tx_tipo." ".$tx_marca." ".$tx_modelo;
				
		//$result = mysqli_query($mysql, $sql);
		//$row = mysqli_fetch_row($result);
		//$count = $row[0];	
		
		//if ($count > 0)	{	
		//	$data = array("error" => true, "message" => "La telefon&iacute;a $men ya existe</br></br>Por favor verifique ... " );				
		//	echo json_encode($data);
		//} else {  	
			
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;
			
			$sql = " UPDATE tbl_empleado_telefonia SET " ; 
			$sql.= " 	id_telefonia		= $id_telefonia, ";
			$sql.= " 	id_telefonia_plan	= $id_telefonia_plan, ";
			$sql.= " 	tx_numero			= '$tx_numero', ";
			$sql.= " 	tx_serie			= '$tx_serie', ";
			$sql.= " 	tx_siaf				= '$tx_siaf', ";
			$sql.= " 	fh_mod 				= '$fh_mod', ";
			$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
			$sql.= " WHERE id_empleado_telefonia = $id_tel ";
					   
			//echo "aaa", $sql;
			//<BITACORA>
			$myBitacora = new Bitacora();
			$valoresBita=$myBitacora->obtenvalores ($mysql, "TBL_EMPLEADO_TELEFONIA", $id_tel );
				  
			if (mysqli_query($mysql, $sql))
			{
				//<BITACORA>
				
	 			$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_EMPLEADO_TELEFONIA" , "$id_login" ,  $valoresBita , $id_tel   ,  "process_empleado_telefonia.php");
				//<\BITACORA
				
				$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente", "html" => "cat_telefonia_lista.php?id=$id&dispatch=save" );				
				echo json_encode($data);
			} else {  
				$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Por favor verifique ..." );				
				echo json_encode($data);
			}	
		//}	
	} 
	
	// FIND
	// ============================
	
	else if ($dispatch=='find') {
	//LL : Esta seccion de codigo no se usa, al parecer fue intento de meter grid
		$auto = '%'.$q.'%';	
	
		if ($campo=='equipo') {
			
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_computo ";
			$sql.= "  WHERE tx_equipo like '$auto' ";
			$sql.= "  GROUP BY tx_equipo ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_equipo'=>$row["tx_equipo"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_equipo	=$TheCatalogo[$i]['tx_equipo'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_equipo;
			}				
		} 
	}
	
	// BORRA
	// ============================
	else if ($dispatch=='delete') {		
		
		$sql = " UPDATE tbl_empleado_telefonia  set tx_indicador=0 ";
		$sql.= " 	   WHERE id_empleado_telefonia = $id_tel ";
				
		//echo "aaa", $sql; 
				
		
			//<BITACORA>
			$myBitacora = new Bitacora();
	 		$myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL_EMPLEADO_TELEFONIA" , "$id_login" ,  "" , $id_tel  ,  "process_empleado_telefonia.php");
			//<\BITACORA
			
	 		
		if (mysqli_query($mysql, $sql)) 
		{
			$data = array("error" => false, "message" => "Se ha dado de BAJA el registro correctamente", "html" => "cat_telefonia_lista.php?id=$id&dispatch=save" );				
			echo json_encode($data);		
		} else {  
			$data = array("error" => true, "message" => "ERROR al BORRAR el registro. </br></br>Por favor verifique ..." );				
			echo json_encode($data);
		}			
	}	

	// BUSQUEDA
	// ============================
	else if ($dispatch=="search") {	
	}	
	mysqli_close($mysql);	

} else {
	echo "Sessi&oacute;n Invalida, Por favor vuelva a firmarse …";
}	
?>