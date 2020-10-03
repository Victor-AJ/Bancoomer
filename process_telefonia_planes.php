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
	$searchOn 			= Strip($_GET["_search"]);
	$tx_proveedor		= $_GET['cap_proveedor']; 	
	$tx_plan 			= $_GET['cap_plan']; 		
	$fl_precio			= $_GET['cap_precio']; 	
	$tx_descripcion		= $_GET['cap_descripcion']; 	
	$id_login 			= $_SESSION['sess_iduser'];	
	$campo 				= $_GET['campo'];	
	$q 					= $_GET['q'];	
	
	$fl_precio = ereg_replace( (","), "", $fl_precio ); 

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
		$sql.= "   FROM tbl_telefonia_plan a, tbl_usuario b, tbl_usuario c ";
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
		
		$sql = " SELECT id_telefonia_plan, id_telefonia_plan, tx_proveedor, tx_plan, fl_precio_mxn, tx_descripcion, a.tx_indicador, b.fh_mod, b.tx_nombre AS usuario_mod, c.fh_alta, c.tx_nombre AS usuario_alta ";
		$sql.= "   FROM tbl_telefonia_plan a, tbl_usuario b, tbl_usuario c ";
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
			$responce->rows[$i]['id']=$row[id_telefonia_plan];
			$responce->rows[$i]['cell']=array($row[id_telefonia_plan],$row[id_telefonia_plan],$row[tx_proveedor],$row[tx_plan],$row[fl_precio_mxn],$row[tx_descripcion],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		
		//<BITACORA>
	 	$myBitacora = new Bitacora();
		$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_TELEFONIA_PLAN" , "$id_login" ,  "", ""  ,  "process_telefonia_planes.php");
	    //<\BITACORA>
		
		
		echo json_encode($responce);	
		mysqli_free_result($result);
	}

	// INSERTA
	// ============================
	else if ($dispatch=="insert") {	
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_telefonia_plan ";
		$sql.= "  WHERE tx_proveedor= '$tx_proveedor' ";	
		$sql.= "    AND tx_plan 	= '$tx_plan' ";				
		
		//echo "sql", $sql;
		
		$plan = $tx_proveedor." ".$tx_plan;
			
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
			
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "El plan $plan que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
			echo json_encode($data);		
		} else {  
								
			$fh_alta=date("Y-m-j, g:i");
			$id_usuarioalta=$id_login;		
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;	
				
			$sql = " INSERT INTO tbl_telefonia_plan SET " ;  			
			$sql.= " tx_proveedor	= '$tx_proveedor', ";
			$sql.= " tx_plan		= '$tx_plan', ";
			$sql.= " fl_precio_usd	= NULL, ";
			$sql.= " fl_precio_mxn	= '$fl_precio', ";
			$sql.= " tx_descripcion	= '$tx_descripcion', ";
			$sql.= " tx_indicador	= '$tx_indicador', ";
			$sql.= " fh_alta		= '$fh_alta', ";
			$sql.= " id_usuarioalta	= '$id_usuarioalta', ";
			$sql.= " fh_mod 		= '$fh_mod', ";
			$sql.= " id_usuariomod	= '$id_usuariomod' "; 
					
			//echo "aaa", $sql;  
			
			if (mysqli_query($mysql, $sql))
			{		
			
				//<BITACORA>
		
			
				$valBita= "tx_proveedor=$tx_proveedor ";
				$valBita.= "tx_plan=$tx_plan ";
				$valBita.= "fl_precio_usd=NULL ";
				$valBita.= "fl_precio_mxn=$fl_precio ";
				$valBita.= "tx_descripcion=$tx_descripcion ";
				$valBita.= "tx_indicador=$tx_indicador ";
				$valBita.= "fh_alta=$fh_alta ";
				$valBita.= "id_usuarioalta=$id_usuarioalta ";
				$valBita.= "fh_mod=$fh_mod ";
				$valBita.= "id_usuariomod=$id_usuariomod "; 
			
			
				$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL_TELEFONIA_PLAN" , "$id_login" ,  $valBita, ""  ,  "process_telefonia_planes.php");
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
		$sql.= "   FROM tbl_telefonia_plan "; 
		$sql.= "  WHERE id_telefonia_plan <> $id "; 	
		$sql.= "    AND tx_proveedor= '$tx_proveedor' ";	
		$sql.= "    AND tx_plan 	= '$tx_plan' ";					
		
		//echo "sql",	$sql;
		$plan = $tx_proveedor." ".$tx_plan;
				
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
		
		if ($count > 0)	{	
			
			$data = array("error" => true, "message" => "El plan $plan ya existe</br></br>Por favor verifique ... " );				
			echo json_encode($data);
			
		} else {  			
			
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;
			
			$sql = " UPDATE tbl_telefonia_plan SET " ; 
			$sql.= " 	tx_proveedor	= '$tx_proveedor', ";
			$sql.= " 	tx_plan			= '$tx_plan', ";
			$sql.= " 	fl_precio_usd	= NULL, ";
			$sql.= " 	fl_precio_mxn	= '$fl_precio', ";
			$sql.= " 	tx_descripcion	= '$tx_descripcion', ";
			$sql.= " 	tx_indicador	= '$tx_indicador', ";
			$sql.= " 	fh_mod 			= '$fh_mod', ";
			$sql.= " 	id_usuariomod	= '$id_usuariomod' "; 
			$sql.= " WHERE id_telefonia_plan = $id ";
					   
			//echo "aaa", $sql;      
	
			  	$myBitacora = new Bitacora();
				$valores=$myBitacora->obtenvalores ($mysql, "TBL_TELEFONIA_PLAN" , $id);
	
	
			if (mysqli_query($mysql, $sql))
			{
			
				$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_TELEFONIA_PLAN" , "$id_login" ,  $valores, "$id"  ,  "process_telefonia_planes.php");
		
		
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
	
		if ($campo=='proveedor') {
			
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_telefonia_plan ";
			$sql.= "  WHERE tx_proveedor like '$auto' ";
			$sql.= "  GROUP BY tx_proveedor ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_proveedor'=>$row["tx_proveedor"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_proveedor	=$TheCatalogo[$i]['tx_proveedor'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_proveedor;
			}	
			
		} else if ($campo=='plan'){			
			
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_telefonia_plan ";
			$sql.= "  WHERE tx_plan like '$auto' ";
			$sql.= "  GROUP BY tx_plan ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_plan'=>$row["tx_plan"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_plan	=$TheCatalogo[$i]['tx_plan'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_plan;
			}	
					
		} 		
	}
	
	// BORRA
	// ============================
	else if ($dispatch=='delete') {		
	
		if ($id==1) {
		
			$data = array("error" => true, "message" => "Este registro no es posible eliminarlo, es necesario para el modulo de Tekefon&iacute;a ..." );				
			echo json_encode($data);	
					
		} else {		
		
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_empleado_telefonia ";
			$sql.= "  WHERE id_telefonia_plan = $id "; 

			//echo "aaa", $sql;
			
			$result = mysqli_query($mysql, $sql);
			$row = mysqli_fetch_row($result);
			$count = $row[0];	
			
			if ($count > 0)	{					
				
				$data = array("error" => true, "message" => "NO ES POSIBLE ELIMINARLO... Ya que el plan telefonico seleccionado est&aacute; asignado a los Empleados ... " );				
				echo json_encode($data);				
							
			} else {  
			
				$sql = " UPDATE tbl_telefonia_plan SET TX_INDICADOR='0' ";
				$sql.= "  WHERE id_telefonia_plan = $id ";
					
				//echo "aaa", $sql; 
					
				if (mysqli_query($mysql, $sql)) 
				{
				
					//<BITACORA>
	 				$myBitacora = new Bitacora();
					$myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL_TELEFONIA_PLAN" , "$id_login" ,  "", "$id"  ,  "process_telefonia_planes.php");
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
	}	

	// BUSQUEDA
	// ============================
	else if ($dispatch=="search") {	
		
		$sql = " SELECT COUNT(*) AS count ";
		$sql.= "   FROM tbl_telefonia_plan a, tbl_usuario b, tbl_usuario c ";
		$sql.= "  WHERE a.id_usuariomod 	= b.id_usuario ";
		$sql.= "    AND a.id_usuarioalta 	= c.id_usuario ".$wh ;	
	
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
		
		$sql = " SELECT id_telefonia_plan, id_telefonia_plan, tx_proveedor, tx_plan, fl_precio_mxn, tx_descripcion, a.tx_indicador, b.fh_mod, b.tx_nombre AS usuario_mod, c.fh_alta, c.tx_nombre AS usuario_alta ";
		$sql.= "   FROM tbl_telefonia_plan a, tbl_usuario b, tbl_usuario c ";
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
			$responce->rows[$i]['id']=$row[id_telefonia_plan];
			$responce->rows[$i]['cell']=array($row[id_telefonia_plan],$row[id_telefonia_plan],$row[tx_proveedor],$row[tx_plan],$row[fl_precio_mxn],$row[tx_descripcion],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		
		//<BITACORA>
	 	$myBitacora = new Bitacora();
	 	 $whr =str_ireplace("'", " " , $wh); 
		$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_TELEFONIA_PLAN" , "$id_login" ,  $whr, ""  ,  "process_telefonia_planes.php");
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