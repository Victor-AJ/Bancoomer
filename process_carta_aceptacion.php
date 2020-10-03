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
	$id_login 			= $_SESSION['sess_iduser'];	
	$campo 				= $_GET['campo'];	
	$q 					= $_GET['q'];	
	$tx_empresa			= $_GET['cap_empresa'];			
	$tx_nombre_glg		= $_GET['cap_nombre_glg'];			
	$tx_codigo_glg		= $_GET['cap_codigo_glg'];
	$tx_linea_gasto		= $_GET['cap_linea_gasto'];
	$tx_codigo_cuenta	= $_GET['cap_codigo_cuenta'];
	$tx_familia			= $_GET['cap_familia'];
	$tx_orden_compra	= $_GET['cap_orden_compra'];
	$tx_requision_numero= $_GET['cao_requision_numero'];
	$tx_numero_partida	= $_GET['cap_numero_partida'];
	$tx_pais			= $_GET['cap_pais'];
	

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
		$sql.= "   FROM tbl_carta_aceptacion a, tbl_usuario b, tbl_usuario c ";
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
		
		$sql = " SELECT id_carta_aceptacion, id_carta_aceptacion, tx_empresa, tx_nombre_glg, tx_codigo_glg, tx_linea_gasto, tx_codigo_cuenta, tx_familia, tx_orden_compra, tx_requision_numero, tx_numero_partida, tx_pais,  a.tx_indicador, a.fh_mod, b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta ";
		$sql.= "   FROM tbl_carta_aceptacion a, tbl_usuario b, tbl_usuario c ";
		$sql.= "  WHERE a.id_usuariomod 	= b.id_usuario ";
		$sql.= "    AND a.id_usuarioalta 	= c.id_usuario  ";
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= " 	LIMIT $start, $limit " ;		
		
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql,$sql); 
		
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
			$responce->rows[$i]['id']=$row[id_carta_aceptacion];
			$responce->rows[$i]['cell']=array($row[id_carta_aceptacion],$row[id_carta_aceptacion],$row[tx_empresa],$row[tx_nombre_glg],$row[tx_codigo_glg],$row[tx_linea_gasto],$row[tx_codigo_cuenta],$row[tx_familia],$row[tx_orden_compra],$row[tx_requision_numero],$row[tx_numero_partida],$row[tx_pais],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		
		//<BITACORA>
	 	$myBitacora = new Bitacora();
		$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_CARTA_ACEPTACION" , "$id_login" ,  "", ""  ,  "process_carta_aceptacion.php");
	    //<\BITACORA>
		
		
		echo json_encode($responce);	
		mysqli_free_result($result);
	}

	// INSERTA
	// ============================
	else if ($dispatch=="insert") {	
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_glg ";
		//$sql.= "  WHERE tx_tipo 	= '$tx_tipo' ";	
		//$sql.= "    AND tx_marca 	= '$tx_marca' ";				
		//$sql.= "    AND tx_modelo 	= '$tx_modelo' ";				
		
		//echo "sql", $sql;
		
		//$men = $tx_tipo." ".$tx_marca." ".$tx_modelo;
			
		//$result = mysqli_query($mysql, $sql);
		//$row = mysqli_fetch_row($result);
		//$count = $row[0];	
			
		//if ($count > 0)	{	
		//	$data = array("error" => true, "message" => "La configuraci&oacute;n telefon&iacute;ca $men que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
		//	echo json_encode($data);		
		//} else {  
								
			$fh_alta=date("Y-m-j, g:i");
			$id_usuarioalta=$id_login;		
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;	
				
			$sql = " INSERT INTO tbl_carta_aceptacion SET " ;  	
			$sql.= " tx_empresa			= '$tx_empresa', ";			
			$sql.= " tx_nombre_glg		= '$tx_nombre_glg', ";			
			$sql.= " tx_codigo_glg		= '$tx_codigo_glg', ";
			$sql.= " tx_linea_gasto		= '$tx_linea_gasto', ";
			$sql.= " tx_codigo_cuenta	= '$tx_codigo_cuenta', ";
			$sql.= " tx_familia			= '$tx_familia', ";
			$sql.= " tx_orden_compra	= '$tx_orden_compra', ";
			$sql.= " tx_requision_numero= '$tx_requision_numero', ";
			$sql.= " tx_numero_partida	= '$tx_numero_partida', ";
			$sql.= " tx_pais			= '$tx_pais', ";
			$sql.= " tx_indicador		= '$tx_indicador', ";
			$sql.= " fh_alta			= '$fh_alta', ";
			$sql.= " id_usuarioalta		= '$id_usuarioalta', ";
			$sql.= " fh_mod 			= '$fh_mod', ";
			$sql.= " id_usuariomod		= '$id_usuariomod' "; 
					
			//echo "aaa", $sql;  
			
			if (mysqli_query($mysql, $sql))
			{		
			
			//<BITACORA>
		
			
			
			$valBita= "tx_empresa=$tx_empresa ";			
			$valBita.= "tx_nombre_glg=$tx_nombre_glg ";			
			$valBita.= "tx_codigo_glg=$tx_codigo_glg ";
			$valBita.= "tx_linea_gasto=$tx_linea_gasto ";
			$valBita.= "tx_codigo_cuenta=$tx_codigo_cuenta ";
			$valBita.= "tx_familia=$tx_familia ";
			$valBita.= "tx_orden_compra=$tx_orden_compra ";
			$valBita.= "tx_requision_numero=$tx_requision_numero ";
			$valBita.= "tx_numero_partida=$tx_numero_partida ";
			$valBita.= "tx_pais=$tx_pais ";
			$valBita.= "tx_indicador=$tx_indicador ";
			$valBita.= "fh_alta=$fh_alta ";
			$valBita.= "id_usuarioalta=$id_usuarioalta ";
			$valBita.= "fh_mod=$fh_mod ";
			$valBita.= "id_usuariomod=$id_usuariomod "; 
			
			
			
				$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL_CARTA_ACEPTACION" , "$id_login" ,  $valBita, ""  ,  "process_carta_aceptacion.php");
				//<\BITACORA>
				
				
				$data = array("error" => false, "message" => "El registro se INSERTO correctamente" );				
				echo json_encode($data);
			} else {  		
				$data = array("error" => true, "message" => "ERROR al INSERTAR el registro !</br></br>Por favor verifique ..." );				
				echo json_encode($data);
			} 		
		//}			
		//mysqli_free_result($result);		
	} 

	// ACTUALIZA
	// ============================
	else if ($dispatch=="save") {	
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_glg "; 
		//$sql.= "  WHERE id_carta_aceptacion <> $id "; 	
		//$sql.= "  	AND tx_tipo 	= '$tx_tipo' ";	
		//$sql.= "    AND tx_marca 	= '$tx_marca' ";				
		//$sql.= "    AND tx_modelo 	= '$tx_modelo' ";				
		
		//echo "sql",	$sql;
		//$men = $tx_tipo." ".$tx_marca." ".$tx_modelo;
				
		//$result = mysqli_query($mysql, $sql);
		//$row = mysqli_fetch_row($result);
		//$count = $row[0];	
		
		//if ($count > 0)	{	
			
		//	$data = array("error" => true, "message" => "La configuraci&oacute;n telefon&iacute;ca $men ya existe</br></br>Por favor verifique ... " );				
		//	echo json_encode($data);
			
		//} else {  			
			
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;
			
			$sql = " UPDATE tbl_carta_aceptacion SET " ; 
			$sql.= " 	tx_empresa			= '$tx_empresa', ";			
			$sql.= " 	tx_nombre_glg		= '$tx_nombre_glg', ";			
			$sql.= " 	tx_codigo_glg		= '$tx_codigo_glg', ";
			$sql.= " 	tx_linea_gasto		= '$tx_linea_gasto', ";
			$sql.= " 	tx_codigo_cuenta	= '$tx_codigo_cuenta', ";
			$sql.= " 	tx_familia			= '$tx_familia', ";
			$sql.= " 	tx_orden_compra		= '$tx_orden_compra', ";
			$sql.= " 	tx_requision_numero	= '$tx_requision_numero', ";
			$sql.= " 	tx_numero_partida	= '$tx_numero_partida', ";
			$sql.= " 	tx_pais				= '$tx_pais', ";
			$sql.= " 	tx_indicador		= '$tx_indicador', ";
			$sql.= " 	fh_mod 				= '$fh_mod', ";
			$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
			$sql.= " WHERE id_carta_aceptacion 	= $id ";
					   
			//echo "aaa", $sql;      
	
				$myBitacora = new Bitacora();
				$valores=$myBitacora->obtenvalores ($mysql, "TBL_CARTA_ACEPTACION" , $id);
	
	
				  
			if (mysqli_query($mysql, $sql))
			{
			
				$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_CARTA_ACEPTACION" , "$id_login" ,  $valores, "$id"  ,  "process_carta_aceptacion.php");
		
		
				$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente" );							
				echo json_encode($data);
			} else {  
				$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Por favor verifique ..." );				
				echo json_encode($data);
			}				
		//}	
		//mysqli_free_result($result);
	} 
	
	// FIND
	// ============================
	
	else if ($dispatch=='find') {
	
	}
	
	// BORRA
	// ============================
	else if ($dispatch=='delete') {		
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_empleado_telefonia ";
		//$sql.= "  WHERE id_carta_aceptacion = $id "; 
		
		//echo "aaa", $sql;
		
		//$result = mysqli_query($mysql, $sql);
		//$row = mysqli_fetch_row($result);
		//$count = $row[0];	
		
		//if ($count > 0)	{			
		
		//	while($row = mysqli_fetch_array($result))
		//	$data = array("error" => true, "message" => "NO ES POSIBLE ELIMINARLO... Ya que la telefonia seleccionada est&aacute; asignada a los Empleados ... " );				
		//	echo json_encode($data);			
			
		//} else {  
		
			$sql = " UPDATE  tbl_carta_aceptacion SET TX_INDICADOR='0' ";
			$sql.= "  WHERE id_carta_aceptacion = $id ";
				
			//echo "aaa", $sql; 
				
			if (mysqli_query($mysql, $sql)) 
			{
			
				//<BITACORA>
			 	$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL_CARTA_ACEPTACION" , "$id_login" ,  "", "$id "  ,  "process_carta_aceptacion.php");
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
		
		$sql = " SELECT id_carta_aceptacion, id_carta_aceptacion, tx_empresa, tx_nombre_glg, tx_codigo_glg, tx_linea_gasto, tx_codigo_cuenta, tx_familia, tx_orden_compra, tx_requision_numero, tx_numero_partida, tx_pais,  a.tx_indicador, a.fh_mod, b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta ";
		$sql.= "   FROM tbl_carta_aceptacion a, tbl_usuario b, tbl_usuario c ";
		$sql.= "  WHERE a.id_usuariomod 	= b.id_usuario ";
		$sql.= "    AND a.id_usuarioalta 	= c.id_usuario  ".$wh ;
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= " 	LIMIT $start, $limit " ;	
			
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
		
		$sql = "   SELECT id_carta_aceptacion, id_carta_aceptacion, tx_glg, tx_cuenta, tx_tipo_gasto, a.tx_indicador, a.fh_mod, c.tx_nombre AS usuario_mod, a.fh_alta, d.tx_nombre AS usuario_alta ";
		$sql.= "     FROM tbl_glg a, tbl_tipo_gasto b, tbl_usuario c, tbl_usuario d ";
		$sql.= "    WHERE a.id_tipo_gasto 	= b.id_tipo_gasto ";
		$sql.= "	  AND a.id_usuariomod 	= c.id_usuario ";
		$sql.= "      AND a.id_usuarioalta 	= d.id_usuario ".$wh ;		
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= " 	LIMIT $start, $limit " ;			
			
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql,$sql); 
		
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;
			while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {			
			$responce->rows[$i]['id']=$row[id_carta_aceptacion];
			$responce->rows[$i]['cell']=array($row[id_carta_aceptacion],$row[id_carta_aceptacion],$row[tx_empresa],$row[tx_nombre_glg],$row[tx_codigo_glg],$row[tx_linea_gasto],$row[tx_codigo_cuenta],$row[tx_familia],$row[tx_orden_compra],$row[tx_requision_numero],$row[tx_numero_partida],$row[tx_pais],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);

			$i++;
		} 	

		//<BITACORA>
	 	$myBitacora = new Bitacora();
	 	 $whr =str_ireplace("'", " " , $wh); 
		$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_CARTA_ACEPTACION" , "$id_login" ,  $whr, ""  ,  "process_carta_aceptacion.php");
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