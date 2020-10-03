<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
$mysql=conexion_db();
if 	(isset($_SESSION["sess_user"])) 
	$id_login = $_SESSION['sess_iduser'];


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
	$examp 				= $_GET["q"];
	$searchOn 			= Strip($_GET["_search"]);
	$tx_tipo			= $_GET['cap_tipo']; 	
	$tx_marca 			= $_GET['cap_marca']; 		
	$tx_modelo			= $_GET['cap_modelo']; 	
	$tx_ram				= $_GET['cap_ram']; 	
	$id_login 			= $_SESSION['sess_iduser'];	
	$campo 				= $_GET['campo'];	
	$q 					= $_GET['q'];	

	if(!$sidx) $sidx = 1;

	$wh = "";
	$searchOn = Strip($_REQUEST['_search']);
	if($searchOn=='true') {
		$dispatch="search";
		$searchstr = Strip($_REQUEST['filters']);
		$wh= constructWhere($searchstr);
	//echo $wh;
	//echo "<br>";
	}

	// Carga la informacion al grid
	// ============================
	if ($dispatch=="load") {		
	
		$sql = " SELECT COUNT(*) AS count ";
		$sql.= "   FROM tbl_telefonia a, tbl_usuario b, tbl_usuario c ";
		$sql.= "  WHERE a.id_usuariomod 	= b.id_usuario ";
		$sql.= "    AND a.id_usuarioalta 	= c.id_usuario ";
				
		//echo "sql",	$sql;
			
		$result = mysqli_query($mysql, $sql);	
		$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		$count = $row['count'];
		
		if( $count>0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
	
		if ($page > $total_pages) $page=$total_pages;
		if ($limit<0) $limit = 0;
		$start = $limit*$page - $limit; 	
		if ($start<0) $start = 0;		
		
		$sql = " SELECT id_telefonia, id_telefonia, tx_tipo, tx_marca, tx_modelo, a.tx_indicador, b.fh_mod, b.tx_nombre AS usuario_mod, c.fh_alta, c.tx_nombre AS usuario_alta ";
		$sql.= "   FROM tbl_telefonia a, tbl_usuario b, tbl_usuario c ";
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
			$responce->rows[$i]['id']=$row[id_telefonia];
			$responce->rows[$i]['cell']=array($row[id_telefonia],$row[id_telefonia],$row[tx_tipo],$row[tx_marca],$row[tx_modelo],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		
		//<BITACORA>
	 	$myBitacora = new Bitacora();
		$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_TELEFONIA" , "$id_login" ,  "", ""  ,  "process_telefonia.php");
	    //<\BITACORA>
		
		
		echo json_encode($responce);	
		mysqli_free_result($result);
	}

	// INSERTA
	// ============================
	else if ($dispatch=="insert") {	
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_telefonia ";
		$sql.= "  WHERE tx_tipo 	= '$tx_tipo' ";	
		$sql.= "    AND tx_marca 	= '$tx_marca' ";				
		$sql.= "    AND tx_modelo 	= '$tx_modelo' ";				
		
		//echo "sql", $sql;
		
		$men = $tx_tipo." ".$tx_marca." ".$tx_modelo;
			
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
			
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "La configuraci&oacute;n telefon&iacute;ca $men que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
			echo json_encode($data);		
		} else {  
								
			$fh_alta=date("Y-m-j, g:i");
			$id_usuarioalta=$id_login;		
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;	
				
			$sql = " INSERT INTO tbl_telefonia SET " ;  			
			$sql.= " tx_tipo		= '$tx_tipo', ";
			$sql.= " tx_marca		= '$tx_marca', ";
			$sql.= " tx_modelo		= '$tx_modelo', ";
			$sql.= " tx_indicador	= '$tx_indicador', ";
			$sql.= " fh_alta		= '$fh_alta', ";
			$sql.= " id_usuarioalta	= '$id_usuarioalta', ";
			$sql.= " fh_mod 		= '$fh_mod', ";
			$sql.= " id_usuariomod	= '$id_usuariomod' "; 
					
			//echo "aaa", $sql;  
			
			if (mysqli_query($mysql, $sql))
			{		
			
			//<BITACORA>
				$valBita= "tx_tipo=$tx_tipo ";
				$valBita.= "tx_marca=$tx_marca ";
				$valBita.= "tx_modelo=$tx_modelo ";
				$valBita.= "tx_indicador=$tx_indicador ";
				$valBita.= "fh_alta=$fh_alta ";
				$valBita.= "id_usuarioalta=$id_usuarioalta ";
				$valBita.= "fh_mod=$fh_mod ";
				$valBita.= "id_usuariomod=$id_usuariomod "; 
			
			
				$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL_TELEFONIA" , "$id_login" ,  $valBita, ""  ,  "process_telefonia.php");
				//<\BITACORA>
				
				
				$data = array("error" => false, "message" => "El registro se INSERTO correctamente" );				
				echo json_encode($data);
			} else {  		
				$data = array("error" => true, "message" => "ERROR al INSERTAR el registro !</br></br>Por favor verifique ..." );				
				echo json_encode($data);
			} 		
		}			
		mysqli_free_result($result);		
	} 

	// ACTUALIZA
	// ============================
	else if ($dispatch=="save") {	
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_telefonia "; 
		$sql.= "  WHERE id_telefonia <> $id "; 	
		$sql.= "  	AND tx_tipo 	= '$tx_tipo' ";	
		$sql.= "    AND tx_marca 	= '$tx_marca' ";				
		$sql.= "    AND tx_modelo 	= '$tx_modelo' ";				
		
		//echo "sql",	$sql;
		$men = $tx_tipo." ".$tx_marca." ".$tx_modelo;
				
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
		
		if ($count > 0)	{	
			
			$data = array("error" => true, "message" => "La configuraci&oacute;n telefon&iacute;ca $men ya existe</br></br>Por favor verifique ... " );				
			echo json_encode($data);
			
		} else {  			
			
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;
			
			$sql = " UPDATE tbl_telefonia SET " ; 
			$sql.= " tx_tipo		= '$tx_tipo', ";
			$sql.= " tx_marca		= '$tx_marca', ";
			$sql.= " tx_modelo		= '$tx_modelo', ";
			$sql.= " tx_indicador	= '$tx_indicador', ";
			$sql.= " fh_mod 		= '$fh_mod', ";
			$sql.= " id_usuariomod	= '$id_usuariomod' "; 
			$sql.= " WHERE id_telefonia = $id ";
					   
			//echo "aaa", $sql;      
	
				  	$myBitacora = new Bitacora();
				$valores=$myBitacora->obtenvalores ($mysql, "TBL_TELEFONIA" , $id);
	
	
				  
			if (mysqli_query($mysql, $sql))
			{
			
				$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_TELEFONIA" , "$id_login" ,  $valores, "$id"  ,  "process_telefonia.php");
			
				$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente" );							
				echo json_encode($data);
			} else {  
				$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Por favor verifique ..." );				
				echo json_encode($data);
			}				
		}	
		mysqli_free_result($result);
	} 
	
	// FIND
	// ============================
	
	else if ($dispatch=='find') {
	
		$auto = '%'.$q.'%';	
	
		if ($campo=='tipo') {
			
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_telefonia ";
			$sql.= "  WHERE tx_tipo like '$auto' ";
			$sql.= "  GROUP BY tx_tipo ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_tipo'=>$row["tx_tipo"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_tipo	=$TheCatalogo[$i]['tx_tipo'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_tipo;
			}	
			
		} else if ($campo=='marca'){			
			
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_telefonia ";
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
			$sql.= "   FROM tbl_telefonia ";
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
		} 		
	}
	
	// BORRA
	// ============================
	else if ($dispatch=='delete') {		
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_empleado_telefonia ";
		$sql.= "  WHERE id_telefonia = $id "; 
		
		//echo "aaa", $sql;
		
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
		
		if ($count > 0)	{			
		
			while($row = mysqli_fetch_array($result))
			$data = array("error" => true, "message" => "NO ES POSIBLE ELIMINARLO... Ya que la telefonia seleccionada est&aacute; asignada a los Empleados ... " );				
			echo json_encode($data);			
			
		} else {  
		
			$sql = " UPDATE  tbl_telefonia  SET TX_INDICADOR='0' ";
			$sql.= "  WHERE id_telefonia = $id ";
				
			//echo "aaa", $sql; 
				
			if (mysqli_query($mysql, $sql)) 
			{
			
			
				//<BITACORA>
	 			$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL_TELEFONIA" , "$id_login" ,  "", "$id"  ,  "process_telefonia.php");
			    //<\BITACORA>
		
		
				$data = array("error" => false, "message" => "Registro dado de BAJA  correctamente" );				
				echo json_encode($data);		
			} else {  
				$data = array("error" => true, "message" => "ERROR al BORRAR el registro. </br></br>Por favor verifique ..." );				
				echo json_encode($data);
			}	
		}		
		mysqli_free_result($result);								
	}	

	// BUSQUEDA
	// ============================
	else if ($dispatch=="search") {	
	
	
		$sql = " SELECT COUNT(*) AS count "; 
		$sql.= "   FROM tbl_telefonia a, tbl_usuario b, tbl_usuario c ";
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
		
		$sql = " SELECT id_telefonia, id_telefonia, tx_tipo, tx_marca, tx_modelo, tx_ram, a.tx_indicador, b.fh_mod, b.tx_nombre AS usuario_mod, c.fh_alta, c.tx_nombre AS usuario_alta ";
		$sql.= "   FROM tbl_telefonia a, tbl_usuario b, tbl_usuario c ";
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
			$responce->rows[$i]['id']=$row[id_telefonia];
			$responce->rows[$i]['cell']=array($row[id_telefonia],$row[id_telefonia],$row[tx_tipo],$row[tx_marca],$row[tx_modelo],$row[tx_ram],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
			//<BITACORA>
	 	$myBitacora = new Bitacora();
	 	 $whr =str_ireplace("'", " " , $wh); 
		$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_TELEFONIA" , "$id_login" ,  $whr, ""  ,  "process_telefonia.php");
	    //<\BITACORA>
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