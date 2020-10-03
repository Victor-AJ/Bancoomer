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
if 	(isset($_SESSION["sess_user"])) 
	$id_login = $_SESSION['sess_iduser'];


	// Recibo variables
	// ============================
	//PAGINACION DESPLEGADO SECCION RESULTSET
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
	$tx_proveedor		= $_GET['cap_proveedor']; 	
	$tx_producto 		= $_GET['cap_producto']; 	
	$tx_producto_corto	= $_GET['cap_producto_corto'];	 
	$tx_descripcion		= $_GET['cap_descripcion']; 	
	$tx_descripcion_corta = $_GET['cap_descripcion_corta']; 	
	$fl_precio 			= $_GET['cap_precio']; 
	$tx_moneda 			= $_GET['cap_moneda'];
	//3.- CUENTASCONT: se agrega parametro  de CuentaContable
	$id_cuenta 			= $_GET['cap_cuenta'];  
	$in_licencia		= $_GET['sel_licencia']; 
	$id_login 			= $_SESSION['sess_iduser'];	
	
	$fl_precio = ereg_replace( (","), "", $fl_precio ); 
	
	//arreglar los indices por llave
	if ($tx_moneda==1) 
		{ 
			$fl_precio_usd=0; 
			$fl_precio_mxn=$fl_precio;
			$fl_precio_eur=0;  
		}
	if ($tx_moneda==2) 
		{ 
			$fl_precio_usd=$fl_precio; 
			$fl_precio_mxn=0; 
			$fl_precio_eur=0;
		}

	if ($tx_moneda==3) 
		{ 
			$fl_precio_usd=0; 
			$fl_precio_mxn=0;
			$fl_precio_eur=$fl_precio; 
		}
		

		
	if(!$sidx) 
		$sidx = 1;

	$wh = "";
	$searchOn = Strip($_REQUEST['_search'] );
	if($searchOn=='true') 
	{
		$dispatch="search";
		$searchstr = Strip($_REQUEST['filters']);
		$wh= constructWhere($searchstr);
	//echo $wh;
	//echo "<br>";
	}

	// Carga la informacion al grid
	// ============================
	if ($dispatch=="load") 
	{
		//6.- CUENTASCONT: se  cambia query por joins
		$responce= busquedaCatalogo ($mysql,'',$page,$limit,$start,$sidx,$sord);
		
		//<BITACORA>
	 	$myBitacora = new Bitacora();
		$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_PRODUCTO" , "$id_login" ,  "", ""  ,  "process_productos.php");
	    //<\BITACORA>
		
		echo json_encode($responce);	
	}

	// INSERTA
	// ============================
	else if ($dispatch=="insert") 
	{	
		
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_producto ";
			$sql.= "  WHERE id_proveedor 	= '$tx_proveedor' ";	
			$sql.= "    AND tx_producto 	= '$tx_producto' ";				
		
			//echo "sql", $sql;
			
			$result = mysqli_query($mysql, $sql);
			$row = mysqli_fetch_row($result);
			$count = $row[0];	
			
			if ($count > 0)	{	
				$data = array("error" => true, "message" => "El Producto $tx_producto que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
				echo json_encode($data);		
			} else {  
								
				$fh_alta=date("Y-m-j, g:i");
				$id_usuarioalta=$id_login;		
				$fh_mod=date("Y-m-j, g:i");
				$id_usuariomod=$id_login;
					
				//	5.- CUENTASCONT: se agrega COLUMNA de CuentaContable 
				$sql = " INSERT INTO tbl_producto SET " ;  			
				$sql.= " id_proveedor		= '$tx_proveedor', ";
				$sql.= " id_moneda			= '$tx_moneda', ";
				$sql.= " tx_producto		= '$tx_producto', ";
				$sql.= " tx_producto_corto	= '$tx_producto_corto', ";
				$sql.= " tx_descripcion		= '$tx_descripcion', ";
				$sql.= " tx_descripcion_corta = '$tx_descripcion_corta', ";
				$sql.= " fl_precio			= '$fl_precio_usd', ";
				$sql.= " fl_precio_mxn		= '$fl_precio_mxn', ";
				$sql.= " fl_precio_eur		= '$fl_precio_eur', ";
				$sql.= " in_licencia		= '$in_licencia', ";
				$sql.= " tx_indicador		= '$tx_indicador', ";
				$sql.= " fh_alta			= '$fh_alta', ";
				$sql.= " id_usuarioalta		= '$id_usuarioalta', ";
				$sql.= " fh_mod 			= '$fh_mod', ";
				$sql.= " id_usuariomod		= '$id_usuariomod', ";
				$sql.= " id_cuenta_contable		= '$id_cuenta' ";  
						
				if (mysqli_query($mysql, $sql))
				{		
				
				$valBita= "id_proveedor=$tx_proveedor ";
				$valBita.= "id_moneda=$tx_moneda ";
				$valBita.= "tx_producto=$tx_producto ";
				$valBita.= "tx_producto_corto=$tx_producto_corto ";
				$valBita.= "tx_descripcion=$tx_descripcion ";
				$valBita.= "tx_descripcion_corta=$tx_descripcion_corta ";
				$valBita.= "fl_precio=$fl_precio_usd ";
				$valBita.= "fl_precio_mxn=$fl_precio_mxn ";
				$valBita.= "fl_precio_eur=$fl_precio_eur ";
				$valBita.= "in_licencia=$in_licencia ";
				$valBita.= "tx_indicador=$tx_indicador ";
				$valBita.= "fh_alta=$fh_alta ";
				$valBita.= "id_usuarioalta=$id_usuarioalta ";
				$valBita.= "fh_mod=$fh_mod ";
				$valBita.= "id_usuariomod=$id_usuariomod ";
				$valBita.= "id_cuenta_contable=$id_cuenta ";  
				
				$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL_PRODUCTO" , "$id_login" ,  $valBita, ""  ,  "process_productos.php");
				//<\BITACORA>
				
				
					$data = array("error" => false, "message" => "El registro se INSERTO correctamente" );				
					echo json_encode($data);
				} else {  		
						$err=$mysql->error;
					$data = array("error" => true, "message" => "ERROR al INSERTAR el registro !</br></br>Informe:  <br>$err" );				
					echo json_encode($data);
				} 		
			}	
		mysqli_free_result($result);		
	} 

	// ACTUALIZA
	// ============================
	else if ($dispatch=="save") {	
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_producto "; 
		$sql.= "  WHERE id_producto <> $id "; 		
		$sql.= " 	AND tx_producto = '$tx_producto'  ";
		
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
		
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "El producto $tx_producto ya existe!</br></br>Por favor verifique ... " );				
			echo json_encode($data);
		} else {  	
		
				$fh_mod=date("Y-m-j, g:i");
				$id_usuariomod=$id_login;

				//	6.- CUENTASCONT: se agrega COLUMNA de CuentaContable
				$sql = " UPDATE tbl_producto SET " ; 
				$sql.= " 	id_proveedor	  	= '$tx_proveedor', ";
				$sql.= " 	id_moneda			= '$tx_moneda', ";
				$sql.= " 	tx_producto		  	= '$tx_producto', ";
				$sql.= " 	tx_producto_corto 	= '$tx_producto_corto', ";
				$sql.= " 	tx_descripcion	  	= '$tx_descripcion', ";
				$sql.= " 	tx_descripcion_corta = '$tx_descripcion_corta', ";
				$sql.= "    fl_precio			= '$fl_precio_usd', ";
				$sql.= "    fl_precio_mxn		= '$fl_precio_mxn', ";
				$sql.= "    fl_precio_eur		= '$fl_precio_eur', ";
				$sql.= " 	in_licencia			= '$in_licencia', ";
				$sql.= " 	tx_indicador		= '$tx_indicador', ";				
				$sql.= " 	fh_mod 				= '$fh_mod', ";
				$sql.= " 	id_usuariomod		= '$id_usuariomod', "; 
				$sql.= " id_cuenta_contable		= '$id_cuenta' "; 
				$sql.= " WHERE id_producto		= $id ";
					   
				//echo "aaa", $sql;      
			
				$myBitacora = new Bitacora();
				$valores=$myBitacora->obtenvalores ($mysql, "TBL_PRODUCTO" , $id);
	
	
				  
				if (mysqli_query($mysql, $sql))
				{
				
					$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_PRODUCTO" , "$id_login" ,  $valores, "$id"  ,  "process_productos.php");
			
					$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente" );							
					echo json_encode($data);
				} else {  
						$err=$mysql->error;
					$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Informe:  <br>$err" );				
					echo json_encode($data);
				}	
		}	
		mysqli_free_result($result);
	} 
	
	// BORRA
	// ============================
	else if ($dispatch=='delete') 
	{		
		//LL:PROBLEMA DETECTADO: DEBIDO A QUE NO TIENE INTEGRIDAD LA BD DEJA BORRAR TODO, PENDIENTE REDISEÑO BD	
			$sql = " update  tbl_producto set tx_indicador= '0' ";
			$sql.= "  WHERE id_producto = $id ";
				
				
			if (mysqli_query($mysql, $sql)) 
			{
			
				//<BITACORA>
	 			$myBitacora = new Bitacora();
					$myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL_PRODUCTO" , "$id_login" ,  "", "$id"  ,  "process_productos.php");
			    //<\BITACORA>
		
		
				$data = array("error" => false, "message" => "Registro dado de BAJA  correctamente" );				
				echo json_encode($data);		
			} else {  
				$err=$mysql->error;
				$data = array("error" => true, "message" => "ERROR al INACTIVAR el registro. </br></br>Informe:  <br>$err" );				
				echo json_encode($data);
			}	
		//}		
		//mysqli_free_result($result);								
	}	

	// BUSQUEDA
	// ============================
	else if ($dispatch=="search") {	
		
		//6.- CUENTASCONT: se agrega join con cuentas
				
		$responce= busquedaCatalogo ($mysql,$wh,$page,$limit,$start,$sidx,$sord);
		
		//<BITACORA>
	 	$myBitacora = new Bitacora();
	 	$whr =str_ireplace("'", " " , $wh); 
		$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_PRODUCTO" , "$id_login" , $whr, ""  ,  "process_productos.php");
	    //<\BITACORA>
		
		echo json_encode($responce);	
				
		 
	}	
	mysqli_close($mysql);	

	}  //if session


	
