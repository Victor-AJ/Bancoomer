<?php
session_start();
include("includes/funciones.php");  
include_once  ("Bitacora.class.php"); 
$mysql=conexion_db();
if 	(isset($_SESSION["sess_user"])) 
	$id_login = $_SESSION['sess_iduser'];
// Recibo variables
// ============================
$inAnio 			= $_GET['anoQueryRep']; 
$sql1=" select P.id_factura_estatus as id_factura_estatus, P.tx_estatus as tx_estatus, P.id_mes as mes, P.tx_mes as tx_mes, D.id_factura_estatus as id_factura_estatus_data, D.id_mes as id_mes_data, D.CANT as cant, D.USD as usd, D.MXN as mxn";
$sql1.=" from ";
$sql1.="	( select E.id_factura_estatus,  E.tx_estatus, M.id_mes , M.tx_mes ";
$sql1.=" 			from tbl_factura_estatus E ";
$sql1.=" 				inner join tbl_mes M  where E.tx_indicador='1' ";
$sql1.="			 order by id_factura_estatus, id_mes ";
$sql1.=" 		) as P "; 
$sql1.="left outer join ";  
$sql1.=" 	( select F.id_factura_estatus, F.id_mes, count(*) as CANT,sum(if (F.id_moneda=2,F.FL_PRECIO_USD,0)) as USD,  sum(if(F.id_moneda=1,F.FL_PRECIO_MXN, 0)) as MXN from tbl_factura F";
	
$sql1.="  		where F.tx_anio='$inAnio' and F.tx_indicador='1'  ";




$sql1.=" 		group by F.id_factura_estatus , F.id_mes  ";
$sql1.=" 		) as D on (P.id_factura_estatus=D.id_factura_estatus and P.id_mes=D.id_mes) ";


$result = mysqli_query($mysql, $sql1);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheResultset[] = array(
			'ID_ESTATUS'		=>$row["id_factura_estatus"],
			'TX_ESTATUS'		=>$row["tx_estatus"],
			'ID_MES'			=>$row["mes"],
			'TX_MES'			=>$row["tx_mes"],
			'ID_FAC_STAT_DATA'		=>$row["id_factura_estatus"],
			'ID_MES_DATA'			=>$row["id_mes_data"],
			'CANT'				=>$row["cant"],
			'USD'				=>$row["usd"],
			'MXN'				=>$row["mxn"]
			);
	} 
	

	$sql2= "select month(current_timestamp)	" ; 
	$result = mysqli_query($mysql, $sql2);
	$row = mysqli_fetch_row($result);
	$mesActual = $row[0];
	
	
	$sql3= " select count(*) as CANT,sum(F.fl_precio_usd) as USD,  sum(F.fl_precio_mxn) as MXN from tbl_factura F";
	$sql3.= " where tx_anio='$inAnio' AND F.TX_INDICADOR='1' " ; 
	//SEGURIDAD: ACCESO A SUS DIRECCIONES
	//$sql3.="  		AND id_factura in  ( ";
		//$sql3.="							select distinct(id_factura) from tbl_factura_detalle d    ";
		//$sql3.="    						inner join tbl_centro_costos c on  c.id_centro_costos = d.id_Centro_costos ";
		//$sql3.="   							inner join  TBL_DIRECCION R ON C.ID_DIRECCION= R.ID_DIRECCION ";
		//$sql3.="    						inner join tbl_perfil_direccion DIR on  ( R.id_direccion = DIR.id_direccion and id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";  
		//$sql3.=" 							) ";
	


	$result = mysqli_query($mysql, $sql3);
	$row = mysqli_fetch_row($result);
	$cantidadTotal = $row[0];
	$cantidadUsd = $row[1];
	$cantidadMxn = $row[2];
	

$TheResultsetTotCan[]=array();
$TheResultsetTotUsd[]=array();
$TheResultsetTotMxn[]=array();				
for ($j= 0; $j < 12; $j++)
{						
$TheResultsetTotCan[$j]=0;
$TheResultsetTotUsd[$j]=0;
$TheResultsetTotMxn[$j]=0;
}


$totalRenglonCant=0;
$totalRenglonUsd=0;
$totalRenglonMxn=0;
$lastStat="";

	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_FACTURA" , "$id_login" ,   "tx_anio=$inAnio" ,"" ,"inf_reporte_seguimiento.php");
	 //<\BITACORA>
	 
