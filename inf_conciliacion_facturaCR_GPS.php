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
$idArchivoGPS			= $_GET['theGPSQueryFileFac']; 
$inAnio 				= $_GET['anoQueryFac']; 
$inMes					= $_GET['mesQueryFac'];


$idEstatusFac 			= $_GET['estatusFac'];
$idProveedorFac			= $_GET['proveedorFac'];
$theFacturaCSI			= $_GET['theFacturaCSI'];
$theFacturaGPS			= $_GET['theFacturaGPS'];

$FiltroAdicFacGPS       = $_GET['FiltroAdicFacGPS'];



//extraccion de parametros BD
$rangoDif_par=0;
$sql = "   select tx_valor from tbl45_catalogo_global where tx_clave='PARAMETRO-3' and tx_indicador=1 " ;
$result = mysqli_query($mysql, $sql);
$row = mysqli_fetch_row($result);
$rangoDif_par = $row[0];	

$rangoMax = $rangoDif_par*1;
$rangoMin = $rangoDif_par*-1;     


if ($inMes==0)  
$cadMes= " ";  
else    
$cadMes="  and id_mes=".$inMes." ";    



IF ($idEstatusFac<>0)
$cadEstatus=" and a.id_factura_estatus=$idEstatusFac ";
ELSE
$cadEstatus="";

IF ($theFacturaCSI<>"")
$cadFActuraCSI=" and a.tx_factura like '%$theFacturaCSI%' ";
ELSE
$cadFActuraCSI="";


IF ($theFacturaGPS<>"")
$cadFacturaGPS=" and G.TX_REFERENCIA like '%$theFacturaGPS%' ";
ELSE
$cadFacturaGPS="";


if ($idProveedorFac<>0)
$cadProveedor = " and P.id_proveedor= $idProveedorFac ";
ELSE
$cadProveedor = "";


$tablaCSI= " (select  e.tx_estatus as ESTATUS,A.ID_FACTURA AS ID_FACTURA, a.tx_factura as FACTURA, a.tx_referencia as REFERENCIA, P.ID_PROVEEDOR as IDPROVEEDOR, P.TX_PROVEEDOR as PROVEEDOR, A.ID_MES AS MES, c.tx_centro_costos as CR , COUNT(*) AS DERRAMAS, sum(f.fl_precio_usd) AS MONTO_USD ,sum(f.fl_precio_mxn) AS MONTO_MXN  ";
$tablaCSI.= " from  tbl_factura_detalle f  ";
$tablaCSI.= " inner join tbl_centro_costos c on c.id_centro_costos=f.id_centro_costos ";
$tablaCSI.="   inner join  TBL_DIRECCION R ON C.ID_DIRECCION= R.ID_DIRECCION ";
//SEGURIDAD: ACCESO A SUS DIRECCIONES
$tablaCSI.="    inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  


$tablaCSI.="    inner join tbl_factura a on (f.id_factura= a.id_factura and f.tx_indicador='1' and a.tx_anio=$inAnio  $cadMes  $cadEstatus $cadFActuraCSI  ) ";
$tablaCSI.=" inner join tbl_factura_estatus e on e.id_factura_estatus = a.id_factura_estatus ";
$tablaCSI.="  INNER JOIN tbl_proveedor P ON (P.ID_PROVEEDOR= A.ID_PROVEEDOR $cadProveedor ) ";
//SEGURIDAD INACTIVOS
$tablaCSI.=" where f.tx_indicador = '1'  ";

$tablaCSI.=" group by    a.tx_factura, c.tx_centro_costos  )  AS TCSI ";


$tablaGPS =" ( SELECT G.TX_CR AS CR_GPS,  G.TX_numero_doc  AS DOCUMENTO  ,G.TX_REFERENCIA AS  TX_REFERENCIA ,  funcionS8(G.TX_numero_doc)  as REFPADRE, SUM(G.IM_MONTO_LOCAL)  AS MONTO_GPS_LOCAL,   SUM(G.IM_MONTO_DESTINO)   AS MONTO_GPS_DESTINO  , COUNT(*) AS REGS  ";
$tablaGPS.=" FROM tbl42_gps G ";
$tablaGPS.=" WHERE G.IN_ANIO=$inAnio   $cadMes   AND G.TX_CLASE IN ('KR','SD') AND G.ID_ARCHIVO=$idArchivoGPS  $cadFacturaGPS  ";
$tablaGPS.=" GROUP BY REFPADRE, CR_GPS ";
$tablaGPS.="  ) AS TGPS ";




