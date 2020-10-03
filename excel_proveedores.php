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
		
		
	$sql = "   SELECT id_proveedor, tx_proveedor, tx_proveedor_corto, tx_direccion, tx_pagina, tx_fax, tx_contacto1, tx_puesto1, tx_telefono1, tx_celular1, tx_correo1, tx_contacto2, tx_puesto2, tx_telefono2, tx_celular2, tx_correo2, a.tx_indicador, a.fh_mod, b.tx_nombre AS usuario_mod, a.fh_alta, c.tx_nombre AS usuario_alta " ; 
	$sql.= "     FROM tbl_proveedor a, tbl_usuario b, tbl_usuario c ";
	$sql.= "    WHERE a.id_usuariomod 	= b.id_usuario ";
	$sql.= "      AND a.id_usuarioalta 	= c.id_usuario ";
	$sql.= " ORDER BY id_proveedor ";
	
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
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>RAZON SOCIAL</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>PROVEEDOR</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>DIRECCION</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>PAGINA WEB</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>FAX</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CONTACTO1 - NOMBRE</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CONTACTO1 - PUESTO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CONTACTO1 - TELEFONO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CONTACTO1 - CELULAR</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CONTACTO1 - CORREO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CONTACTO2 - NOMBRE</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CONTACTO2 - PUESTO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CONTACTO2 - TELEFONO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CONTACTO2 - CELULAR</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CONTACTO2 - CORREO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>INDICADOR</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>FECHA MODIFICACION</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>USUARIO MODIFICACION</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>FECHA ALTA</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>USUARIO ALTA</font></td>";
	$titulos.="</tr>";
	
	$html = "<table border='1'>". $titulos . implode("\r\n", $html) ."</table>";

	$fileName = 'csi_proveedores';
	header("Content-type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=$fileName");

	//echo $tsv;	
	//echo "<br>";	
	echo $html;
	
		 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "EXCEL", "TBL_PROVEEDOR" , "$id_login" ,   "" , ""  ,  "excel_proveedores.php");
	 //<\BITACORA>
	
	mysqli_close($mysql);	
//} else {
//	echo "Sessi&oacute;n Invalida. Por favor vuelva a registrarse. ";
//}	
?>	