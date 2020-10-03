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
	$tx_proveedor_corto = $_GET['cap_proveedor_corto']; 	
	$tx_rfc 			= $_GET['cap_rfc']; 
	$tx_descripcion 	= $_GET['cap_descripcion']; 
	$tx_contrato 		= $_GET['cap_contrato']; 
	$tx_extranjero 		= $_GET['sel_extranjero']; 
	$tx_iva 			= $_GET['sel_iva']; 
	$tx_gps 			= $_GET['cap_gps']; 
	$tx_direccion 		= $_GET['cap_direccion']; 
	$tx_pagina 			= $_GET['cap_pagina']; 
	$tx_fax 			= $_GET['cap_fax']; 
	$tx_contacto1 		= $_GET['cap_contacto1']; 
	$tx_puesto1 		= $_GET['cap_puesto1']; 
	$tx_telefono1 		= $_GET['cap_telefono1']; 
	$tx_celular1 		= $_GET['cap_celular1']; 
	$tx_correo1 		= $_GET['cap_correo1']; 
	$tx_contacto2 		= $_GET['cap_contacto2']; 
	$tx_puesto2 		= $_GET['cap_puesto2']; 
	$tx_telefono2 		= $_GET['cap_telefono2']; 
	$tx_celular2 		= $_GET['cap_celular2']; 
	$tx_correo2 		= $_GET['cap_correo2']; 
	$id_login 			= $_SESSION['sess_iduser'];	
	$q 					= $_GET['q'];	

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
		$sql.= "   FROM tbl_proveedor a, tbl_usuario b, tbl_usuario c ";
		$sql.= "  WHERE a.id_usuariomod 	= b.id_usuario ";
		$sql.= "    AND a.id_usuarioalta 	= c.id_usuario ";
		
		//echo "sql",	$sql;
			
		$result = mysqli_query($mysql, $sql);	
		$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		$count = $row['count'];
		
		if( $count>0 ) {
			$total_pages = ceil($count/$limit);
		//	$total_pages = 1;
		} else {
			$total_pages = 0;
		}
	
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; 		
		
		$sql = "   SELECT id_proveedor, id_proveedor, tx_proveedor, tx_proveedor_corto, tx_rfc, tx_descripcion, tx_contrato, tx_extranjero, tx_iva, tx_gps, tx_direccion, tx_pagina, tx_fax, tx_contacto1, tx_puesto1, tx_telefono1, tx_celular1, tx_correo1, tx_contacto2, tx_puesto2, tx_telefono2, tx_celular2, tx_correo2, a.tx_indicador, a.fh_mod, b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta " ; 
		$sql.= "     FROM tbl_proveedor a, tbl_usuario b, tbl_usuario c ";
		$sql.= "    WHERE a.id_usuariomod 	= b.id_usuario ";
		$sql.= "      AND a.id_usuarioalta 	= c.id_usuario ";
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= " 	LIMIT $start, $limit " ;		
		
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql,$sql); 
		
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
			$responce->rows[$i]['id']=$row[id_proveedor];
			$responce->rows[$i]['cell']=array($row[id_proveedor],$row[id_proveedor],$row[tx_proveedor],$row[tx_proveedor_corto],$row[tx_rfc],$row[tx_descripcion],$row[tx_contrato],$row[tx_extranjero],$row[tx_iva],$row[tx_gps],$row[tx_direccion],$row[tx_pagina],$row[tx_fax],$row[tx_contacto1],$row[tx_puesto1],$row[tx_telefono1],$row[tx_celular1],$row[tx_correo1],$row[tx_contacto2],$row[tx_puesto2],$row[tx_telefono2],$row[tx_celular2],$row[tx_correo2],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		
		//<BITACORA>
	 	$myBitacora = new Bitacora();
		$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_PROVEEDOR" , "$id_login" ,  "", ""  ,  "process_proveedores.php");
	    //<\BITACORA>
	 
		echo json_encode($responce);	
		mysqli_free_result($result);
	}

	// INSERTA
	// ============================
	else if ($dispatch=="insert") {	
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_proveedor ";
		$sql.= "  WHERE tx_proveedor = '$tx_proveedor' ";	
	
		//echo "sql", $sql;
		
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
		
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "La Raz&oacute;n Social  $tx_proveedor que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
			echo json_encode($data);		
		} else {  
		
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_proveedor ";
			$sql.= "  WHERE tx_proveedor_corto = '$tx_proveedor_corto' ";	
		
			//echo "sql", $sql;
			
			$result = mysqli_query($mysql, $sql);
			$row = mysqli_fetch_row($result);
			$count = $row[0];	
			
			if ($count > 0)	{	
				$data = array("error" => true, "message" => "El Proveedor $tx_proveedor_corto que desea dar de alta ya existe !</br></br> Por favor vefique ..." );					
				echo json_encode($data);		
			} else {  
								
				$fh_alta=date("Y-m-j, g:i");
				$id_usuarioalta=$id_login;		
				$fh_mod=date("Y-m-j, g:i");
				$id_usuariomod=$id_login;	
				
				$sql = " INSERT INTO tbl_proveedor SET " ;   			
				$sql.= " tx_proveedor		= '$tx_proveedor', ";
				$sql.= " tx_proveedor_corto	= '$tx_proveedor_corto', ";				
				$sql.= " tx_rfc				= '$tx_rfc', ";
				$sql.= " tx_descripcion		= '$tx_descripcion', ";
				$sql.= " tx_contrato		= '$tx_contrato', ";
				$sql.= " tx_extranjero		= '$tx_extranjero', ";
				$sql.= " tx_iva				= '$tx_iva', ";				
				$sql.= " tx_gps				= '$tx_gps', ";				
				$sql.= " tx_direccion		= '$tx_direccion', ";
				$sql.= " tx_pagina			= '$tx_pagina', ";
				$sql.= " tx_fax				= '$tx_fax', ";
				$sql.= " tx_contacto1		= '$tx_contacto1', ";
				$sql.= " tx_puesto1			= '$tx_puesto1', ";
				$sql.= " tx_telefono1		= '$tx_telefono1', ";
				$sql.= " tx_celular1		= '$tx_celular1', ";
				$sql.= " tx_correo1			= '$tx_correo1', ";
				$sql.= " tx_contacto2		= '$tx_contacto2', ";
				$sql.= " tx_puesto2			= '$tx_puesto2', ";
				$sql.= " tx_telefono2		= '$tx_telefono2', ";
				$sql.= " tx_celular2		= '$tx_celular2', ";
				$sql.= " tx_correo2			= '$tx_correo2', ";
				$sql.= " tx_indicador		= '$tx_indicador', ";
				$sql.= " fh_alta			= '$fh_alta', ";
				$sql.= " id_usuarioalta		= '$id_usuarioalta', ";
				$sql.= " fh_mod 			= '$fh_mod', ";
				$sql.= " id_usuariomod		= '$id_usuariomod' "; 
						
				//echo "aaa", $sql;  
				
				if (mysqli_query($mysql, $sql))
				{		
				
				//<BITACORA>
				
				
			
				$valBita= "tx_proveedor=$tx_proveedor ";
				$valBita.= "tx_proveedor_corto=$tx_proveedor_corto ";				
				$valBita.= "tx_rfc=$tx_rfc ";
				$valBita.= "tx_descripcion=$tx_descripcion ";
				$valBita.= "tx_contrato=$tx_contrato ";
				$valBita.= "tx_extranjero=$tx_extranjero ";
				$valBita.= "tx_iva=$tx_iva ";				
				$valBita.= "tx_gps=$tx_gps ";				
				$valBita.= "tx_direccion=$tx_direccion ";
				$valBita.= "tx_pagina=$tx_pagina ";
				$valBita.= "tx_fax=$tx_fax ";
				$valBita.= "tx_contacto1=$tx_contacto1 ";
				$valBita.= "tx_puesto1=$tx_puesto1 ";
				$valBita.= "tx_telefono1=$tx_telefono1 ";
				$valBita.= "tx_celular1=$tx_celular1 ";
				$valBita.= "tx_correo1=$tx_correo1 ";
				$valBita.= "tx_contacto2=$tx_contacto2 ";
				$valBita.= "tx_puesto2=$tx_puesto2 ";
				$valBita.= "tx_telefono2=$tx_telefono2 ";
				$valBita.= "tx_celular2=$tx_celular2 ";
				$valBita.= "tx_correo2=$tx_correo2 ";
				$valBita.= "tx_indicador=$tx_indicador ";
				$valBita.= "fh_alta=$fh_alta ";
				$valBita.= "id_usuarioalta=$id_usuarioalta ";
				$valBita.= "fh_mod=$fh_mod ";
				$valBita.= "id_usuariomod=$id_usuariomod "; 
				
				
				$myBitacora = new Bitacora();
				$myBitacora->anotaBitacora ($mysql, "ALTA" , "TBL_PROVEEDOR" , "$id_login" ,  $valBita, ""  ,  "process_proveedores.php");
				//<\BITACORA>
				
				
					$data = array("error" => false, "message" => "El registro se INSERTO correctamente" );				
					echo json_encode($data);
				} else {  		
					$data = array("error" => true, "message" => "ERROR al INSERTAR el registro !</br></br>Por favor verifique ..." );				
					echo json_encode($data);
				} 		
			}	
		}	
		mysqli_free_result($result);		
	} 

	// ACTUALIZA
	// ============================
	else if ($dispatch=="save") {	
		
		$sql = " SELECT * ";
		$sql.= "   FROM tbl_proveedor "; 
		$sql.= "  WHERE id_proveedor 		<> $id "; 		
		$sql.= " 	AND tx_proveedor 		= '$tx_proveedor'  ";
		
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql, $sql);
		$row = mysqli_fetch_row($result);
		$count = $row[0];	
		
		if ($count > 0)	{	
			$data = array("error" => true, "message" => "La Raz&oacute;n Social $tx_proveedor ya existe!</br></br>Por favor verifique ... " );				
			echo json_encode($data);
		} else {  	
		
			$sql = " SELECT * ";
			$sql.= "   FROM tbl_proveedor "; 
			$sql.= "  WHERE id_proveedor 		<> $id "; 		
			$sql.= " 	AND tx_proveedor_corto	= '$tx_proveedor_corto'  ";
			
			//echo "sql",	$sql;
			
			$result = mysqli_query($mysql, $sql);
			$row = mysqli_fetch_row($result);
			$count = $row[0];	
			
			if ($count > 0)	{	
				$data = array("error" => true, "message" => "El Proveedor $tx_proveedor ya existe!</br></br>Por favor verifique ... " );				
				echo json_encode($data);
			} else {  
			
				$fh_mod=date("Y-m-j, g:i");
				$id_usuariomod=$id_login;
			
				$sql = " UPDATE tbl_proveedor SET " ; 
				$sql.= " 	tx_proveedor		= '$tx_proveedor', ";
				$sql.= " 	tx_proveedor_corto	= '$tx_proveedor_corto', ";
				$sql.= " 	tx_rfc				= '$tx_rfc', ";
				$sql.= " 	tx_descripcion		= '$tx_descripcion', ";
				$sql.= " 	tx_contrato			= '$tx_contrato', ";
				$sql.= " 	tx_extranjero		= '$tx_extranjero', ";
				$sql.= " 	tx_iva				= '$tx_iva', ";				
				$sql.= " 	tx_gps				= '$tx_gps', ";				
				$sql.= " 	tx_direccion		= '$tx_direccion', ";
				$sql.= " 	tx_pagina			= '$tx_pagina', ";
				$sql.= " 	tx_fax				= '$tx_fax', ";
				$sql.= " 	tx_contacto1		= '$tx_contacto1', ";
				$sql.= " 	tx_puesto1			= '$tx_puesto1', ";
				$sql.= " 	tx_telefono1		= '$tx_telefono1', ";
				$sql.= " 	tx_celular1			= '$tx_celular1', ";
				$sql.= " 	tx_correo1			= '$tx_correo1', ";
				$sql.= " 	tx_contacto2		= '$tx_contacto2', ";
				$sql.= " 	tx_puesto2			= '$tx_puesto2', ";
				$sql.= " 	tx_telefono2		= '$tx_telefono2', ";
				$sql.= " 	tx_celular2			= '$tx_celular2', ";
				$sql.= " 	tx_correo2			= '$tx_correo2', ";
				$sql.= " 	tx_indicador		= '$tx_indicador', ";
				$sql.= " 	fh_mod 				= '$fh_mod', ";
				$sql.= " 	id_usuariomod		= '$id_usuariomod' "; 
				$sql.= " WHERE id_proveedor		= $id ";
					   
				//echo "aaa", $sql;      
				$myBitacora = new Bitacora();
				$valores=$myBitacora->obtenvalores ($mysql, "TBL_PROVEEDOR" , $id);
	
	
				  
				if (mysqli_query($mysql, $sql))
				{
		
					$myBitacora->anotaBitacora ($mysql, "MODIFICACION" , "TBL_PROVEEDOR" , "$id_login" ,  $valores, "$id"  ,  "process_proveedores.php");
		
					$data = array("error" => false, "message" => "El registro se ACTUALIZO correctamente" );							
					echo json_encode($data);
				} else {  
					$data = array("error" => true, "message" => "ERROR al ACTUALIZAR el registro. </br></br>Por favor verifique ..." );				
					echo json_encode($data);
				}	
			}		
		}	
		mysqli_free_result($result);
	} 
	
	else if ($dispatch=='find') {	
	
		$auto = '%'.$q.'%';	
	
		if ($campo=='proveedor') {
			
			$sql = " SELECT tx_proveedor_corto ";
			$sql.= "   FROM tbl_proveedor ";
			$sql.= "  WHERE tx_proveedor_corto like '$auto' ";
			$sql.= "  GROUP BY tx_proveedor_corto ";
			
			//echo "sql",$sql;
			
			$result = mysqli_query($mysql, $sql);	
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{	
				$TheCatalogo[] = array(				
					'tx_proveedor_corto'=>$row["tx_proveedor_corto"]
				);
			} 	
			
			for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_proveedor_corto	=$TheCatalogo[$i]['tx_proveedor_corto'];	
				echo $row[0], "|", $row[1], "\n";
				echo $tx_proveedor_corto;
			}	
		}
	}

	
	// BORRA
	// ============================
	else if ($dispatch=='delete') {		
		
		//$sql = " SELECT * ";
		//$sql.= "   FROM tbl_proveedor a, tbl_estado b ";
		//$sql.= "  WHERE a.id_proveedor = $id "; 
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
		//	$data = array("error" => true, "message" => "La ubicaci&oacute;n de esta relacionado al cat&aacute;logo de proveedores, no es posible eliminarlo ... " );				
		//	echo json_encode($data);			
		//} else {  
		
			$sql = " UPDATE  tbl_proveedor  set tx_indicador='0'  ";
			$sql.= "  WHERE id_proveedor = $id ";
				
			//echo "aaa", $sql; 
				
			if (mysqli_query($mysql, $sql)) 
			{
			
			//<BITACORA>
	 	$myBitacora = new Bitacora();
		$myBitacora->anotaBitacora ($mysql, "BAJA" , "TBL_PROVEEDOR" , "$id_login" ,  "", "$id"  ,  "process_proveedores.php");
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
		
		$sql = "   SELECT id_proveedor, id_proveedor, tx_proveedor, tx_proveedor_corto, tx_rfc, tx_descripcion, tx_contrato, tx_extranjero, tx_iva, tx_gps, tx_direccion, tx_pagina, tx_fax, tx_contacto1, tx_puesto1, tx_telefono1, tx_celular1, tx_correo1, tx_contacto2, tx_puesto2, tx_telefono2, tx_celular2, tx_correo2, a.tx_indicador, a.fh_mod, b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta " ; 
		$sql.= "     FROM tbl_proveedor a, tbl_usuario b, tbl_usuario c ";
		$sql.= "    WHERE a.id_usuariomod 	= b.id_usuario ";
		$sql.= "      AND a.id_usuarioalta 	= c.id_usuario ".$wh ; 
		$sql.= " ORDER BY $sidx $sord " ;
		$sql.= " 	LIMIT $start, $limit " ;	
			
		//echo "sql",	$sql;
		
		$result = mysqli_query($mysql,$sql); 
		
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
			$responce->rows[$i]['id']=$row[id_proveedor];
			$responce->rows[$i]['cell']=array($row[id_proveedor],$row[id_proveedor],$row[tx_proveedor],$row[tx_proveedor_corto],$row[tx_rfc],$row[tx_descripcion],$row[tx_contrato],$row[tx_extranjero],$row[tx_iva],$row[tx_gps],$row[tx_direccion],$row[tx_pagina],$row[tx_fax],$row[tx_contacto1],$row[tx_puesto1],$row[tx_telefono1],$row[tx_celular1],$row[tx_correo1],$row[tx_contacto2],$row[tx_puesto2],$row[tx_telefono2],$row[tx_celular2],$row[tx_correo2],$row[tx_indicador],$row[fh_mod],$row[usuario_mod],$row[fh_alta],$row[usuario_alta]);
			$i++;
		} 	
		
		//<BITACORA>
	 	$myBitacora = new Bitacora();
	 	$whr =str_ireplace("'", " " , $wh); 
		$myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_PROVEEDOR" , "$id_login" ,  $whr, ""  ,  "process_proveedores.php");
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