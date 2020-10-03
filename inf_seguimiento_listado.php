<?php
session_start();
include("includes/funciones.php");  
include_once  ("Bitacora.class.php"); 
$mysql=conexion_db();
if 	(isset($_SESSION["sess_user"])) 
	$id_login = $_SESSION['sess_iduser'];

// Recibo variables
// ============================
$id_mes			= $_GET['id_mes']; 
$id_status 		= $_GET['id_status']; 
$id_anio		= $_GET['id_anio'];



$sql=" SELECT  F.ID_FACTURA, F.ID_FACTURA_ESTATUS, F.TX_FACTURA, F.TX_REFERENCIA, M.TX_MONEDA , ";
$sql.=" if (M.TX_MONEDA='USD',F.FL_PRECIO_USD,0) as FL_PRECIO_USD  , if(M.TX_MONEDA='MXN', F.FL_PRECIO_MXN,0) as  FL_PRECIO_MXN , P.TX_PROVEEDOR  ";
$sql.= " FROM  TBL_FACTURA F";
$sql.= " INNER JOIN TBL_PROVEEDOR P ON P.ID_PROVEEDOR = F.ID_PROVEEDOR ";
$sql.= " INNER JOIN TBL_MONEDA  M ON M.ID_MONEDA = F.id_moneda ";
$sql.= " WHERE  ID_MES=$id_mes and TX_ANIO=$id_anio and ID_FACTURA_ESTATUS=  $id_status  and  F.tx_indicador ='1'  ";



$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheResultset[] = array(
			
			'ID_FACTURA'	=>$row["ID_FACTURA"],
			'ID_FACTURA_ESTATUS'	=>$row["ID_FACTURA_ESTATUS"],
			'TX_FACTURA'	=>$row["TX_FACTURA"],
			'TX_REFERENCIA'	=>$row["TX_REFERENCIA"],
			'TX_MONEDA'	=>$row["TX_MONEDA"],
			'FL_PRECIO_USD'	=>$row["FL_PRECIO_USD"],
			'FL_PRECIO_MXN'	=>$row["FL_PRECIO_MXN"],
			'TX_PROVEEDOR'	=>$row["TX_PROVEEDOR"]
			
			);
	} 

 
 	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_FACTURA" , "$id_login" ,   "tx_anio=$inAnio id_status=$id_status id_mes=$id_mes" ,"" ,"inf_seguimiento_listado.php");
	 //<\BITACORA>
	
?>




<table   cellpadding="1" cellspacing="1" align="center" >
	<tr>
		<td class='ui-state-highlight align-center'  align="center" > <b>ID FACTURA</b> </td> 
		<td class='ui-state-highlight align-center'    align="center"  > <b>PROVEEDOR</b> </td>
        <td class='ui-state-highlight align-center'   align="center"  > <b>FACTURA</b> </td>
        <td class='ui-state-highlight align-center'   align="center"  > <b>REFERENCIA</b> </td>
        <td class='ui-state-highlight align-center'   align="center"  > <b>MONEDA</b> </td>
      	<td class='ui-state-highlight align-center'   align="center"  > <b>PRECIO_USD</b> </td>
    	<td class='ui-state-highlight align-center'  align="center"  > <b>PRECIO_MXN</b> </td>
	</tr>

	
<tr ><td colspan="5">&nbsp;  </td> </tr>
<?								
$total1=0;
$total2=0;
$contador=0;
								for ($i = 0; $i < count($TheResultset); $i++)
								{	         			 
										$campo0=$TheResultset[$i]['ID_FACTURA'];	
										$campo1=$TheResultset[$i]['TX_PROVEEDOR'];		
										$campo2=$TheResultset[$i]['TX_FACTURA'];
										$campo3=$TheResultset[$i]['TX_REFERENCIA'];
										$campoMoneda=$TheResultset[$i]['TX_MONEDA'];			
										$campo4=$TheResultset[$i]['FL_PRECIO_USD'];	
										$total1+=$campo4;
										$campo5=$TheResultset[$i]['FL_PRECIO_MXN'];	
										$total2+=$campo5;
										$claseDif="class='ui-state-verde align-right'";
										$contador+=1;	
?>
                           <tr>
                           <td class='ui-state-verde' align="center" > <? echo $campo0 ?></td> 
                           <td class='ui-state-verde' align="left"> <? echo $campo1 ?></td> 
                           <td class='ui-state-verde' align="right"> <? echo $campo2 ?></td> 
                           <td class='ui-state-verde' > <? echo $campo3 ?></td> 
                           <td class='ui-state-verde' > <? echo $campoMoneda?></td>
                           
                           <td class='ui-state-verde' align="right"> <? echo  number_format($campo4,2) ?></td> 
                           <td class='ui-state-verde' align="right"> <? echo  number_format($campo5,2) ?></td> 
                                                                                                            
                           
                          
                           
                           </tr>  		  
 <?
											
									}
							mysqli_close($mysql);						 
?>

	<tr>
		<td align="center"> <b><? echo $contador ?></b> </td> 
		<td align="center"> </td> 
		<td colspan='3'>  </td>
		<td align="right"> <b><? echo number_format($total1,2) ?></b>  </td>
		<td align="right"> <b><? echo number_format($total2,2) ?></b>  </td>
	</tr>
</table>

<br>

<!--   <? echo "<font face='Arial' size='0.5'>".$sql."</font>"  ?>    -->
<!-- <?echo "<br>VARS: (idArchivoGPS= $idArchivoGPS )  (inAnio = $inAnio ) (inMes = $inMes ) (IdSubDireccion= $idSubDireccion	)   rango=  $rango<BR>" ?> -->   








