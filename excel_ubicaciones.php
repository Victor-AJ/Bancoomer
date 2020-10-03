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
	
	$sql = "   SELECT id_ubicacion, tx_pais, tx_estado, tx_ubicacion, a.tx_indicador, a.fh_mod, d.tx_nombre AS usuario_mod, a.fh_alta, e.tx_nombre AS usuario_alta " ; 
	$sql.= "     FROM tbl_ubicacion a, tbl_estado b, tbl_pais c, tbl_usuario d, tbl_usuario e ";
	$sql.= "    WHERE a.id_estado 		= b.id_estado ";
	$sql.= "      AND a.id_pais 		= c.id_pais ";		
	$sql.= "      AND a.id_usuariomod 	= d.id_usuario ";
	$sql.= "      AND b.id_usuarioalta 	= e.id_usuario ";
	$sql.= " ORDER BY id_ubicacion ";
	
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
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>PAIS</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>ESTADO</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>UBICACION</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>INDICADOR</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>FECHA MODIFICACION</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>USUARIO MODIFICACION</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>FECHA ALTA</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>USUARIO ALTA</font></td>";
	$titulos.="</tr>";
	
	$html = "<table border='1'>". $titulos . implode("\r\n", $html) ."</table>";

	$fileName = 'csi_ubicaciones';
	header("Content-type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=$fileName");

	//echo $tsv;	
	//echo "<br>";	
	echo $html;
	
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "EXCEL", "TBL_UBICACION" , "$id_login" ,   "" , ""  ,  "excel_ubicaciones.php");
	 //<\BITACORA>
	
	mysqli_close($mysql);	
//} else {
//	echo "Sessi&oacute;n Invalida. Por favor vuelva a registrarse. ";
//}	
?>  	