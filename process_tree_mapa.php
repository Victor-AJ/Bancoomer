<?php
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
session_start();

include("includes/funciones.php");  
include_once  ("Bitacora.class.php"); 
$mysql=conexion_db();
if (isset($_SESSION["sess_user"])) 
						 $id_login = $_SESSION['sess_iduser'];
	
// First we need to determine the leaf nodes
$SQLL = "SELECT t1.id_pantalla FROM tbl47_pantalla AS t1 " ;
$SQLL .= " LEFT JOIN tbl47_pantalla as t2 ON (t1.id_pantalla = t2.id_parent and (t2.tx_indicador = 1 or t2.tx_indicador = 2)   )" ; 
$SQLL .= "  WHERE t2.id_pantalla IS NULL and t1.tx_indicador=1";

$result = mysqli_query($mysql , $SQLL ) or die("Couldn t execute query.".mysql_error());

$leafnodes = array();

while($rw = mysqli_fetch_array($result,MYSQL_ASSOC)) 
{           
   $leafnodes[$rw['id_pantalla']] = $rw['id_pantalla'];
}
 

// Recursive function that do the job
function display_node($parent, $level) 
{
   global $leafnodes;
   global $mysql;
   if($parent >0) 
   {
      $wh = 'id_parent='.$parent;
   } else 
   {
      $wh = 'ISNULL(id_parent)';
   }
   
   $SQL = "SELECT id_pantalla, tx_programa as name , tx_name,  tx_modulo, tx_tipo, tx_indicador, id_parent ,img0, img1, img2  FROM tbl47_pantalla WHERE ".$wh . " and (tx_indicador='1' or tx_indicador='2'  )ORDER BY id_pantalla ";
   
   $result = mysqli_query($mysql, $SQL ) or die("Couldn t execute query.".mysql_error());
   
   while($row = mysqli_fetch_array($result,MYSQL_ASSOC)) 
   {
   	//$nombre= ($row['name']<>"")?"(".$row['name'].")":"";
   	$nombre= "";
   	      echo "<item text='".$row['tx_name']."  ".$nombre."' id='".$row['id_pantalla']."'    im0='".$row['img0']."' im1='".$row['img1']."' im2='".$row['img2']."'  ";         

      

      
      if($row['id_pantalla'] == $leafnodes[$row['id_pantalla']]) 
	  {
	  $leaf='true'; 
	  echo "/>";
	  }
	  else 
	  {
	  $leaf = 'false';  // isLeaf comparation
	  echo ">";
	  }
	  
        // recursion
      display_node((integer)$row[id_pantalla],$level+1);
	  if( $leaf == 'false' ) 
	  echo "</item>";
   }
}
 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) 
{
   header("Content-type: application/xhtml+xml;charset=utf-8");
} else 
{
   header("Content-type: text/xml;charset=utf-8");
}

$et = ">";
echo "<?xml version='1.0' encoding='utf-8'?$et\n";
echo "<tree id='0'>";
// Here we call the function at root level
display_node('',0);
echo "</tree>";

 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL47_PANTALLA" , "$id_login" ,  "", ""  ,  "process_tree_mapa.php");
	 //<\BITACORA>
	 
	 
mysqli_close($mysql);
?>
