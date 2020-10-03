<?php
//header("Content-type: application/vnd.ms-excel; name='excel'"); 
//header("Content-Disposition: attachment; filename=ficheroExcel.xls");
//header("Pragma: no-cache");
//header("Expires: 0");
//echo $_POST["datos_a_enviar"];
header("Content-Type: application/force-download");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0" );
header("Content-Disposition: attachment; filename=\"Eficiencia_Inventario.xls\"");
header("Pragma: public");
header("Expires: 0");
echo stripslashes($_POST["datos_a_enviar"]);
?>
