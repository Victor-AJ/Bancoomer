<?

session_start();
if 	(isset($_SESSION['sess_user']))
{
$id_login = $_SESSION['sess_iduser']
?>
	<script type="text/javascript">
	
		$("#verLicencias").find("tr").hover(		 
         	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
        );		 
		 
	</script> 
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$id_login = $_SESSION['sess_iduser'];
	$mysql=conexion_db();
	
	$id	= $_GET['id']; 		
	//CAMBIO CUENTAS CONTABLES					
	$sql = "  SELECT id_licencia, tx_proveedor_corto, tx_producto, tx_cuenta, c.tx_descripcion, in_licencia, c.fl_precio, c.fl_precio_mxn, z.tx_valor AS  tx_concepto_contable , tx_login, tx_sid_terminal, tx_serial_number, a.tx_indicador ";
	$sql.= "    FROM tbl_licencia a  ";
	$sql.= "   INNER JOIN tbl_empleado b on a.id_empleado 	= b.id_empleado";
	$sql.= "   INNER JOIN tbl_producto c on a.id_producto 	= c.id_producto ";
	$sql.= "   INNER JOIN tbl_proveedor d on  c.id_proveedor = d.id_proveedor ";
	$sql.= "   INNER JOIN tbl_cuenta e  on a.id_cuenta 	= e.id_cuenta ";
	$sql.= " left outer join tbl45_catalogo_global z  on z.id= c.id_cuenta_contable ";
	$sql.= "   WHERE a.id_empleado	= $id   and a.tx_indicador = '1'   ";
	$sql.= " ORDER BY tx_proveedor_corto, tx_producto, tx_cuenta " ;	
	
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			'id_licencia'		=>$row["id_licencia"],
			'tx_proveedor_corto'=>$row["tx_proveedor_corto"],
	  		'tx_producto'		=>$row["tx_producto"],
	  		'tx_cuenta'			=>$row["tx_cuenta"],
	  		'tx_descripcion'	=>$row["tx_descripcion"],
	  		'in_licencia'		=>$row["in_licencia"],
	  		'fl_precio_usd'		=>$row["fl_precio"],
	  		'fl_precio_mxn'		=>$row["fl_precio_mxn"],
	  		'tx_moneda'			=>$row["tx_moneda"],
  			'tx_producto'		=>$row["tx_producto"],	  		
			'tx_concepto_contable'=>$row["tx_concepto_contable"],
			'tx_login'			=>$row["tx_login"],																																										
			'tx_sid_terminal'	=>$row["tx_sid_terminal"],																																										
			'tx_serial_number'	=>$row["tx_serial_number"],																																										
			'tx_indicador'		=>$row["tx_indicador"]
		);
	} 	
	
	$registros=count($TheCatalogo);	
	
	if ($registros==0) { }
	else {
		echo "<table id='verLicencias' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<12; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					echo "<td width='2%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='Proveedor'; 
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Cuenta';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='Producto';
					echo "<td width='16%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 4 : $TheField='Licencia';
					echo "<td width='5%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 5 : $TheField='Descripci&oacute;n';
					echo "<td width='20%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField='Precio USD';
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 7 : $TheField='Precio MXN'; 
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 8 : $TheField='Concepto Contable'; 
					echo "<td width='5%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 9 : $TheField='Login'; 
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 10 : $TheField='SID Terminal'; 
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 11 : $TheField='Serial Number'; 
					echo "<td width='6%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		
		$j=0;		
		$in_total_licencias=0;	
		$fl_total_precio_usd=0;	
		$fl_total_precio_mxn=0;	
		
		for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$id_licencia			=$TheCatalogo[$i]['id_licencia'];
				$tx_proveedor_corto		=$TheCatalogo[$i]['tx_proveedor_corto'];
				$tx_producto			=$TheCatalogo[$i]['tx_producto'];
				$tx_cuenta				=$TheCatalogo[$i]['tx_cuenta'];
				$tx_descripcion			=$TheCatalogo[$i]['tx_descripcion'];
				$in_licencia			=$TheCatalogo[$i]['in_licencia'];
				$fl_precio_usd			=$TheCatalogo[$i]['fl_precio_usd'];
				$fl_precio_mxn			=$TheCatalogo[$i]['fl_precio_mxn'];
				$tx_producto			=$TheCatalogo[$i]['tx_producto'];	  		
				$tx_concepto_contable	=$TheCatalogo[$i]['tx_concepto_contable'];
				$tx_login				=$TheCatalogo[$i]['tx_login'];																																										
				$tx_sid_terminal		=$TheCatalogo[$i]['tx_sid_terminal'];																																										
				$tx_serial_number		=$TheCatalogo[$i]['tx_serial_number'];																																										
				$tx_indicador			=$TheCatalogo[$i]['tx_indicador'];
				
				$j++;
				
				$in_total_licencias=$in_total_licencias+$in_licencia;
				$fl_total_precio_usd=$fl_total_precio_usd+$fl_precio_usd;
				$fl_total_precio_mxn=$fl_total_precio_mxn+$fl_precio_mxn;
				
				for ($a=0; $a<12; $a++)
					{
						switch ($a) 
						{   
							case 0: $TheColumn=$j; 
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;						
							case 1: $TheColumn=$tx_proveedor_corto;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 2: $TheColumn=$tx_cuenta;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 3: $TheColumn=$tx_producto;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 4: $TheColumn=$in_licencia;
									if($TheColumn=="") echo "<td class='align-right' valign='top'>&nbsp;</td>";			
									else echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;
							case 5: $TheColumn=$tx_descripcion;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 6: $TheColumn=number_format($fl_precio_usd,0); 
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;
							case 7: $TheColumn=number_format($fl_precio_mxn,0); 
									echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;
							case 8: $TheColumn=$tx_concepto_contable;
									if($TheColumn=="") echo "<td class='align-right' valign='top'>-</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";		
							break;
							case 9: $TheColumn=$tx_login;
									if($TheColumn=="") echo "<td class='align-right' valign='top'>-</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";							
							break;
							case 10: $TheColumn=$tx_sid_terminal;
									if($TheColumn=="") echo "<td class='align-right' valign='top'>-</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;
							case 11: $TheColumn=$tx_serial_number;								  
									if($TheColumn=="") echo "<td class='align-right' valign='top'>-</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";
							break;
						}							
					}				
					echo "</tr>";					
		}	
		echo "<tr>";								  
		for ($a=0; $a<12; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='Total';
					echo "<td colspan='4' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
				break;
				case 5 : $TheField=number_format($in_total_licencias,0); 	
					echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField=""; 
					echo "<td class='ui-state-highlight align-center'>&nbsp;</td>";					
				break;
				case 7 : $TheField=number_format($fl_total_precio_usd,0); 
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 8 : $TheField=number_format($fl_total_precio_mxn,0);  
					echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
				break;
				case 9 : $TheField=""; 
					echo "<td colspan='6' class='ui-state-highlight align-center'>&nbsp;</td>";					
				break;
			}							
		}	
		echo "</tr>";	
		echo "</table>";
	}
	
		//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_LICENCIA" , "$id_login" ,   "id_empleado=$id" ,"" ,"cat_licencias_lista_read.php");
	 //<\BITACORA>
	 
	
mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      