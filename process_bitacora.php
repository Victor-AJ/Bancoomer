<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
session_start();
if 	(!isset($_SESSION['sess_user']))
{
	echo "Sessi&oacute;n Invalida";
}
else
{
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();
		$id_login = $_SESSION['sess_iduser'];
	// Recibo variables
	// ============================
	
	$page 				= $_GET['page']; 
	$limit 				= $_GET['rows'];  
	$start				= $_GET['start'];
	$sidx 				= $_GET['sidx']; 
	$sord 				= $_GET['sord']; 
	$dispatch			= $_GET["dispatch"];
	
	//$tx_catalogo		= $_GET["tx_catalogo"];
	//$tx_clave 		= $_GET['tx_clave'];
	//$tx_valor 		= $_GET['tx_valor'];
	///$tx_valor_complementario = $_GET['tx_valor_complementario'];
	//$tx_observaciones = $_GET['tx_observaciones'];
	//$tx_indicador 		= $_GET['indicador'];
	
	

		
	$id				= $_GET["id"];
	$examp 			= $_GET["q"];


$searchOn 		 =  false;

	//$tx_indicador	= $_GET['tx_indicador']; 



if(!$sidx) 
	$sidx = 1;

	$wh = "";
	$searchOn = Strip($_REQUEST['_search'] );
	if($searchOn=='true') 
	{
		$dispatch="search";
		$searchstr = Strip($_REQUEST['filters']);
		$wh= constructWhere($searchstr);

	}
		

	// Carga la informacion al grid
	// ============================
	if ($dispatch=="load") 
	{
		
		$responce= busquedaCatalogo ($mysql,'',$page,$limit,$start,$sidx,$sord);
		    
		    //<BITACORA>
			$myBitacora = new Bitacora();
			$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL46_BITACORA" , "$id_login" ,  "", ""  ,  "process_bitacora.php");
			//<\BITACORA>
			
		echo json_encode($responce);
		
		
	}
		else if ($dispatch=="search") 
						{	
							$responce= busquedaCatalogo ($mysql,$wh,$page,$limit,$start,$sidx,$sord);

							//<BITACORA>
							$myBitacora = new Bitacora();
							$whr =str_ireplace("'", " " , $wh); 
							$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL46_BITACORA" , "$id_login" ,  $whr , ""  ,  "process_bitacora.php");
							//<\BITACORA>
			
							echo json_encode($responce);	
						}
				
mysqli_close($mysql);	

}	




//<CUENTASCONTABLES>  FUNCION DESPLEGADO INFORMACION
function busquedaCatalogo ($Conn,$where,$page,$limit,$start,$sidx,$sord)
{
	//page: gets de current page, ej. 1
	//limit: ancho de bloque, ej:100
	//start: ej. null
	//sidx: gets de index row, ej: tx_anio
	//sord: get the direction , ej:asc
	
		
		$sql = " SELECT COUNT(*) AS count ";
		$sql.= "   FROM tbl46_bitacora B  ";
		$sql.= " left outer join tbl_usuario U on B.id_usuario = U.id_usuario ";
		$sql.= " left outer join tbl47_pantalla P on (P.tx_programa = B.tx_programa  and P.tx_indicador <> '2' ) ";
		$sql.= " where 1=1 ".$where ;
			
		$result = mysqli_query($Conn, $sql);	
		$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		$count = $row['count'];
		
		if( $count>0 ) {
			$total_pages = ceil($count/$limit); //redondear hacia arriba para Obtener pagina
		} else {
			$total_pages = 0;
		}
		
		if ($page > $total_pages) 
				$page=$total_pages;
		
		 
		if( $count>0 ) 
		$start = $limit*$page - $limit; 	//inicial:0
		else
		$start =0;
		
		$sql = " select id_bitacora  ,tx_usuario, tx_nombre,  tx_tipo_operacion, fh_evento,  tx_modulo, tx_name,  B.tx_programa as tx_programa  , tx_remote_ip, tx_forward_ip, tx_client_ip, tx_tabla , id_key , tx_valores ";
		$sql.= " from tbl46_bitacora B ";
		$sql.= " left outer join tbl_usuario U on B.id_usuario = U.id_usuario ";
		$sql.= " left outer join tbl47_pantalla P on ( P.tx_programa = B.tx_programa and P.tx_indicador <> '2' ) ";
		$sql.= " where 1=1 ".$where ;
		
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= " 	LIMIT $start, $limit " ;
				
		//echo "sql",	$sql;
		
		$result = mysqli_query($Conn,$sql); 
		
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) 
		{
			$responce->rows[$i]['id']=$row['id_bitacora'];
			$responce->rows[$i]['cell']=array($row['id_bitacora'],$row['tx_usuario'],$row['tx_nombre'],$row['tx_tipo_operacion'],$row['fh_evento'],$row['tx_modulo'],$row['tx_name'], $row['tx_programa'],$row['tx_remote_ip'],$row['tx_forward_ip'],$row['tx_client_ip'],$row['tx_tabla'],$row['tx_valores'] ,$row['id_key'] );
			$i++;
		} 	
		
		mysqli_free_result($result);
	return $responce;	
		
	
	
}