?>

<table border="0"  cellpadding="1" cellspacing="2" align="center" >
<TR >
<TD class='ui-state-highlight align-center' >&nbsp;ESTATUS&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;ENERO&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;FEBRERO&nbsp; </TD>
<TD class='ui-state-highlight align-center' >&nbsp;MARZO&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;ABRIL&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;MAYO&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;JUNIO&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;JULIO&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;AGOSTO&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;SEPTIEMBRE&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;OCTUBRE&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;NOVIEMBRE&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;DICIEMBRE&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;TOTAL&nbsp;</TD>
<TD class='ui-state-highlight align-center' >&nbsp;IMPACTO % &nbsp;</TD>
<?					
				
			
				
				for ($i = 0; $i < count($TheResultset); $i++)
				{	 
					if ($lastStat <> $TheResultset[$i]['TX_ESTATUS'])
					{
?>
					</tr><tr> 
                        	<td align="right">
	                        	<TABLE border=0  cellspacing=0 cellpadding=0 width="100%"> 
	                        		<TR><TD class="ui-state-default align-left" align="left">
	                        			<B><? echo $TheResultset[$i]['TX_ESTATUS']?></B>
	                        		</TD></TR>
	                        		<TR><TD> <b>USD</b></TD></TR>
	                        		<TR><TD> <b>MXN</b></TD></TR>
	                        	</TABLE>
                        	 </td>
<?
					$lastStat=$TheResultset[$i]['TX_ESTATUS'];
					$totalRenglon=0;
					}
					$ID_STATUS = $TheResultset[$i]['ID_ESTATUS'];
					//DEFINIR COLORES PARA CIFRAS:
					$MES=$TheResultset[$i]['ID_MES'];
					$CANTIDAD=$TheResultset[$i]['CANT'];
					$USD = $TheResultset[$i]['USD'];
					$MXN = $TheResultset[$i]['MXN'];
					
					$estiloUsd="class='ui-state-white align-right' ";
					$estiloMxn="class='ui-state-white align-right' ";
					
					
					IF ($lastStat=="TRAMITE" && $MES==$mesActual && $USD > 0 )
					{
						$estiloUsd= "class='ui-state-verde align-right' ";
					}
					IF ($lastStat=="TRAMITE" && $MES==$mesActual && $MXN  > 0 )
					{
						$estiloMxn= "class='ui-state-verde align-right' ";
					}
					
					IF ( ($lastStat=="TRAMITE" || $lastStat=="PROVISIONADA" ) && $MES==$mesActual-1 && $USD > 0 )
					{
						$estiloUsd= "class='ui-state-amarillo align-right' ";
					}
					IF ( ($lastStat=="TRAMITE"  || $lastStat=="PROVISIONADA" ) && $MES==$mesActual-1 && $MXN  > 0 )
					{
						$estiloMxn= "class='ui-state-amarillo align-right' ";
					}
					IF ( ($lastStat=="TRAMITE" || $lastStat=="PROVISIONADA" ) && $MES <= $mesActual-2 && $USD > 0 )
					{
						$estiloUsd= "class='ui-state-rojo align-right' ";
					}
					IF ( ($lastStat=="TRAMITE"  || $lastStat=="PROVISIONADA" ) && $MES <= $mesActual-2 && $MXN  > 0 )
					{
						$estiloMxn= "class='ui-state-rojo align-right' ";
					}
					
					$TheResultsetTotCan[$MES]+=$CANTIDAD;
					$TheResultsetTotUsd[$MES]+=$USD;
					$TheResultsetTotMxn[$MES]+=$MXN;
					$totalRenglonCant+=$CANTIDAD;
					$totalRenglonUsd+=$USD;
					$totalRenglonMxn+=$MXN;

					$vinculo="javascript:btnVerFacturasListado ('$MES','$ID_STATUS','$inAnio')";
?>                        

					<td align="right"> 
					 
							<TABLE border=0 cellspacing=0 cellpadding=0 width="100%"> 
                        		<TR><TD align="right" >
                                <a href='#' style='cursor:pointer'onclick="<?echo $vinculo ?>" >  
                                <B><? echo number_format($CANTIDAD,0) ?></B>
                                </a>
                                </TD></TR>
                        		<TR><TD align="right" <?php echo $estiloUsd ?> ><? echo number_format($USD,2) ?></TD></TR>
                        		<TR><TD align="right" <?php echo $estiloMxn ?> > <? echo number_format($MXN,2) ?></TD></TR>
                        	</TABLE>
 					 
					 </td>
									
<?

			if ($MES==12)  //colocar columna totales
				{
					$impactoCant= $totalRenglonCant*100/$cantidadTotal;
					$impactoUsd = $totalRenglonUsd*100/$cantidadUsd;
					$impactoMxn = $totalRenglonMxn*100/$cantidadMxn;
				
?>
				<td align="right"> 
					 
							<TABLE border=0 cellspacing=0 cellpadding=0 width="100%"> 
                        		<TR><TD align="right" ><B><? echo number_format($totalRenglonCant,0) ?></B></TD></TR>
                        		<TR><TD align="right" ><B><? echo number_format($totalRenglonUsd,2) ?></B></TD></TR>
                        		<TR><TD align="right" ><B><? echo number_format($totalRenglonMxn,2) ?></B></TD></TR>
                        	</TABLE>
 					 
					 </td>
					 
					 <td align="right"> 
							<TABLE border=0 cellspacing=0 cellpadding=0 width="100%"> 
                        		<TR><TD align="right" ><B><? echo number_format($impactoCant,2) ?> %</B></TD></TR>
                        		<TR><TD align="right" ><B><? echo number_format($impactoUsd,2) ?> %</B></TD></TR>
                        		<TR><TD align="right" ><B><? echo number_format($impactoMxn,2) ?> % </B></TD></TR>
                        	</TABLE>
 					 
					 </td>
					 
							
<?php 
					$totalRenglonCant=0;
					$totalRenglonUsd=0;
					$totalRenglonMxn=0;
				}
			}

				
				