//<CUENTASCONTABLES>  FUNCION DESPLEGADO INFORMACION
function busquedaCatalogo ($Conn,$where,$page,$limit,$start,$sidx,$sord)
{
	//page: gets de current page, ej. 1
	//limit: ancho de bloque, ej:100
	//start: ej. null
	//sidx: gets de index row, ej: tx_anio
	//sord: get the direction , ej:asc
	
	$sql = " SELECT COUNT(*) AS count "; 
		$sql.= "   FROM tbl_producto b  ";
		$sql.= " inner join tbl_proveedor a on b.id_proveedor 	= a.id_proveedor ";
		$sql.= " inner join tbl_moneda c on b.id_moneda 	= c.id_moneda ";
		$sql.= " inner join tbl_usuario d on  b.id_usuariomod = d.id_usuario ";
		$sql.= " inner join tbl_usuario e on  b.id_usuarioalta = e.id_usuario ";
		$sql.= " left outer join tbl45_catalogo_global p  on  b.id_cuenta_contable = p.id  where 1=1 " .$where;
		
		$result = mysqli_query($Conn, $sql);	
		$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		$count = $row['count'];
		
		if( $count>0 ) {
			$total_pages = ceil($count/$limit); //round up
		//	$total_pages = 1;
		} else {
			$total_pages = 0;
		}
		
		if ($page > $total_pages) 
		$page=$total_pages;  //inicial: 1
		
		
		if( $count>0 ) 
		$start = $limit*$page - $limit; 	//inicial:0
		else
		$start =0;
		
		//7.- CUENTASCONT: se agrega join con cuentas
		$sql = " SELECT id_producto, id_producto, tx_proveedor, tx_producto, tx_valor , tx_producto_corto, b.tx_descripcion, tx_descripcion_corta, fl_precio, fl_precio_mxn, fl_precio_eur,  in_licencia, b.tx_indicador, b.fh_mod, d.tx_nombre AS usuario_mod, b.fh_alta, e.tx_nombre AS usuario_alta ";
		$sql.= "   FROM tbl_producto b  ";
		$sql.= " inner join tbl_proveedor a on b.id_proveedor 	= a.id_proveedor ";
		$sql.= " inner join tbl_moneda c on b.id_moneda 	= c.id_moneda ";
		$sql.= " inner join tbl_usuario d on  b.id_usuariomod = d.id_usuario ";
		$sql.= " inner join tbl_usuario e on  b.id_usuarioalta = e.id_usuario ";
		$sql.= " left outer join tbl45_catalogo_global p  on  b.id_cuenta_contable = p.id  where 1=1 " .$where;
	
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= " 	LIMIT $start, $limit " ;		
		
		

		
		$result = mysqli_query($Conn , $sql); 
		
		//$responce->page = $page;
		//$responce->total = $total_pages;
		//$responce->records = $count;
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) 
		{
			$responce->rows[$i]['id']=$row[id_producto];
			$responce->rows[$i]['cell']=array($row[id_producto],$row[id_producto],$row[tx_proveedor],$row[tx_producto], $row[tx_valor],  $row[tx_producto_corto],$row[tx_descripcion],$row[tx_descripcion_corta],$row[fl_precio],$row[fl_precio_mxn],$row[fl_precio_eur],$row[in_licencia],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
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
                	$field = "b.tx_indicador";
                	
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
		case 'id':
			$tmpcad= intval($val);
			break;
		case 'amount':
		case 'tax':
		case 'total':
			$tmpcad= floatval($val);
			break;
		case 'b.fh_mod': 
		case 'b.fh_alta':
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