<?php
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
//for run
session_start();
if 	(!isset($_SESSION['sess_user']))
{
	echo "Sessi&oacute;n Invalida";
}
else
{
include("includes/funciones.php");  
$mysql=conexion_db();

$tx_p1	= $_GET['tx_p1']; //consultar
$tx_p2	= $_GET['tx_p2']; //insertar
$tx_p3	= $_GET['tx_p3']; //actualziar
$tx_p4	= $_GET['tx_p4']; //borrar
$tx_p5	= $_GET['tx_p5']; //exportar


?>
<link rel='STYLESHEET' type='text/css' href='common/style.css'>
<link rel="STYLESHEET" type="text/css" href="codebase/dhtmlxtree.css">
<script  src="codebase/dhtmlxcommon.js"></script>
	<script  src="codebase/dhtmlxtree.js"></script>
    
<div class="ui-widget-header align-center">CATALOGO</div>

<form id="catalogForm"   action="">   
   	<input id="tx_p1" name="tx_p1" type="hidden" value="<? echo $tx_p1 ?>" />
	<input id="tx_p2" name="tx_p2" type="hidden" value="<? echo $tx_p2 ?>" />
    <input id="tx_p3" name="tx_p3" type="hidden" value="<? echo $tx_p3 ?>" />
    <input id="tx_p4" name="tx_p4" type="hidden" value="<? echo $tx_p4 ?>" />
    <input id="tx_p5" name="tx_p5" type="hidden" value="<? echo $tx_p5 ?>" />
    
    <div id="divError"></div> 
    <div id="divGrid" style="padding:1%;width:98%" class="ui-widget ui-widget-content ui-corner-all">
    	<div id="errorGrid" style="display:none">
        	<div id="errorContent" class="ui-corner-all" style="padding: 0pt 0.7em;" ></div>
        </div>
        
        
      <table>
        <tr height="1">
            <td><a href="#" onclick="javascript:tree.openAllItems(0);" > Expandir</a></td>
            <td><a href="#" onclick="javascript:tree.closeAllItems(0);" >Colapsar</a></td>
        </tr>
        <tr><td>&nbsp;</td></tr>
    </table>   

    <div id="treeboxbox_tree" style="width:800; height:600;background-color:#f5f5f5;border :1px solid Silver;"></div>


   
       
<script type="text/javascript">

tree=new dhtmlXTreeObject("treeboxbox_tree","100%","100%",0);
tree.setImagePath("codebase/imgs/csh_yellowbooks/");
tree.enableCheckBoxes(0);
tree.loadXML("process_tree_mapa.php");

</script>        
 </div>
 </form>
<?php 
}
?>