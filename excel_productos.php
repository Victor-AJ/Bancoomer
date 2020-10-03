<?php 
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
	
	$sql = "   SELECT id_producto, tx_proveedor, tx_producto, tx_valor  ,  tx_producto_corto, b.tx_descripcion, b.tx_descripcion_corta, fl_precio, tx_moneda, in_licencia, b.tx_indicador, b.fh_mod, d.tx_nombre AS usuario_mod, b.fh_alta, e.tx_nombre AS usuario_alta " ; 
	$sql.= "     FROM tbl_producto b ";
	$sql.= " inner join tbl_proveedor a on b.id_proveedor 	= a.id_proveedor ";
	$sql.= " inner join tbl_moneda c on b.id_moneda 	= c.id_moneda ";
	$sql.= " inner join tbl_usuario d on  b.id_usuariomod = d.id_usuario ";
	$sql.= " inner join tbl_usuario e on  b.id_usuarioalta = e.id_usuario ";
	$sql.= " left outer join tbl45_catalogo_global p  on  b.id_cuenta_contable = p.id ";
		
	$sql.= " ORDER BY tx_proveedor, tx_producto ";
	
	//echo "sql ".$sql;
	
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
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>PROVEEDOR</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>PRODUCTO</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>CUENTA CONTABLE</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>PRODUCTO CORTO </font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>DESCRIPCION</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>DESCRIPCION CORTA</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>PRECIO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>MONEDA</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>LICENCIA</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>INDICADOR</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>FECHA MODIFICACION</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>USUARIO MODIFICACION</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>FECHA ALTA</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>USUARIO ALTA</font></td>";
	$titulos.="</tr>";
	
	$html = "<table border='1'>". $titulos . implode("\r\n", $html) ."</table>";

	$fileName = 'csi_productos';
	header("Content-type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=$fileName");

	//echo $tsv;	
	//echo "<br>";	
	echo $html;
	
		 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "EXCEL", "TBL_PRODUCTO" , "$id_login" ,   "" , ""  ,  "excel_productos.php");
	 //<\BITACORA>
	 
	mysqli_close($mysql);	

?>	