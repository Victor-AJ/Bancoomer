<?php
$XLS			= $_GET['XLS'];
IF ($XLS==1)
	{
    header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    session_cache_limiter("must-revalidate");
    header("Content-Type: application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename="fileToExport.xls"');
	$actionBita="EXCEL";
	}
	else 
	$actionBita="CONSULTA";
	
	
session_start();
include("includes/funciones.php");  
include_once  ("Bitacora.class.php"); 
$mysql=conexion_db();
if 	(isset($_SESSION["sess_user"])) 
	$id_login = $_SESSION['sess_iduser'];
	
// Recibo variables
// ============================
$idArchivoGPS			= $_GET['theGpsQueryFile'];
$idArchivoESB			= $_GET['theEssbaseQueryFile'];  
$inAnio 			= $_GET['anoQueryInf']; 
$inMes		= $_GET['mesQueryInf'];
$idSubDireccionG	= $_GET['idSubDireccionG'];
$idSubDireccionE	= $_GET['idSubDireccionE'];
$rango				= $_GET['rango'];


	

//extraccion de parametros BD
$rangoDif_par=0;
$sql = "   select tx_valor from tbl45_catalogo_global where tx_clave='PARAMETRO-4' and tx_indicador=1 " ;
$result = mysqli_query($mysql, $sql);
$row = mysqli_fetch_row($result);
$rangoDif_par = $row[0];	

$rangoMax = $rangoDif_par*1;
$rangoMin = $rangoDif_par*-1;  


//exacto
if ($rango=="A")   //Acumulado
$cadMes= " and id_mes <= ".$inMes." ";  
else    //Mensual exacto
$cadMes="  and id_mes=".$inMes." ";  

//falta acumulado
 

$tablaGPS =" (SELECT G.TX_CR AS CR_GPS, D.TX_DEPARTAMENTO AS TX_DEPTO_IN_CSI, c.id_subdireccion as SUBDIRPADRE, SUM(G.IM_MONTO_LOCAL)  AS MONTO_GPS   FROM tbl42_gps G";
$tablaGPS.=" LEFT OUTER JOIN TBL_centro_costos c ON c.tx_centro_costos= g.tx_cr ";  
$tablaGPS.=" left outer join  TBL_DEPARTAMENTO D ON C.ID_DEPARTAMENTO=D.ID_DEPARTAMENTO ";
$tablaGPS.="    left outer join  TBL_DIRECCION R ON C.ID_DIRECCION= R.ID_DIRECCION ";
//SEGURIDAD: ACCESO A SUS DIRECCIONES
$tablaGPS.="    inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  

$tablaGPS.="  WHERE IN_ANIO=$inAnio $cadMes  AND TX_CLASE IN ('S2','KR','SD') AND ID_ARCHIVO=$idArchivoGPS ";
if ($idCuenta<>0)
	$tablaGPS.="   and  G.id_cuenta_contable= $idCuenta ";
	
$tablaGPS.="    GROUP  BY TX_CR)  AS TGPSA ";



$tablaESB= " (SELECT G.TX_CR AS CR_ESB, D.TX_DEPARTAMENTO AS TX_ESB_IN_CSI, SUM(G.IM_MONTO) AS MONTO_ESB, c.id_subdireccion as SUBDIRPADRE_ESB ";
$tablaESB.= "  FROM TBL43_ESSBASE G ";
$tablaESB.="    left outer join tbl_centro_costos c ON c.tx_centro_costos= g.tx_cr  ";
$tablaESB.=" left outer join  TBL_DEPARTAMENTO D ON C.ID_DEPARTAMENTO=D.ID_DEPARTAMENTO ";
$tablaESB.=" left outer join  TBL_DIRECCION R ON C.ID_DIRECCION=R.ID_DIRECCION ";
//SEGURIDAD: ACCESO A SUS DIRECCIONES
$tablaESB.="    inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  

$tablaESB.="    WHERE  in_anio=$inAnio  $cadMes AND ID_ARCHIVO=$idArchivoESB ";

if ($idCuenta<>0)
	$tablaESB.="   and  G.id_cuenta_contable= $idCuenta ";
	
$tablaESB.="    group by G.TX_CR)  AS TESB ";


$sql="SELECT TGPSA.CR_GPS, TGPSA.TX_DEPTO_IN_CSI, TGPSA.MONTO_GPS  ,TESB.CR_ESB ,  TESB.MONTO_ESB  , TESB.TX_ESB_IN_CSI, ifnull(TGPSA.MONTO_GPS,0) -  ifnull(TESB.MONTO_ESB,0)   AS DIF , TGPSA.SUBDIRPADRE , TESB.SUBDIRPADRE_ESB ";
$sql.= " FROM ";
$sql.=   $tablaGPS ;
$sql.="  LEFT JOIN ";  
$sql.=   $tablaESB ;
$sql.= " ON TGPSA.CR_GPS=TESB.CR_ESB ";

if ($idSubDireccionG <> 0)
$sql.= " WHERE TGPSA.SUBDIRPADRE = $idSubDireccionG  "; 

if ($idSubDireccionE <> 0)
$sql.= " WHERE TESB.SUBDIRPADRE_ESB = $idSubDireccionE  ";


$sql.= " UNION ALL ";

$sql.="SELECT TGPSA.CR_GPS, TGPSA.TX_DEPTO_IN_CSI, TGPSA.MONTO_GPS  ,TESB.CR_ESB ,  TESB.MONTO_ESB  ,   TESB.TX_ESB_IN_CSI, ifnull(TGPSA.MONTO_GPS,0) -  ifnull(TESB.MONTO_ESB,0)   AS DIF , TGPSA.SUBDIRPADRE , TESB.SUBDIRPADRE_ESB ";
$sql.= " FROM ";
$sql.=   $tablaGPS ;
$sql.="  RIGHT JOIN ";  
$sql.=   $tablaESB ;
$sql.= " ON TGPSA.CR_GPS=TESB.CR_ESB ";

$sql.="   WHERE TGPSA.CR_GPS  IS NULL ";

if ($idSubDireccionG <> 0)
$sql.= " and TGPSA.SUBDIRPADRE = $idSubDireccionG  "; 

if ($idSubDireccionE <> 0)
$sql.= " and TESB.SUBDIRPADRE_ESB = $idSubDireccionE  "; 



$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheResultset[] = array(
			'CR_GPS'	=>$row["CR_GPS"],
			'TX_DEPTO_IN_CSI'	=>$row["TX_DEPTO_IN_CSI"],
			'MONTO_GPS'	=>$row["MONTO_GPS"],
			'CR_ESB'	=>$row["CR_ESB"],
			'MONTO_ESB'	=>$row["MONTO_ESB"],
			'TX_ESB_IN_CSI'	=>$row["TX_ESB_IN_CSI"],
			'DIF'	=>$row["DIF"],
			'SUBDIRPADRE'	=>$row["SUBDIRPADRE"],
			'SUBDIRPADRE_ESB'	=>$row["SUBDIRPADRE_ESB"]  
		
			);
	} 
	
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, $actionBita , "TBL42_GPS TBL43_ESSBASE" , "$id_login" ,   "tx_anio=$inAnio id_mes=$inMes id_rango=$rango id_cuenta_contable=$idCuenta id_archivo_g=$idArchivoGPS id_archivo_e=$idArchivoESB xls=$XLS  " , ""  ,  "inf_conciliacion_departamento_ESB.php");
	 //<\BITACORA>
	
