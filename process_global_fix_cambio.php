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
	
	$tx_catalogo		= $_GET["tx_catalogo"];
	$tx_clave 		= $_GET['tx_clave'];
	$tx_valor 		= $_GET['tx_valor'];
	$tx_valor_complementario = $_GET['tx_valor_complementario'];
	$tx_observaciones = $_GET['tx_observaciones'];
	$tx_indicador 		= $_GET['indicador'];
	
	

	$id_login 			= $_SESSION['sess_iduser'];	
	$id				= $_GET["id"];
	$examp 			= $_GET["q"];


$searchOn 		 =  false;

$tx_indicador	= $_GET['tx_indicador']; 



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
		
		$responce= busquedaCatalogo ($mysql,'',$page,$limit,$start,$sidx,$sord, $tx_catalogo);
		//<BITACORA>
			$myBitacora = new Bitacora();
			$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL45_CATALOGO_GLOBAL $tx_catalogo" , "$id_login" ,  "$tx_catalogo", ""  ,  "process_global.php");
			//<\BITACORA>
			
		echo json_encode($responce);
		
		
	}
	
	else if ($dispatch=="insert") 
		{	
			//obtener siguiente clave automatica
			$sql = " select max(cast(substr( tx_clave,instr(tx_clave ,'-')+1,CHAR_LENGTH(tx_clave)-instr(tx_clave ,'-')+1) as  UNSIGNED))   ";
			$sql.= " from tbl45_catalogo_global";
			$sql.= " WHERE substr( tx_clave,1,instr(tx_clave ,'-')-1) = '$tx_catalogo'";  
			$result = mysqli_query($mysql, $sql);
			$row = mysqli_fetch_row($result);
			$maximo = floor($row[0]);
			$maximo+=1;
	
	
			if ($tx_catalogo == "CUENTA_CONTABLE")
			{
				$sql = " SELECT count(*) ";
				$sql.= "   FROM tbl45_catalogo_global ";
				$sql.= "  WHERE  tx_valor = '$tx_valor'    "  ;
				$sql.= " and  substr( tx_clave,1,instr(tx_clave ,'-')-1) = '$tx_catalogo'" ;   		
				
				$result = mysqli_query($mysql, $sql);
				$row = mysqli_fetch_row($result);
				$keyReg = $row[0];	
				
				if ($keyReg > 0)	
				{	
					$data = array("error" => true, "message" => "El valor que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
					echo json_encode($data);
					return ;		
				}
			} 
			  						
				
				$id_usuarioalta= $id_login;		
				$id_usuariomod= $id_login;		
				$clavecompuesta = $tx_catalogo."-".$maximo;
				$sql = " INSERT INTO tbl45_catalogo_global  SET " ;   
				$sql.= " tx_clave		= '$clavecompuesta', ";
				$sql.= " tx_valor		= '$tx_valor', ";
				$sql.= " tx_valor_complementario= '$tx_valor_complementario', ";
				$sql.= " tx_observaciones = '$tx_observaciones', ";
				$sql.= " tx_indicador 			= '$indicador', ";
				$sql.= " fh_fecha_alta			= current_timestamp ,";
				$sql.= " fh_fecha_modifica		= current_timestamp , ";
				$sql.= " id_usuario_alta		= '$id_usuarioalta', ";
				$sql.= " id_usuario_modifica	= '$id_usuariomod' "; 
				if (mysqli_query($mysql, $sql))
				{		
					
				//<BITACORA>
				
				$valBita= "tx_clave=$clavecompuesta ";
				$valBita.= "tx_valor=$tx_valor ";
				$valBita.= "tx_valor_complementario=$tx_valor_complementario ";
				$valBita.= "tx_observaciones=$tx_observaciones ";
				$valBita.= "tx_indicador=$indicador ";
				$valBita.= "id_usuario_alta=$id_usuarioalta ";
				$valBita.= "id_usuario_modifica=$id_usuariomod "; 

					
				$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL45_CATALOGO_GLOBAL $tx_catalogo" , "$id_login" ,  $valBita, ""  ,  "process_global.php");
				//<\BITACORA>
				
				
					$data = array("error" => false, "message" => "El registro se INSERTO correctamente" );				
					echo json_encode($data);
				} else {  		
					$err=$mysql->error;
					$data = array("error" => true, "message" => "ERROR al INSERTAR el registro !</br></br>Informe: <br>$err" );				
					echo json_encode($data);
				} 		
				
			mysqli_free_result($result);		
		} 
		
		
			else if ($dispatch=="update") 
			{	
				/*
				$sql = " SELECT count(*) ";
				$sql.= "   FROM tbl45_catalogo_global "; 
				$sql.= "  WHERE id <> '$id' "; 		
				$sql.= "    AND tx_valor = '$tx_valor' "; 		
				$result = mysqli_query($mysql, $sql);
				$row = mysqli_fetch_row($result);
				$keyReg = $row[0];	
				
				if ($keyReg > 0)	{	
					$data = array("error" => true, "message" => "El valor: $tx_valor ya existe!</br></br>Por favor verifique ... " );				
					echo json_encode($data);
				} 
				*/
				  	
				
					if ($tx_catalogo == "CUENTA_CONTABLE")
			{
				$sql = " SELECT count(*) ";
				$sql.= "   FROM tbl45_catalogo_global ";
				$sql.= "  WHERE  tx_valor = '$tx_valor'   and id <> $id "  ;
				$sql.= " and  substr( tx_clave,1,instr(tx_clave ,'-')-1) = '$tx_catalogo'";   		
				
				$result = mysqli_query($mysql, $sql);
				$row = mysqli_fetch_row($result);
				$keyReg = $row[0];	
				
				if ($keyReg > 0)	
				{	
					$data = array("error" => true, "message" => "El valor que desea asignar  ya existe !</br></br> Por favor vefique ..." );					
					echo json_encode($data);
					return ;		
				}
			} 
			
					
					$fh_mod=date("Y-m-j, g:i");
					$id_usuariomod=$id_login;
				
					$sql = " UPDATE tbl45_catalogo_global SET " ; 
					$sql.= " tx_valor		= '$tx_valor', ";
					$sql.= " tx_valor_complementario= '$tx_valor_complementario', ";
					$sql.= " tx_observaciones = '$tx_observaciones', ";
					$sql.= " tx_indicador 			= '$indicador', ";
					$sql.= " fh_fecha_modifica		= current_timestamp , ";
					$sql.= " id_usuario_modifica	= '$id_usuariomod' "; 
					$sql.= " WHERE id= '$id' ";
						   
					//echo "aaa", $sql;
						//<BITACORA>
						$myBitacora = new Bitacora();
						$valores=$myBitacora->obtenvalores ($mysql, "TBL45_CATALOGO_GLOBAL", $id);
							      
					  
					if (mysqli_query($mysql, $sql))
					{
						
						$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL45_CATALOGO_GLOBAL $tx_catalogo" , "$id_login" ,  $valores, "$id"  ,  "process_global.php");
						//<\BITACORA>
							
							
						$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente" );							
						echo json_encode($data);
					} else {  
						$err=$mysql->error;
						$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Informe: <br>$err" );				
						echo json_encode($data);
					}		
					
			} 
				
				else if ($dispatch=='delete') {		
					
					$sql = " UPDATE  tbl45_catalogo_global SET TX_INDICADOR=0 ";
					$sql.= "  WHERE id = $id ";
						
					if (mysqli_query($mysql, $sql)) 
					{
						
							//<BITACORA>
							$myBitacora = new Bitacora();
							$myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL45_CATALOGO_GLOBAL $tx_catalogo" , "$id_login" ,  "", "$id"  ,  "process_anios.php");
							//<\BITACORA>
							
						$data = array("error" => false, "message" => "Registro dado de BAJA correctamente" );				
						echo json_encode($data);		
					} else {  
						$err=$mysql->error;
						
						//if ( strpos($err,'FOREIGN KEY') > 0 )
						//	$err=" El registro esta siendo usado por otro(s) registro(s).";
							
						 
						$data = array("error" => true, "message" => "ERROR al INACTIVAR el registro. </br></br>Informe:  <br>$err" );				
						echo json_encode($data);
					}										
				}	
				else if ($dispatch=="search") 
						{	
							$responce= busquedaCatalogo ($mysql,$wh,$page,$limit,$start,$sidx,$sord, $tx_catalogo);

							//<BITACORA>
							$myBitacora = new Bitacora();
							$whr =str_ireplace("'", " " , $wh); 
							$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL45_CATALOGO_GLOBAL $tx_catalogo" , "$id_login" ,  $whr , ""  ,  "process_global.php");
							//<\BITACORA>
			
							echo json_encode($responce);	
						}
				
mysqli_close($mysql);	

}	




