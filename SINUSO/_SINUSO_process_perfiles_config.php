<?
include("includes/funciones.php");  
$mysql=conexion_db();

// Recibo variables
// ============================
$dispatch		= $_GET["dispatch"];


// Carga la informacion al grid
// ============================
if ($dispatch=="load") {
	
	$sql = " SELECT COUNT(*) AS count ";
	$sql.= "   FROM tbl_perfil a, tbl_usuario b, tbl_usuario c ";
	$sql.= "  WHERE a.id_usuariomod = b.id_usuario ";
	$sql.= "    AND a.id_usuarioalta = c.id_usuario ";

	$result = mysqli_query($mysql, $sql);	
	$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	$count = $row['count'];
	
	if( $count>0 ) {
		$total_pages = ceil($count/$limit);
	} else {
		$total_pages = 0;
	}
	
	if ($page > $total_pages) $page=$total_pages;
	$start = $limit*$page - $limit; 
	
	$sql = "   SELECT  a.id_perfil, a.id_perfil, a.tx_nombre, a.tx_indicador, a.fh_mod, b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta " ; 
	$sql.= "     FROM  tbl_perfil a, tbl_usuario b, tbl_usuario c " ; 
	$sql.= "    WHERE a.id_usuariomod = b.id_usuario " ;
	$sql.= " 	  AND a.id_usuarioalta = c.id_usuario " ; 
	$sql.= " ORDER BY $sidx $sord " ;
	$sql.= " 	LIMIT $start, $limit " ;		
	//echo "sql",	$sql;
	
	$result = mysqli_query($mysql,$sql); 
	
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		$responce->rows[$i]['id']=$row[id_perfil];
		$responce->rows[$i]['cell']=array($row[id_perfil],$row[id_perfil],$row[tx_nombre],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
		$i++;
	} 	
	echo json_encode($responce);	
	mysqli_free_result($result);
}

// INSERTA
// ============================
else if ($dispatch=="insert") {	
	
	$sql = " SELECT * ";
	$sql.= "   FROM tbl_perfil ";
	$sql.= "  WHERE tx_nombre = '$tx_nombre' " ; 		

	//echo "sql", $sql;
	
	$result = mysqli_query($mysql, $sql);
	$row = mysqli_fetch_row($result);
	$count = $row[0];	
	
	if ($count > 0)	{	
		$data = array("error" => true, "message" => "El Perfil que desa dar de alta ya existe !</br></br> Por favor vefique ..." );					
		echo json_encode($data);		
	} else {  						
		$fh_alta=date("Y-m-j, g:i");
		$id_usuarioalta="1";		
		$fh_mod=date("Y-m-j, g:i");
		$id_usuariomod="1";		
		
		$sql = " INSERT INTO tbl_perfil SET " ;   
		$sql.= " tx_nombre		= '$tx_nombre', ";
		$sql.= " tx_indicador	= '$tx_indicador', ";
		$sql.= " fh_alta		= '$fh_alta', ";
		$sql.= " fh_mod 		= '$fh_mod', ";
		$sql.= " id_usuarioalta	= '$id_usuarioalta', ";
		$sql.= " id_usuariomod	= '$id_usuariomod' "; 
				
		//echo "aaa", $sql;  
		
		if (mysqli_query($mysql, $sql))
		{		
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
	$sql.= "   FROM tbl_perfil "; 
	$sql.= "  WHERE id_perfil <> $id "; 		
	$sql.= "    AND tx_nombre = '$tx_nombre' "; 		
	
	//echo "sql",	$sql;
	
	$result = mysqli_query($mysql, $sql);
	$row = mysqli_fetch_row($result);
	$count = $row[0];	
	
	if ($count > 0)	{	
		$data = array("error" => true, "message" => "El Perfil $tx_usuario ya existe!</br></br>Por favor verifique ... " );				
		echo json_encode($data);
	} else {  	
		
		$fh_mod=date("Y-m-j, g:i");
		$id_usuariomod="1";
	
		$sql = " UPDATE tbl_perfil SET " ; 
		$sql.= " tx_nombre		= '$tx_nombre', ";
		$sql.= " tx_indicador	= '$tx_indicador', ";	
		$sql.= " fh_mod 		= '$fh_mod', ";	
		$sql.= " id_usuariomod	= '$id_usuariomod' "; 
		$sql.= " WHERE id_perfil= $id ";
			   
		//echo "aaa", $sql;      
		  
		if (mysqli_query($mysql, $sql))
		{
			$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente" );							
			echo json_encode($data);
		} else {  
			$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Por favor verifique ..." );				
			echo json_encode($data);
		}		
	}	
} 
	
// BORRA
// ============================
else if ($dispatch=='delete') {		
	
	$sql = " DELETE FROM tbl_perfil ";
	$sql.= "  WHERE id_perfil = $id ";
		
	//echo "aaa", $sql; 
		
	if (mysqli_query($mysql, $sql)) 
	{
		$data = array("error" => false, "message" => "El registro se BORRO correctamente" );				
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
	$sql.= "   FROM tbl_perfil a, tbl_usuario b, tbl_usuario c ";
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
	
	$sql = "   SELECT  a.id_perfil, a.id_perfil, a.tx_nombre, a.tx_indicador, a.fh_mod, b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta " ; 
	$sql.= "     FROM  tbl_perfil a, tbl_usuario b, tbl_usuario c " ; 
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
		$responce->rows[$i]['id']=$row[id_perfil];
		$responce->rows[$i]['cell']=array($row[id_perfil],$row[id_perfil],$row[tx_nombre],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
		$i++;
	} 	
	echo json_encode($responce);	
	mysqli_free_result($result);
   
}	

mysqli_close($mysql);	

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