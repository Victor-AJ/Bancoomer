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
	
	
	$sql = "   SELECT id_moneda, tx_moneda, tx_descripcion, a.tx_indicador, a.fh_mod, b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta " ; 
	$sql.= "     FROM tbl_moneda a, tbl_usuario b, tbl_usuario c " ; 
	$sql.= "    WHERE a.id_usuariomod = b.id_usuario " ;
	$sql.= " 	  AND a.id_usuarioalta = c.id_usuario " ; 
	$sql.= " ORDER BY id_moneda ";
	
	$result = mysqli_query($mysql,$sql); 
	
	$tsv  = array();
	$html = array();
	
	while($row = mysqli_fetch_array($result, MYSQL_NUM))
	{
		$tsv[]  = implode("\t", $row);
   		$html[] = "<tr><td>" .implode("</td><td>", $row) ."</td></tr>";	
	}	
	
	$tsv 	 = implode("\r\n", $tsv);
	$titulos ="<tr>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>ID</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>MONEDA</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>DESCRIPCION</font></td>";
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
	 $myBitacora->anotaBitacora ($mysql, "EXCEL", "TBL_MONEDA" , "$id_login" ,   "" , ""  ,  "excel_monedas.php");
	 //<\BITACORA>
	 
	mysqli_close($mysql);	
//} else {
//	echo "Sessi&oacute;n Invalida. Por favor vuelva a registrarse. ";
//}	
?>  	