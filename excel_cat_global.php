<?
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    session_cache_limiter("must-revalidate");
    $fileName = "CSI_catalogo.xls";
	header("Content-type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=$fileName");
	
	
	session_start();
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();
	if 	(isset($_SESSION["sess_user"])) 
		$id_login = $_SESSION['sess_iduser'];
	
	
	
	$tx_catalogo		= $_GET["tx_catalogo"];
	
	$sql = " SELECT id, tx_clave, tx_valor, tx_valor_complementario  ,  tx_observaciones , b.tx_indicador, fh_fecha_modifica, e.tx_nombre , fh_fecha_alta, d.tx_nombre " ; 
	$sql.= " FROM tbl45_catalogo_global b ";
	$sql.= " inner join tbl_usuario d on  d.id_usuario = b.id_usuario_alta ";
	$sql.= " inner join tbl_usuario e on  e.id_usuario = b.id_usuario_modifica ";
	$sql.= " where  substr( tx_clave,1,instr(tx_clave ,'-')-1) = '$tx_catalogo'";
	$sql.= " ORDER BY tx_clave ";
	
	//echo "sql ".$sql;
	
	$result = mysqli_query($mysql,$sql); 
	
	$tsv  = array();
	$html = array();
	
	while($row = mysqli_fetch_array($result, MYSQL_NUM))
	{
		$tsv[]  = implode("\t", $row);
   		$html[] = "<tr><td>" .implode("</td><td>", $row) ."</td></tr>";	
	}	
	
	 $tx_catalogo=str_replace('_', ' ', $tx_catalogo);
	
	$tsv 	 = implode("\r\n", $tsv);
	$titulos ="<tr>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>ID</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>CLAVE ASIGNADA</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>VALOR DE $tx_catalogo </font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>VALOR COMPLEMENTARIO</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>DESCRIPCION </font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>INDICADOR</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>FECHA MODIFICACION</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>USUARIO MODIFICACION</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>FECHA ALTA</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>USUARIO ALTA</font></td>";
	$titulos.="</tr>";
	
	$html = "<table border='1'>". $titulos . implode("\r\n", $html) ."</table>";



	//echo $tsv;	
	//echo "<br>";	
	echo $html;
	
	//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "EXCEL", "TBL45_CATALOGO_GLOBAL $tx_catalogo" , "$id_login" ,   "" , ""  ,  "excel_cat_global.php");
	 //<\BITACORA>
	
	mysqli_close($mysql);	
//} else {
//	echo "Sessi&oacute;n Invalida. Por favor vuelva a registrarse. ";
//}	
?>	