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

$id_login =NULL;
if 	(isset($_SESSION["sess_user"])) 
	$id_login = $_SESSION['sess_iduser'];
	
	
// Recibo variables
// ============================
$idArchivoGPS			= $_GET['theGpsQueryFile'];
$idArchivoESB			= $_GET['theEssbaseQueryFile'];  
$inAnio 			= $_GET['anoQueryInf']; 
$inMes				= $_GET['mesQueryInf'];
$idDireccionG		= $_GET['idDireccionG'];
$idDireccionE		= $_GET['idDireccionE'];
$rango				= $_GET['rango'];
$XLS			= $_GET['XLS'];



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

 
//echo $sql  ;
//ECHO "<BR>";

$tablaGPS =" (SELECT C.ID_SUBDIRECCION AS ID_SUBDIRGPS, S.TX_SUBDIRECCION AS TX_SUBDIR_IN_CSI,C.ID_DIRECCION AS DIRPADRE_G,  SUM(G.IM_MONTO_LOCAL)  AS MONTO_GPS   FROM tbl42_gps G";
$tablaGPS.=" LEFT OUTER JOIN TBL_centro_costos c ON c.tx_centro_costos= g.tx_cr ";  
$tablaGPS.=" left outer join  TBL_DEPARTAMENTO D ON C.ID_DEPARTAMENTO=D.ID_DEPARTAMENTO ";
$tablaGPS.=" left outer join  TBL_SUBDIRECCION S ON C.ID_SUBDIRECCION=S.ID_SUBDIRECCION ";
$tablaGPS.="    left outer join  TBL_DIRECCION R ON C.ID_DIRECCION= R.ID_DIRECCION ";
//SEGURIDAD: ACCESO A SUS DIRECCIONES
$tablaGPS.="    inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  



$tablaGPS.="  WHERE IN_ANIO=$inAnio $cadMes  AND TX_CLASE IN ('S2','KR','SD') AND ID_ARCHIVO=$idArchivoGPS ";
if ($idCuenta<>0)
	$tablaGPS.="   and  G.id_cuenta_contable= $idCuenta ";
	
$tablaGPS.="    GROUP  BY C.ID_SUBDIRECCION)  AS TGPSA ";



$tablaESB= " (SELECT C.ID_SUBDIRECCION AS ID_SUBDIRESB, S.TX_SUBDIRECCION  AS TX_ESBSUBDIR_IN_CSI, C.ID_DIRECCION AS DIRPADRE_E, SUM(G.IM_MONTO) AS MONTO_ESB ";
$tablaESB.= "  FROM TBL43_ESSBASE G ";
$tablaESB.="    left outer join tbl_centro_costos c ON c.tx_centro_costos= g.tx_cr  ";
$tablaESB.=" left outer join  TBL_DEPARTAMENTO D ON C.ID_DEPARTAMENTO=D.ID_DEPARTAMENTO ";
$tablaESB.=" left outer join  TBL_SUBDIRECCION S ON C.ID_SUBDIRECCION=S.ID_SUBDIRECCION ";
$tablaESB.=" left outer join  TBL_DIRECCION R ON C.ID_DIRECCION=R.ID_DIRECCION ";
//SEGURIDAD: ACCESO A SUS DIRECCIONES
$tablaESB.="    inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  


$tablaESB.="    WHERE  in_anio=$inAnio  $cadMes AND ID_ARCHIVO=$idArchivoESB ";

if ($idCuenta<>0)
	$tablaESB.="   and  G.id_cuenta_contable= $idCuenta ";
	
$tablaESB.="    group by C.ID_SUBDIRECCION)  AS TESB ";



//$sql = " SELECT SUM(ID_SUBDIRGPS) as ID_SUBDIRGPS, TX_SUBDIR_IN_CSI as TX_SUBDIR_IN_CSI, SUM(MONTO_GPS) as MONTO_GPS, SUM(ID_SUBDIRESB ) as ID_SUBDIRESB , SUM(MONTO_ESB) as MONTO_ESB, SUM(DIRPADRE) as DIRPADRE, SUM(DIF) as DIF  from  (     ";

