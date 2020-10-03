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
$idArchivoGPS		= $_GET['theGpsQueryFile'];
$idArchivoESB		= $_GET['theEssbaseQueryFile'];  
$inAnio 			= $_GET['anoQueryInf']; 
$inMes				= $_GET['mesQueryInf'];
$idDireccion		= $_GET['idDireccion'];
$rango				= $_GET['rango'];
$idCuenta			= $_GET['tipoCuenta'];

	
	
	

//extraccion de parametros BD
$rangoDif_par=0;
$sql = "   select tx_valor from tbl45_catalogo_global where tx_clave='PARAMETRO-4' and tx_indicador=1 " ;
$result = mysqli_query($mysql, $sql);
$row = mysqli_fetch_row($result);
$rangoDif_par = $row[0];	

$rangoMax = $rangoDif_par*1;
$rangoMin = $rangoDif_par*-1;  


$long_coment=5;
$sql = "   select tx_valor from tbl45_catalogo_global where tx_clave='PARAMETRO-5' and tx_indicador=1 " ;
$result = mysqli_query($mysql, $sql);
$row = mysqli_fetch_row($result);
$long_coment = $row[0];	


//exacto
if ($rango=="A")   //Acumulado
$cadMes= " and id_mes <= ".$inMes." ";  
else
$cadMes="  and id_mes=".$inMes." ";

//falta acumulado

//ECHO "<BR>";

$tablaGPS =" (SELECT C.ID_DIRECCION AS ID_DIRGPS, R.TX_NOMBRE_CORTO AS TX_DIR_IN_CSI, SUM(G.IM_MONTO_LOCAL)  AS MONTO_GPS   FROM tbl42_gps G";
$tablaGPS.=" LEFT OUTER JOIN TBL_centro_costos c ON c.tx_centro_costos= g.tx_cr ";  
$tablaGPS.=" left outer join  TBL_DEPARTAMENTO D ON C.ID_DEPARTAMENTO=D.ID_DEPARTAMENTO ";
$tablaGPS.=" left outer join  TBL_SUBDIRECCION S ON C.ID_SUBDIRECCION=S.ID_SUBDIRECCION ";
$tablaGPS.=" left outer join  TBL_DIRECCION R ON C.ID_DIRECCION=R.ID_DIRECCION ";
//SEGURIDAD: ACCESO A SUS DIRECCIONES
$tablaGPS.="    inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  


$tablaGPS.="  WHERE IN_ANIO=$inAnio $cadMes  AND TX_CLASE IN ('S2','KR','SD') AND ID_ARCHIVO=$idArchivoGPS ";
if ($idCuenta<>0)
	$tablaGPS.="   and  G.id_cuenta_contable= $idCuenta ";
	
$tablaGPS.="    GROUP  BY C.ID_DIRECCION)  AS TGPSA ";

$tablaESB= " (SELECT C.ID_DIRECCION AS ID_DIRESB, R.TX_NOMBRE_CORTO  AS TX_ESBDIR_IN_CSI, SUM(G.IM_MONTO) AS MONTO_ESB ";
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

$tablaESB.="    group by C.ID_DIRECCION)  AS TESB ";


$sql = " select sum(ID_DIRGPS) AS ID_DIRGPS, TX_DIR_IN_CSI       , sum(MONTO_GPS) AS MONTO_GPS , sum(ID_DIRESB) AS ID_DIRESB, sum(MONTO_ESB) AS MONTO_ESB , TX_ESBDIR_IN_CSI, SUM(DIF) AS DIF  FROM  ( "; 
$sql.= " SELECT TGPSA.ID_DIRGPS           , TGPSA.TX_DIR_IN_CSI, TGPSA.MONTO_GPS        ,  TESB.ID_DIRESB          , TESB.MONTO_ESB                 , TESB.TX_ESBDIR_IN_CSI, ifnull(TGPSA.MONTO_GPS,0) -  ifnull(TESB.MONTO_ESB,0)   AS DIF ";
$sql.= " FROM ";
$sql.=   $tablaGPS ;
$sql.="  left JOIN ";  
$sql.=   $tablaESB ;
$sql.= " ON TGPSA.ID_DIRGPS=TESB.ID_DIRESB ";
 

$sql.= " UNION  all ";

