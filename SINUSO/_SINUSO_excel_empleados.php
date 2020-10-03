<?
//session_start();
//if 	(isset($_SESSION['sess_user']))
//{
	include("includes/funciones.php");  
	$mysql=conexion_db();	
	
	$sql = " SELECT a.id_empleado, tx_registro, tx_usuario_red, tx_usuario_espacio, tx_empleado, e.tx_centro_costos, h.tx_nombre, g.tx_subdireccion, f.tx_departamento, tx_categoria, tx_tipologia, tx_funcion, tx_responsable, tx_telefono, tx_correo, tx_notas, tx_n3, tx_n4, tx_n5, tx_n6, tx_n7, tx_n8, tx_n9 ";
	$sql.= "   FROM tbl_empleado a, tbl_ubicacion b, tbl_estado c, tbl_pais d, tbl_centro_costos e, tbl_departamento f, tbl_subdireccion g, tbl_direccion h ";
	$sql.= "  WHERE a.id_ubicacion 		= b.id_ubicacion ";
	$sql.= "    AND b.id_estado  		= c.id_estado ";
	$sql.= "    AND b.id_pais   		= c.id_pais ";
	$sql.= "    AND a.id_centro_costos 	= e.id_centro_costos ";
	$sql.= "    AND e.id_departamento 	= f.id_departamento ";
	$sql.= "    AND e.id_subdireccion 	= g.id_subdireccion ";
	$sql.= "    AND h.id_direccion 		= g.id_direccion ";
	$sql.= " ORDER BY tx_empleado ";
		
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
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>REGISTRO</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>USUARIO RED</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>USUARIO ESPACIO</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>NOMBRE</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CR</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>DIRECCION</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>SUBDIRECCION</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>DEPARTAMENTO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CATEGORIA</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>TIPOLOGIA</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>FUNCIONES</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>RESPONSABLE</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>TELEFONO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>CORREO</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>NOTAS</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>N3</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>N4</font></td>";
	$titulos.="	<td align='center' bgcolor='#003399'><font color=white>N5</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>N6</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>N7</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>N8</font></td>";
	$titulos.=" <td align='center' bgcolor='#003399'><font color=white>N9</font></td>";
	$titulos.="</tr>";
	
	$html = "<table border='1'>". $titulos . implode("\r\n", $html) ."</table>";

	$fileName = 'csi_proveedores';
	header("Content-type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=$fileName");

	//echo $tsv;	
	//echo "<br>";	
	echo $html;
	
	mysqli_close($mysql);	
//} else {
//	echo "Sessi&oacute;n Invalida. Por favor vuelva a registrarse. ";
//}	
?>	