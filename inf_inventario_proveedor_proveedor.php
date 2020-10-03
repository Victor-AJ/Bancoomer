<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	$id_login = $_SESSION['sess_iduser'];
?>
	<script type="text/javascript">			
		 
		 $("#tablaInventarioProveedorN1").find("tr").hover(		 
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

	if (is_null($id_direccion)) $id_direccion=0; 
		
	if ($id_direccion==0) 
	{
			
		$sql = " SELECT b.id_cuenta, tx_cuenta, SUM( in_licencia ) AS in_licencia, SUM( fl_precio ) AS fl_precio_usd, SUM( d.fl_precio_mxn ) AS fl_precio_mxn ";
		$sql.= "    FROM tbl_proveedor a, tbl_cuenta b, tbl_licencia c, tbl_producto d, tbl_empleado e, tbl_centro_costos f "; 
		$sql.= "   WHERE a.id_proveedor	= $id_proveedor and a.tx_indicador='1' ";
		$sql.= "     AND a.id_proveedor = b.id_proveedor  and b.tx_indicador='1' ";
		$sql.= "     AND b.id_cuenta 	= c.id_cuenta  and c.tx_indicador='1' ";
		$sql.= "     AND c.id_producto 	= d.id_producto  and d.tx_indicador='1' ";
		$sql.= "     AND c.id_empleado 	= e.id_empleado ";
		$sql.= "     AND e.id_centro_costos = f.id_centro_costos  and f.tx_indicador='1' ";
		$sql.= "   AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
			
		$sql.= "GROUP BY b.id_cuenta ";
		$sql.= "ORDER BY fl_precio_usd DESC, fl_precio_mxn DESC, in_licencia DESC, tx_cuenta ";						
		
	} else {	
		
		$sql = " SELECT b.id_cuenta, tx_cuenta, SUM( in_licencia ) AS in_licencia, SUM( fl_precio ) AS fl_precio_usd, SUM( d.fl_precio_mxn ) AS fl_precio_mxn ";
		$sql.= "    FROM tbl_proveedor a, tbl_cuenta b, tbl_licencia c, tbl_producto d, tbl_empleado e, tbl_centro_costos f ";
		$sql.= "   WHERE a.id_proveedor 	= $id_proveedor  and a.tx_indicador='1' ";
		$sql.= "     AND a.id_proveedor 	= b.id_proveedor and b.tx_indicador='1' ";
		$sql.= "     AND b.id_cuenta 		= c.id_cuenta and c.tx_indicador='1'  ";
		$sql.= "     AND c.id_producto 		= d.id_producto and d.tx_indicador='1' ";
		$sql.= "     AND c.id_empleado 		= e.id_empleado ";
		$sql.= "     AND e.id_centro_costos = f.id_centro_costos and f.tx_indicador='1'  ";
		$sql.= "   AND f.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	
		$sql.= "     AND f.id_direccion 	= $id_direccion ";
		$sql.= "GROUP BY b.id_cuenta ";
		$sql.= "ORDER BY fl_precio_usd DESC, fl_precio_mxn DESC, in_licencia DESC, tx_cuenta ";		
		
	}
	
	//echo "<br>";
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeProveedor[] = array(
			'id_cuenta'		=>$row["id_cuenta"],
			'tx_cuenta'		=>$row["tx_cuenta"],
	  		'in_licencia'	=>$row["in_licencia"],
	  		'fl_precio_usd'	=>$row["fl_precio_usd"],
	  		'fl_precio_mxn'	=>$row["fl_precio_mxn"],
		);
	} 	
	
	$registros=count($TheInformeProveedor);	
	
	$sql = " SELECT tx_proveedor_corto ";
	$sql.= "   FROM tbl_proveedor ";
	$sql.= "  WHERE id_proveedor = $id_proveedor ";

	//echo "aaa",$sql;			
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoProveedor[] = array(			
	  		'tx_proveedor_corto'	=>$row["tx_proveedor_corto"]
		);
	} 	
	
	for ($i=0; $i < count($TheCatalogoProveedor); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogoProveedor[$i]))					  		
			$tx_proveedor_corto	=$TheCatalogoProveedor[$i]['tx_proveedor_corto'];		
	}		
	
	echo "<br>";
	echo "<div class='ui-widget-header align-center'>INVENTARIO DE $tx_proveedor_corto POR CUENTA</div>";	
	if ($registros==0) { 
	
		echo "<table id='tablaInventarioProveedorN1' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		echo "<td class='align-center'>Sin informaci&oacute;n ...</td>";		
		echo "</tr>";					
		echo "</table>";
		
	} else {	

		echo "<table id='tablaInventarioProveedorN1' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<5; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='Cuenta'; 
					echo "<td width='64%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Licencias';
					echo "<td width='11%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='Monto USD';
					echo "<td width='11%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 4 : $TheField='Monto MXN';
					echo "<td width='11%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		
		$j=0;		
		$in_total_licencias=0;	
		$fl_total_precio_usd=0;	
		$fl_total_precio_mxn=0;	
		
		for ($i=0; $i < count($TheInformeProveedor); $i++)	{ 	        			 
			while ($elemento = each($TheInformeProveedor[$i]))					  		
				$id_cuenta	=$TheInformeProveedor[$i]['id_cuenta'];
				$tx_cuenta		=$TheInformeProveedor[$i]['tx_cuenta'];
				$in_licencia	=$TheInformeProveedor[$i]['in_licencia'];
				$fl_precio_usd	=$TheInformeProveedor[$i]['fl_precio_usd'];
				$fl_precio_mxn	=$TheInformeProveedor[$i]['fl_precio_mxn'];				
				$j++;
				
				$in_total_licencias	=$in_total_licencias+$in_licencia;
				$fl_total_precio_usd=$fl_total_precio_usd+$fl_precio_usd;
				$fl_total_precio_mxn=$fl_total_precio_mxn+$fl_precio_mxn;
				
				for ($a=0; $a<5; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$j; 
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;						
						case 1: $TheColumn=$tx_cuenta;
								echo "<td class='align-left' valign='top'>$TheColumn</td>";
						break;
						case 2: $TheColumn = "<a href='#' onclick='javascript:btnInformesProveedorLicencias($id_proveedor, $id_direccion, $id_cuenta)' title='Presione para ver el detalle de las Licencias de la cuenta $tx_cuenta ...';>$in_licencia</a>";
								echo "<td class='align-center' valign='top'>$TheColumn</td>";
						break;
						case 3: $TheColumn=number_format($fl_precio_usd,0); 
								echo "<td class='align-right' valign='top'>$TheColumn</td>";
						break;
						case 4: $TheColumn=number_format($fl_precio_mxn,0); 
								echo "<td class='align-right' valign='top'>$TheColumn</td>";
						break;					
					}							
				}				
				echo "</tr>";					
			}	
			echo "<tr>";								  
			for ($a=0; $a<5; $a++)
			{
				switch ($a) 
				{   
					case 0 : $TheField='Totales';
						echo "<td colspan='2' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
					break;
					case 2 : $TheField=number_format($in_total_licencias,0); 	
						echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
					break;
					case 3 : $TheField=number_format($fl_total_precio_usd,0); 
						echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;
					case 4 : $TheField=number_format($fl_total_precio_mxn,0);  
						echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
					break;
				}							
			}	
			echo "</tr>";	
			echo "<tr>";
			for ($a=0; $a<5; $a++)
			{
				switch ($a) 
				{   
					case 0 : $TheField='#';
						echo "<td class='ui-state-highlight align-center'>$TheField</td>";							 
					break;
					case 1 : $TheField='Cuenta'; 
						echo "<td class='ui-state-highlight align-center'>$TheField</td>";					
					break;
					case 2 : $TheField='Licencias';
						echo "<td class='ui-state-highlight align-center'>$TheField</td>";	
					break;
					case 3 : $TheField='Monto USD';
						echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
					break;
					case 4 : $TheField='Monto MXN';
						echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
					break;
				}							
			}	
			echo "</tr>";	
			echo "<tr>";			
			echo "<td colspan='5' align='left'>";	
			echo "<ul type='square'> ";
			echo "<li>Licencias calculadas en base al Inventario.</em></li>";
			echo "<li>Monto calculado en base al Inventario.</li>";
			echo "</ul>";      
			echo "</td>";
			echo "</tr>";			
		echo "</table>";	
	}
	
		
	
	
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_LICENCIA" , "$id_login" ,   "id_direccion=$id_direccion id_proveedor=$id_proveedor"  ,"" ,"inf_inventario_proveedor_proveedor.php");
	 //<\BITACORA>
	 
	 
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>
