<?php
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=archivo.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	
	echo "<table border=1>\n";
	echo "<tr>\n";
	echo "<th>Nombre</th>\n";
	echo "<th>Email</th>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td><font color=green>Manuel Gomez</font></td>\n";
	echo "<td>manuel@gomez.com</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td><font color=blue>Pago gomez</font></td>\n";
	echo "<td>paco@gomez.com</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
?>


<?php 
$shtml="<table>"; 
$shtml=$shtml."<tr>"; 
$shtml=$shtml."<td>Id</td><td>Codigo</td><td>US$</td>"; 
$shtml=$shtml."</tr>"; 
$shtml=$shtml."<tr>"; 
$shtml=$shtml."<td>1</td><td>C4325</td><td>2000.00</td>"; 
$shtml=$shtml."</tr>"; 
$shtml=$shtml."<tr>"; 
$shtml=$shtml."<td>2</td><td>DX456</td><td>1000.00</td>"; 
$shtml=$shtml."</tr>"; 
$shtml=$shtml."<tr>"; 
$shtml=$shtml."<td>3</td><td>&nbsp;</td><td>-50.00</td>"; 
$shtml=$shtml."</tr>"; 
$shtml=$shtml."<tr>"; 
$shtml=$shtml."<td>4</td><td>A18-TG</td><td>20.64</td>"; 
$shtml=$shtml."</tr>"; 
$shtml=$shtml."</table>"; 
$scarpeta=""; //carpeta donde guardar el archivo. 
//debe tener permisos 775 por lo menos 
$sfile=$scarpeta."/xxxx.xls"; //ruta del archivo a generar 
$fp=fopen($sfile,"w"); 
fwrite($fp,$shtml); 
fclose($fp); 
echo "<a href='".$sfile."'>Haz click aqui</a>"; 
?> 





<?php
include 'library/config.php';
include 'library/opendb.php';

$query  = "SELECT fname, lname FROM students";
$result = mysql_query($query) or die('Error, query failed');

$tsv  = array();
$html = array();
while($row = mysql_fetch_array($result, MYSQL_NUM))
{
   $tsv[]  = implode("\t", $row);
   $html[] = "<tr><td>" .implode("</td><td>", $row) .              "</td></tr>";
}

$tsv = implode("\r\n", $tsv);
$html = "<table>" . implode("\r\n", $html) . "</table>";

$fileName = 'mysql-to-excel.xls';
header("Content-type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=$fileName");

echo $tsv;
//echo $html;

include 'library/closedb.php';
?>

