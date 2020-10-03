<?php
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
$XLS			= $_GET['XLS'];
$tx_CR 			= $_GET['tx_CR'];


	



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






$tablaGPS =" (SELECT G.TX_CR AS CR_GPS, C.ID_SUBDIRECCION AS SUBDIRPADRE, G.IM_MONTO_LOCAL  AS MONTO_GPS ,TX_NUMERO_DOC, TX_REFERENCIA  FROM tbl42_gps G";
$tablaGPS.=" LEFT OUTER JOIN TBL_centro_costos c ON c.tx_centro_costos= g.tx_cr ";  
$tablaGPS.=" LEFT OUTER JOIN TBL_DIRECCION R ON  C.ID_DIRECCION= R.ID_DIRECCION ";
//SEGURIDAD: ACCESO A SUS DIRECCIONES
$tablaGPS.="    inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  


$tablaGPS.="  WHERE IN_ANIO=$inAnio $cadMes  AND TX_CLASE IN ('KR','SD') AND ID_ARCHIVO=$idArchivoGPS ";
if ($idCuenta<>0)
	$tablaGPS.="   and  G.id_cuenta_contable= $idCuenta ";

$tablaGPS.="    )  AS TGPSA ";


$sql=" SELECT  TGPSA.CR_GPS ,  TGPSA.MONTO_GPS , TGPSA.TX_NUMERO_DOC, TGPSA.TX_REFERENCIA ";
$sql.= " FROM ";
$sql.=   $tablaGPS ;
$sql.= " WHERE TGPSA.CR_GPS = $tx_CR  ";
$sql.= " ORDER BY TGPSA.MONTO_GPS  "; 




$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheResultset[] = array(
			'CR_GPS'	=>$row["CR_GPS"],
			'MONTO_GPS'	=>$row["MONTO_GPS"],
			'TX_NUMERO_DOC'	=>$row["TX_NUMERO_DOC"],
			'TX_REFERENCIA'	=>$row["TX_REFERENCIA"]
			);
	} 
	
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL42_GPS" , "$id_login" ,   "tx_cr=$tx_CR" ,"" ,"inf_conciliacion_factura_GPS.php");
	 //<\BITACORA>
	 
	 
?>
<table  cellpadding="1" cellspacing="1" >

	<tr >
		<td class='ui-notas align-center' > <b>CR GPS</b> </td> 
		<td class='ui-notas align-center' > <b>MONTO LOCAL </b>  </td> 
		<td class='ui-notas align-center' > <b>DOCUMENTO</b>  </td>
		<td class='ui-notas align-center' > <b>REFERENCIA</b>  </td> 
	</tr>
<tr ><td colspan="5">&nbsp;  </td> </tr>
<?								
$total1=0;
$total2=0;
								for ($i = 0; $i < count($TheResultset); $i++)
								{	         			 
										$campo1=$TheResultset[$i]['CR_GPS'];		
										$campo2=$TheResultset[$i]['MONTO_GPS'];	
										$total1+=$campo2;
										
										$campo3=$TheResultset[$i]['TX_NUMERO_DOC'];	
										$campo4=$TheResultset[$i]['TX_REFERENCIA'];	

											$claseDif="class='ui-state-verde align-right'";
?>
                           <tr>
                           <td class='ui-state-verde align-left'  align="left"  > 
                          	 <b><? echo $campo1 ?></b>
                           </td> 
                           <td class='ui-state-verde align-right' > <? echo number_format($campo2,2) ?> </td> 
                           <td class='ui-state-verde align-left' >
                           	 <? echo $campo3 ?>
                           </td>
                           <td <? echo  $claseDif?>  > <? echo $campo4 ?></td>
                           </tr>  		  
 <?
											
									}
							mysqli_close($mysql);						 
?>


 

<tr><td>  </td> <td>  </td><td align="right"> <b><? echo number_format($total1,2) ?></b>  </td> </tr>
</table>

 <!-- <? echo "<font face='Arial' size='0.5'>".$sql."</font>"  ?> -->  
<!-- <?echo "<br>VARS: (idArchivoGPS= $idArchivoGPS )  (inAnio = $inAnio ) (inMes = $inMes ) (IdSubDireccion= $idSubDireccion	)   rango=  $rango<BR>" ?> -->   