$sql.="SELECT TGPSA.ID_DIRGPS, TGPSA.TX_DIR_IN_CSI, TGPSA.MONTO_GPS  ,  TESB.ID_DIRESB  , TESB.MONTO_ESB , TESB.TX_ESBDIR_IN_CSI, ifnull(TGPSA.MONTO_GPS,0) -  ifnull(TESB.MONTO_ESB,0)   AS DIF ";
$sql.= " FROM ";
$sql.=   $tablaGPS ;
$sql.="  RIGHT JOIN ";  
$sql.=   $tablaESB ;
$sql.= " ON TGPSA.ID_DIRGPS=TESB.ID_DIRESB ";
$sql.="   WHERE TGPSA.ID_DIRGPS  IS NULL ";

$sql.=" )  as tabla  group by TX_DIR_IN_CSI , TX_ESBDIR_IN_CSI  order by TX_ESBDIR_IN_CSI,TX_DIR_IN_CSI";




$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheResultset[] = array(
			'ID_DIRGPS'	=>$row["ID_DIRGPS"],
			'TX_DIR_IN_CSI'	=>$row["TX_DIR_IN_CSI"],
			'MONTO_GPS'	=>$row["MONTO_GPS"],
			'ID_DIRESB'	=>$row["ID_DIRESB"],
			'MONTO_ESB'	=>$row["MONTO_ESB"],
			'TX_ESBDIR_IN_CSI'	=>$row["TX_ESBDIR_IN_CSI"],
		
			'DIF'	=>$row["DIF"]
			);
	}

      

	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, $actionBita , "TBL42_GPS TBL43_ESSBASE" , "$id_login" ,   "tx_anio=$inAnio id_mes=$inMes id_rango=$rango id_cuenta_contable=$idCuenta id_archivo_g=$idArchivoGPS id_archivo_e=$idArchivoESB xls=$XLS  " , ""  ,  "inf_conciliacion_direccion_ESB.php");
	 //<\BITACORA>
	

	$vinculoTitle="javascript:btnVerDetalleGE('','','DireccionG' , 'DireccionE'  , 'subdireccion','#divSubDireccionConcESB')";
	$vinculoXls="javascript: enviarPopUpXls('inf_conciliacion_direccion_ESB.php?theGpsQueryFile=$idArchivoGPS&theEssbaseQueryFile=$idArchivoESB&anoQueryInf=$inAnio&mesQueryInf=$inMes&rango=$rango&tipoCuenta=$idCuenta&XLS=1'); ";
	$border = ($XLS ==1)? " border='1' ": " border='0' ";
	
?>

<table<?php echo $border ?>  cellpadding="1" cellspacing="2">
<tr >
 
	
	<td class='ui-notas align-center'>
		<b> 
			<a  class='align-center' href='#' style='cursor:pointer' onclick="<?echo $vinculoTitle ?>" >
		        DIRECCI&Oacute;N EN CSI
		       
		    </a>
		</b>
	</td>
	
	<td class='ui-notas align-center'> <b>MONTO GPS</b>  </td>

	<td class='ui-notas align-center'> <b>MONTO ESB</b>  </td>

	<td  class='ui-notas align-center'> <b>DIFERENCIA</b>  </td>
	
	<td  class='ui-notas align-center'> <b>COMENTARIO</b>  </td>
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
										$campo1=$TheResultset[$i]['ID_DIRGPS'];
										$campo2=$TheResultset[$i]['TX_DIR_IN_CSI'];
										$campo3=$TheResultset[$i]['MONTO_GPS'];	
										$campo4=$TheResultset[$i]['ID_DIRESB'];
										$campo5=$TheResultset[$i]['MONTO_ESB'];	
										$campo6=$TheResultset[$i]['TX_ESBDIR_IN_CSI'];
										$campo7=$TheResultset[$i]['DIF'];
										$diferencia= 0;
										$total1+=$campo3;
										$total2+=$campo5;
										$diferencia+= $campo7;
										
										$vinculo="javascript:void(0)";
										$idEtiqueta=0;
										$idNameEtiqueta="0";
										$etiqueta="";
										$tipoComm="G"; //default
										
										if ($campo2 <> null && $campo2 <> "") //si hay texto en direccion csi enontrada de GPS, cinculo por DIR CSI
											{
											$vinculo="javascript:btnVerDetalleGE('$campo1','',		'DireccionG','DireccionE','subdireccion','#divSubDireccionConcESB')";
											$idEtiqueta=$campo1;
											$idNameEtiqueta=$campo1;
											$etiqueta=$campo2;
											}
											elseif ( $campo6 <>null && $campo6 <> "") //si hay texto en direccion csi encontrada de esb, vinculo por direccion ESB
												{
												$vinculo="javascript:btnVerDetalleGE(''		,  '$campo4' , 'DireccionG','DireccionE','subdireccion','#divSubDireccionConcESB')";
												$idEtiqueta=$campo4;
												$idNameEtiqueta=$campo4;
												$etiqueta=$campo6;
												}
												
												
										IF ( $diferencia >= $rangoMin &&  $diferencia <= $rangoMax)
											$claseDif="class='ui-state-verde align-right'";
										ELSE 
										{	
											$claseDif="class='ui-state-rojo align-right'";  
											
											
											if ($idEtiqueta==0)
                             				{
                             					if ( $campo3<>0) //hay monto gps
                             					{   
                             						$idNameEtiqueta="0_G";  
                             						$tipoComm="G";  
                             					}
                             					else
                             					{	
                             						$idNameEtiqueta="0_E";     
                             						$tipoComm="E"; 
                             					}
		                             		}
                             		
											$suComentario=""; 
											
											if ($idEtiqueta==0)
											$dirQ="is null";
											else 
											$dirQ="=".$idEtiqueta;
											
											
											
											$sqlCom=" select ifnull(tx_comentario,'') from tbl44_comentarios_concilia where id_direccion $dirQ and in_anio=".$inAnio." and id_mes=".$inMes."  and";  
											$sqlCom.=" tx_rango='".$rango."' and id_archivo_g=".$idArchivoGPS."  and id_archivo_e=".$idArchivoESB." and tx_tipo='$tipoComm' and id_cuenta=$idCuenta" ;
											
											$result = mysqli_query($mysql, $sqlCom);
											$row = mysqli_fetch_row($result);
											$suComentario = $row[0];	
											$suComentario= ($suComentario==null)?'':$suComentario;
											
											
										} 
										
		
                             	