//<CUENTASCONTABLES>  FUNCION DESPLEGADO INFORMACION
function busquedaCatalogo ($Conn,$where,$page,$limit,$start,$sidx,$sord, $theCatalogo)
{
	//page: gets de current page, ej. 1
	//limit: ancho de bloque, ej:100
	//start: ej. null
	//sidx: gets de index row, ej: tx_anio
	//sord: get the direction , ej:asc
	
		
		$sql = " SELECT COUNT(*) AS count ";
		$sql.= "   FROM tbl45_catalogo_global a  ";
		$sql.= " left outer  join tbl_usuario b on a.id_usuario_modifica = b.id_usuario ";
		$sql.= " left outer   join tbl_usuario c on a.id_usuario_alta = c.id_usuario ";
		$sql.= "  WHERE substr( tx_clave,1,instr(tx_clave ,'-')-1) = '".$theCatalogo."' ".$where ;
		$sql.= "     order by tx_clave ";
	
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
		
		$sql = " select id, tx_clave, tx_valor, tx_valor_complementario, tx_observaciones, a.tx_indicador as indicador, fh_fecha_modifica , b.tx_nombre as usuario_modifica, fh_fecha_alta, c.tx_nombre  as usuario_alta ";
		$sql.= " from tbl45_catalogo_global a ";
		$sql.= " left outer  join tbl_usuario b on a.id_usuario_modifica = b.id_usuario ";
		$sql.= " left outer   join tbl_usuario c on a.id_usuario_alta = c.id_usuario ";
		$sql.= " where substr( tx_clave,1,instr(tx_clave ,'-')-1) ='$theCatalogo'    ".$where ;
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
			$responce->rows[$i]['id']=$row['id'];
			$responce->rows[$i]['cell']=array($row['id'],$row['id'],$row['tx_clave'],$row['tx_valor'],$row['tx_valor_complementario'],$row['tx_observaciones'],$row['indicador'],$row['fh_fecha_modifica'],$row['usuario_modifica'],$row['fh_fecha_alta'],$row['usuario_alta'],);
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
                $posActivo=strpos($field,"ACTIVO");
                $posInactivo=strpos($field,"INACTIVO");
                //si contiene ACTIVO o INACTIVO sustituir por b.indicador
                if ($posActivo>0 && $posInactivo>0)
                	$field = "a.tx_indicador";
                
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