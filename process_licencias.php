<?

session_start();
if 	(isset($_SESSION['sess_user']))
{
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$id_login = $_SESSION['sess_iduser'];
	
	$mysql=conexion_db();

	// Recibo variables
	// ============================
	$page 				= $_GET['page']; 
	$limit 				= $_GET['rows']; 
	$start				= $_GET['start'];
	$sidx 				= $_GET['sidx']; 
	$sord 				= $_GET['sord']; 
	$dispatch			= $_GET["dispatchLic"];
	$id					= $_GET["id"];
	$id_lic				= $_GET["id_lic"];
	$tx_indicador		= $_GET['tx_indicador']; 	
	$examp 				= $_GET["q"];
	$searchOn 			= Strip($_GET["_search"]);
	$tx_proveedor		= $_GET['sel_proveedor']; 	
	$tx_producto 		= $_GET['sel_producto']; 	
	$tx_cuenta			= $_GET['sel_cuenta'];	 
	$fl_precio 			= $_GET['cap_precio']; 
	$tx_moneda 			= $_GET['cap_moneda']; 
	//CAMBIO CONCEPTO CONTABLE
	//$tx_concepto_contable= $_GET['cap_concepto_contable']; 
	$tx_sid_terminal 	= $_GET['cap_sid_terminal']; 
	$tx_login 			= $_GET['cap_login']; 
	$tx_serial_number 	= $_GET['cap_serial_number']; 		
	$id_login 			= $_SESSION['sess_iduser'];		
	
	//echo "<br>";
	//echo "Moneda",$tx_moneda;
	
	$fl_precio = ereg_replace( (","), "", $fl_precio ); 

	// Carga la informacion al grid
	// ============================
	if ($dispatch=="load") {
		
		//LL: POSIBLEMENTE NO SE USE, INTENTO DE USAR GRID
		$sql = " SELECT COUNT(*) AS count "; 
		$sql.= "    FROM tbl_licencia a, tbl_empleado b, tbl_producto c, tbl_proveedor d, tbl_cuenta e, tbl_moneda f ";
		$sql.= "   WHERE a.id_empleado	= $id";
		$sql.= "     AND a.id_empleado 	= b.id_empleado ";
		$sql.= "     AND a.id_producto 	= c.id_producto ";
		$sql.= "     AND c.id_proveedor = d.id_proveedor ";
		$sql.= "     AND a.id_cuenta 	= e.id_cuenta ";
		$sql.= "     AND c.id_moneda 	= f.id_moneda ";
								
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
		
		$sql = "  SELECT id_licencia, id_licencia, tx_proveedor_corto, tx_producto, tx_cuenta, c.tx_descripcion, in_licencia, a.fl_precio, tx_moneda,  z.tx_valor  AS tx_concepto_contable, tx_login, tx_sid_terminal, tx_serial_number, a.tx_indicador ";
		$sql.= "    FROM tbl_licencia a   ";
		$sql.= "  INNER JOIN tbl_empleado b on a.id_empleado 	= b.id_empleado ";
		$sql.= "  INNER JOIN tbl_producto c on  a.id_producto 	= c.id_producto ";
		$sql.= "  INNER JOIN tbl_proveedor d on  c.id_proveedor = d.id_proveedor ";
		$sql.= "  INNER JOIN tbl_cuenta e on  a.id_cuenta 	= e.id_cuenta ";
		$sql.= "  INNER JOIN tbl_moneda f on   c.id_moneda 	= f.id_moneda ";
		$sql.= " left outer join tbl45_catalogo_global z  on z.id= c.id_cuenta_contable "; 
		
		$sql.= "   WHERE a.id_empleado	= $id ";
	
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= " 	LIMIT $start, $limit " ;		
		
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql,$sql); 
		
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;		
		
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
				$responce->rows[$i]['id']=$row[id_licencia];
				$responce->rows[$i]['cell']=array($row[id_licencia],$row[id_licencia],$row[tx_proveedor_corto],$row[tx_producto],$row[tx_cuenta],$row[tx_descripcion],$row[in_licencia],$row[fl_precio],$row[tx_moneda],$row[tx_concepto_contable],$row[tx_login],$row[tx_sid_terminal],$row[tx_serial_number],$row[tx_indicador]);
				$i++;
		} 	
		
		echo json_encode($responce);	
		mysqli_free_result($result);
	}

	// INSERTA
	// ============================
	else if ($dispatch=="insert") {	
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_licencia ";
		//$sql.= "  WHERE id_empleado	= '$id' ";	
		//$sql.= "    AND id_producto	= $tx_producto ";	
		//$sql.= " 	AND id_cuenta	= $tx_cuenta ";			
		
		//echo "sql", $sql;
			
		//$result = mysqli_query($mysql, $sql);
		//$row = mysqli_fetch_row($result);
		//$count = $row[0];	
			
		//if ($count > 0)	{	
		//	$data = array("error" => true, "message" => "La licencia que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
		//	echo json_encode($data);		
		//} else {  
			
			if ($tx_moneda=="USD")
			{
				$fl_precio_usd=$fl_precio;
				$fl_precio_mxn=NULL;
			} else if ($tx_moneda=="MXN") {
				$fl_precio_usd=NULL;
				$fl_precio_mxn=$fl_precio;
			}
			
			$tx_indicador="1";				
			$fh_alta=date("Y-m-j, g:i");
			$id_usuarioalta=$id_login;		
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;	
				
			$sql = " INSERT INTO tbl_licencia SET " ;  			
			$sql.= " id_empleado		= $id, ";
			$sql.= " id_producto		= $tx_producto, ";
			$sql.= " id_cuenta			= $tx_cuenta, ";
			$sql.= " fl_precio_usd		= '$fl_precio_usd', ";
			$sql.= " fl_precio_mxn		= '$fl_precio_mxn', ";
			//CAMBIO CUENTAS CONTABLES
			//$sql.= " tx_concepto_contable = '$tx_concepto_contable', ";
			$sql.= " tx_login			= '$tx_login', ";
			$sql.= " tx_sid_terminal 	= '$tx_sid_terminal', ";
			$sql.= " tx_serial_number 	= '$tx_serial_number', ";
			$sql.= " tx_indicador		= '$tx_indicador', ";
			$sql.= " fh_alta			= '$fh_alta', ";
			$sql.= " id_usuarioalta		= '$id_usuarioalta', ";
			$sql.= " fh_mod 			= '$fh_mod', ";
			$sql.= " id_usuariomod		= '$id_usuariomod' "; 
						
			//echo "aaa", $sql;
			//vaslores para bitacora  
			$valoresBita= "id_empleado=$id ";
			$valoresBita.= "id_producto=$tx_producto ";
			$valoresBita.= "id_cuenta=$tx_cuenta ";
			$valoresBita.= "fl_precio_usd=$fl_precio_usd ";
			$valoresBita.= "fl_precio_mxn=$fl_precio_mxn ";
			$valoresBita.= "tx_login=$tx_login ";
			$valoresBita.= "tx_sid_terminal=$tx_sid_terminal ";
			$valoresBita.= "tx_serial_number=$tx_serial_number ";
			$valoresBita.= "tx_indicador=$tx_indicador ";
			$valoresBita.= "fh_alta=$fh_alta ";
			$valoresBita.= "id_usuarioalta=$id_usuarioalta ";
			$valoresBita.= "fh_mod=$fh_mod ";
			$valoresBita.= "id_usuariomod=$id_usuariomod "; 
			
			
			
				
			if (mysqli_query($mysql, $sql))
			{		
				

				//<BITACORA>
				$myBitacora = new Bitacora();
	 			$myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL_LICENCIA" , "$id_login" ,  $valoresBita , ""  ,  "process_licencias.php");
				//<\BITACORA
				
				
				$data = array("error" => false, "message" => "El registro se INSERTO correctamente", "html" => "cat_licencias_lista.php?id=$id&dispatch=save" );				
				echo json_encode($data);
			} else {  		
				$data = array("error" => true, "message" => "ERROR al INSERTAR el registro !</br></br>Por favor verifique ..." );				
				echo json_encode($data);
			} 		
		//}	
	} 

	// ACTUALIZA
	// ============================
	else if ($dispatch=="save") {			
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_licencia ";
		$sql.= "  WHERE id_licencia	<> $id_lic ";
		$sql.= "    AND id_empleado	= '$id' ";	
		$sql.= "    AND id_producto	= $tx_producto ";	
		$sql.= " 	AND id_cuenta	= $tx_cuenta ";		
			
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
			
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "La licencia que modifico ya existe!</br></br>Por favor verifique ... " );				
			echo json_encode($data);
		} else {  
			
			if ($tx_moneda=="USD")
			{
				$fl_precio_usd=$fl_precio;
				$fl_precio_mxn=NULL;
			} else if ($tx_moneda=="MXN") {
				$fl_precio_usd=NULL;
				$fl_precio_mxn=$fl_precio;
			}
			
			$fh_mod=date("Y-m-j, g:i");
			$id_usuariomod=$id_login;
			
			$sql = " UPDATE tbl_licencia SET " ; 
			$sql.= " id_empleado		= $id, ";
			$sql.= " id_producto		= $tx_producto, ";
			$sql.= " id_cuenta			= $tx_cuenta, ";
			$sql.= " fl_precio_usd		= '$fl_precio_usd', ";
			$sql.= " fl_precio_mxn		= '$fl_precio_mxn', ";

			//CAMBIO CUENTAS CONTABLES
			//$sql.= " tx_concepto_contable = '$tx_concepto_contable', ";
			$sql.= " tx_login			= '$tx_login', ";
			$sql.= " tx_sid_terminal 	= '$tx_sid_terminal', ";
			$sql.= " tx_serial_number 	= '$tx_serial_number', ";
//			$sql.= " tx_indicador		= '$tx_indicador', ";
			$sql.= " fh_mod 			= '$fh_mod', ";
			$sql.= " id_usuariomod		= '$id_usuariomod' "; 
			$sql.= " WHERE id_licencia	= $id_lic ";
					   
			//echo "aaa", $sql;  
			//<BITACORA>
			$myBitacora = new Bitacora();
			$valoresBita=$myBitacora->obtenvalores ($mysql, "TBL_LICENCIA", $id_lic );    
				  
			if (mysqli_query($mysql, $sql))
			{
				
				//<BITACORA>
				
	 			$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_LICENCIA" , "$id_login" ,  $valoresBita , $id_lic   ,  "process_licencias.php");
				//<\BITACORA
				
	 			
				$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente", "html" => "cat_licencias_lista.php?id=$id"  );							
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
		
		$sql = " UPDATE  tbl_licencia SET TX_INDICADOR=0 ";
		$sql.= "  WHERE id_licencia = $id_lic ";
				
		//echo "aaa", $sql; 
				
		if (mysqli_query($mysql, $sql)) 
		{
			
			//<BITACORA>
			$myBitacora = new Bitacora();
	 		$myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL_LICENCIA" , "$id_login" ,  "" , $id_lic   ,  "process_licencias.php");
			//<\BITACORA
			
			
			$data = array("error" => false, "message" => "Se ha dado de BAJA el registro exitosamente", "html" => "cat_licencias_lista.php?id=$id" );				
			echo json_encode($data);		
		} else {  
			$data = array("error" => true, "message" => "ERROR al BORRAR el registro. </br></br>Por favor verifique ..." );				
			echo json_encode($data);
		}			
	}	

	// BUSQUEDA
	// ============================
	else if ($dispatch=="search") {	
	
		//LL: esta seccion al parecer no se usa, fue intento de integrar grid
		$sql = " SELECT COUNT(*) AS count ";
		$sql.= "   FROM tbl_proveedor a, tbl_usuario b, tbl_usuario c ";
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
		$sql.= "     FROM  tbl_proveedor a, tbl_usuario b, tbl_usuario c " ; 
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