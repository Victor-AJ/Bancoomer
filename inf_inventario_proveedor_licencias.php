<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
		$id_login = $_SESSION['sess_iduser'];
		
?>
	<script type="text/javascript">		
		 
		 $("#tablaInventarioProveedorN2").find("tr").hover(		 
         	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
         );		 
		 
	</script> 
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");	
		
	$mysql=conexion_db();	
	
	$id_proveedor	= $_GET['id_proveedor'];	
	$id_direccion	= $_GET['id_direccion'];		
	$id_cuenta		= $_GET['id_cuenta'];		

	if (is_null($id_direccion)) $id_direccion=0; 
		
	if ($id_direccion==0) 
	{
		if ($id_cuenta==0) 
		{	
		
			$sql = " SELECT c.id_empleado, f.id_direccion, f.tx_nombre, tx_registro, tx_empleado, tx_producto, tx_sid_terminal, in_licencia, fl_precio as fl_precio_usd, d.fl_precio_mxn, c.tx_indicador ";
			$sql.= "    FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f  ";
			$sql.= "   WHERE a.id_proveedor		= $id_proveedor and a.tx_indicador='1'   ";
			$sql.= "     AND a.id_proveedor		= d.id_proveedor and d.tx_indicador='1' ";
			$sql.= "     AND b.id_empleado 		= c.id_empleado and b.tx_indicador='1' ";
			$sql.= "     AND b.id_producto 		= d.id_producto ";
			$sql.= "     AND c.id_centro_costos = e.id_centro_costos and e.tx_indicador='1' ";
			$sql.= "     AND e.id_direccion 	= f.id_direccion and f.tx_indicador='1' ";
			$sql.= "   AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	
			$sql.= "ORDER BY f.tx_nombre, tx_empleado, tx_empleado, in_licencia ";						
			
		} else {	
			
			$sql = " SELECT c.id_empleado, f.id_direccion, f.tx_nombre, tx_registro, tx_empleado, tx_producto, tx_sid_terminal, in_licencia, fl_precio as fl_precio_usd, d.fl_precio_mxn, c.tx_indicador ";
			$sql.= "    FROM tbl_cuenta a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f  ";
			$sql.= "   WHERE a.id_cuenta 		= $id_cuenta and a.tx_indicador='1' ";
			$sql.= "     AND a.id_cuenta 		= b.id_cuenta and b.tx_indicador='1'";
			$sql.= "     AND b.id_empleado 		= c.id_empleado ";
			$sql.= "     AND b.id_producto 		= d.id_producto and d.tx_indicador='1'";
			$sql.= "     AND c.id_centro_costos = e.id_centro_costos and e.tx_indicador='1'";
			$sql.= "     AND e.id_direccion 	= f.id_direccion and f.tx_indicador='1'";
			$sql.= "   AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
			$sql.= "ORDER BY f.tx_nombre, tx_empleado, tx_empleado, in_licencia ";								
		}	
		
	} else {	
		if ($id_cuenta==0) 
		{	
		
			$sql = " SELECT c.id_empleado, f.id_direccion, f.tx_nombre, tx_registro, tx_empleado, tx_producto, tx_sid_terminal, in_licencia, fl_precio as fl_precio_usd, d.fl_precio_mxn, c.tx_indicador ";
			$sql.= "    FROM tbl_proveedor a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f ";			
			$sql.= "   WHERE a.id_proveedor		= $id_proveedor and a.tx_indicador='1' ";
			$sql.= "     AND a.id_proveedor		= d.id_proveedor and d.tx_indicador='1' ";
			$sql.= "     AND b.id_empleado 		= c.id_empleado and b.tx_indicador='1' ";
			$sql.= "     AND b.id_producto 		= d.id_producto ";
			$sql.= "     AND c.id_centro_costos = e.id_centro_costos ";
			$sql.= "     AND e.id_direccion 	= $id_direccion and e.tx_indicador='1' ";
			$sql.= "     AND e.id_direccion 	= f.id_direccion and f.tx_indicador='1' ";
			$sql.= "   AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
			$sql.= "ORDER BY f.tx_nombre, tx_empleado, tx_empleado, in_licencia ";			
			
		} else {
		
			$sql = " SELECT c.id_empleado, f.id_direccion, f.tx_nombre, tx_registro, tx_empleado, tx_producto, tx_sid_terminal, in_licencia, fl_precio as fl_precio_usd, d.fl_precio_mxn, c.tx_indicador ";
			$sql.= "    FROM tbl_cuenta a, tbl_licencia b, tbl_empleado c, tbl_producto d, tbl_centro_costos e, tbl_direccion f ";
			$sql.= "   WHERE a.id_cuenta 		= $id_cuenta  and a.tx_indicador='1' ";
			$sql.= "     AND a.id_cuenta 		= b.id_cuenta and b.tx_indicador='1' ";
			$sql.= "     AND b.id_empleado 		= c.id_empleado ";
			$sql.= "     AND b.id_producto 		= d.id_producto and d.tx_indicador='1'";
			$sql.= "     AND c.id_centro_costos = e.id_centro_costos ";
			$sql.= "     AND e.id_direccion 	= $id_direccion and e.tx_indicador='1' ";
			$sql.= "     AND e.id_direccion 	= f.id_direccion  and f.tx_indicador='1' ";
			$sql.= "   AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";			
			$sql.= "ORDER BY f.tx_nombre, tx_empleado, tx_empleado, in_licencia ";			
		
		}		
	}
	
	//echo "<br>";
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeEmpleado[] = array(
			'id_empleado'		=>$row["id_empleado"],
			'id_direccion'		=>$row["id_direccion"],
			'tx_direccion'		=>$row["tx_nombre"],
			'tx_registro'		=>$row["tx_registro"],
			'tx_empleado'		=>$row["tx_empleado"],
			'tx_producto'		=>$row["tx_producto"],
			'tx_sid_terminal'	=>$row["tx_sid_terminal"],
	  		'in_licencia'		=>$row["in_licencia"],
	  		'fl_precio_usd'		=>$row["fl_precio_usd"],
	  		'fl_precio_mxn'		=>$row["fl_precio_mxn"],
			'tx_indicador'		=>$row["tx_indicador"]
			
		);
	} 	
	
	$registros=count($TheInformeEmpleado);	
	
	if ($id_cuenta==0) 
	{	
		$sql = " SELECT tx_proveedor_corto, tx_cuenta ";
		$sql.= "    FROM tbl_proveedor a, tbl_cuenta b ";
		$sql.= "   WHERE a.id_proveedor	= $id_proveedor ";
		$sql.= "     AND a.id_proveedor = b.id_proveedor ";
	} else {
		$sql = " SELECT tx_proveedor_corto, tx_cuenta ";
		$sql.= "    FROM tbl_proveedor a, tbl_cuenta b ";
		$sql.= "   WHERE a.id_proveedor	= $id_proveedor ";
		$sql.= "     AND a.id_proveedor = b.id_proveedor ";
		$sql.= "     AND b.id_cuenta 	= $id_cuenta ";
	}
	//echo "aaa",$sql;			
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoProveedorCuenta[] = array(			
	  		'tx_proveedor_corto'=>$row["tx_proveedor_corto"],
			'tx_cuenta'			=>$row["tx_cuenta"]
		);
	} 	
	
	for ($i=0; $i < count($TheCatalogoProveedorCuenta); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogoProveedorCuenta[$i]))					  		
			$tx_proveedor_corto	=$TheCatalogoProveedorCuenta[$i]['tx_proveedor_corto'];		
			$tx_cuenta			=$TheCatalogoProveedorCuenta[$i]['tx_cuenta'];		
	}		
	
	echo "<br/>";
	if ($id_cuenta==0) echo "<div class='ui-widget-header align-center'>INVENTARIO DE $tx_proveedor_corto</div>";	
	else echo "<div class='ui-widget-header align-center'>INVENTARIO DE $tx_proveedor_corto DE LA CUENTA $tx_cuenta</div>";	
	if ($registros==0) { 
	
		echo "<table id='tablaInventarioProveedorN2' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		echo "<td class='align-center'>Sin informaci&oacute;n ...</td>";		
		echo "</tr>";					
		echo "</table>";
		
	} else {	
	
		echo "<table id='tablaInventarioProveedorN2' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<9; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='-'; 
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Registro'; 
					echo "<td width='7%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 3 : $TheField='Nombre'; 
					echo "<td width='26%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 4 : $TheField='Producto'; 
					echo "<td width='26%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 5 : $TheField='SID Terminal'; 
					echo "<td width='8%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 6 : $TheField='Licencias';
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 7 : $TheField='Monto USD';
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 8 : $TheField='Monto MXN';
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		
		$j=0;		
		$in_total_licencias=0;	
		$fl_total_precio_usd=0;	
		$fl_total_precio_mxn=0;	
		
		for ($i=0; $i < count($TheInformeEmpleado); $i++)	{ 	        			 
			while ($elemento = each($TheInformeEmpleado[$i]))					  		
				$id_empleado	=$TheInformeEmpleado[$i]['id_empleado'];
				$id_direccion	=$TheInformeEmpleado[$i]['id_direccion'];
				$tx_direccion	=$TheInformeEmpleado[$i]['tx_direccion'];
				$tx_registro	=$TheInformeEmpleado[$i]['tx_registro'];
				$tx_empleado	=$TheInformeEmpleado[$i]['tx_empleado'];
				$tx_producto	=$TheInformeEmpleado[$i]['tx_producto'];
				$tx_sid_terminal=$TheInformeEmpleado[$i]['tx_sid_terminal'];
				$in_licencia	=$TheInformeEmpleado[$i]['in_licencia'];
				$fl_precio_usd	=$TheInformeEmpleado[$i]['fl_precio_usd'];
				$fl_precio_mxn	=$TheInformeEmpleado[$i]['fl_precio_mxn'];				
				$tx_indicador	=$TheInformeEmpleado[$i]['tx_indicador'];	
							
				$j++;
				
				$in_total_licencias	=$in_total_licencias+$in_licencia;
				$fl_total_precio_usd=$fl_total_precio_usd+$fl_precio_usd;
				$fl_total_precio_mxn=$fl_total_precio_mxn+$fl_precio_mxn;
				
				if ($i==0)				
				{
					echo "<tr>";							
					echo "<td class='ui-state-default' colspan='9'><em>$tx_direccion</em></td>";						
					echo "</tr>";	
				}

				if ($i>0)
				{	
					if ($id_direccion_tmp==$id_direccion) { }
					else
					{
						echo "<tr>";							
						echo "<td class='ui-state-default' colspan='9'><em>$tx_direccion</em></td>";						
						echo "</tr>";	
						$id_direccion_tmp=$id_direccion;
					}
				} else {				
					$id_direccion_tmp=$id_direccion;
				}					
				
				for ($a=0; $a<9; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$j; 
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;		
						case 1: if ($tx_indicador=="0") $TheColumn="<img src='images/redball.png' alt='$tx_notas'/>"; 
								else $TheColumn="<img src='images/greenball.png'/>";
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;				
						case 2:	$TheColumn="<a href='#' onclick='javascript:Ventana($id_empleado)' title='Presione para ver el Detalle de $tx_empleado ...'>$tx_registro</a>";	
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;
						case 3:	$TheColumn=$tx_empleado;	
								echo "<td class='align-left' valign='top'>$TheColumn</td>";
						break;
						case 4: $TheColumn=$tx_producto;
								echo "<td class='align-left' valign='top'>$TheColumn</td>";
						break;
						case 5: if ($tx_sid_terminal=="") $tx_sid_terminal="-";
								$TheColumn=$tx_sid_terminal;
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;
						case 6: $TheColumn=$in_licencia;
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;
						case 7: $TheColumn=number_format($fl_precio_usd,0); 
								echo "<td class='align-right' valign='top'>$TheColumn</td>";
						break;
						case 8: $TheColumn=number_format($fl_precio_mxn,0); 
								echo "<td class='align-right' valign='top'>$TheColumn</td>";
						break;
					}							
				}				
		echo "</tr>";					
		}	
		echo "<tr>";								  
		for ($a=0; $a<9; $a++)
		{
			switch ($a) 
			{   
				case 0: $TheField='Totales';
					echo "<td colspan='6' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
				break;
				case 5: $TheField=number_format($in_total_licencias,0); 	
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6: $TheField=number_format($fl_total_precio_usd,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 7: $TheField=number_format($fl_total_precio_mxn,0);  
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		for ($a=0; $a<9; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='-'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Registro'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 3 : $TheField='Nombre'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 4 : $TheField='Producto'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 5 : $TheField='SID Terminal'; 
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 6 : $TheField='Licencias';
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 7 : $TheField='Monto USD';
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 8 : $TheField='Monto MXN';
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
			}							
		}	
		echo "</tr>";	
	echo "</table>";	
	}
	
	$valBita="id_proveedor=$id_proveedor "; 	
	$valBita.="id_direccion=$id_direccion ";		
	$valBita.="id_cuenta=$id_cuenta ";	
	
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_LICENCIA" , "$id_login" ,   $valBita  ,"" ,"inf_inventario_proveedor_licencias.php");
	 //<\BITACORA>
	 
	 
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      