$sql = "select sum(ID_SUBDIRGPS) AS ID_SUBDIRGPS, TX_SUBDIR_IN_CSI, sum(MONTO_GPS) AS MONTO_GPS , sum(ID_SUBDIRESB) AS ID_SUBDIRESB, sum(MONTO_ESB) AS MONTO_ESB , TX_ESBSUBDIR_IN_CSI, sum(DIRPADRE_G) AS DIRPADRE_G, sum(DIRPADRE_E) AS DIRPADRE_E , SUM(DIF) AS DIF FROM  ( "; 

$sql.=" SELECT TGPSA.ID_SUBDIRGPS,    TGPSA.TX_SUBDIR_IN_CSI, TGPSA.MONTO_GPS  ,  TESB.ID_SUBDIRESB  , TESB.MONTO_ESB , TESB.TX_ESBSUBDIR_IN_CSI, TGPSA.DIRPADRE_G, TESB.DIRPADRE_E,  ifnull(TGPSA.MONTO_GPS,0) -  ifnull(TESB.MONTO_ESB,0)   AS DIF ";
$sql.= " FROM ";
$sql.=   $tablaGPS ;
$sql.="  LEFT JOIN ";  
$sql.=   $tablaESB ;
$sql.= " ON TGPSA.ID_SUBDIRGPS=TESB.ID_SUBDIRESB ";
 
IF ($idDireccionG <> 0)
$sql.= " WHERE TGPSA.DIRPADRE_G = $idDireccionG  "; 

IF ($idDireccionE <> 0)
$sql.= " WHERE TESB.DIRPADRE_E = $idDireccionE  "; 


$sql.= " UNION ALL ";

$sql.="SELECT TGPSA.ID_SUBDIRGPS, TGPSA.TX_SUBDIR_IN_CSI, TGPSA.MONTO_GPS  ,  TESB.ID_SUBDIRESB  ,  TESB.MONTO_ESB ,TESB.TX_ESBSUBDIR_IN_CSI,TGPSA.DIRPADRE_G ,  TESB.DIRPADRE_E,  ifnull(TGPSA.MONTO_GPS,0) -  ifnull(TESB.MONTO_ESB,0)   AS DIF ";
$sql.= " FROM ";
$sql.=   $tablaGPS ;
$sql.="  RIGHT JOIN ";  
$sql.=   $tablaESB ;
$sql.= " ON TGPSA.ID_SUBDIRGPS=TESB.ID_SUBDIRESB ";

$sql.="   WHERE TGPSA.ID_SUBDIRGPS  IS NULL ";

IF ($idDireccionG <> 0)
$sql.= " and TGPSA.DIRPADRE_G= $idDireccionG  "; 

IF ($idDireccionE <> 0)
$sql.= " and TESB.DIRPADRE_E = $idDireccionE  "; 

$sql.=" )  as tabla  group by tx_subdir_in_csi , tx_esbsubdir_in_csi order by tx_esbsubdir_in_csi ,  tx_subdir_in_csi ";


$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheResultset[] = array(
			'ID_SUBDIRGPS'	=>$row["ID_SUBDIRGPS"],
			'TX_SUBDIR_IN_CSI'	=>$row["TX_SUBDIR_IN_CSI"],
			'MONTO_GPS'	=>$row["MONTO_GPS"],
			'ID_SUBDIRESB'	=>$row["ID_SUBDIRESB"],
			'MONTO_ESB'	=>$row["MONTO_ESB"],
			'TX_ESBSUBDIR_IN_CSI'	=>$row["TX_ESBSUBDIR_IN_CSI"],
			'DIF'	=>$row["DIF"],
			'DIRPADRE_G' =>$row["DIRPADRE_G"],
			'DIRPADRE_E' =>$row["DIRPADRE_E"]
		
			);
	} 
	
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, $actionBita , "TBL_FACTURA_DETALLE TBL42_GPS" , "$id_login" ,   "tx_anio=$inAnio id_mes=$inMes id_rango=$rango id_cuenta_contable=$idCuenta id_Archivo_g=$idArchivoGPS id_archivo_e=$idArchivoESB xls=$XLS  " ,"" ,"inf_conciliacion_subdireccion_ESB.php");
	 //<\BITACORA>
	

	$vinculoTitle="javascript:btnVerDetalleGE('','','SubDireccionG','SubDireccionE','departamento','#divDepartamentoConcESB')";
	$vinculoXls="javascript: enviarPopUpXls('inf_conciliacion_subdireccion_ESB.php?theGpsQueryFile=$idArchivoGPS&theEssbaseQueryFile=$idArchivoESB&anoQueryInf=$inAnio&mesQueryInf=$inMes&rango=$rango&idDireccionG=$idDireccionG&idDireccionE=$idDireccionE&XLS=1'); ";
	$border = ($XLS ==1)? " border='1' ": " border='0' ";
	
	
