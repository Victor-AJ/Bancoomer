<?

session_start();
if 	(isset($_SESSION['sess_user']))
{
	$id_login = $_SESSION['sess_iduser'];
?>	
    <script type="text/javascript">		

		$("#verFacturasDetalle").find("tr").hover(		 
        	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
         );		
		 
	</script> 
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");	
	$mysql=conexion_db();
	
	# ============================
	# Recibo variables
	# ============================
	$id_factura	= $_GET['id']; 		
	//CAMBIO CUENTA CONTABLE
	$sql = "   SELECT a.id_factura_detalle, b.tx_registro, b.tx_empleado, tx_centro_costos, tx_producto, a.fl_precio_usd, a.fl_precio_mxn, a.fl_precio_eur, z.tx_valor as tx_concepto_contable, tx_cr_estado, tx_afectable, f.id_direccion, f.tx_nombre_corto, g.id_subdireccion, g.tx_subdireccion, h.id_departamento, h.tx_departamento ";
	$sql.= "     FROM tbl_factura_detalle a   ";
	$sql.= "   	INNER JOIN tbl_empleado b    on  a.id_empleado			= b.id_empleado ";
	$sql.= "   	INNER JOIN tbl_centro_costos c on ( a.id_centro_costos	= c.id_centro_costos and c.tx_indicador='1' )";
	$sql.= "   	INNER JOIN tbl_producto d  on  (a.id_producto			= d.id_producto  and d.tx_indicador='1' )";
	$sql.= "   	INNER JOIN  tbl_cr_estado e on  (c.id_cr_estado		= e.id_cr_estado  and e.tx_indicador='1' )";
	$sql.= "   	INNER JOIN  tbl_direccion f  on   (c.id_direccion 		= f.id_direccion and  f.tx_indicador='1'  )";
	$sql.= "   	INNER JOIN  tbl_subdireccion g  on (c.id_subdireccion 	= g.id_subdireccion   and g.tx_indicador='1'  )";
	$sql.= "   	INNER JOIN   tbl_departamento h  on ( c.id_departamento 	= h.id_departamento   and h.tx_indicador='1' ) ";
	$sql.= "   	LEFT OUTER  JOIN   tbl45_catalogo_global z on ( z.id = d.id_cuenta_contable  and z.tx_indicador='1' )";
	
	$sql.= "    WHERE a.id_factura 			= $id_factura  and a.tx_indicador='1' ";
	
	$sql.= " ORDER BY f.id_direccion, g.id_subdireccion, h.id_departamento, b.tx_empleado, tx_centro_costos, tx_producto ";   
	
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			'id_factura_detalle'	=>$row["id_factura_detalle"],
			'tx_registro'			=>$row["tx_registro"],
	  		'tx_empleado'			=>$row["tx_empleado"],
	  		'tx_centro_costos'		=>$row["tx_centro_costos"],
	  		'tx_producto'			=>$row["tx_producto"],
	  		'fl_precio_usd'			=>$row["fl_precio_usd"],
	  		'fl_precio_mxn'			=>$row["fl_precio_mxn"],
	  		'fl_precio_eur'			=>$row["fl_precio_eur"],
	  		'tx_concepto_contable'	=>$row["tx_concepto_contable"],
	  		'tx_cr_estado'			=>$row["tx_cr_estado"],
	  		'tx_afectable'			=>$row["tx_afectable"],
			'id_direccion'			=>$row["id_direccion"],
			'tx_nombre_corto'		=>$row["tx_nombre_corto"],
			'id_subdireccion'		=>$row["id_subdireccion"],
			'tx_subdireccion'		=>$row["tx_subdireccion"],
			'id_departamento'		=>$row["id_departamento"],
			'tx_departamento'		=>$row["tx_departamento"]
		);
	} 	
	
	$registros=count($TheCatalogo);	
	?>
    <br>    
	<?
	if ($registros==0) { }
	else {			
		echo "<table id='verFacturasDetalle' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<10; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					echo "<td width='2%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='Registro'; 
					echo "<td width='7%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Nombre';
					echo "<td width='21%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='CR';
					echo "<td width='5%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 4 : $TheField='Estado del CR';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 5 : $TheField='Producto';
					echo "<td width='20%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField='Monto USD';
					echo "<td width='7%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 7 : $TheField='Monto MXN';
					echo "<td width='7%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 8 : $TheField='Monto EUR';
					echo "<td width='7%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 9 : $TheField='Concepto Contable'; 
					echo "<td width='8%' class='ui-state-highlight align-center'>$TheField</td>";					
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
				$id_factura_detalle		= $TheCatalogo[$i]['id_factura_detalle'];
				$tx_registro			= $TheCatalogo[$i]['tx_registro'];
				$tx_empleado			= $TheCatalogo[$i]['tx_empleado'];
				$tx_centro_costos		= $TheCatalogo[$i]['tx_centro_costos'];
				$tx_producto			= $TheCatalogo[$i]['tx_producto'];
				$fl_precio_usd			= $TheCatalogo[$i]['fl_precio_usd'];
				$fl_precio_mxn			= $TheCatalogo[$i]['fl_precio_mxn'];
				$fl_precio_eur			= $TheCatalogo[$i]['fl_precio_eur'];
				$tx_concepto_contable	= $TheCatalogo[$i]['tx_concepto_contable'];	  		
				$tx_cr_estado			= $TheCatalogo[$i]['tx_cr_estado'];	  		
				$tx_afectable			= $TheCatalogo[$i]['tx_afectable'];	  
				$id_direccion			= $TheCatalogo[$i]['id_direccion'];
				$tx_nombre_corto		= $TheCatalogo[$i]['tx_nombre_corto'];		
				$id_subdireccion		= $TheCatalogo[$i]['id_subdireccion'];
				$tx_subdireccion		= $TheCatalogo[$i]['tx_subdireccion'];	
				$id_departamento		= $TheCatalogo[$i]['id_departamento'];
				$tx_departamento		= $TheCatalogo[$i]['tx_departamento'];			
				
				$j++;				

				$fl_total_precio_usd = $fl_total_precio_usd+$fl_precio_usd;
				$fl_total_precio_mxn = $fl_total_precio_mxn+$fl_precio_mxn;
				$fl_total_precio_eur = $fl_total_precio_eur+$fl_precio_eur;
								
				# ============================					
				# Inicio de los cortes
				# ============================				
				if ($i==0)	{
					echo "<tr>";							
					echo "<td class='ui-state-default' colspan='13'><em>$tx_nombre_corto</em></td>";						
					echo "</tr>";	
					echo "<tr>";							
					echo "<td class='ui-state-default align-center'>-</td>";	
					echo "<td class='ui-state-default' colspan='12'><em>$tx_subdireccion</em></td>";	
					echo "</tr>";	
					echo "<tr>";	
					echo "<td class='ui-state-default align-center'>&nbsp</td>";	
					echo "<td class='ui-state-default' colspan='12'><em>&nbsp&nbsp&nbsp&nbsp-&nbsp&nbsp&nbsp$tx_departamento</em></td>";						
					echo "</tr>";	
				}	
				
				# =============================================
				# Corte 1 - Direccion
				# =============================================				
				if ($i>0)
				{															
					if ($id_direccion_tmp==$id_direccion) { }					
					else {
						echo "<tr>";							
						echo "<td class='ui-state-default' colspan='13'><em>$tx_nombre_corto</em></td>";						
						echo "</tr>";	
						$id_direccion_tmp=$id_direccion;
					}
				} else {				
					$id_direccion_tmp=$id_direccion;
				}	
				
				# =============================================
				# Corte 2 - Subdireccion
				# =============================================				
				if ($i>0)
				{															
					if ($id_subdireccion_tmp==$id_subdireccion) { }					
					else {
						echo "<tr>";	
						echo "<td class='ui-state-default align-center'>-</td>";							
						echo "<td class='ui-state-default' colspan='12'><em>$tx_subdireccion</em></td>";						
						echo "</tr>";	
						$id_subdireccion_tmp=$id_subdireccion;
					}
				} else {				
					$id_subdireccion_tmp=$id_subdireccion;
				}			
				
				# =============================================
				# Corte 3 - Departamento
				# =============================================				
				if ($i>0)
				{															
					if ($id_departamento_tmp==$id_departamento) { }					
					else {
						echo "<tr>";	
						echo "<td class='ui-state-default align-center'>&nbsp</td>";	
						echo "<td class='ui-state-default' colspan='12'><em>&nbsp&nbsp&nbsp&nbsp-&nbsp&nbsp&nbsp$tx_departamento</em></td>";						
						echo "</tr>";
						$id_departamento_tmp=$id_departamento;
					}
				} else {				
					$id_departamento_tmp=$id_departamento;
				}			
				
				for ($a=0; $a<10; $a++)
				{
					switch ($a) 
					{   
						case 0: $TheColumn=$j; 									
								if ($tx_afectable==1) echo "<td class='align-center'>$TheColumn</td>";
								else echo "<td class='ui-state-rojo align-center'>$TheColumn</td>";
						break;						
						case 1: $TheColumn=$tx_registro;
								if ($tx_afectable==1) echo "<td class='align-center'>$TheColumn</td>";
								else echo "<td class='ui-state-rojo align-center'>$TheColumn</td>";
						break;
						case 2: $TheColumn=$tx_empleado;
								if ($tx_afectable==1) echo "<td class='align-left'>$TheColumn</td>";
								else echo "<td class='ui-state-rojo align-left'>$TheColumn</td>";
						break;
						case 3: $TheColumn=$tx_centro_costos;
								if ($tx_afectable==1) echo "<td class='align-center'>$TheColumn</td>";
								else echo "<td class='ui-state-rojo align-center'>$TheColumn</td>";
						break;
						case 4: $TheColumn=$tx_cr_estado;
								if ($tx_afectable==1) echo "<td class='align-center'>$TheColumn</td>";
								else echo "<td class='ui-state-rojo align-center'>$TheColumn</td>";
						break;
						case 5: $TheColumn=$tx_producto;
								if ($tx_afectable==1) echo "<td class='align-left'>$TheColumn</td>";
								else echo "<td class='ui-state-rojo align-left'>$TheColumn</td>";
						break;
						case 6: $TheColumn=number_format($fl_precio_usd,2); 
								if($TheColumn=="0") {
									if ($tx_afectable==1) echo "<td class='align-right'>-</td>";	
									else echo "<td class='ui-state-rojo align-right'>-</td>";	
								} else {
									if ($tx_afectable==1) echo "<td class='align-right'>$TheColumn</td>";
									else echo "<td class='ui-state-rojo align-right'>$TheColumn</td>";
								}	
						break;
						case 7: $TheColumn=number_format($fl_precio_mxn,2); 
								if($TheColumn=="0") {
									if ($tx_afectable==1) echo "<td class='align-right'>-</td>";
									else echo "<td class='ui-state-rojo align-right'>-</td>";										
								} else { 
									if ($tx_afectable==1) echo "<td class='align-right'>$TheColumn</td>";
									else echo "<td class='ui-state-rojo align-right'>$TheColumn</td>"; 
								}
						break;
						case 8: $TheColumn=number_format($fl_precio_eur,2); 
								if($TheColumn=="0") {
									if ($tx_afectable==1) echo "<td class='align-right'>-</td>";
									else echo "<td class='ui-state-rojo align-right'>-</td>";	
								} else {
									if ($tx_afectable==1) echo "<td class='align-right'>$TheColumn</td>";
									else echo "<td class='ui-state-rojo align-right'>$TheColumn</td>"; 
								}	
						break;
						case 9: $TheColumn=$tx_concepto_contable; 
								if($TheColumn=="") {
									if ($tx_afectable==1) echo "<td class='align-center'>&nbsp;</td>";	
									else echo "<td class='ui-state-rojo align-center'>&nbsp;</td>";	
								} else {
									if ($tx_afectable==1) echo "<td class='align-center'>$TheColumn</td>";
									else echo "<td class='ui-state-rojo align-center'>$TheColumn</td>"; 
								}	
						break;
					}							
				}				
				echo "</tr>";					
				}	
				echo "<tr>";								  
				for ($a=0; $a<10; $a++)
				{
					switch ($a) 
					{   
							case 0 : $TheField='Total Derrama por Empleado';
									 echo "<td colspan='6' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
							break;
							case 6 : $TheField=number_format($fl_total_precio_usd,2); 
									 if($TheField=="0") echo "<td class='ui-state-highlight align-right'>-</td>";		
									 else echo "<td class='ui-state-highlight align-right'>$TheField</td>";						 
							break;
							case 7 : $TheField=number_format($fl_total_precio_mxn,2); 
									 if($TheField=="0") echo "<td class='ui-state-highlight align-right'>-</td>";		
									 else echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
							break;
							case 8 : $TheField=number_format($fl_total_precio_eur,2); 
									 if($TheField=="0") echo "<td class='ui-state-highlight align-right'>-</td>";		
									 else echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
							break;
							case 9 : $TheField=""; 
									 echo "<td class='ui-state-highlight align-right'>&nbsp</td>";		
							break;
						}							
					}	
					echo "</tr>";	
					echo "<tr>";								  
					for ($a=0; $a<10; $a++)
					{
						switch ($a) 
						{   
							case 0 : $TheField='Total Factura';
									echo "<td colspan='6' class='ui-state-highlight align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
							break;
							case 6 : $TheField=number_format($fl_precio_usd_cabecera,2); 
									 if($TheField=="0") echo "<td class='ui-state-highlight align-right'>-</td>";		
									 else echo "<td class='ui-state-highlight align-right'>$TheField</td>";						 
							break;
							case 7 : $TheField=number_format($fl_precio_mxn_cabecera,2); 
									 if($TheField=="0") echo "<td class='ui-state-highlight align-right'>-</td>";		
									 else echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
							break;
							case 8 : $TheField=number_format($fl_precio_eur_cabecera,2); 
									 if($TheField=="0") echo "<td class='ui-state-highlight align-right'>-</td>";		
									 else echo "<td class='ui-state-highlight align-right'>$TheField</td>";					
							break;
							case 9 : $TheField=""; 
									 echo "<td class='ui-state-highlight align-right'>&nbsp</td>";		
							break;
						}							
					}	
					echo "</tr>";	
					echo "<tr>";								  
					for ($a=0; $a<10; $a++)
					{
						switch ($a) 
						{   
							case 0 : $TheField='Diferencia';
									echo "<td colspan='6' class='ui-state-default align-right'>$TheField&nbsp;&nbsp;&nbsp;</td>";						 
							break;
							case 6 : $fl_diferencia_usd=$fl_precio_usd_cabecera-$fl_total_precio_usd;
									 $TheField=number_format($fl_diferencia_usd,2); 
									 if($TheField=="0") echo "<td class='ui-state-default align-right'>-</td>";		
									 else echo "<td class='ui-state-rojo align-right'>$TheField</td>";						 
							break;
							case 7 : $fl_diferencia_mxn=$fl_precio_mxn_cabecera-$fl_total_precio_mxn;
									 $TheField=number_format($fl_diferencia_mxn,2); 
									 if($TheField=="0") echo "<td class='ui-state-default align-right'>-</td>";		
									 else echo "<td class='ui-state-rojo align-right'>$TheField</td>";					
							break;
							case 8 : $fl_diferencia_eur=$fl_precio_eur_cabecera-$fl_total_precio_eur;
									 $TheField=number_format($fl_diferencia_eur,2); 
									 if($TheField=="0") echo "<td class='ui-state-default align-right'>-</td>";		
									 else echo "<td class='ui-state-rojo align-right'>$TheField</td>";					
							break;
							case 9 : $TheField=""; 
									 echo "<td class='ui-state-default align-right'>&nbsp</td>";		
							break;
						}							
					}	
					echo "</tr>";	
				echo "</table>";
			}
			
			
		//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_FACTURA_DETALLE" , "$id_login" ,   "id_factura=$id_factura" ,"" ,"inf_facturas_lista_detalle.php");
	 //<\BITACORA>
	
	 
	mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      