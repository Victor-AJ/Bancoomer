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
	$page 			= $_GET['page']; 
	$limit 			= $_GET['rows']; 
	$start			= $_GET['start'];
	$sidx 			= $_GET['sidx']; 
	$sord 			= $_GET['sord']; 
	$dispatch		= $_GET["dispatch"];
	$id				= $_GET["id"];
	$examp 			= $_GET["q"];
	$searchOn 		= Strip($_GET["_search"]);
	$id_pais 		= $_GET['selPais']; 
	$id_estado 		= $_GET['selEstado']; 
	$tx_ubicacion 	= $_GET['va_ubicacion']; 
	$tx_indicador	= $_GET['tx_indicador']; 
	$id_login 		= $_SESSION['sess_iduser'];	

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
		$sql.= "   FROM tbl_ubicacion a, tbl_estado b, tbl_pais c, tbl_usuario d, tbl_usuario e ";
		$sql.= "  WHERE a.id_estado 		= b.id_estado ";
		$sql.= "    AND a.id_pais 			= c.id_pais ";		
		$sql.= "    AND a.id_usuariomod 	= d.id_usuario ";
		$sql.= "    AND b.id_usuarioalta 	= e.id_usuario ";
		
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
		$start = $limit*$page - $limit; 
		
		$sql = "   SELECT id_ubicacion, id_ubicacion, tx_pais, tx_estado, tx_ubicacion, a.tx_indicador, a.fh_mod, d.tx_nombre AS usuario_mod, a.fh_alta, e.tx_nombre AS usuario_alta " ; 
		$sql.= "   FROM tbl_ubicacion a, tbl_estado b, tbl_pais c, tbl_usuario d, tbl_usuario e ";
		$sql.= "  WHERE a.id_estado 		= b.id_estado ";
		$sql.= "    AND a.id_pais 			= c.id_pais ";		
		$sql.= "    AND a.id_usuariomod 	= d.id_usuario ";
		$sql.= "    AND a.id_usuarioalta 	= e.id_usuario ";
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= " 	LIMIT $start, $limit " ;		
		
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql,$sql); 
		
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
			$responce->rows[$i]['id']=$row[id_ubicacion];
			$responce->rows[$i]['cell']=array($row[id_ubicacion],$row[id_ubicacion],$row[tx_pais],$row[tx_estado],$row[tx_ubicacion],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		
		 //<BITACORA>
	 $myBitacora = new Bitacora();
		 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_UBICACION" , "$id_login" ,  "", ""  ,  "process_ubicaciones.php");
	 //<\BITACORA>
	 
	 
		echo json_encode($responce);	
		mysqli_free_result($result);
	}

	// INSERTA
	// ============================
	else if ($dispatch=="insert") {	
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_ubicacion ";
		$sql.= "  WHERE id_pais		 = '$id_pais' ";	
		$sql.= " 	AND id_estado	 = '$id_estado' ";
		$sql.= "    AND tx_ubicacion = '$tx_ubicacion' " ; 	
	
		//echo "sql", $sql;
		
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
		
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "La Ubicaci&oacute;n $tx_ubicacion que desa dar de alta ya existe !</br></br> Por favor vefique ..." );					
			echo json_encode($data);		
		} else {  						
			$fh_alta=date("Y-m-j, g:i");
			$id_usuarioalta=$id_login;		
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;	
			
			$sql = " INSERT INTO tbl_ubicacion SET " ;   			
			$sql.= " id_estado		= '$id_estado', ";
			$sql.= " id_pais		= '$id_pais', ";
			$sql.= " tx_ubicacion	= '$tx_ubicacion', ";
			$sql.= " tx_indicador	= '$tx_indicador', ";
			$sql.= " fh_alta		= '$fh_alta', ";
			$sql.= " id_usuarioalta	= '$id_usuarioalta', ";
			$sql.= " fh_mod 		= '$fh_mod', ";
			$sql.= " id_usuariomod	= '$id_usuariomod' "; 
					
			//echo "aaa", $sql;  
			
			if (mysqli_query($mysql, $sql))
			{		
			
				//<BITACORA>
				
				$valBita= "id_estado=$id_estado ";
				$valBita.= "id_pais=$id_pais ";
				$valBita.= "tx_ubicacion=$tx_ubicacion ";
				$valBita.= "tx_indicador=$tx_indicador ";
				$valBita.= "fh_alta=$fh_alta ";
				$valBita.= "id_usuarioalta=$id_usuarioalta ";
				$valBita.= "fh_mod=$fh_mod ";
				$valBita.= "id_usuariomod=$id_usuariomod "; 
			
				$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL_UBICACION" , "$id_login" ,  $valBita, ""  ,  "process_ubicaciones.php");
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
		$sql.= "   FROM tbl_ubicacion "; 
		$sql.= "  WHERE id_ubicacion <> $id "; 		
		$sql.= " 	AND id_pais		 = '$id_pais' ";	
		$sql.= " 	AND id_estado	 = '$id_estado' ";
		$sql.= "    AND tx_ubicacion = '$tx_ubicacion' " ; 	
		
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
		
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "La Ubicaci&oacute;n $tx_ubicacion ya existe!</br></br>Por favor verifique ... " );				
			echo json_encode($data);
		} else {  	
			
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;
		
			$sql = " UPDATE tbl_ubicacion SET " ; 
			$sql.= " 	id_pais			= '$id_pais', ";
			$sql.= " 	id_estado		= '$id_estado', ";
			$sql.= " 	tx_ubicacion	= '$tx_ubicacion', ";
			$sql.= " 	tx_indicador	= '$tx_indicador', ";
			$sql.= " 	fh_mod 			= '$fh_mod', ";
			$sql.= " 	id_usuariomod	= '$id_usuariomod' "; 
			$sql.= "  WHERE id_ubicacion= $id ";
				   
			//echo "aaa", $sql;      
	
			$myBitacora = new Bitacora();
			$valores=$myBitacora->obtenvalores ($mysql, "TBL_UBICACION" , $id);
	
	
			  
			if (mysqli_query($mysql, $sql))
			{
				$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_UBICACION" , "$id_login" ,  $valores, "$id"  ,  "process_ubicaciones.php");
	
				$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente" );							
				echo json_encode($data);
			} else {  
				$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Por favor verifique ..." );				
				echo json_encode($data);
			}		
		}	
		mysqli_free_result($result);
	} 
	
	// BORRA
	// ============================
	else if ($dispatch=='delete') {		
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_ubicacion a, tbl_estado b ";
		//$sql.= "  WHERE a.id_ubicacion = $id "; 
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
		//	$data = array("error" => true, "message" => "La ubicaci&oacute;n de esta relacionado al cat&aacute;logo de Ubicaciones, no es posible eliminarlo ... " );				
		//	echo json_encode($data);			
		//} else {  
		
			$sql = " UPDATE  tbl_ubicacion SET TX_INDICADOR='0'  ";
			$sql.= "  WHERE id_ubicacion = $id ";
				
			//echo "aaa", $sql; 
				
			if (mysqli_query($mysql, $sql)) 
			{
				 //<BITACORA>
	 			$myBitacora = new Bitacora();
				 $myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL_UBICACION" , "$id_login" ,  "", "$id"  ,  "process_ubicaciones.php");
				 //<\BITACORA>
	 
				$data = array("error" => false, "message" => "Registro dado de BAJA correctamente" );				
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
	
		$sql = " SELECT COUNT(*) AS count ";
		$sql.= "   FROM tbl_ubicacion a, tbl_usuario b, tbl_usuario c ";
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
		$sql.= "     FROM  tbl_ubicacion a, tbl_usuario b, tbl_usuario c " ; 
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
		 //<BITACORA>
	 $myBitacora = new Bitacora();
	 	 $whr =str_ireplace("'", " " , $wh); 
		 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_UBICACION" , "$id_login" ,  $whr, ""  ,  "process_ubicaciones.php");
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