?>

<table <?php echo $border ?> cellpadding="1" cellspacing="1">

<tr>
 


	<td class='ui-notas align-center'>
		<b> 
			<a  class='align-center' href='#' style='cursor:pointer' onclick="<?echo $vinculoTitle ?>" >
		        SUBDIRECCI&Oacute;N EN CSI
		    </a>
		</b>
	</td>
	
<td class='ui-notas align-center'> <b>MONTO GPS</b>  </td> 

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
										$campo1=$TheResultset[$i]['ID_SUBDIRGPS'];		
										$campo2=$TheResultset[$i]['TX_SUBDIR_IN_CSI'];
										$campo3=$TheResultset[$i]['MONTO_GPS'];	
										$total1+=$campo3;
										$campo4=$TheResultset[$i]['ID_SUBDIRESB'];	
										$campo5=$TheResultset[$i]['MONTO_ESB'];	
										$total2+=$campo5;
										$campo6=$TheResultset[$i]['TX_ESBSUBDIR_IN_CSI'];
										$campo7=$TheResultset[$i]['DIF'];
										
										$diferencia= 0;
										$diferencia+= $campo7;
										
										IF ( $diferencia >=$rangoMin &&  $diferencia <=$rangoMax)
											$claseDif="class='ui-state-verde align-right'";
										ELSE 
											$claseDif="class='ui-state-rojo align-right'";

											
											
										$vinculo="javascript:void(0)";
										$etiqueta="";
										if ($campo2 <> "")
											{
											$vinculo="javascript:btnVerDetalleGE('$campo1','',		'SubDireccionG','SubDireccionE','departamento','#divDepartamentoConcESB')";
											$etiqueta=$campo2;
											}
											elseif ($campo6 <> "")
												{
												$vinculo="javascript:btnVerDetalleGE(''		,  '$campo4' , 'SubDireccionG','SubDireccionE','departamento','#divDepartamentoConcESB')";
												$etiqueta=$campo6;
												}
										
										$campo8=$TheResultset[$i]['DIRPADRE_G'];
										$campo9=$TheResultset[$i]['DIRPADRE_E'];
										
										
										
										
?>
                           <tr>
	                           <td class='ui-state-verde align-left'  align="left"  > 
	                           		
	                           		<a href='#' style='cursor:pointer' onclick="<?echo $vinculo ?>"   >  
	                           			 <? echo $etiqueta ?>
	                           		</a> 
	                           		&nbsp;
	                           		</td>
	                           		
	                         
	                           <td class='ui-state-verde align-right' > <? echo number_format($campo3,2) ?> </td> 
	                           
	                           <td class='ui-state-verde align-right'  > <? echo number_format($campo5,2) ?></td> 
	                           
	                           <td  <? echo $claseDif  ?> > <? echo number_format($campo7,2) ?></td>
	                          
                           </tr>  		  
 <?
											
									}

									mysqli_close($mysql);
									
?>

<tr>
 <td>  </td>
 <td align="right"> <b><? echo number_format($total1,2) ?></b>  </td> 
<td align="right"> <b><? echo number_format($total2,2) ?></b>  </td> 
<td>  </td></tr>
</table>
  <!--   <? echo "<font face='Arial' size='0.5'>".$sql."</font>"  ?>  --> 
  <!--  <? echo "VARS: (idArchivoGPS= $idArchivoGPS )  (inAnio = $inAnio ) (inMes = $inMes ) (IdDireccion= $idDireccion	)(idArchivoESB	= $idArchivoESB	)   rango=  $rango<BR>"; ?>   -->






