<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
	<script type="text/javascript">			
	 
		$("#verFacturasDetalleCr").find("tr").hover(		 
        	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
         );
	</script> 
<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	// ============================
	// Recibo variables
	// ============================
	$id_factura	= $_GET['id']; 		
	
	$sql = "   SELECT tx_centro_costos, tx_nombre, tx_subdireccion, tx_departamento, COUNT(id_factura_detalle) AS tx_usuario, SUM( fl_precio_usd ) AS fl_precio_usd, SUM( fl_precio_mxn ) AS fl_precio_mxn ";
	$sql.= "   	 FROM tbl_factura_detalle a, tbl_centro_costos b, tbl_direccion c, tbl_subdireccion d, tbl_departamento e ";
	$sql.= "    WHERE id_factura =$id_factura ";
	$sql.= "      AND a.id_centro_costos = b.id_centro_costos ";
	$sql.= "      AND b.id_direccion = c.id_direccion ";
	$sql.= "      AND b.id_subdireccion = d.id_subdireccion ";
	$sql.= "      AND b.id_departamento = e.id_departamento ";
	$sql.= " GROUP BY tx_centro_costos ";

	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			'tx_centro_costos'	=>$row["tx_centro_costos"],
			'tx_direccion'		=>$row["tx_nombre"],
	  		'tx_subdireccion'	=>$row["tx_subdireccion"],
  			'tx_departamento'	=>$row["tx_departamento"],
  			'tx_usuario'		=>$row["tx_usuario"],
	  		'fl_precio_usd'		=>$row["fl_precio_usd"],
	  		'fl_precio_mxn'		=>$row["fl_precio_mxn"]
		);
	} 	
	
	$registros=count($TheCatalogo);	
	?>
    <br/>    
	<?
	if ($registros==0) { }
	else {
		echo "<table id='verFacturasDetalleCr' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<8; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					echo "<td width='2%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='CR'; 
					echo "<td width='5%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Nivel 1 - Direcci&oacute;n';
					echo "<td width='22%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='Nivel 2 - Subdirecci&oacute;n';
					echo "<td width='23%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 4 : $TheField='Nivel 3 - Departamento'  ;
					echo "<td width='27%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 5 : $TheField='Usuarios';
					echo "<td width='5%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField='Monto USD';
					echo "<td width='8%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 7 : $TheField='Monto MXN';
					echo "<td width='8%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		
		$j=0;		
		$tx_total_usuario=0;	
		$fl_total_precio_usd=0;	
		$fl_total_precio_mxn=0;	
		
		for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$tx_centro_costos	=$TheCatalogo[$i]['tx_centro_costos'];
				$tx_direccion		=$TheCatalogo[$i]['tx_direccion'];
				$tx_subdireccion	=$TheCatalogo[$i]['tx_subdireccion'];
				$tx_departamento	=$TheCatalogo[$i]['tx_departamento'];
				$tx_usuario			=$TheCatalogo[$i]['tx_usuario'];
				$fl_precio_usd		=$TheCatalogo[$i]['fl_precio_usd'];
				$fl_precio_mxn		=$TheCatalogo[$i]['fl_precio_mxn'];
				
				$j++;				

				$tx_total_usuario=$tx_total_usuario+$tx_usuario;
				$fl_total_precio_usd=$fl_total_precio_usd+$fl_precio_usd;
				$fl_total_precio_mxn=$fl_total_precio_mxn+$fl_precio_mxn;
				
				for ($a=0; $a<8; $a++)
					{
						switch ($a) 
						{   
							case 0: $TheColumn=$j; 									
									echo "<td class='align-center'>$TheColumn</td>";
							break;						
							case 1: $TheColumn=$tx_centro_costos;
									echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 2: $TheColumn=$tx_direccion;
									echo "<td class='align-left'>$TheColumn</td>";
							break;
							case 3: $TheColumn=$tx_subdireccion;
									echo "<td class='align-left'>$TheColumn</td>";
							break;
							case 4: $TheColumn=$tx_departamento;
									echo "<td class='align-left'>$TheColumn</td>";
							break;
							case 5: $TheColumn=$tx_usuario;
									echo "<td class='align-center'>$TheColumn</td>";
							break;
							case 6: $TheColumn=number_format($fl_precio_usd,0); 
									if($TheColumn=="0") echo "<td class='align-right'>-</td>";	
									else echo "<td class='align-right'>$TheColumn</td>";
							break;
							case 7: $TheColumn=number_format($fl_precio_mxn,0); 
									if($TheColumn=="0") echo "<td class='align-right'>-</td>";	
									else echo "<td class='align-right'>$TheColumn</td>";
							break;
						}							
					}				
					echo "</tr>";					
					}	
					echo "<tr>";								  
					for ($a=0; $a<8; $a++)
					{
						switch ($a) 
						{   
							case 0 : $TheField='Total Derrama por CR';
									echo "<td colspan='5' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
							break;
							case 5 : $TheField=number_format($tx_total_usuario,0); 
									 if($TheField=="0") echo "<td class='ui-state-highlight align-center'>-</td>";		
									 else echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
							break;
							case 6 : $TheField=number_format($fl_total_precio_usd,0); 
									 if($TheField=="0") echo "<td class='ui-state-highlight align-right'>-</td>";		
									 else echo "<td class='ui-state-highlight align-right'>$TheField</td>";						 
							break;
							case 7 : $TheField=number_format($fl_total_precio_mxn,0); 
									 if($TheField=="0") echo "<td class='ui-state-highlight align-right'>-</td>";		
									 else echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
							break;
						}							
					}	
					echo "</tr>";	
					echo "<tr>";								  
					for ($a=0; $a<8; $a++)
					{
						switch ($a) 
						{   
							case 0 : $TheField='Total Factura';
									echo "<td colspan='5' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
							break;
							case 5 : $TheField=number_format($tx_total_usuario,0); 
									 if($TheField=="0") echo "<td class='ui-state-highlight align-center'>-</td>";		
									 else echo "<td class='ui-state-highlight align-center'>$TheField</td>";						 
							break;
							case 6 : $TheField=number_format($fl_precio_usd_cabecera,0); 
									 if($TheField=="0") echo "<td class='ui-state-highlight align-right'>-</td>";		
									 else echo "<td class='ui-state-highlight align-right'>$TheField</td>";						 
							break;
							case 7 : $TheField=number_format($fl_precio_mxn_cabecera,0); 
									 if($TheField=="0") echo "<td class='ui-state-highlight align-right'>-</td>";		
									 else echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
							break;						
						}							
					}	
					echo "</tr>";	
					echo "<tr>";								  
					for ($a=0; $a<8; $a++)
					{
						switch ($a) 
						{   
							case 0 : $TheField='Diferencia';
									echo "<td colspan='5' class='ui-state-default align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
							break;
							case 5 : $TheField="-"; 
									 echo "<td class='ui-state-default align-center'>$TheField</td>";						 
							break;
							case 6 : $fl_diferencia_usd=$fl_precio_usd_cabecera-$fl_total_precio_usd;
									 $TheField=number_format($fl_diferencia_usd,0); 
									 if($TheField=="0") echo "<td class='ui-state-default align-right'>-</td>";		
									 else echo "<td class='ui-state-default align-right'>$TheField</td>";						 
							break;
							case 7 : $fl_diferencia_mxn=$fl_precio_mxn_cabecera-$fl_total_precio_mxn;
									 $TheField=number_format($fl_diferencia_mxn,0); 
									 if($TheField=="0") echo "<td class='ui-state-default align-right'>-</td>";		
									 else echo "<td class='ui-state-default align-right'>$TheField</td>";					
							break;
						}							
					}	
					echo "</tr>";	
				echo "</table>";
			}
	?>
    <br/>
<?
mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      