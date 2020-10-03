<?
session_start();
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
if 	(isset($_SESSION['sess_user']))
{
	$id_login = $_SESSION['sess_iduser']
?>
	 <script type="text/javascript">
	 		 
		 $("#verComputo").find("tr").hover(		 
         	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
         );
		 
	</script> 
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();
	
	$id	= $_GET['id']; 		
		
	$sql = "   SELECT id_empleado_computo, tx_equipo, tx_marca, tx_modelo, tx_ram, tx_serie, tx_siaf, tx_compartido ";
	$sql.= "     FROM tbl_empleado_computo a, tbl_empleado b, tbl_computo c ";
	$sql.= "    WHERE a.id_empleado=$id ";
	$sql.= "      AND a.id_empleado= b.id_empleado ";
	$sql.= "      AND a.id_computo = c.id_computo and a.tx_indicador='1'  ";
	$sql.= " ORDER BY tx_compartido, tx_equipo, tx_marca, tx_marca " ;	
	
	//echo "sql",$sql;	
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			'id_empleado_computo'	=>$row["id_empleado_computo"],
			'tx_equipo'				=>$row["tx_equipo"],
	  		'tx_marca'				=>$row["tx_marca"],
	  		'tx_modelo'				=>$row["tx_modelo"],
	  		'tx_ram'				=>$row["tx_ram"],
	  		'tx_serie'				=>$row["tx_serie"],
	  		'tx_siaf'				=>$row["tx_siaf"],
	  		'tx_compartido'			=>$row["tx_compartido"]
		);
	} 	
	
	$registros=count($TheCatalogo);	
	
	if ($registros==0) { }
	else {
		echo "<table id='verComputo' align='center' width='100%' border='1' cellspacing='1' cellpadding='1'>";
		echo "<tr>";
		for ($a=0; $a<8; $a++)
		{
			switch ($a) 
			{   
				case 0 : $TheField='#';
					echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";							 
				break;
				case 1 : $TheField='Equipo'; 
					echo "<td width='18%' class='ui-state-highlight align-center'>$TheField</td>";					
				break;
				case 2 : $TheField='Marca';
					echo "<td width='17%' class='ui-state-highlight align-center'>$TheField</td>";	
				break;
				case 3 : $TheField='Modelo';
					echo "<td width='17%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 4 : $TheField='RAM';
					echo "<td width='9%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 5 : $TheField='Serie';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 6 : $TheField='SIAF';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
				case 7 : $TheField='Uso';
					echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";						 
				break;
			}							
		}	
		echo "</tr>";	
		echo "<tr>";
		
		$j=0;		
		
		for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
			while ($elemento = each($TheCatalogo[$i]))					  		
				$id_empleado_computo	=$TheCatalogo[$i]['id_empleado_computo'];
				$tx_equipo				=$TheCatalogo[$i]['tx_equipo'];
				$tx_marca				=$TheCatalogo[$i]['tx_marca'];
				$tx_modelo				=$TheCatalogo[$i]['tx_modelo'];
				$tx_ram					=$TheCatalogo[$i]['tx_ram'];
				$tx_serie				=$TheCatalogo[$i]['tx_serie'];
				$tx_siaf				=$TheCatalogo[$i]['tx_siaf'];
				$tx_compartido			=$TheCatalogo[$i]['tx_compartido'];
				
				$j++;				
				
				for ($a=0; $a<8; $a++)
					{
						switch ($a) 
						{   
							case 0: $TheColumn=$j; 
									echo "<td class='align-center' valign='top'>$TheColumn</td>";
							break;						
							case 1: $TheColumn=$tx_equipo;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 2: $TheColumn=$tx_marca;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 3: $TheColumn=$tx_modelo;
									echo "<td class='align-left' valign='top'>$TheColumn</td>";
							break;
							case 4: $TheColumn=$tx_ram;
									if($TheColumn=="") echo "<td class='align-right' valign='top'>&nbsp;</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";		
							break;
							case 5: $TheColumn=$tx_serie;
									if($TheColumn=="") echo "<td class='align-right' valign='top'>&nbsp;</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";		
							break;
							case 6: $TheColumn=$tx_siaf; 
									if($TheColumn=="") echo "<td class='align-right' valign='top'>&nbsp;</td>";						
									else echo "<td class='align-right' valign='top'>$TheColumn</td>";		
							break;
							case 7: if ($tx_compartido=="0") $TheColumn="PARTICULAR";
									else if ($tx_compartido=="1") $TheColumn="COMPARTIDO";
									else $TheColumn="&nbsp;";						
									echo "<td class='align-left' valign='top'>$TheColumn</td>";		
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
				case 0 : $TheField=""; 
					echo "<td colspan='8' class='ui-state-highlight align-center'>&nbsp;</td>";					
				break;
			}							
		}	
		echo "</tr>";	
		echo "</table>";
	}
	
		//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_EMPLEADO_COMPUTO" , "$id_login" ,   "id_empleado=$id" ,"" ,"cat_computo_lista_read.php");
	 //<\BITACORA>
	 
	 
mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      