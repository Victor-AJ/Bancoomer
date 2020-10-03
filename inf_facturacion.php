<?php
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION["sess_user"]))
{
?>
<script type="text/javascript">	

	//var url="inf_facturacion_tabs.php?par_anio=2010";   
	var url="inf_facturacion_tabs.php";   
	loadHtmlAjax(true, $("#divTabs"), url);	
	
</script>
<form id="opInfomesFac" action=""> 
	<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"> 
    <div class="ui-widget-header align-center">RESUMEN EJECUTIVO - FACTURACION</div>	
    	<div id="divTabs"></div>
    </div> 
</form> 
<?
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  