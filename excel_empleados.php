<?

	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    session_cache_limiter("must-revalidate");
    $fileName = "CSI_inventario.xls";
	header("Content-type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=$fileName");

	
	session_start();
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php"); 
	$mysql=conexion_db();
	if 	(isset($_SESSION["sess_user"])) 
		$id_login = $_SESSION['sess_iduser'];

		
$sql = " select R.tx_nombre, S.tx_subdireccion, D.tx_departamento, C.tx_centro_costos,  E.tx_registro, L.id_empleado, E.tx_empleado,  E.tx_indicador, ";
$sql .=" L.id_producto, L.fl_precio_usd, L.fl_precio_mxn, PA.tx_Valor , L.tx_login, L.tx_sid_terminal, L.tx_serial_number, P.tx_producto, P.in_licencia, "; 
$sql .=" P1.tx_proveedor, P.fl_precio, P.fl_precio_mxn , L.id_cuenta,  T.tx_cuenta, P2.tx_proveedor ";
$sql .=" from "; 
$sql .=" tbl_licencia L ";
$sql .=" inner join tbl_empleado E on (E.id_empleado= L.id_empleado ) ";
$sql .=" inner  join tbl_centro_costos C on (C.id_centro_costos=E.id_centro_costos ) ";
$sql .=" inner  join tbl_departamento D on (D.id_departamento=C.id_departamento ) ";
$sql .=" inner  join tbl_subdireccion S on (S.id_subdireccion=C.id_subdireccion ) ";
$sql .=" inner  join tbl_direccion R on (R.id_direccion = C.id_direccion ) ";
$sql .=" inner  join tbl_producto P on (P.id_producto=L.id_producto ) ";
$sql .=" inner  join tbl_proveedor P1 on (P1.id_proveedor=P.id_proveedor ) ";
$sql .=" inner join tbl45_catalogo_global PA on PA.id=P.id_cuenta_contable ";
$sql .=" inner  join tbl_cuenta T on (T.id_cuenta = L.id_cuenta ) ";
$sql .=" inner  join tbl_proveedor P2 on (P2.id_proveedor=T.id_proveedor ) ";
$sql .=" where l.tx_indicador = '1' ";
$sql .=" order by R.tx_nombre, S.tx_subdireccion , D.tx_departamento, E.tx_empleado, id_producto ";
	
 	
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

	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>DIRECCION</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>SUBDIRECCION</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>DEPARTAMENTO</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>CR</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>REGISTRO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>ID EMPLEADO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>EMPLEADO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>INDICADOR EMPLEADO ACTIVO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>ID PROD LIC CSI</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>PRECIO LICENCIA USD</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>PRECIO LICENCIA MXN</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CONCEPTO CONTABLE EN PROD</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>LOGIN LICENCIA</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>SID TERMINAL</font></td>";

	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>SERIAL NUMBER LICENCIA</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>PRODUCTO DE LA LICENCIA</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>INDICADOR LICENCIA</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>PROVEEDOR DEL PRODUCTO</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>PRECIO PRODUCTO USD</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>PRECIO PRODUCTO MXN</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>ID CUENTA CSI</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>CUENTA</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>PROVEEDOR</font></td>";
	
	$titulos.="</tr>";
	
	$html = "<table border='1'>". $titulos . implode("\r\n", $html) ."</table>";

	echo $html;
	  $myBitacora = new Bitacora();
	  $myBitacora->anotaBitacora ($mysql, "EXCEL", "TBL_LICENCIAS" , "$id_login" ,   "" , ""  ,  "excel_empleados.php");
	
	mysqli_close($mysql);	
 
?>	