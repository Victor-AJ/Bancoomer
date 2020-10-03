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




$tablaCSI= " (SELECT C.TX_CENTRO_COSTOS AS CR_CSI ,D.TX_DEPARTAMENTO AS TX_DEPARTAMENTO_CSI,  C.ID_SUBDIRECCION AS SUBDIRPADRE,  b.fl_precio_mxn AS MONTO_CSI_MXN, F.TX_FACTURA AS TX_FACTURA ";
$tablaCSI.= " FROM tbl_factura_detalle b ";

if ($idCuenta<>0)
	$tablaCSI.="    inner join tbl_producto p on ( b.id_producto = p.id_producto  and  p.id_cuenta_contable= $idCuenta) ";

$tablaCSI.="    inner join tbl_factura f on ( f.id_factura= b.id_factura and f.tx_indicador='1' and  f.id_factura_estatus=$statusCompleto and  tx_anio=$inAnio  $cadMes ) ";
$tablaCSI.="    left outer join tbl_centro_costos c on  c.id_centro_costos = b.id_Centro_costos ";
$tablaCSI.="    left outer join  TBL_DEPARTAMENTO D ON C.ID_DEPARTAMENTO=D.ID_DEPARTAMENTO ";
$tablaCSI.="    left outer join  TBL_SUBDIRECCION S ON C.ID_SUBDIRECCION= S.ID_SUBDIRECCION ";
$tablaCSI.="    left outer join  TBL_DIRECCION R ON C.ID_DIRECCION= R.ID_DIRECCION ";
//SEGURIDAD: ACCESO A SUS DIRECCIONES
$tablaCSI.="    inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and DIR.tx_indicador='1' and  id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  
//SEGURIDAD INACTIVOS
$tablaCSI.=" where b.tx_indicador = '1'  ";

$tablaCSI.="    )  AS TCSI ";






$sql="SELECT TCSI.CR_CSI, TCSI.TX_FACTURA ,   SUM(TCSI.MONTO_CSI_MXN) AS MONTO_CSI_MXN ";
$sql.= " FROM ";
$sql.=   $tablaCSI ;
$sql.= " WHERE TCSI.CR_CSI = $tx_CR  "; 
$sql.= " GROUP BY TCSI.CR_CSI, TCSI.TX_FACTURA ";
$sql.= " ORDER  BY MONTO_CSI_MXN ";



$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheResultset[] = array(
			'CR_CSI'	=>$row["CR_CSI"],
			'MONTO_CSI_MXN'	=>$row["MONTO_CSI_MXN"],
			'TX_FACTURA'	=>$row["TX_FACTURA"]
			);
	} 
	
	 
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_FACTURA_DETALLE" , "$id_login" ,   "tx_cr=$tx_CR" ,"" ,"inf_conciliacion_factura_CSI.php");
	 //<\BITACORA>

	
?>




<table  cellpadding="1" cellspacing="1" >




	<tr >
		<td class='ui-notas align-center' > <b>CR CSI</b> </td> 
		<td class='ui-notas align-center' > <b>MONTO CSI (MXN)</b>  </td> 
		<td class='ui-notas align-center' > <b>FACTURA</b>  </td>
	</tr>
    
<tr ><td colspan="5">&nbsp;  </td> </tr>
<?								
$total1=0;
$total2=0;
								for ($i = 0; $i < count($TheResultset); $i++)
								{	         			 
										$campo1=$TheResultset[$i]['CR_CSI'];		
										$campo3=$TheResultset[$i]['MONTO_CSI_MXN'];	
										$total1+=$campo3;
										$campo4=$TheResultset[$i]['TX_FACTURA'];	
									
										$claseDif="class='ui-state-verde align-right'";
										
											
?>
                           <tr>
                           <td class='ui-state-verde align-left'  align="left"  > <b><? echo $campo1 ?></b>&nbsp; </td> 
                           <td class='ui-state-verde align-right' > <? echo number_format($campo3,2) ?> </td> 
                           <td class='ui-state-verde align-left' ><b> <? echo $campo4 ?></b>&nbsp; </td>
                           </tr>  		  
 <?
											
									}
							mysqli_close($mysql);						 
?>


 

<tr><td>  </td> <td align="right"> <b><? echo number_format($total1,2) ?></b>  </td> 
<td>  </td></tr>
</table>

 <!-- <? echo "<font face='Arial' size='0.5'>".$sql."</font>"  ?> -->  
<!-- <?echo "<br>VARS: (idArchivoGPS= $idArchivoGPS )  (inAnio = $inAnio ) (inMes = $inMes ) (IdSubDireccion= $idSubDireccion	)   rango=  $rango<BR>" ?> -->   






