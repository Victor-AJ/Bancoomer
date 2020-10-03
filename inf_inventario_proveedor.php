<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	$id_login = $_SESSION['sess_iduser'];
	
?>
	<script type="text/javascript">			
		 
		 $("#tablaInventarioProveedor").find("tr").hover(		 
         	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
         );		
		 
		 function btnInformesProveedorProveedor(valor0, valor1)  {
		
			var id0="id_proveedor="+valor0;
			var id1="&id_direccion="+valor1;
			//alert ("Entre 1"+id0+id1);
			$("#verInventarioProveedorN2").hide(); 
			
			loadHtmlAjax(true, $("#verInventarioProveedorN1"), "inf_inventario_proveedor_proveedor.php?"+id0+id1); 
		 } 
		
		 function btnInformesProveedorLicencias(valor0, valor1, valor2) {
		
			var id0="id_proveedor="+valor0;
			var id1="&id_direccion="+valor1;
			var id2="&id_cuenta="+valor2;
			//alert ("Entre 1"+id0+id1+id2);
			$("#verInventarioProveedorN2").hide();
			
			if (valor2==0) loadHtmlAjax(true, $("#verInventarioProveedorN1"), "inf_inventario_proveedor_licencias_matriz.php?"+id0+id1+id2); 
			else loadHtmlAjax(true, $("#verInventarioProveedorN2"), "inf_inventario_proveedor_licencias.php?"+id0+id1+id2); 
			
		 }

		 
	</script> 
<?
	include("includes/funciones.php");
	include_once  ("Bitacora.class.php");	  
	$mysql=conexion_db();	
	
	$id_direccion	= $_GET['id_direccion'];	
	
	if ($id_direccion==0) $dispatch="insert";
	else if (is_null($id_direccion)) { $dispatch="insert"; $id_direccion=0; }
	else $dispatch="save";
		
	if ($dispatch=="save") 
	{
			
		$sql = "  SELECT a.id_proveedor, tx_proveedor_corto, SUM( in_licencia ) AS in_licencia, SUM( b.fl_precio ) AS fl_precio_usd, SUM( b.fl_precio_mxn ) AS fl_precio_mxn ";
		$sql.= "    FROM tbl_proveedor a, tbl_producto b, tbl_licencia c, tbl_empleado d, tbl_centro_costos e ";
		$sql.= "   WHERE a.id_proveedor 	= b.id_proveedor  and a.tx_indicador='1'  and b.tx_indicador='1' ";
		$sql.= "     AND b.id_producto 		= c.id_producto and c.tx_indicador='1' ";
		$sql.= "     AND c.id_empleado 		= d.id_empleado ";
		$sql.= "     AND d.id_centro_costos = e.id_centro_costos and e.tx_indicador='1' ";
		$sql.= "     AND e.id_direccion 	= $id_direccion ";
		$sql.= "   AND e.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	
		$sql.= "GROUP BY a.id_proveedor ";
		$sql.= "ORDER BY fl_precio_usd DESC, fl_precio_usd DESC, in_licencia DESC, tx_proveedor_corto ";

	} else if ($dispatch=="insert")  {
	
		$sql = "  SELECT a.id_proveedor, tx_proveedor_corto, SUM( in_licencia ) AS in_licencia, SUM( b.fl_precio ) AS fl_precio_usd, SUM( b.fl_precio_mxn ) AS fl_precio_mxn ";
		$sql.= "    FROM tbl_proveedor a, tbl_producto b, tbl_licencia c, tbl_empleado d , tbl_centro_costos e";
		$sql.= "   WHERE a.id_proveedor	= b.id_proveedor and a.tx_indicador='1'  and b.tx_indicador='1' ";
		$sql.= "     AND b.id_producto 	= c.id_producto and c.tx_indicador='1' ";
		$sql.= "     AND c.id_empleado 	= d.id_empleado ";
		$sql.= "     AND d.id_centro_costos = e.id_centro_costos and e.tx_indicador='1' ";
		$sql.= "   AND e.id_direccion in (select id_direccion from tbl_perfil_direccion DIR where DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
		
		$sql.= "GROUP BY a.id_proveedor ";
		$sql.= "ORDER BY fl_precio_usd DESC, fl_precio_usd DESC, in_licencia DESC, tx_proveedor_corto ";
		
	}

	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheInformeProveedor[] = array(
			'id_proveedor'		=>$row["id_proveedor"],
			'id_licencia'		=>$row["id_licencia"],
			'tx_proveedor_corto'=>$row["tx_proveedor_corto"],
	  		'in_licencia'		=>$row["in_licencia"],
	  		'fl_precio_usd'		=>$row["fl_precio_usd"],
	  		'fl_precio_mxn'		=>$row["fl_precio_mxn"],
		);
	} 	
	
	$registros=count($TheInformeProveedor);	
	?>
	<div id="tablaInventarioProveedor"> 
	<?
	if ($registros==0) { }
	else {
		echo "<div class='ui-widget-header align-center'>INVENTARIO POR PROVEEDOR</div>";	
		echo "<table align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<5; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='Proveedor'; 
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
				$id_proveedor		=$TheInformeProveedor[$i]['id_proveedor'];	  		
				$in_licencia		=$TheInformeProveedor[$i]['in_licencia'];
				$tx_proveedor_corto	=$TheInformeProveedor[$i]['tx_proveedor_corto'];
				$in_licencia		=$TheInformeProveedor[$i]['in_licencia'];
				$fl_precio_usd		=$TheInformeProveedor[$i]['fl_precio_usd'];
				$fl_precio_mxn		=$TheInformeProveedor[$i]['fl_precio_mxn'];				
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
							case 1: $TheColumn = "<a href='#' onclick='javascript:btnInformesProveedorProveedor($id_proveedor, $id_direccion)' title='Presione para ver el detalle por cuenta de $tx_proveedor_corto ...';>$tx_proveedor_corto</a>";
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;														
							case 2: $TheColumn = "<a href='#' onclick='javascript:btnInformesProveedorLicencias($id_proveedor, $id_direccion, 0)' title='Presione para ver el detalle de las Licencias de la cuenta $tx_cuenta ...';>$in_licencia</a>";
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
				case 1 : $TheField='Proveedor'; 
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
		echo "<li>Licencias calculadas en base al Inventario.</em></li> ";
        echo "<li>Monto calculado en base al Inventario.</li> ";
        echo "</ul>";      
        echo "</td>";
		echo "</tr>";
	echo "</table>";
	
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_LICENCIA" , "$id_login" ,   "id_direccion=$id_direccion"  ,"" ,"inf_inventario_proveedor.php");
	 //<\BITACORA>
	 
	 
	?>
    </div>
    <div id="verInventarioProveedorN1"></div>
    <div id="verInventarioProveedorN2"></div>
    <?
	}
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      