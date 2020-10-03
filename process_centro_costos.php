<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
session_start();
session_start();
if 	(isset($_SESSION['sess_user']))
{
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
$mysql=conexion_db();
if 	(isset($_SESSION["sess_user"])) 
	$id_login = $_SESSION['sess_iduser'];

	
	# Recibo variables
	# ============================
	$page 				= $_GET['page']; 
	$limit 				= $_GET['rows']; 
	$start				= $_GET['start'];
	$sidx 				= $_GET['sidx']; 
	$sord 				= $_GET['sord']; 
	$dispatch			= $_GET['dispatch'];
	$id					= $_GET['id'];
	$examp 				= $_GET['q'];
	$searchOn 			= Strip($_GET['_search']);
	$tx_centro_costos	= $_GET['va_centro_costos'];  
	$tx_direccion		= $_GET['selDireccion']; 
	$tx_subdireccion	= $_GET['tx_subdireccion']; 
	$tx_departamento	= $_GET['tx_departamento']; 
	$tx_cr_estado		= $_GET['selCrEstado']; 
	$tx_indicador		= $_GET['tx_indicador']; 
	$id_login 			= $_SESSION['sess_iduser'];	
	
	if ($dispatch=="insert" || $dispatch=="save" ) {
		$tx_subdireccion= $_GET['selSubdireccion']; 
		$tx_departamento= $_GET['selDepartamento']; 
	}
	
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
	
	//echo "TX CR",$tx_centro_costos;
	//echo "<br>";
	
	// Carga la informacion al grid
	// ============================
	if ($dispatch=="load") {		
		
		$sql = " SELECT COUNT(*) AS count ";
		$sql.= "   FROM tbl_centro_costos a, tbl_departamento b, tbl_subdireccion c, tbl_direccion d ";
		$sql.= "  WHERE a.id_departamento 	= b.id_departamento ";
		$sql.= "    AND a.id_subdireccion 	= c.id_subdireccion ";
		$sql.= "    AND c.id_direccion 		= d.id_direccion ";
		
		//echo "sql",	$sql;
	
		$result = mysqli_query($mysql, $sql);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$count = $row['count'];
		
		if( $count>0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; 
		
		$sql = "   SELECT id_centro_costos, id_centro_costos, tx_centro_costos, d.tx_nombre as tx_direccion, tx_subdireccion, tx_departamento, tx_cr_estado, a.tx_indicador, a.fh_mod, e.tx_nombre AS usuario_mod, a.fh_alta, f.tx_nombre AS usuario_alta  ";
		$sql.= "     FROM tbl_centro_costos a, tbl_departamento b, tbl_subdireccion c, tbl_direccion d, tbl_usuario e, tbl_usuario f, tbl_cr_estado g ";
		$sql.= "    WHERE a.id_departamento = b.id_departamento ";
		$sql.= "      AND a.id_subdireccion = c.id_subdireccion ";
		$sql.= "      AND c.id_direccion 	= d.id_direccion ";
		$sql.= "      AND a.id_usuariomod 	= e.id_usuario ";
		$sql.= "      AND a.id_usuarioalta 	= f.id_usuario ";
		$sql.= "      AND a.id_cr_estado 	= g.id_cr_estado ";
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= "    LIMIT $start, $limit " ;
				
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql,$sql); 
		
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
			$responce->rows[$i]['id']=$row[id_centro_costos];
			$responce->rows[$i]['cell']=array($row[id_centro_costos],$row[id_centro_costos],$row[tx_centro_costos],$row[tx_direccion],$row[tx_subdireccion],$row[tx_departamento],$row[tx_cr_estado],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_CENTRO_COSTOS" , "$id_login" ,  "", ""  ,  "process_centro_costos.php");
	 //<\BITACORA>
	 
	 
	 
		echo json_encode($responce);	
		mysqli_free_result($result);
	}
	
	// INSERTA
	// ============================
	else if ($dispatch=="insert") {		
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_centro_costos ";
		$sql.= "  WHERE tx_centro_costos = '$tx_centro_costos' "; 
		
		//echo "sql", $sql;
		
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
		
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "El Centro de Costos $tx_centro_costos ya existe !</br></br> Por favor vefique ..." );					
			echo json_encode($data);		
		} else {
		
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_centro_costos ";
			$sql.= "  WHERE id_departamento	= '$tx_departamento' ";
			$sql.= " 	AND id_subdireccion	= '$tx_subdireccion' ";
			$sql.= " 	AND id_direccion	= '$tx_direccion' ";
	
			//echo "sql", $sql;
		
			$result = mysqli_query($mysql, $sql);
			$row = mysqli_fetch_row($result);
			$count = $row[0];	
		
			if ($count > 0)	{	
				$data = array("error" => true, "message" => "La configuraci&oacute;n del centro de costos ya existe !</br></br> Por favor vefique ..." );					
				echo json_encode($data);		
			} else {
			
				$tx_indicador	= "1";  						
				$fh_alta		= date("Y-m-j, g:i");
				$id_usuarioalta	= $id_login;		
				$fh_mod			= date("Y-m-j, g:i");
				$id_usuariomod	= $id_login;	
				
				$sql = " INSERT INTO tbl_centro_costos SET " ;   
				$sql.= " 	id_departamento		= '$tx_departamento', ";
				$sql.= " 	id_subdireccion		= '$tx_subdireccion', ";
				$sql.= " 	id_direccion		= '$tx_direccion', ";
				$sql.= " 	id_cr_estado		= '$tx_cr_estado', ";		
				$sql.= " 	tx_centro_costos	= '$tx_centro_costos', ";		
				$sql.= " 	tx_indicador		= '$tx_indicador', ";
				$sql.= " 	fh_alta				= '$fh_alta', ";
				$sql.= " 	fh_mod 				= '$fh_mod', ";
				$sql.= " 	id_usuarioalta		= '$id_usuarioalta', ";
				$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
						
				//echo "aaa", $sql;  
				
				if (mysqli_query($mysql, $sql))
				{		
				
				
			//<BITACORA>

				$valBita= "id_departamento=$tx_departamento ";
				$valBita.= "id_subdireccion=$tx_subdireccion ";
				$valBita.= "id_direccion=$tx_direccion ";
				$valBita.= "id_cr_estado=$tx_cr_estado ";		
				$valBita.= "tx_centro_costos=$tx_centro_costos ";		
				$valBita.= "tx_indicador=$tx_indicador ";
				$valBita.= "fh_alta=$fh_alta ";
				$valBita.= "fh_mod=$fh_mod ";
				$valBita.= "id_usuarioalta=$id_usuarioalta ";
				$valBita.= "id_usuariomod=$id_usuariomod ";
				
			$myBitacora = new Bitacora();
			$myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL_CENTRO_COSTOS" , "$id_login" ,  $valBita, ""  ,  "process_centro_costos.php");
			//<\BITACORA>
			
			
					$data = array("error" => false, "message" => "El registro se INSERTO correctamente" );				
					echo json_encode($data);
				} else {  		
					$data = array("error" => true, "message" => "ERROR al INSERTAR el registro !</br></br>Por favor verifique ..." );				
					echo json_encode($data);
				} 	
			}	
		}	
		//mysqli_free_result($result);		
	} 
	
	// ACTUALIZA
	// ============================
	else if ($dispatch=="save") {	
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_centro_costos ";
		$sql.= "  WHERE id_centro_costos <> $id "; 
		$sql.= "    AND tx_centro_costos = '$tx_centro_costos' "; 
	
		//echo "sql", $sql;
		
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		
		$registros=count($TheInformeDireccion);	
		$count = $row[0];	
		
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "El Centro de Costos $tx_centro_costos ya existe !</br></br> Por favor vefique ..." );					
			echo json_encode($data);		
		} else {
		
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_centro_costos ";
			$sql.= "  WHERE id_centro_costos 	<> $id ";
			$sql.= "    AND id_departamento		= '$tx_departamento' ";
			$sql.= " 	AND id_subdireccion		= '$tx_subdireccion' ";
			$sql.= " 	AND id_direccion		= '$tx_direccion' ";
			$sql.= " 	AND tx_centro_costos	= '$tx_centro_costos' ";
				
			//echo "sql", $sql;
		
			$result = mysqli_query($mysql, $sql);
			$row = mysqli_fetch_row($result);
			$count = $row[0];	
		
			if ($count > 0)	{	
				$data = array("error" => true, "message" => "La configuraci&oacute;n del centro de costos ya existe !</br></br> Por favor vefique ..." );					
				echo json_encode($data);		
			} else {
			
				$tx_indicador 	= "1";
				$fh_mod			= date("Y-m-j, g:i");
				$id_usuariomod	= $id_login;		
				//$id_usuariomod	= "1";		
							
				$sql = " UPDATE tbl_centro_costos SET " ; 
				$sql.= " 	id_departamento		= '$tx_departamento', ";
				$sql.= " 	id_subdireccion		= '$tx_subdireccion', ";
				$sql.= " 	id_direccion		= '$tx_direccion', ";
				$sql.= " 	id_cr_estado		= '$tx_cr_estado', ";		
				$sql.= " 	tx_centro_costos	= '$tx_centro_costos', ";		
				$sql.= " 	tx_indicador		= '$tx_indicador', ";
				$sql.= " 	fh_mod 				= '$fh_mod', ";
				$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
				$sql.= " WHERE id_centro_costos	= $id ";
					   
				//echo "aaa", $sql;      
				
				$myBitacora = new Bitacora();
				$valores=$myBitacora->obtenvalores ($mysql, "TBL_CENTRO_COSTOS" , $id);
	
				  
				if (mysqli_query($mysql, $sql))
				{
	
					$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_CENTRO_COSTOS" , "$id_login" ,  $valores, "$id"  ,  "process_centro_costos.php");
	
					$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente" );							
					echo json_encode($data);
				} else {  
					$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Por favor verifique ..." );				
					echo json_encode($data);
				}	
			}	
		}	
	} 
		
	// BORRA
	// ============================
	else if ($dispatch=='delete') {		
		
		$sql = " update  tbl_centro_costos  set tx_indicador='0' ";
		$sql.= "  WHERE id_centro_costos = $id ";
			
		//echo "aaa", $sql; 
			
		if (mysqli_query($mysql, $sql)) 
		{
		
			//<BITACORA>
			 $myBitacora = new Bitacora();
			 $myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL_CENTRO_COSTOS" , "$id_login" ,  "", "$id"  ,  "process_centro_costos.php");
			 //<\BITACORA>
		
			$data = array("error" => false, "message" => "Registro dado de BAJA correctamente" );				
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
		$sql.= "   FROM tbl_centro_costos a, tbl_departamento b, tbl_subdireccion c, tbl_direccion d ";
		$sql.= "  WHERE a.id_departamento 	= b.id_departamento ";
		$sql.= "    AND a.id_subdireccion 	= c.id_subdireccion ";
		$sql.= "    AND c.id_direccion 		= d.id_direccion ".$wh ;
		
		
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
		
		$sql = "   SELECT id_centro_costos, id_centro_costos, tx_centro_costos, d.tx_nombre as tx_direccion, tx_subdireccion, tx_departamento, tx_cr_estado, a.tx_indicador, a.fh_mod, e.tx_nombre AS usuario_mod, a.fh_alta, f.tx_nombre AS usuario_alta  ";
		$sql.= "     FROM tbl_centro_costos a, tbl_departamento b, tbl_subdireccion c, tbl_direccion d, tbl_usuario e, tbl_usuario f, tbl_cr_estado g ";
		$sql.= "    WHERE a.id_departamento = b.id_departamento ";
		$sql.= "      AND a.id_subdireccion = c.id_subdireccion ";
		$sql.= "      AND c.id_direccion 	= d.id_direccion ";
		$sql.= "      AND a.id_usuariomod 	= e.id_usuario ";
		$sql.= "      AND a.id_usuarioalta 	= f.id_usuario ";
		$sql.= "      AND a.id_cr_estado 	= g.id_cr_estado ".$wh ; 
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= "    LIMIT $start, $limit " ;		
		
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql,$sql); 
		
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
			$responce->rows[$i]['id']=$row[id_centro_costos];
			$responce->rows[$i]['cell']=array($row[id_centro_costos],$row[id_centro_costos],$row[tx_centro_costos],$row[tx_direccion],$row[tx_subdireccion],$row[tx_departamento],$row[tx_cr_estado],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $whr =str_ireplace("'", " " , $wh); 
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_CENTRO_COSTOS" , "$id_login" ,  $whr, ""  ,  "process_centro_costos.php");
	 //<\BITACORA>
	 
	 
	 
		echo json_encode($responce);	
		mysqli_free_result($result);
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