?>
</tr>
<tr> 
                        	<td align="right">
	                        	<TABLE border=0  cellspacing=0 cellpadding=0 width="100%"> 
	                        		<TR><TD align="left" ><B>TOTAL</B></TD></TR>
	                        		<TR><TD> <b>USD</b></TD></TR>
	                        		<TR><TD> <b>MXN</b></TD></TR>
	                        	</TABLE>
                        	 </td>
                        	 
<?php 
for ($j= 1; $j <= 12; $j++)
{						
?>
					
						<td>
							<TABLE border=0 cellspacing=0 cellpadding=0 width="100%"> 
                        		<TR><TD align="right"  class="ui-state-default align-right" ><B><? echo number_format($TheResultsetTotCan[$j],0) ?></B></TD></TR>
                        		<TR><TD align="right" ><B> &nbsp;<? echo number_format($TheResultsetTotUsd[$j],2) ?></B></TD></TR>
                        		<TR><TD align="right" ><B> &nbsp;<? echo number_format($TheResultsetTotMxn[$j],2) ?></B></TD></TR>
                        	</TABLE>
					 	</td>

<?php 
					$totalRenglonCant+=$TheResultsetTotCan[$j];
					$totalRenglonUsd+=$TheResultsetTotUsd[$j];
					$totalRenglonMxn+=$TheResultsetTotMxn[$j];
}
mysqli_close($mysql);
?>

							<td align="right">
	                        	<TABLE border=0  cellspacing=0 cellpadding=0 width="100%"> 
	                        		<TR><TD class="ui-state-default align-right" >
	                        		<B><? echo number_format($totalRenglonCant,0) ?></B>
	                        		</TD></TR>
	                        		<TR><TD> <b><? echo number_format($totalRenglonUsd,2) ?></b></TD></TR>
	                        		<TR><TD> <b><? echo number_format($totalRenglonMxn,2) ?></b></TD></TR>
	                        	</TABLE>
                        	</td>
                        	
                        	<td align="right">
	                        	<TABLE border=0  cellspacing=0 cellpadding=0 width="100%"> 
	                        		<TR><TD class="ui-state-default align-right" >
	                        		<B>100 %</B>
	                        		</TD></TR>
	                        		<TR><TD> <b>100 %</b></TD></TR>
	                        		<TR><TD> <b>100 %</b></TD></TR>
	                        	</TABLE>
                        	</td>
                        	
</tr>
</table>