$vinculoXls="javascript: enviarPopUpXls('inf_conciliacion_departamento_ESB.php?theGpsQueryFile=$idArchivoGPS&theEssbaseQueryFile=$idArchivoESB&anoQueryInf=$inAnio&mesQueryInf=$inMes&rango=$rango&idSubDireccionG=$idSubDireccionG&idSubDireccionE=$idSubDireccionE&XLS=1'); ";
$border = ($XLS ==1)? " border='1' ": " border='0' ";
	

?>



<table <?php echo $border ?>  cellpadding="1" cellspacing="1" >

	<tr  >
		<td class='ui-notas align-center'> <b>CR GPS</b> </td> 
		<td class='ui-notas align-center'> <b>&Aacute;REA EN CSI</b>  </td>
		<td class='ui-notas align-center'> <b>MONTO GPS</b>  </td> 
		<td class='ui-notas align-center'> <b>CR ESB</b>  </td>
		<td class='ui-notas align-center'> <b>MONTO ESB</b>  </td> 
		<td class='ui-notas align-center'> <b>DIFERENCIA</b>  </td>
		<?php
     	if ($XLS <> 1)
     	{  
     	?>
     	<td align="right"   > &nbsp;&nbsp;&nbsp;	
	 	<a class='align-right' href='#' style='cursor:pointer' onclick="<?echo $vinculoXls?>"  ><img src="images/iconxls.jpg" border="0">	</a>
	 	</td>
		<?php 
      	}
		?>
	 
	 
	
	</tr>
