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
	$dispatch			= $_GET["dispatchCom"];
	$id					= $_GET["id"];
	$id_com				= $_GET["id_com"];
	$tx_indicador		= $_GET['tx_indicador']; 	
	$examp 				= $_GET["q"];
	$searchOn 			= Strip($_GET["_search"]);
	$tx_equipo			= $_GET['sel_equipo']; 	
	$tx_marca 			= $_GET['sel_marca']; 		
	$tx_modelo			= $_GET['sel_modelo']; 	
	$tx_ram				= $_GET['cap_ram']; 	
	$tx_serie			= $_GET['cap_serie']; 	
	$tx_siaf			= $_GET['cap_siaf']; 	
	$tx_serial_number	= $_GET['cap_serial_number']; 	
	$tx_compartido		= $_GET['cap_compartido']; 	
	$id_login 			= $_SESSION['sess_iduser'];	
	$campo 				= $_GET['campo'];	
	$q 					= $_GET['q'];	

	if(!$sidx) $sidx = 1;
	
	//echo "dispatch",$dispatch;

	$wh = "";
	$searchOn = Strip($_REQUEST['_search']);
	if($searchOn=='true') {
		$dispatch="search";
		$searchstr = Strip($_REQUEST['filters']);
		$wh= constructWhere($searchstr);
	//echo $wh;
	//echo "<br>";
	}
	
	// Busca el id del computo
	// ============================	
	$sql = " SELECT * ";
	$sql.= "   FROM tbl_computo ";
	$sql.= "  WHERE tx_equipo 	= '$tx_equipo' ";	
	$sql.= "    AND tx_marca 	= '$tx_marca' ";				
	$sql.= "    AND tx_modelo 	= '$tx_modelo' ";				
		
	//echo "sql", $sql;
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoInicial[] = array(				
			'id_computo'=>$row["id_computo"]
		);
	} 	
			
	for ($i=0; $i < count($TheCatalogoInicial); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogoInicial[$i]))					  		
			$id_computo	=$TheCatalogoInicial[$i]['id_computo'];					
	}	

	// Carga la informacion al grid
	// ============================
	if ($dispatch=="load") {		
	//LL: ESTA PARTE AL PARECER NO SE USA, PARECE SER NITENTO DE USO DE GRID 
		$sql = " SELECT COUNT(*) AS count ";
		$sql.= "   FROM tbl_computo a, tbl_usuario b, tbl_usuario c ";
		$sql.= "  WHERE a.id_usuariomod 	= b.id_usuario ";
		$sql.= "    AND a.id_usuarioalta 	= c.id_usuario ";
				
		//echo "sql",	$sql;
			
		$result = mysqli_query($mysql, $sql);	
		$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		$count = $row['count'];
		
		if( $count>0 ) {
			$total_pages = ceil($count/$limit);
		//	$total_pages = 1;
		} else {
			$total_pages = 0;
		}
	
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; 		
		
		$sql = " SELECT id_computo, id_computo, tx_equipo, tx_marca, tx_modelo, tx_ram, a.tx_indicador, b.fh_mod, b.tx_nombre AS usuario_mod, c.fh_alta, c.tx_nombre AS usuario_alta ";
		$sql.= "   FROM tbl_computo a, tbl_usuario b, tbl_usuario c ";
		$sql.= "  WHERE a.id_usuariomod = b.id_usuario  ";
		$sql.= "    AND a.id_usuarioalta = c.id_usuario  ";
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= " 	LIMIT $start, $limit " ;		
		
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql,$sql); 
		
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
			$responce->rows[$i]['id']=$row[id_computo];
			$responce->rows[$i]['cell']=array($row[id_computo],$row[id_computo],$row[tx_equipo],$row[tx_marca],$row[tx_modelo],$row[tx_ram],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		echo json_encode($responce);	
		mysqli_free_result($result);
	}

	// INSERTA
	// ============================
	else if ($dispatch=="insert") {	
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_empleado_computo ";
		//$sql.= "  WHERE id_empleado	= '$id' ";	
		//$sql.= "    AND id_computo 	= '$id_computo' ";				
		
		//echo "sql", $sql;
		
		//$men = $tx_equipo." ".$tx_marca." ".$tx_modelo;
			
		//$result = mysqli_query($mysql, $sql);
		//$row = mysqli_fetch_row($result);
		//$count = $row[0];	
			
		//if ($count > 0)	{	
		//	$data = array("error" => true, "message" => "El computo $men que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
		//	echo json_encode($data);		
		//} else {  
			$tx_indicador="1";						
			$fh_alta=date("Y-m-j, g:i");
			$id_usuarioalta=$id_login;		
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;	
				
			$sql = " INSERT INTO tbl_empleado_computo SET " ;
			$sql.= " 	id_empleado		= '$id', ";  			
			$sql.= " 	id_computo		= '$id_computo', ";
			$sql.= " 	tx_ram			= '$tx_ram', ";
			$sql.= " 	tx_serie		= '$tx_serie', ";
			$sql.= " 	tx_siaf			= '$tx_siaf', ";
			$sql.= " 	tx_compartido	= '$tx_compartido', ";
			$sql.= " 	tx_indicador	= '$tx_indicador', ";
			$sql.= " 	fh_alta			= '$fh_alta', ";
			$sql.= " 	id_usuarioalta	= '$id_usuarioalta', ";
			$sql.= " 	fh_mod 			= '$fh_mod', ";
			$sql.= " 	id_usuariomod	= '$id_usuariomod' "; 
					
			//echo "aaa", $sql;  
			  			
			$valoresBita= "id_empleado=$id ";  			
			$valoresBita.= "id_computo=$id_computo ";
			$valoresBita.= "tx_ram=$tx_ram ";
			$valoresBita.= "tx_serie=$tx_serie ";
			$valoresBita.= "tx_siaf=$tx_siaf ";
			$valoresBita.= "tx_compartido=$tx_compartido ";
			$valoresBita.= "tx_indicador=$tx_indicador ";
			$valoresBita.= "fh_alta	=$fh_alta ";
			$valoresBita.= "id_usuarioalta=$id_usuarioalta ";
			$valoresBita.= "fh_mod=$fh_mod ";
			$valoresBita.= "id_usuariomod=$id_usuariomod "; 
			
			if (mysqli_query($mysql, $sql))
			{		
				//<BITACORA>
				$myBitacora = new Bitacora();
	 			$myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL_EMPLEADO_COMPUTO" , "$id_login" ,  $valoresBita , ""  ,  "process_empleado_computo.php");
				//<\BITACORA
				
	 			
				$data = array("error" => false, "message" => "El registro se INSERTO correctamente", "html" => "cat_computo_lista.php?id=$id&dispatch=save" );				
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
		//$sql.= "   FROM tbl_empleado_computo ";
		//$sql.= "  WHERE id_empleado	<> '$id' ";	
		//$sql.= "    AND id_computo 	= '$id_computo' ";				
		
		//echo "sql",	$sql;
		//$men = $tx_equipo." ".$tx_marca." ".$tx_modelo;
				
		//$result = mysqli_query($mysql, $sql);
		//$row = mysqli_fetch_row($result);
		//$count = $row[0];	
		
		//if ($count > 0)	{	
		//	$data = array("error" => true, "message" => "El computo $men ya existe</br></br>Por favor verifique ... " );				
		//	echo json_encode($data);
		//} else {  	
			
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;
			
			$sql = " UPDATE tbl_empleado_computo SET " ; 
			$sql.= " 	id_empleado		= $id, ";  			
			$sql.= " 	id_computo		= $id_computo, ";
			$sql.= " 	tx_ram			= '$tx_ram', ";
			$sql.= " 	tx_serie		= '$tx_serie', ";
			$sql.= " 	tx_siaf			= '$tx_siaf', ";
			$sql.= " 	tx_compartido	= '$tx_compartido', ";
			$sql.= " 	tx_indicador	= '$tx_indicador', ";			
			$sql.= " 	fh_mod 			= '$fh_mod', ";
			$sql.= " 	id_usuariomod	= '$id_usuariomod' "; 
			$sql.= " WHERE id_empleado_computo = $id_com ";
					   
			//echo "aaa", $sql;      
			//<BITACORA>
			$myBitacora = new Bitacora();
			$valoresBita=$myBitacora->obtenvalores ($mysql, "TBL_EMPLEADO_COMPUTO", $id_com );
			
				  
			if (mysqli_query($mysql, $sql))
			{
				
				//<BITACORA>
				
	 			$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_EMPLEADO_COMPUTO" , "$id_login" ,  $valoresBita , $id_com   ,  "process_empleado_computo.php");
				//<\BITACORA>
				
	 			
				$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente", "html" => "cat_computo_lista.php?id=$id&dispatch=save" );	
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
		//LL: ESTA PARTE NO SE USA, AL PARECER FUE INTENTO DE METER GRID
	
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
			
		} else if ($campo=='marca'){			
			
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_computo ";
			$sql.= "  WHERE tx_marca like '$auto' ";
			$sql.= "  GROUP BY tx_marca ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_marca'=>$row["tx_marca"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_marca	=$TheCatalogo[$i]['tx_marca'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_marca;
			}	
					
		} else if ($campo=='modelo'){			
			
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_computo ";
			$sql.= "  WHERE tx_modelo like '$auto' ";
			$sql.= "  GROUP BY tx_modelo ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_modelo'=>$row["tx_modelo"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_modelo	=$TheCatalogo[$i]['tx_modelo'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_modelo;
			}		
				
		} else if ($campo=='ram'){			
			
			$sql = " SELECT tx_ram ";
			$sql.= "   FROM tbl_empleado_computo ";
			$sql.= "  WHERE tx_ram like '$auto' ";
			$sql.= "  GROUP BY tx_ram ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_ram'=>$row["tx_ram"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_ram	=$TheCatalogo[$i]['tx_ram'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_ram;
			}			
		}
	}
	
	// BORRA
	// ============================
	else if ($dispatch=='delete') {		
		
		$sql = " UPDATE  tbl_empleado_computo  SET tx_indicador=0 ";
		$sql.= " 	   WHERE id_empleado_computo = $id_com ";
				
		//echo "aaa", $sql; 
				
		if (mysqli_query($mysql, $sql)) 
		{
			//<BITACORA>
			$myBitacora = new Bitacora();
	 		$myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL_EMPLEADO_COMPUTO" , "$id_login" ,  "" , $id_com  ,  "process_empleado_computo.php");
			//<\BITACORA
			
	 		
			
			$data = array("error" => false, "message" => "Se ha dado de BAJA el registro exitosamente", "html" => "cat_computo_lista.php?id=$id&dispatch=save" );				
			echo json_encode($data);		
		} else {  
			$data = array("error" => true, "message" => "ERROR al BORRAR el registro. </br></br>Por favor verifique ..." );				
			echo json_encode($data);
		}			
	}	

	// BUSQUEDA
	// ============================
	else if ($dispatch=="search") {	
	
	//LL: ESTA PARTE AL PARECER FUE INTENTO DE METER GRID, NO SE USA
		$sql = " SELECT COUNT(*) AS count "; 
		$sql.= "   FROM tbl_computo a, tbl_usuario b, tbl_usuario c ";
		$sql.= "  WHERE a.id_usuariomod = b.id_usuario  ";
		$sql.= "    AND a.id_usuarioalta = c.id_usuario  ".$wh ;	
	
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
		
		$sql = " SELECT id_computo, id_computo, tx_equipo, tx_marca, tx_modelo, tx_ram, a.tx_indicador, b.fh_mod, b.tx_nombre AS usuario_mod, c.fh_alta, c.tx_nombre AS usuario_alta ";
		$sql.= "   FROM tbl_computo a, tbl_usuario b, tbl_usuario c ";
		$sql.= "  WHERE a.id_usuariomod = b.id_usuario  ";
		$sql.= "    AND a.id_usuarioalta = c.id_usuario  ".$wh ;	
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= " 	LIMIT $start, $limit " ;			
			
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql,$sql); 
		
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;
			while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
			$responce->rows[$i]['id']=$row[id_computo];
			$responce->rows[$i]['cell']=array($row[id_computo],$row[id_computo],$row[tx_equipo],$row[tx_marca],$row[tx_modelo],$row[tx_ram],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		echo json_encode($responce);			 
	}	
	mysqli_close($mysql);	

} else {
	echo "Sessi&oacute;n Invalida, Por favor vuelva a firmarse …";
}	