$sql=" SELECT  ID_FACTURA, ESTATUS, FACTURA, IDPROVEEDOR, PROVEEDOR, MES, CR, DERRAMAS, MONTO_USD, MONTO_MXN ,  CR_GPS, DOCUMENTO, TX_REFERENCIA, MONTO_GPS_DESTINO , MONTO_GPS_LOCAL ,  REFPADRE , REGS  FROM  ( ";


$sql.=" SELECT TCSI.ID_FACTURA, TCSI.ESTATUS, TCSI.FACTURA, TCSI.IDPROVEEDOR, TCSI.PROVEEDOR, TCSI.MES, TCSI.CR, TCSI.DERRAMAS, TCSI.MONTO_USD, TCSI.MONTO_MXN ,  TGPS.CR_GPS, TGPS.DOCUMENTO, TGPS.TX_REFERENCIA, TGPS.MONTO_GPS_DESTINO , TGPS.MONTO_GPS_LOCAL ,  TGPS.REFPADRE , TGPS.REGS";
$sql.= " FROM ";
$sql.=   $tablaCSI ;
$sql.=" left JOIN ";  
$sql.=   $tablaGPS ;
$sql.= " ON ( TCSI.CR=TGPS.CR_GPS and TCSI.FACTURA=TGPS.REFPADRE  AND  ( TCSI.MONTO_MXN  - TGPS.MONTO_GPS_LOCAL  >= $rangoMin AND  TCSI.MONTO_MXN  - TGPS.MONTO_GPS_LOCAL  <= $rangoMax ) )   ";


$sql.= " UNION ALL ";

$sql.=" SELECT TCSI.ID_FACTURA,  TCSI.ESTATUS, TCSI.FACTURA, TCSI.IDPROVEEDOR, TCSI.PROVEEDOR, TCSI.MES, TCSI.CR, TCSI.DERRAMAS, TCSI.MONTO_USD, TCSI.MONTO_MXN ,  TGPS.CR_GPS, TGPS.DOCUMENTO, TGPS.TX_REFERENCIA, TGPS.MONTO_GPS_DESTINO , TGPS.MONTO_GPS_LOCAL ,  TGPS.REFPADRE , TGPS.REGS ";
$sql.= " FROM ";
$sql.=   $tablaCSI ;
$sql.="  RIGHT JOIN ";  
$sql.=   $tablaGPS ;
$sql.= " ON ( TCSI.CR=TGPS.CR_GPS and TCSI.FACTURA=TGPS.REFPADRE   AND  ( TCSI.MONTO_MXN  - TGPS.MONTO_GPS_LOCAL  >=  $rangoMin AND  TCSI.MONTO_MXN  - TGPS.MONTO_GPS_LOCAL  <= $rangoMax ) )   ";
$sql.="   WHERE TCSI.CR  IS NULL ";



$sql.="   ) AS TABLA "; 

if ( $FiltroAdicFacGPS <> null && $FiltroAdicFacGPS <>"" )
	$sql.= " where REFPADRE='".$FiltroAdicFacGPS."'  "  ;
	

$sql.="   ORDER BY REFPADRE  ";



$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheResultset[] = array(
			'ID_FACTURA'	=>$row["ID_FACTURA"],
			'CSI_ESTATUS'	=>$row["ESTATUS"],
			'FACTURA'	=>$row["FACTURA"],
			'IDPROVEEDOR'	=>$row["IDPROVEEDOR"],
			'PROVEEDOR'	=>$row["PROVEEDOR"],
			'MES'	=>$row["MES"],
			'CR'	=>$row["CR"],

			'DERRAMAS'	=>$row["DERRAMAS"],
			'MONTO_USD'=>$row["MONTO_USD"],
			'MONTO_MXN' =>$row["MONTO_MXN"],
			'CR_GPS'=>$row["CR_GPS"], 
						'REGS'	=>$row["REGS"],
			'DOCUMENTO'=>$row["DOCUMENTO"],
			'TX_REFERENCIA'=>$row["TX_REFERENCIA"],
			'MONTO_GPS_DESTINO'=>$row["MONTO_GPS_DESTINO"],
			'MONTO_GPS_LOCAL' =>$row["MONTO_GPS_LOCAL"],
			'REFPADRE' =>$row["REFPADRE"]
			);
	} 

	//	<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql,$actionBita , "TBL_FACTURA_DETALLE  TBL42_GPS " , "$id_login" ,   "tx_anio=$inAnio id_mes=$inMes id_estatus_factura=$idEstatusFac id_provedor=$idProveedorFac tx_factura_csi=$theFacturaCSI tx_factura_gps=$theFacturaGPS id_archivo_g=$idArchivoGPS xls=$XLS  " , ""  ,  "inf_conciliacion_facturaCR_GPS.php");
	 //<\BITACORA>
 
 
