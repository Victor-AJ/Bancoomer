<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	 $id_login = $_SESSION['sess_iduser'];
?>
	<script type="text/javascript">
	
		$("#divDatos").hide();
		
		 $("#verEmpleados").find("tr").hover(		 
         	function() { $(this).addClass('ui-state-hover'); },
         	function() { $(this).removeClass('ui-state-hover'); }
         );	
		 
	</script>
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");  
	$mysql=conexion_db();
	
	// Recibo variables
	// ============================
	$tx_busca	= $_GET['tx_busca']; 	
	$bemp = '%'.$tx_busca.'%';		
						
  	$sql = " SELECT * ";
	$sql.= "   FROM tbl_empleado E ";
	
	
	//SEGURIDAD: ACCESO A SUS DIRECCIONES
$sql.="   inner join  tbl_centro_costos c on  c.id_centro_costos = E.id_Centro_costos ";
$sql.="   inner join  TBL_DIRECCION R ON C.ID_DIRECCION= R.ID_DIRECCION ";
$sql.="   inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  
	
	$sql.= "  WHERE tx_empleado like '$bemp' ";
	
	$sql.= " ORDER BY tx_empleado ";
						
	//echo "aaa",$sql;			
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			'id_empleado'=>$row["id_empleado"],
			'tx_registro'=>$row["tx_registro"],
	  		'tx_empleado'=>$row["tx_empleado"]
		);
	} 	
	
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_EMPLEADO" , "$id_login" ,   "tx_busca=$tx_busca" ,"" ,"busca_empleados.php");
	 //<\BITACORA>
	
	?>
    <div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"> 
    <fieldset><legend class="ui-state-default"><b><em>RESULTADO DE LA B&Uacute;SQUEDA ...</em></b></legend>
    <br/>
	<?
	echo "<table id='verEmpleados' align='left' width='98%' border='1' cellspacing='1' cellpadding='1'>";           
	echo "<tr>";								  
	for ($a=0; $a<4; $a++)
	{
		switch ($a) 
		{   
			case 0 : $TheField='#';
				echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";							 
			break;
			case 1 : $TheField='Editar'; 
				echo "<td width='3%' class='ui-state-highlight align-center'>$TheField</td>";					
			break;
			case 2 : $TheField='Registro';
				echo "<td width='10%' class='ui-state-highlight align-center'>$TheField</td>";	
			break;
			case 3 : $TheField='Nombre';
				echo "<td width='84%' class='ui-state-highlight align-center'>$TheField</td>";						 
			break;
		}							
	}	
	echo "</tr>";	
	
	$j=0;	
	
	for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_empleado	=$TheCatalogo[$i]['id_empleado'];
			$tx_registro	=$TheCatalogo[$i]['tx_registro'];		
			$tx_empleado	=$TheCatalogo[$i]['tx_empleado'];	
			
			$j++;
			
			for ($a=0; $a<4; $a++)
			{
				switch ($a) 
				{   
					case 0 : $TheColumn=$j; 
							echo "<td class='align-center' valign='top'>$TheColumn</td>";
					break;			
					case 1 : $TheColumn = "<a href='#' style='cursor:pointer' onclick='javascript:buscaEmpleado($id_empleado)';><span class='ui-icon ui-icon-pencil' title='Presione para editar ...'></span></a>";
							echo "<td class='ui-widget-header' align='center' valign='top'>$TheColumn</td>";
					break;						
					case 2 : $TheColumn=$tx_registro;
							if ($TheColumn=="") echo "<td class='align-center' valign='top'>&nbsp;</td>";	
							else echo "<td class='align-center' valign='top'>$TheColumn</td>";
					break;
					case 3 : $TheColumn=$tx_empleado;
							if ($TheColumn=="") echo "<td class='align-center' valign='top'>&nbsp;</td>";
							else echo "<td class='align-letf' valign='top'>$TheColumn</td>";
					break;
				}							
			}				
			echo "</tr>";					
	}	
	echo "</table>";
	?>
    <br/>
    </fieldset>   
    <div>
<?
mysqli_close($mysql);

} else {
	echo "Sessi&oacute;n Invalida";
}	
?>      