// FUNCIONES PARA BUSQUEDA
// ============================

function constructWhere($s){
    $qwery = "";
	//['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc']
    $qopers = array(
				  'eq'=>" = ",
				  'ne'=>" <> ",
				  'lt'=>" < ",
				  'le'=>" <= ",
				  'gt'=>" > ",
				  'ge'=>" >= ",
				  'bw'=>" LIKE ",
				  'bn'=>" NOT LIKE ",
				  'in'=>" IN ",
				  'ni'=>" NOT IN ",
				  'ew'=>" LIKE ",
				  'en'=>" NOT LIKE ",
				  'cn'=>" LIKE " ,
				  'nc'=>" NOT LIKE " );
    if ($s) {
        $jsona = json_decode($s,true);
        if(is_array($jsona)){
			$gopr = $jsona['groupOp'];
			$rules = $jsona['rules'];
            $i =0;
            foreach($rules as $key=>$val) {
                $field = $val['field'];
                $op = $val['op'];
                $v = $val['data'];
				
				//if ($field=="a.tx_indicador"){
				//	if ($v=="ACTIVO") $v=1;
				//	else if ($v=="INACTIVO") $v=0;
				//}
				
				if($v && $op) {
	                $i++;
					// ToSql in this case is absolutley needed
					$v = ToSql($field,$op,$v);
					if (i == 1) $qwery = " AND ";
					else $qwery .= " " .$gopr." ";
					switch ($op) {
						// in need other thing
					    case 'in' :
					    case 'ni' :
					        $qwery .= $field.$qopers[$op]." (".$v.")";
					        break;
						default:
					        $qwery .= $field.$qopers[$op].$v;
					}
				}
            }
        }
    }
    return $qwery;
}
function ToSql ($field, $oper, $val) {
	// we need here more advanced checking using the type of the field - i.e. integer, string, float
	switch ($field) {
		case 'id':
			return intval($val);
			break;
		case 'amount':
		case 'tax':
		case 'total':
			return floatval($val);
			break;
		default :
			//mysql_real_escape_string is better
			if($oper=='bw' || $oper=='bn') return "'" . addslashes($val) . "%'";
			else if ($oper=='ew' || $oper=='en') return "'%" . addcslashes($val) . "'";
			else if ($oper=='cn' || $oper=='nc') return "'%" . addslashes($val) . "%'";
			else return "'" . addslashes($val) . "'";
	}
}

function Strip($value)
{
	if(get_magic_quotes_gpc() != 0)
  	{
    	if(is_array($value))  
			if ( array_is_associative($value) )
			{
				foreach( $value as $k=>$v)
					$tmp_val[$k] = stripslashes($v);
				$value = $tmp_val; 
			}				
			else  
				for($j = 0; $j < sizeof($value); $j++)
        			$value[$j] = stripslashes($value[$j]);
		else
			$value = stripslashes($value);
	}
	return $value;
}

function array_is_associative ($array)
{
    if ( is_array($array) && ! empty($array) )
    {
        for ( $iterator = count($array) - 1; $iterator; $iterator-- )
        {
            if ( ! array_key_exists($iterator, $array) ) { return true; }
        }
        return ! array_key_exists(0, $array);
    }
    return false;
}

?>