// FUNCIONES PARA BUSQUEDA
// ============================
function constructWhere($s){
    $qwery = "";
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
        if(is_array($jsona))
        {
			$gopr = $jsona['groupOp'];
			$rules = $jsona['rules'];
            $i =0;
            foreach($rules as $key=>$val) 
            	{
                $field = $val['field'];
                //$posActivo=strpos($field,"ACTIVO");
                //$posInactivo=strpos($field,"INACTIVO");
                //si contiene ACTIVO o INACTIVO sustituir por b.indicador
                //if ($posActivo>0 && $posInactivo>0)
                //	$field = "a.tx_indicador";
                
                $op = $val['op'];
                $v = $val['data'];
                
                
				if($v<>null && $op<>null) 
				{
	                $i++;
					// ToSql in this case is absolutley needed
					$v = ToSql($field,$op,$v);
					if ($i == 1) 
						$qwery = " AND ( ";
					else 
						$qwery .= " " .$gopr." ";
					
					switch ($op) 
						{
						// in need other thing
					    case 'in' :
					    case 'ni' :
					        $qwery .= $field.$qopers[$op]." (".$v.")";
					        break;
						default:
					        $qwery .= $field;
					        $tmp = $qopers[$op];
					        $qwery .=$tmp;
					        $qwery .=$v;
					        break;
						}
					
					
				}
				
            }
            if($qwery <> "" )
		           $qwery .= " ) " ;
        }
    }
    
    
    return $qwery;
}
function ToSql ($field, $oper, $val) 
{
	// we need here more advanced checking using the type of the field - i.e. integer, string, float
	$tmpcad="";
	
	switch ($field) 
	{
		//casos especiales 
		case 'id':
			$tmpcad= intval($val);
			break;
		case 'amount':
		case 'tax':
		case 'total':
			$tmpcad= floatval($val);
			break;
			//campos de fecha agregar date ()
		case 'fh_fecha_modifica': 
		case 'fh_fecha_alta':
			$tmpcad=  addslashes($val) ;
			$tmpcad= " date('" . $tmpcad  . "') ";
			break;
			
			default :
			//mysql_real_escape_string is better
			
			if($oper=="bw" || $oper=="bn") 
			{
				$tmpcad= "'" . addslashes($val) . "%'";
			}
				else 
				{
				if ($oper=="ew" || $oper=="en")
				{ 
					$tmpcad= "'%" . addslashes($val) . "'";
				}
					else 
					{
						if ($oper=="cn" || $oper=="nc")
					
						$tmpcad= "'%" . addslashes($val) . "%'";
						else 
							$tmpcad= "'" . addslashes($val) . "'";
					}
				}
	}
	
	return  $tmpcad;
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
            if ( ! array_key_exists($iterator, $array) ) 
            {
            	return true; 
            }
        }
        return ! array_key_exists(0, $array);
    }
    return false;
}
				
				
?>