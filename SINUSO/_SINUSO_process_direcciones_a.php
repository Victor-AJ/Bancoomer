<?
include("includes/funciones.php");  
require_once("includes/JSON.php");    
$json = new Services_JSON;	
//$jsona = new Services_JSON;	
conexion_db_a();

// Recibo variables
// ============================


$page 		= $_GET['page']; 
$limit 		= $_GET['rows']; 
$sidx 		= $_GET['sidx']; 
$sord 		= $_GET['sord']; 
$dispatch	= $_GET["dispatch"];
$id			= $_GET["id"];
$anio		= $_GET["anio"];
$indicador	= $_GET["indicador"]; 
$examp 		= $_GET["q"];
$searchOn 	= Strip($_GET["_search"]);


if(!$sidx) $sidx = 1;

$wh = "";
if($searchOn=="true") {		
	$dispatch="search";	
	//$searchstr = Strip($_GET["filters"]);
}

// Carga la informacion al grid
// ============================
if ($dispatch=="load") {
	
	$result = mysql_query(" SELECT COUNT(*) AS count FROM tbl_direccion a, tbl_usuario b, tbl_usuario c WHERE a.id_usuariomod = b.id_usuario AND a.id_usuarioalta = c.id_usuario ");
	
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$count = $row['count'];
	
	if( $count >0 ) {
		$total_pages = ceil($count/$limit);
	} else {
		$total_pages = 0;
	}
	
	if ($page > $total_pages) $page=$total_pages;
	$start = $limit*$page - $limit; // do not put $limit*($page - 1)
	
	$sql = "  SELECT  a.id_direccion, a.id_direccion, a.tx_nombre, a.tx_indicador, a.fh_mod, b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta " ; 
	$sql.= " 	FROM  tbl_direccion a, tbl_usuario b, tbl_usuario c " ; 
	$sql.= " 	WHERE a.id_usuariomod = b.id_usuario AND a.id_usuarioalta = c.id_usuario " ; 
	$sql.= " ORDER BY $sidx $sord " ;
		
	//echo "sql",	$sql;
	
	$result = mysql_query( $sql ) or die("Error al ejecutar la consulta.".mysql_error());
	
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i=0;
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
		$responce->rows[$i]['id']=$row[id_direccion];
		$responce->rows[$i]['cell']=array($row[id_direccion],$row[id_direccion],$row[tx_nombre],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
		$i++;
	} 	
	echo $json->encode($responce);
}

// INSERTA
// ============================
else if ($dispatch=="insert") {	
	
	$sql = " SELECT * FROM tbl_direccion WHERE tx_nombre = '$tx_nombre' " ; 		
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
		
		$sql = " INSERT INTO tbl_direccion SET " ;   
		$sql.= " tx_nombre		= '$tx_nombre', ";
		$sql.= " tx_indicador	= '$tx_indicador', ";
		$sql.= " fh_alta		= '$fh_alta', ";
		$sql.= " fh_mod 		= '$fh_mod', ";
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
	
	$sql = " UPDATE tbl_direccion SET " ; 
	$sql.= " tx_nombre		= '$tx_nombre', ";
	$sql.= " tx_indicador	= '$tx_indicador', ";	
	$sql.= " fh_mod 		= '$fh_mod', ";	
	$sql.= " id_usuariomod	= '$id_usuariomod' "; 
	$sql.= " WHERE id_direccion= $id ";
		   
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
	
	$sql = " DELETE FROM tbl_direccion ";
	$sql.= "  WHERE id_direccion = $id ";
		
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
		
		$result = mysql_query(" SELECT COUNT(*) AS count FROM tbl_direccion a, usuario b WHERE a.idusuario_alta = b.idusuario ".$wh);
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

mysql_close();

// Funciones
// ============================

function Strip($value)
{
	//echo "Entre",$value;
	//echo "<br>";
	
	if(get_magic_quotes_gpc() != 0)
  	{
		//echo "Entre",$value;
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
	
	//echo "Entre",$value;
	//echo "<br>";
	
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

function constructWhere($s){
	
	//echo "Entre", $s;

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
		//echo "S", $s;
	    //$jsona = json_decode($s,true);
        //$json -> json_decode($s);
        //$json -> decode($s);
		//$json -> decode($s);
		//echo $json->encode($data); 
		//echo $json->encode($data); 		
		//$obj = $json->decode($s); 
		
		//echo "S", $s;
		
		$obj = $json->decode($s);			
		
		//$json -> decode($s);
		//$json -> decode($s);
		
		//echo "Entre";
		//echo "js", $obj;
        if(is_array($obj)){
			
			$gopr = $obj['groupOp'];
			$rules = $obj['rules'];
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
	
	//echo "Entre", $qwery;
    return $qwery;
}

function ToSql ($field, $oper, $val) {
	// we need here more advanced checking using the type of the field - i.e. integer, string, float
	switch ($field) {
		case 'idanio':
			return intval($val);
			break;
		case 'anio':
		case 'indicador':
		case 'fecha_alta':
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

// ============================

?>