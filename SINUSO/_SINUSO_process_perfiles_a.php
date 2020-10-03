<?
include("includes/funciones.php");  
$mysql=conexion_db();

// Recibo variables
// ============================
$page 			= $_GET["page"]; 
$limit 			= $_GET["rows"]; 
$start			= $_GET["start"];
$sidx 			= $_GET["sidx"]; 
$sord 			= $_GET["sord"]; 
$dispatch		= $_GET["dispatch"];
$id				= $_GET["id"];
$examp 			= $_GET["q"];
$searchOn 		= Strip($_GET["_search"]);
$tx_nombre		= $_GET["tx_nombre"];
$tx_indicador	= $_GET["tx_indicador"];

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
	
	$sql = "  SELECT  a.id_perfil, a.id_perfil, a.tx_nombre, a.tx_indicador, a.fh_mod, b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta " ; 
	$sql.= " 	FROM  tbl_perfil a, tbl_usuario b, tbl_usuario c " ; 
	$sql.= " 	WHERE a.id_usuariomod = b.id_usuario AND a.id_usuarioalta = c.id_usuario " ; 
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
	
	$sql = " SELECT * FROM tbl_perfil WHERE tx_nombre = '$tx_nombre'" ; 		
	//echo "sql",	$sql;
	
	$busqueda = @mysql_query($sql);
	$NumRows = @mysql_num_rows($busqueda);
	
	if ($NumRows > 0)	{
	
		$data = array("error" => true, "message" => "El Perfil que desa dar de alta ya existe !</br></br> Por favor vefique ..." );				
		echo $json->encode($data); 
		
	} else {  
	
		$fh_alta=date("Y-m-j, g:i");
		$id_usuarioalta="1";		
		$fh_mod=date("Y-m-j, g:i");
		$id_usuariomod="1";		
		
		$sql = " INSERT INTO tbl_perfil SET " ;   
		$sql.= " tx_nombre		= '$tx_nombre', ";
		$sql.= " tx_indicador	= '$tx_indicador', ";
		$sql.= " fh_alta		= '$fh_alta', ";
		$sql.= " fh_mod 		= '$f_mod', ";
		$sql.= " id_usuarioalta	= '$id_usuarioalta', ";
		$sql.= " id_usuariomod	= '$id_usuariomod' "; 
				
		//echo "aaa", $sql;  
			
		if (@mysql_query($sql)) 
		{		
			$data = array("error" => false, "message" => "El registro se INSERTO correctamente" );				
			echo $json->encode($data); 
			
		} else {  		
			$data = array("error" => true, "message" => "ERROR al INSERTAR el registro. Verifique con el Administrador del Sistema" );				
			echo $json->encode($data); 			
		} 		
	}			
} 

// ACTUALIZA
// ============================
else if ($dispatch=="save") {	
	
	$fh_mod=date("Y-m-j, g:i");
	$id_usuariomod="1";
	
	$sql = " UPDATE tbl_perfil SET " ; 
	$sql.= " tx_nombre		= '$tx_nombre', ";
	$sql.= " tx_indicador	= '$tx_indicador', ";	
	$sql.= " fh_mod 		= '$fh_mod', ";	
	$sql.= " id_usuariomod	= '$id_usuariomod' "; 
	$sql.= " WHERE id_perfil= $id ";
		   
	//echo "aaa", $sql;      
	  
	if (@mysql_query($sql)) 
	{
		$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente." );				
		echo $json->encode($data); 
	} else {  
		$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro.</br></br>Por favor verifique con el Administrador del Sistema. " );				
		echo $json->encode($data); 
	}		
} 
	
// BORRA
// ============================
else if ($dispatch=='delete') {		
	
	$sql = " DELETE FROM tbl_perfil ";
	$sql.= "  WHERE id_perfil = $id ";
		
	//echo "aaa", $sql; 
		
	if (@mysql_query($sql)) 
	{
		$data = array("error" => false, "message" => "El registro se BORRO correctamente." );				
		echo $json->encode($data); 
	} else {  
		$data = array("error" => true, "message" => "ERROR al BORRAR el registro.</br></br>Por favor verifique con el Administrador del Sistema.");				
		echo $json->encode($data); 
	}										
}	

	
// BUSQUEDA
// ============================
else if ($dispatch=="search") {	
	
	//$aa = $_GET["filters"];
	//echo "Entre", aa;
	//$wh= constructWhere($searchstr);	
	//$searchstr = $_GET['filters'];
	//echo "a", $searchstr;	
	
	//echo "wh",$wh;
	
	//switch ($examp) {
    //case 1:
		//$result = mysql_query("SELECT COUNT(*) AS count FROM invheader a, clients b WHERE a.client_id=b.client_id".$wh);
		
		//$result = mysql_query(" SELECT COUNT(*) AS count FROM anio a, usuario b WHERE a.idusuario_alta = b.idusuario ".$wh);
		
		
		$searchstr = Strip($_GET['filters']);	
		$wh= constructWhere($searchstr);	
		
		$result = mysql_query(" SELECT COUNT(*) AS count FROM tbl_perfile a, usuario b WHERE a.idusuario_alta = b.idusuario ".$wh);
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$count = $row['count'];
		
		//echo "Entre", $count;

		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
        if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
        if ($start<0) $start = 0;
		
        //$SQL = "SELECT a.id, a.invdate, b.name, a.amount,a.tax,a.total,a.note FROM invheader a, clients b WHERE a.client_id=b.client_id".$wh." ORDER BY ".$sidx." ".$sord. " LIMIT ".$start." , ".$limit;
		
		$sql = "  SELECT  a.idanio, a.idanio, a.anio, a.indicador, a.fecha_mod, b.nombre AS usuario_mod, a.fecha_alta, c.nombre AS usuario_alta " ; 
		$sql.= " 	FROM  anio a, usuario b, usuario c " ; 
		$sql.= " 	WHERE a.idusuario_mod = b.idusuario AND a.idusuario_alta = c.idusuario ";
		//.$wh ; 
		//$sql.= " ORDER BY $sidx $sord " ;
		
		//echo "sql", $sql;
		
		$result = mysql_query( $sql ) or die("Error al ejecutar la consulta.".mysql_error());
	
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$responce->rows[$i]['id']=$row[idanio];
			$responce->rows[$i]['cell']=array($row[idanio],$row[idanio],$row[anio],$row[indicador],$row[fecha_mod],$row[usuario_mod],$row[fecha_alta],$row[usuario_alta]);
		$i++;
		} 	
		//echo $json->encode($responce);	
					
		//$result = mysql_query( $SQL ) or die("Could not execute query.".mysql_error());
        //$responce->page = $page;
        //$responce->total = $total_pages;
        //$responce->records = $count;
        //$i=0;
		//while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
		//	$responce->rows[$i]['id']=$row[id];
        //   $responce->rows[$i]['cell']=array($row[id],$row[invdate],$row[name],$row[amount],$row[tax],$row[total],$row[note]);
        //    $i++;
		//} 
		//echo $json->encode($responce); // coment if php 5
        //echo json_encode($responce);
           
       //break;
    //case 3:
//}
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