<tr ><td colspan="5">&nbsp;  </td> </tr>
<?								
$total1=0;
$total2=0;
								for ($i = 0; $i < count($TheResultset); $i++)
								{	         			 
										$campo1=$TheResultset[$i]['CR_GPS'];		
										$campo2=$TheResultset[$i]['TX_DEPTO_IN_CSI'];
										$campo3=$TheResultset[$i]['MONTO_GPS'];	
										$total1+=$campo3;

										$campo4=$TheResultset[$i]['CR_ESB'];	
										$campo5=$TheResultset[$i]['MONTO_ESB'];	
										$total2+=$campo5;

										$campo6=$TheResultset[$i]['TX_ESB_IN_CSI'];
										
										$campo7=$TheResultset[$i]['DIF'];
										
										$diferencia= 0;
										$diferencia+= $campo7;
										
										IF ( $diferencia >=$rangoMin &&  $diferencia <=$rangoMax)
											$claseDif="class='ui-state-verde align-right'";
										ELSE 
											$claseDif="class='ui-state-rojo align-right'";
										
											$campo8=$TheResultset[$i]['SUBDIRPADRE'];
											$campo9=$TheResultset[$i]['SUBDIRPADRE_ESB'];
										
									$etiqueta="";
										if ($campo2 <> "")
										$etiqueta=$campo2;
											elseif ($campo6 <> "")
											$etiqueta=$campo6;
										
										
											
?>
                           <tr>
                           <td class='ui-state-verde align-left'   align="left"  ><b><? echo $campo1 ?></b>&nbsp; </td> 
                           <td class='ui-state-verde align-left' align="left">
                            <? echo $etiqueta ?> &nbsp;
                            </td>
                           
                           <td class='ui-state-verde align-right' > <? echo number_format($campo3,2) ?> </td> 
                           <td class='ui-state-verde align-left' > <b><? echo $campo4 ?></b>&nbsp;</td>
                           <td class='ui-state-verde align-right'  > <? echo number_format($campo5,2) ?></td> 
                           <td <? echo $claseDif ?> > <? echo number_format($campo7,2) ?></td>
                         
                           </tr>  		  
<?
											
		}	
mysqli_close($mysql);					 
?>

<tr><td>  </td> <td>  </td><td align="right"> <b><? echo number_format($total1,2) ?></b>  </td> <td>  </td><td align="right"> <b><? echo number_format($total2,2) ?></b>  </td> <td>  </td></tr>
</table>

<!-- <? echo "<font face='Arial' size='0.5'>".$sql."</font>"  ?>  -->
<!-- <?echo "<br>VARS: (idArchivoGPS= $idArchivoGPS )  (inAnio = $inAnio ) (inMes = $inMes ) (IdSubDireccion= $idSubDireccion	)(idArchivoESB	= $idArchivoESB	)   rango=  $rango<BR>" ?>  -->