$vinculoXls="javascript: enviarPopUpXls('inf_conciliacion_facturaCR_GPS.php?theGPSQueryFileFac=$idArchivoGPS&anoQueryFac=$inAnio&mesQueryFac=$inMes&estatusFac=$idEstatusFac&proveedorFac=$idProveedorFac&theFacturaCSI=$theFacturaCSI&theFacturaGPS=$theFacturaGPS&FiltroAdicFacGPS=$FiltroAdicFacGPS&XLS=1'); ";
$border = ($XLS ==1)? " border='1' ": " border='0' ";
	
	
?>




<table <?php echo $border ?>  cellpadding="1" cellspacing="1" align="center" >
	<tr >
		<td class='ui-state-highlight align-center'  colspan="8"  align="center" > <b>CSI</b> </td> 
		<td class='ui-state-highlight align-center'  colspan="7"   align="center" > <b>GPS</b> </td>
		<td align="right"> 
		
		
		<?php
    	 if ($XLS <> 1)
     	{  
     	?>
	     <a class='align-right' href='#' style='cursor:pointer' onclick="<?echo $vinculoXls?>"  ><img src="images/iconxls.jpg" border="0">	</a>
		<?php 
      	}
		?>
		
		
  
		
		</td>
	</tr>
	<tr >
		<td class="ui-state-default align-center" > <b>ESTATUS</b> </td> 
		<td class="ui-state-default align-center" > 
		 		<b>FACTURA</b>
		 </td>
		<td class="ui-state-default align-center" > <b>PROVEEDOR</b>  </td> 
		<td class="ui-state-default align-center" > <b>MES</b>  </td>
		<td class="ui-state-default align-center" > <b>CR</b>  </td> 
		<td class="ui-state-default align-center" > <b>DERRAMAS</b>  </td>
		<td class="ui-state-default align-center" > <b>MONTO USD</b>  </td>
		<td class="ui-state-default align-center" > <b>MONTO MXN</b>  </td>
		<td class="ui-state-default align-center" > <b>CR GPS</b>  </td>
   		<td class="ui-state-default align-center" > <b>REGS</b>  </td>
		<td class="ui-state-default align-center" > <b>DOCUMENTO</b>  </td>

        <td class="ui-state-default align-center" > <b>REFERENCIA</b>  </td>
		<td class="ui-state-default align-center" > <b>MONTO GPS DESTINO</b>  </td>
		<td class="ui-state-default align-center" > <b>MONTO GPS LOCAL</b>  </td>
		<td class="ui-state-default align-center" > <b>REF PADRE</b>  </td>
		<td class="ui-state-default align-center" > <b>DIFERENCIA MXN </b>  </td>
		
		
	</tr>
	