?>
                           <tr>
                           <td class='ui-state-verde align-left' align="left" ><a href='#' style='cursor:pointer' onclick="<?echo $vinculo ?>" > 
                           <? echo $etiqueta?></a>&nbsp;</td> 
                           
                           <!--    <td>(<? echo $campo1 ?> ) <? echo $campo2 ?></td>  -->
                           
                           <td class='ui-state-verde align-right' >  
                           <? echo number_format($campo3,2) ?> 
                           </td> 
                           
                           <!--   <td>(<? echo $campo4 ?> ) <? echo $campo6 ?></td>   -->
                           
                           <td class='ui-state-verde align-right'  >
                           <? echo number_format($campo5,2) ?> 
                           </td>
                           <td  <? echo $claseDif  ?>  >
                           <? echo number_format($campo7,2) ?>
                           </td>
                           
                           
                            <?
                            IF (  !($diferencia >=$rangoMin &&  $diferencia <=$rangoMax) ) 
                            {
                            ?>
                           
                           <td nowrap="nowrap">
	                           <?php
     							if ($XLS <> 1)
	     							{  
     							?>	
                           			<input  maxlength="200" size="<?php echo $long_coment ?>" type='text' class="mytext"    name='tx_esb_<? echo $idNameEtiqueta ?>'                           value='<? echo $suComentario?>' >
                           			<a class='align-right' href='#' style='cursor:pointer' onclick="javascript:saveComent(document.forms[0].tx_esb_<? echo $idNameEtiqueta ?>,<? echo $idEtiqueta ?>,'<? echo $tipoComm?>');" >
                             			<img border="0" src="images/iconsave.jpg">
                             		</a> 
                            	<?php 
      								} 
      							else
      								{
							  			echo $suComentario;
      								} 
								?>
                           </td>
                           	<?php  
                             }
                            ?>
                              
                             </tr>  		  

                     <?php  
                     }
                     mysqli_close($mysql);
                     ?>

 

<tr>
 	<td align="right">  </td>
	 <td align="right"> <b><? echo number_format($total1,2) ?></b>  </td>
	 <td align="right"> <b><? echo number_format($total2,2) ?></b>  </td>
	 <td>  </td>
 </tr>
</table>

 <!-- <?php  echo "VARS: (idArchivoGPS= $idArchivoGPS )  (inAnio = $inAnio ) (inMes = $inMes ) (IdDireccion= $idDireccion	)(idArchivoESB	= $idArchivoESB	) rango=  $rango<BR>"; ?>  -->
 <!--   <?php echo $sql  ;?>  -->  





