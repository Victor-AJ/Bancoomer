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
$inAnio 			= $_GET['anoQueryInf']; 
$inMes		= $_GET['mesQueryInf'];
$idSubDireccion		= $_GET['idSubDireccion'];
$idCuenta			= $_GET['tipoCuenta'];
$rango				= $_GET['rango'];


	

//extraccion de parametros BD
$rangoDif_par=0;
$sql = "   select tx_valor from tbl45_catalogo_global where tx_clave='PARAMETRO-4' and tx_indicador=1 " ;
$result = mysqli_query($mysql, $sql);
$row = mysqli_fetch_row($result);
$rangoDif_par = $row[0];	

$rangoMax = $rangoDif_par*1;
$rangoMin = $rangoDif_par*-1;     

$statusCompleto=0;
$sql = "select id_factura_estatus  from tbl_factura_estatus where tx_Estatus= 'PAGADA'";
$result = mysqli_query($mysql, $sql);
$row = mysqli_fetch_row($result); 
$statusCompleto = $row[0];	//debe ser 4


//exacto
if ($rango=="A")   //Acumulado
$cadMes= " and id_mes <= ".$inMes." ";  
else    //Mensual exacto
$cadMes="  and id_mes=".$inMes." ";  

//falta acumulado




$tablaCSI= " (SELECT C.TX_CENTRO_COSTOS AS CR_CSI ,D.TX_DEPARTAMENTO AS TX_DEPARTAMENTO_CSI,  C.ID_SUBDIRECCION AS SUBDIRPADRE,  sum(b.fl_precio_mxn) AS MONTO_CSI_MXN ";
$tablaCSI.= " FROM tbl_factura_detalle b ";
if ($idCuenta<>0)
	$tablaCSI.="    inner join tbl_producto p on ( b.id_producto = p.id_producto  and  p.id_cuenta_contable= $idCuenta) ";
$tablaCSI.="    inner join tbl_factura f on ( f.id_factura= b.id_factura   and f.tx_indicador='1' and f.id_factura_estatus=$statusCompleto and  tx_anio=$inAnio  $cadMes ) ";
$tablaCSI.="    left outer join tbl_centro_costos c on  c.id_centro_costos = b.id_Centro_costos ";
$tablaCSI.="    left outer join  TBL_DEPARTAMENTO D ON C.ID_DEPARTAMENTO=D.ID_DEPARTAMENTO ";
$tablaCSI.="    left outer join  TBL_SUBDIRECCION S ON C.ID_SUBDIRECCION= S.ID_SUBDIRECCION ";
$tablaCSI.=" left outer join  TBL_DIRECCION R ON C.ID_DIRECCION= R.ID_DIRECCION ";
//SEGURIDAD: ACCESO A SUS DIRECCIONES
$tablaCSI.="    inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  


//SEGURIDAD INACTIVOS
$tablaCSI.=" where b.tx_indicador = '1'  ";


$tablaCSI.="    group by C.TX_CENTRO_COSTOS)  AS TCSI ";



$tablaGPS =" (SELECT G.TX_CR AS CR_GPS, C.ID_SUBDIRECCION AS SUBDIRPADRE, SUM(G.IM_MONTO_LOCAL)  AS MONTO_GPS   FROM tbl42_gps G";
$tablaGPS.=" LEFT OUTER JOIN TBL_centro_costos c ON c.tx_centro_costos= g.tx_cr ";

$tablaGPS.=" LEFT OUTER JOIN TBL_DIRECCION R ON  C.ID_DIRECCION= R.ID_DIRECCION ";
//SEGURIDAD: ACCESO A SUS DIRECCIONES
$tablaGPS.="    inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  


$tablaGPS.="  WHERE IN_ANIO=$inAnio $cadMes  AND TX_CLASE IN ('KR','SD') AND ID_ARCHIVO=$idArchivoGPS ";

if ($idCuenta<>0)
	$tablaGPS.="   and  G.id_cuenta_contable= $idCuenta ";
	
$tablaGPS.="    GROUP  BY TX_CR)  AS TGPSA ";


$sql="SELECT TCSI.CR_CSI, TCSI.TX_DEPARTAMENTO_CSI, TCSI.MONTO_CSI_MXN ,TGPSA.CR_GPS ,  TGPSA.MONTO_GPS  , TCSI.SUBDIRPADRE, ifnull(TCSI.MONTO_CSI_MXN,0) -  ifnull(TGPSA.MONTO_GPS,0)  AS DIF ";
$sql.= " FROM ";
$sql.=   $tablaCSI ;
$sql.="  LEFT JOIN ";  
$sql.=   $tablaGPS ;
$sql.= " ON TCSI.CR_CSI=TGPSA.CR_GPS";
 
if ($idSubDireccion <> 0)
$sql.= " WHERE TCSI.SUBDIRPADRE = $idSubDireccion  "; 

$sql.= " UNION ALL ";

$sql.="SELECT TCSI.CR_CSI, TCSI.TX_DEPARTAMENTO_CSI, TCSI.MONTO_CSI_MXN ,TGPSA.CR_GPS ,  TGPSA.MONTO_GPS  ,TCSI.SUBDIRPADRE, ifnull(TCSI.MONTO_CSI_MXN,0) -  ifnull(TGPSA.MONTO_GPS,0)  AS DIF ";
$sql.= " FROM ";
$sql.=   $tablaCSI ;
$sql.="  RIGHT JOIN ";  
$sql.=   $tablaGPS ;
$sql.= " ON TCSI.CR_CSI=TGPSA.CR_GPS";