<tr ><td colspan="5">&nbsp;  </td> </tr>
<?								
$total0=0;
$total1=0;
$total2=0;
$total3=0;
$total4=0;
$total5=0;
$contador=0;
								for ($i = 0; $i < count($TheResultset); $i++)
								{	         			 
										$campo0=$TheResultset[$i]['ID_FACTURA'];	
										$campo1=$TheResultset[$i]['CSI_ESTATUS'];		
										$campo2=$TheResultset[$i]['FACTURA'];
										$campoIdProv=$TheResultset[$i]['IDPROVEEDOR'];		
										$campo3=$TheResultset[$i]['PROVEEDOR'];	
										$campo4=$TheResultset[$i]['MES'];	
										$campo5=$TheResultset[$i]['CR'];	
										$campo6=$TheResultset[$i]['DERRAMAS'];
										$total0+=$campo6;
										$campo7=$TheResultset[$i]['MONTO_USD'];
										$total1+=$campo7;
										$campo8=$TheResultset[$i]['MONTO_MXN' ];
										$total2+=$campo8;
										$campo9=$TheResultset[$i]['CR_GPS' ];
										$camporegs=$TheResultset[$i]['REGS' ];
										$campoDoc=$TheResultset[$i]['DOCUMENTO'];
										$campo10=$TheResultset[$i]['TX_REFERENCIA'];
										$campo11=$TheResultset[$i]['MONTO_GPS_DESTINO'];
										$total3+=$campo11;
										$campo12=$TheResultset[$i]['MONTO_GPS_LOCAL'];
										$total4+=$campo12;
										$campo13=$TheResultset[$i]['REFPADRE'];  
										$contador++;
										$diferencia=$campo8 - $campo12;
										$total5+=$diferencia;
											IF ($diferencia >= $rangoMin && $diferencia <=$rangoMax )
										$claseDif="class='ui-state-verde align-right'";
										else		
										$claseDif="class='ui-state-rojo align-right'";
											
							$vinculo="javascript:btnVerDetalleFacCRDerrama('$campo0')";			
											
?>
                           <tr>
                           <td class='ui-state-verde align-left' > <? echo $campo1 ?></td> 
                           
                           <td class='ui-state-verde align-left' > 
							<a  class='align-center' href='#' style='cursor:pointer' onclick="<?echo $vinculo ?>" >                           
							<? echo $campo2 ?>
							</a> 
                           </td>
                           
                           <td class='ui-state-verde align-left'  >   <? echo $campo3 ?> </td>
                           <td class='ui-state-verde align-left'  > <? echo $campo4 ?> </td>
                           <td class='ui-state-verde align-center'  > <b><? echo $campo5 ?></b> </td>
                           <td class='ui-state-verde align-center' > <? echo $campo6 ?></td>
                           <td class='ui-state-verde align-right' > <? echo number_format($campo7,2) ?> </td>
                           <td class='ui-state-verde align-right' > <? echo number_format($campo8,2) ?> </td>
                           <td class='ui-state-verde align-center'  ><b> <? echo $campo9 ?> </b> </td>

                           <td class='ui-state-verde align-center'  > <? echo $camporegs ?>  </td>
                           
                           <td class='ui-state-verde align-center'  > <? echo $campoDoc ?> </td>
                           <td class='ui-state-verde align-left'  > <? echo $campo10 ?> </td>
                           <td class='ui-state-verde align-right'  > <? echo number_format($campo11,2) ?> </td>
                           <td class='ui-state-verde align-right'  > <? echo number_format($campo12,2) ?> </td>
                           <td class='ui-state-verde align-right'  > <b><? echo $campo13 ?></b> </td>
                           <td <? echo $claseDif ?> > <? echo number_format($diferencia,2) ?> </td>
                           
                           </tr>  		  
 <?
											
									}
							mysqli_close($mysql);						 
?>

	<tr>
		<td colspan='2'>  </td> 
		<td align="center">Registros: <b><? echo $contador ?></b></td> 
		<td colspan='2'>  </td>
		
		<td align="center"> <b><? echo $total0 ?></b></td>
		<td align="right"> <b><? echo number_format($total1,2) ?></b>  </td>
		<td align="right"> <b><? echo number_format($total2,2) ?></b>  </td>
		<td colspan='4'>  </td> 
		<td align="right"> <b><? echo number_format($total3,2) ?></b>  </td>
		<td align="right"> <b><? echo number_format($total4,2) ?></b>  </td>
		<td >   </td>
		<td align="right"> <b><? echo number_format($total5,2) ?></b>  </td>
	</tr>
</table>
<br>

<!--    <? echo "<font face='Arial' size='0.5'>".$sql."</font>"  ?>  -->   
<!-- <?echo "<br>VARS: (idArchivoGPS= $idArchivoGPS )  (inAnio = $inAnio ) (inMes = $inMes ) (IdSubDireccion= $idSubDireccion	)   rango=  $rango<BR>" ?> -->   

<br>