$sql.="   WHERE TCSI.CR_CSI  IS NULL ";

if ($idSubDireccion <> 0)
$sql.= " and TCSI.SUBDIRPADRE = $idSubDireccion  "; 



$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheResultset[] = array(
			'CR_CSI'	=>$row["CR_CSI"],
			'TX_DEPARTAMENTO_CSI'	=>$row["TX_DEPARTAMENTO_CSI"],
			'MONTO_CSI_MXN'	=>$row["MONTO_CSI_MXN"],
			'CR_GPS'	=>$row["CR_GPS"],
			'MONTO_GPS'	=>$row["MONTO_GPS"],
			'DIF'	=>$row["DIF"]
			);
	} 
	
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, $actionBita , "TBL_FACTURA_DETALLE TBL42_GPS" , "$id_login" ,   "tx_anio=$inAnio id_mes=$inMes id_rango=$rango id_cuenta_contable=$idCuenta id_archivo_g=$idArchivoGPS  xls=$XLS  " ,"" ,"inf_conciliacion_departamento_GPS.php");
	 //<\BITACORA>
	
$vinculoXls="javascript: enviarPopUpXls('inf_conciliacion_departamento_GPS.php?theGpsQueryFile=$idArchivoGPS&theEssbaseQueryFile=$idArchivoESB&anoQueryInf=$inAnio&mesQueryInf=$inMes&idSubDireccion=$idSubDireccion&rango=$rango&tipoCuenta=$idCuenta&XLS=1'); ";
$border = ($XLS ==1)? " border='1' ": " border='0' ";
	
?>




<table <?php echo $border ?> cellpadding="1" cellspacing="1" >




	<tr >
		<td class='ui-notas align-center' > <b>CR CSI</b> </td> 
		<td class='ui-notas align-center' > <b>&Aacute;REA</b>  </td>
		<td class='ui-notas align-center' > <b>MONTO CSI (MXN)</b>  </td> 
		<td class='ui-notas align-center' > <b>CR GPS</b>  </td>
		<td class='ui-notas align-center' > <b>MONTO GPS</b>  </td> 
		<td class='ui-notas align-center' > <b>DIFERENCIA</b>  </td>
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
										$campo1=$TheResultset[$i]['CR_CSI'];		
										$campo2=$TheResultset[$i]['TX_DEPARTAMENTO_CSI'];	
/*
										if ($campo2 == null || $campo2 == "")
											$campo2="?";
										else
											$campo2=SUBSTR($campo2,0,20)."...";
*/
										
										$campo3=$TheResultset[$i]['MONTO_CSI_MXN'];	
										$total1+=$campo3;
										$campo4=$TheResultset[$i]['CR_GPS'];	
										$campo5=$TheResultset[$i]['MONTO_GPS'];	
										$total2+=$campo5;
										$campo6=$TheResultset[$i]['DIF'];
										$diferencia= 0;
										$diferencia+= $campo6;
										
										IF ( $diferencia >=$rangoMin  &&  $diferencia <= $rangoMax)
											$claseDif="class='ui-state-verde align-right'";
										ELSE 
											$claseDif="class='ui-state-rojo align-right'";
											
										
										$vinculo1="javascript:btnVerFacturasCSI('".$campo1."')";
										$vinculo2="javascript:btnVerFacturasGPS('".$campo4."')";	
?>
                           <tr>
                           <td class='ui-state-verde align-left'  align="left"  > 
                           <a href='#' style='cursor:pointer'onclick="<?echo $vinculo1 ?>" >  
                           <b><? echo $campo1 ?></b>
							</a>
                            </td> 
                           <td class='ui-state-verde align-left' align="left" > <? echo $campo2 ?> &nbsp;</td>
                           <td class='ui-state-verde align-right' > <? echo number_format($campo3,2) ?> </td> 
                           <td class='ui-state-verde align-left' >
                           <a href='#' style='cursor:pointer'onclick="<?echo $vinculo2 ?>" >  
                           <b> <? echo $campo4 ?></b> 
							</a>
                           </td>
                           <td class='ui-state-verde align-right'  > <? echo number_format($campo5,2) ?></td> 
                           <td <? echo  $claseDif?>  > <? echo number_format($campo6,2) ?></td>
                           </tr>  		  
 <?
											
									}
							mysqli_close($mysql);						 
?>


 

<tr><td>  </td> <td>  </td><td align="right"> <b><? echo number_format($total1,2) ?></b>  </td> <td>  </td><td align="right"> <b><? echo number_format($total2,2) ?></b>  </td> <td>  </td></tr>
</table>

 <!-- <? echo "<font face='Arial' size='0.5'>".$sql."</font>"  ?> -->  
<!-- <?echo "<br>VARS: (idArchivoGPS= $idArchivoGPS )  (inAnio = $inAnio ) (inMes = $inMes ) (IdSubDireccion= $idSubDireccion	)   rango=  $rango<BR>" ?> -->   






